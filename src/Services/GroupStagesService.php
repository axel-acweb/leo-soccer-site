<?php

namespace App\Services;

use App\Entity\Book;
use App\Entity\GroupStage;
use App\Entity\GroupStageMatch;
use Doctrine\ORM\EntityManagerInterface;

class GroupStagesService {

    private $teamsInCourse = [];
    private $gsTeams = [];
    private $ret = [];

    private $matches = [];

    const MATCHES_LOGIC = [
        "1" => [2, 3, 4],
        "2" => [3, 4],
        "3" => [4]
    ];

    const TEAM_GS = [
        "first",
        "second",
        "third"
    ];

    const nextGroupStages = [
        "PRO",
        "Semi-PRO",
        "Amateur"
    ];

    static function canDisplayGroupStages(EntityManagerInterface $em) {
        $teams = $em->getRepository(Book::class)->findBy(["status" => "valid"]);
        $gStagesMatch = $em->getRepository(GroupStageMatch::class)->findAll();

        if(count($teams) == 12 && !empty($gStagesMatch)) {
            return true;
        }
        
        return false;
    }

    static function isAllMatchPlayed(EntityManagerInterface $em): bool {
        $gStagesMatch = $em->getRepository(GroupStageMatch::class)->findAll();
        $gStagesMatchPlayed = $em->getRepository(GroupStageMatch::class)->findBy([
            "played" => true
        ]);

        $gStage = $em->getRepository(GroupStage::class)->findOneBy(["id" => 1]);

        if(count($gStagesMatch) == count($gStagesMatchPlayed) && $gStage->getName() != 'PRO') {
            return true;
        }
        
        return false;
    }

    public function purgeGroupStages(EntityManagerInterface $em) {
        $gsM = $em->getRepository(GroupStageMatch::class)->findAll();
        foreach($gsM as $match) {
            $em->remove($match);
        }
        $em->flush();
    } 

    public function getAllGroupeStagesMatch(EntityManagerInterface $em) {
        $group_stages = $em->getRepository(GroupStage::class)->findAll();
        $ret_state = [];
        foreach($group_stages as $gs) {
            $teams_team1 = $em->getRepository(Book::class)->findByGroupStage($gs);
            $teams_team2 = $em->getRepository(Book::class)->findByGroupStage($gs, 2);
            $teams_merge = [...$teams_team1, ...$teams_team2];
            $merge = [];
            foreach($teams_merge as $tm) {
                if(!in_array($tm, $merge)) {
                    $merge[] = $tm;
                }
            }

            $gsM = $em->getRepository(GroupStageMatch::class)->findBy(["group_stage" => $gs]);

            $this->setAllToZero($gsM);
            foreach($gsM as $match) {
                $gt1 = $match->getTeam1Score();
                $gt2 = $match->getTeam2Score();

                if($match->isPlayed()) {
                    $team1 = $match->getTeam1();
                    $team2 = $match->getTeam2();

                    $team1->setVictories($gt1 > $gt2 ? $team1->getVictories() + 1 : $team1->getVictories());
                    $team1->setDraw($gt1 == $gt2 ? $team1->getDraw() + 1 : $team1->getDraw());
                    $team1->setDefeat($gt1 < $gt2 ? $team1->getDefeat() + 1 : $team1->getDefeat());

                    $team2->setVictories($gt2 > $gt1 ? $team2->getVictories() + 1 : $team2->getVictories());
                    $team2->setDraw($gt1 == $gt2 ? $team2->getDraw() + 1 : $team2->getDraw());
                    $team2->setDefeat($gt2 < $gt1 ? $team2->getDefeat() + 1 : $team2->getDefeat());

                    $team1->setPoints($this->calculatePoints($team1));
                    $team2->setPoints($this->calculatePoints($team2));

                    $this->setAllToZeroVerif($team1, $team2);
                    $em->persist($team1);
                    $em->persist($team2);
                }
            }


            usort($merge, function($a, $b)
            {
                if($b->getPoints() == $a->getPoints()) {
                    $bAverage = $b->getGoalFor() - $b->getScoreAgainst();
                    $aAverage = $a->getGoalFor() - $a->getScoreAgainst();
                    return ($aAverage > $bAverage) ? -1 : +1;
                }
                return strcmp($b->getPoints(), $a->getPoints());
            });

            foreach($merge as $key => $team) {
                if($team->getVictories() + $team->getDraw() + $team->getDefeat() > 0) {
                    $team->setPosition($key + 1);
                    $em->persist($team);
                }
            }

            $em->flush();


            $ret_state[] = [
                "group" => $gs,
                "teams" => $merge,
                "matchs" => $gsM
            ];
        }
        return $ret_state;
    }

    public function calculatePoints(Book $team) {
        $t1v = $team->getVictories();
        $t1d = $team->getDraw();
        return $t1v * 3 + $t1d * 1;
    }

    public function setAllToZero($gsM) {
        foreach($gsM as $match) {

            $team1 = $match->getTeam1();
            $team2 = $match->getTeam2();

            $team1->setVictories(0);
            $team1->setDraw(0);
            $team1->setDefeat(0);
            $team1->setPoints(0);


            $team2->setVictories(0);
            $team2->setDraw(0);
            $team2->setDefeat(0);
            $team2->setPoints(0);
        }
    }

