<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Services\GroupStagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DefaultController extends AbstractController
{

    private $em, $bookRepo;

    public function __construct(EntityManagerInterface $em, BookRepository $bookRepo) {
        $this->em = $em;
        $this->bookRepo = $bookRepo;
    }

    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        $books = $this->bookRepo->findBy(["status" => "valid"], ["points" => "DESC"]);

        usort($books, function($a, $b)
        {
            if($b->getPoints() == $a->getPoints()) {
                $bAverage = $b->getGoalFor() - $b->getScoreAgainst();
                $aAverage = $a->getGoalFor() - $a->getScoreAgainst();
                return ($aAverage > $bAverage) ? -1 : +1;
            }
            return strcmp($b->getPoints(), $a->getPoints());
        });
        return $this->render('index.html.twig', [
            "page" => "home",
            "books" => $books
        ]);
    }
}
