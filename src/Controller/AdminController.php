<?php

namespace App\Controller;

use App\Entity\GroupStageMatch;
use App\Repository\BookRepository;
use App\Repository\GroupStageMatchRepository;
use App\Repository\GroupStageRepository;
use App\Services\GroupStagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class AdminController extends AbstractController
{
    private $gsRepo, $gsmRepo, $bookRepo, $em;

    public function __construct(GroupStageRepository $gsRepo, GroupStageMatchRepository $gsmRepo, BookRepository $bookRepo, EntityManagerInterface $em) {
        $this->gsRepo = $gsRepo;
        $this->gsmRepo = $gsmRepo;
        $this->bookRepo = $bookRepo;
        $this->em = $em;
    }

    #[Route('/admin/score/{id}', name: 'app_admin_score_add', methods: ['POST'])]
    public function addScore(HttpFoundationRequest $request, $id): Response
    {
        $team1Score = $request->request->get('team1');
        $team2Score = $request->request->get('team2');
        $groupStagesMatch = $this->gsmRepo->findOneBy(['id' => $id]);
        if($groupStagesMatch instanceof GroupStageMatch) {
            $oldTeam1Score = $groupStagesMatch->getTeam1Score();
            $oldTeam2Score = $groupStagesMatch->getTeam2Score();

            $team1 = $groupStagesMatch->getTeam1();
            $team2 = $groupStagesMatch->getTeam2();

            $team1->setGoalFor($team1->getGoalFor() + $team1Score - $oldTeam1Score);
            $team2->setGoalFor($team2->getGoalFor() + $team2Score - $oldTeam2Score);

            $team1->setScoreAgainst($team1->getScoreAgainst() + $team2Score - $oldTeam2Score);
            $team2->setScoreAgainst($team2->getScoreAgainst() + $team1Score - $oldTeam1Score);

            $groupStagesMatch->setTeam1Score($team1Score);
            $groupStagesMatch->setTeam2Score($team2Score);

            $groupStagesMatch->setPlayed(true);
            $this->em->persist($groupStagesMatch);
            $this->em->persist($team1);
            $this->em->persist($team2);
            $this->em->flush();
        }
        return $this->redirectToRoute("app_admin");
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $gsService = new GroupStagesService();
        $gStages = $this->gsRepo->findAll();
        $gStagesMatch = $this->gsmRepo->findAll();
        $teams = $this->bookRepo->findBy(["status" => "valid"]);
        if(empty($gStagesMatch) && count($teams) == 12) {
            $group_stages = $gsService->generateGroupeStages($teams, $gStages);
            foreach($group_stages as $key => $gs) {
                $gsEntity = $gStages[$key - 1];
                foreach($gs['matches'] as $match) {
                    $match = (new GroupStageMatch())
                            ->setGroupStage($gsEntity)
                            ->setTeam1($match[0])
                            ->setTeam2($match[1]);
                    $this->em->persist($match);
                }
            }
        }
        $this->em->flush();

        $gsValid = !empty($gStagesMatch);


        $groupStagesMatch = $gsService->getAllGroupeStagesMatch($this->em);

        //dd($groupStagesMatch);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            "group_stages" => $gStages,
            "gsM" => $groupStagesMatch,
            "begin" => $gsValid,
            "allMatchPlayed" => GroupStagesService::isAllMatchPlayed($this->em)
        ]);
    }

    #[Route('/admin/next-group', name: 'app_admin_next_group')]
    public function generateNexGroupStages(): Response
    {
        $service = new GroupStagesService;
        //$service->purgeGroupStages($this->em);

        $gStages = $this->gsRepo->findAll();
        $gStagesMatch = $this->gsmRepo->findAll();
        $teams = $this->bookRepo->findBy(["status" => "valid"]);

        $group_stages = $service->generateNexGroupStages($teams, $gStages, $this->em);
            
        $this->em->flush();

        return $this->redirectToRoute("app_admin");
    }
}