    public function setAllToZeroVerif(Book $team1, Book $team2) {
        $t1v = $team1->getVictories();
        $t1d = $team1->getDraw();
        $t1df = $team1->getDefeat();
        $t1s = $team1->getPoints();

        $t2v = $team2->getVictories();
        $t2d = $team2->getDraw();
        $t2df = $team2->getDefeat();
        $t2s = $team2->getPoints();

        $team1->setVictories(empty($t1v) ? 0 : $t1v);
        $team1->setDraw(empty($t1d) ? 0 : $t1d);
        $team1->setDefeat(empty($t1df) ? 0 : $t1df);
        $team1->setPoints(empty($t1s) ? 0 : $t1s);

        $team2->setVictories(empty($t2v) ? 0 : $t2v);
        $team2->setDraw(empty($t2d) ? 0 : $t2d);
        $team2->setDefeat(empty($t2df) ? 0 : $t2df);
        $team2->setPoints(empty($t2s) ? 0 : $t2s);
    }

    public function generateGroupeStages($teams, $groupStages) {
        $this->teamsInCourse = $teams;

        foreach($groupStages as $gs) {
            $this->randomTeamGs();
            $this->ret[$gs->getId()] = $this->gsTeams;
            $this->gsTeams = [];

        }
        $this->generateMatches();
        return $this->ret;
    }   

    public function generateNexGroupStages($teams, $groupStages, EntityManagerInterface $em) {

        $teamsFirst = $em->getRepository(Book::class)->findBy(["position" => 1]);
        $teamsSecond = $em->getRepository(Book::class)->findBy(["position" => 2]);
        $teamsThird = $em->getRepository(Book::class)->findBy(["position" => 3]);
        $last = $em->getRepository(Book::class)->findBy(["position" => 4]);


        usort($teamsSecond, function($a, $b)
        {
            if($b->getPoints() == $a->getPoints()) {
                $bAverage = $b->getGoalFor() - $b->getScoreAgainst();
                $aAverage = $a->getGoalFor() - $a->getScoreAgainst();
                return ($aAverage > $bAverage) ? -1 : +1;
            }
            return strcmp($b->getPoints(), $a->getPoints());
        });

        usort($teamsThird, function($a, $b)
        {
            if($b->getPoints() == $a->getPoints()) {
                $bAverage = $b->getGoalFor() - $b->getScoreAgainst();
                $aAverage = $a->getGoalFor() - $a->getScoreAgainst();
                return ($aAverage > $bAverage) ? -1 : +1;
            }
            return strcmp($b->getPoints(), $a->getPoints());
        });

        $teamsFirst[] = $teamsSecond[0];

        $teamsSecond[] = $teamsThird[0];
        $teamsSecond[] = $teamsThird[1];

        $teamsThird = [...$teamsThird, ...$last];

        unset($teamsSecond[0]);
        unset($teamsThird[0]);
        unset($teamsThird[1]);


        $groupStages = $em->getRepository(GroupStage::class)->findAll();
        $teamsFirst = array_values($teamsFirst);
        $teamsSecond = array_values($teamsSecond);
        $teamsThird = array_values($teamsThird);

        $this->purgeGroupStages($em);

        //dd($teamsFirst, $teamsSecond, $teamsThird);

        foreach($groupStages as $key => $gs) {
            $gs->setName(self::nextGroupStages[$key]);
            $em->persist($gs);
            $teams = ${'teams'.ucfirst(self::TEAM_GS[$key])};
            $this->generateNextGroupMatchs($gs, $teams, $em);
            $this->setTeamsToZero($teams, $em);
        }

        $em->flush();
        //return $this->ret;
    }  

    public function setTeamsToZero($teams, EntityManagerInterface $em) {
        foreach($teams as $team) {
            $team->setScoreAgainst(0);
            $team->setGoalFor(0);
            $team->setVictories(0);
            $team->setDraw(0);
            $team->setDefeat(0);
            $team->setPoints(0);
            $team->setPosition(0);

            $em->persist($team);
        }
        $em->flush();
    }

    public function generateNextGroupMatchs(GroupStage $groupStage, $teams, EntityManagerInterface $em) {
        $i = 1;
        foreach(self::MATCHES_LOGIC as $team1 => $matches) {
            foreach($matches as $team2) {
                $match = (new GroupStageMatch)
                    ->setGroupStage($groupStage)
                    ->setTeam1($teams[$team1 - 1])
                    ->setTeam2($teams[$team2 - 1]);

                $em->persist($match);
                $i++;
            } 
        }

    }

    public function generateMatches() {
        foreach($this->ret as $key => $gs) {
            $this->generateGsMatches($gs);
            $this->ret[$key]['matches'] = $this->matches;
            $this->matches = [];
        }
    }

    public function generateGsMatches($group_stage) {
        //dd($group_stage);
        $i = 1;
        foreach(self::MATCHES_LOGIC as $team1 => $matches) {
            foreach($matches as $team2) {
                $this->matches[] = [
                    $group_stage[$team1 - 1],
                    $group_stage[$team2 - 1]
                ];
                $i++;
            } 
        }
    }

    public function randomTeamGs() {
        $random_array_entry = array_rand($this->teamsInCourse);
        $this->gsTeams[] = $this->teamsInCourse[$random_array_entry];
        unset($this->teamsInCourse[$random_array_entry]);
        //unset($this->teamsInCourse[$int]);
        if(count($this->gsTeams) < 4) {
            $this->randomTeamGs();
        }
        return;
    }
}