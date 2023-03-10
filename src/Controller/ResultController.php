<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Services\GroupStagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ResultController extends AbstractController
{

    private $em, $bookRepo;

    public function __construct(EntityManagerInterface $em, BookRepository $bookRepo) {
        $this->em = $em;
        $this->bookRepo = $bookRepo;
    }

    #[Route('/resultats', name: 'app_results')]
    public function index(): Response
    {
        $gsService = new GroupStagesService();
        $groupStagesMatch = $gsService->getAllGroupeStagesMatch($this->em);
        return $this->render('results/index.html.twig', [
            "page" => "results",
            "gsM" => $groupStagesMatch
        ]);
    }

    #[Route('/resultats/html', name: 'app_results_html')]
    public function indexHtml(): Response
    {
        $gsService = new GroupStagesService();
        $groupStagesMatch = $gsService->getAllGroupeStagesMatch($this->em);
        $html = $this->render('results/results_html.html.twig', [
            "gsM" => $groupStagesMatch
        ]);

        return new Response($html, 200);
    }
}
