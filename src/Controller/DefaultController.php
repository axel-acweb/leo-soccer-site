<?php

namespace App\Controller;

use App\Repository\BookRepository;
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
        $books = $this->bookRepo->findBy(["status" => "valid"]);
        return $this->render('index.html.twig', [
            "page" => "home",
            "books" => $books
        ]);
    }
}
