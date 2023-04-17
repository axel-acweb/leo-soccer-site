<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\GroupStageMatch;
use App\Entity\Player;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JoinController extends AbstractController
{
    private $em, $bookRepo, $client;

    public function __construct(EntityManagerInterface $em, BookRepository $bookRepo, HttpClientInterface $client) {
        $this->em = $em;
        $this->bookRepo = $bookRepo;
        $this->client = $client;
    }

    #[Route('/payment/validate/{id}', name: 'app_payment_validate', methods: ['POST'])]
    public function validatePayment(Request $request, $id): JsonResponse
    {
        $request_body = json_decode($request->getContent(), true);;
        if(isset($request_body['orderID'])) {
            $orderId = $request_body['orderID'];
            //$ch = curl_init();
            //$clientId = "AZmihrs6ngq1-8Bfd6lu8dCCV6Qte2zLPXUxlfSnvTH5DaSQ3uZPPYQl6YidsBVNpXGqKL5lYyg4MOxM";
            $clientId = "AZdgiKT-xu2poZL03xNGq6p7dSCZdG5WkSSAH3X_5v-CqLwyclOZXPRBzGTUl608IS5M_Kd3LXd3l3mf";
            //$secret = "EAOrjYlQKRRGiqGx2q3OlBmOLngLDoW8vMUtT7QZYL-kpKp50IlgoFQfXQ3oTKivi78IM9IINVt3jhhx";
            $secret = "EDNAs2HtyLmoDCbX7ZJWq2PcnC43f66lSTQCVsJDDT4hIpRHOqGMIMxp2Ee5SRlCPFBz_WjFRvobqAUT";
            //dd("https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId");
            //curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId");



            $response = $this->client->request('GET', "https://api-m.paypal.com/v2/checkout/orders/$orderId", [
                'auth_basic' => [$clientId, $secret],
            ]);

            $content = $response->toArray();

            $book = $this->bookRepo->findOneBy(["id" => $id]);
            //dd($content);
            if($book instanceof Book && isset($content['status']) && $content['status'] == "APPROVED") {
                $capture = $this->client->request('POST', "https://api-m.paypal.com/v2/checkout/orders/$orderId/capture", [
                    'auth_basic' => [$clientId, $secret],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]);
                
                $book->setStatus('valid');
                $this->em->persist($book);
                $this->em->flush();
                return new JsonResponse('', 200);

            }
        }
        return new JsonResponse('nok', 404);
    }

    #[Route('/payment/create', name: 'app_payment_create', methods: ['GET'])]
    public function createPayment(Request $request): JsonResponse
    {
        $request_body = json_decode($request->getContent(), true);;
            //$ch = curl_init();
            //$clientId = "AZmihrs6ngq1-8Bfd6lu8dCCV6Qte2zLPXUxlfSnvTH5DaSQ3uZPPYQl6YidsBVNpXGqKL5lYyg4MOxM";
            $clientId = "AZdgiKT-xu2poZL03xNGq6p7dSCZdG5WkSSAH3X_5v-CqLwyclOZXPRBzGTUl608IS5M_Kd3LXd3l3mf";
            //$secret = "EAOrjYlQKRRGiqGx2q3OlBmOLngLDoW8vMUtT7QZYL-kpKp50IlgoFQfXQ3oTKivi78IM9IINVt3jhhx";
            $secret = "EDNAs2HtyLmoDCbX7ZJWq2PcnC43f66lSTQCVsJDDT4hIpRHOqGMIMxp2Ee5SRlCPFBz_WjFRvobqAUT";
            //dd("https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId");
            //curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId");

            $fields_string = '{
                "intent":"CAPTURE",
                "purchase_units": [
                    {
                        "amount": {
                            "value": 25,
                            "currency_code": "EUR"
                        }
                    }
                ]
            }';

            //return new JsonResponse($fields_string, 200);



            $response = $this->client->request('POST', "https://api-m.paypal.com/v2/checkout/orders", [
                'auth_basic' => [$clientId, $secret],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                "body" => $fields_string
            ]);

            $content = $response->toArray();

            return new JsonResponse($content, 200);
    }

    #[Route('/reserver', name: 'app_join')]
    public function index(Request $request): Response
    {

        if (empty($this->getUser())) {
            return $this->redirectToRoute('app_login');
        }

        $isBook = $this->bookRepo->findOneBy([
            "user" => $this->getUser()
        ]);

        if($isBook instanceof Book) {
            $gsM = $this->em->getRepository(GroupStageMatch::class)->findByTeam($isBook);
            $gs = $gsM instanceOf GroupStageMatch ? $gsM->getGroupStage() : null;
            return $this->render("book/already_booked.html.twig", ["book" => $isBook, "status" => Book::STATUS, "group_stage" => $gs, "page" => "book"]);
        }

        $books = $this->bookRepo->findAll();

        if(count($books) != 12) {
            $book = (new Book())->setUser($this->getUser());
            $i = 1;
            while($i <= 4) {
                $i++;
                $player = new Player();
                $book->addPlayer($player);
            }
            $this->em->persist($book);
            $form = $this->createForm(BookType::class, $book);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->persist($book);
                $this->em->flush();
                return $this->redirectToRoute('app_join');
            }
        }

        return $this->render('book/index.html.twig', [
            'form' => isset($form) ? $form->createView() : null
        ]);
    }
}
