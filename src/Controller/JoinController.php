<?php

namespace App\Controller;

use App\Entity\Book;
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
            $clientId = "AZmihrs6ngq1-8Bfd6lu8dCCV6Qte2zLPXUxlfSnvTH5DaSQ3uZPPYQl6YidsBVNpXGqKL5lYyg4MOxM";
            $secret = "EAOrjYlQKRRGiqGx2q3OlBmOLngLDoW8vMUtT7QZYL-kpKp50IlgoFQfXQ3oTKivi78IM9IINVt3jhhx";
            //dd("https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId");
            //curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId");
            $response = $this->client->request('GET', "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId", [
                'auth_basic' => [$clientId, $secret],
            ]);

            $content = $response->toArray();

            $book = $this->bookRepo->findOneBy(["id" => $id]);
            //dd($content);
            if($book instanceof Book && isset($content['status']) && $content['status'] == "APPROVED") {
                $book->setStatus('valid');
                $this->em->persist($book);
                $this->em->flush();
                return new JsonResponse('', 200);

            }
        }
        return new JsonResponse('nok', 404);
    }

    #[Route('/reserver', name: 'app_join')]
    public function index(Request $request): Response
    {

        if (empty($this->getUser())) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $user->setRoles(['ROLE_ADMIN']);
        $this->em->persist($user);
        $this->em->flush();
        $isBook = $this->bookRepo->findOneBy([
            "user" => $this->getUser()
        ]);

        if($isBook) {
            return $this->render("book/already_booked.html.twig", ["book" => $isBook, "status" => Book::STATUS, "page" => "book"]);
        }

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

        return $this->render('book/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
