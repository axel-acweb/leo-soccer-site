<?php

namespace App\Entity;

use App\Repository\GroupStageMatchRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupStageMatchRepository::class)]
class GroupStageMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $team_1 = null;

    #[ORM\ManyToOne]
    private ?Book $team_2 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupStage $group_stage = null;

    #[ORM\Column(nullable: true)]
    private ?int $team_1_score = null;

    #[ORM\Column(nullable: true)]
    private ?int $team_2_score = null;

    #[ORM\Column(nullable: true)]
    private ?bool $played = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam1(): ?Book
    {
        return $this->team_1;
    }

    public function setTeam1(?Book $team_1): self
    {
        $this->team_1 = $team_1;

        return $this;
    }

    public function getTeam2(): ?Book
    {
        return $this->team_2;
    }

    public function setTeam2(?Book $team_2): self
    {
        $this->team_2 = $team_2;

        return $this;
    }

    public function getGroupStage(): ?GroupStage
    {
        return $this->group_stage;
    }

    public function setGroupStage(?GroupStage $group_stage): self
    {
        $this->group_stage = $group_stage;

        return $this;
    }

    public function getTeam1Score(): ?int
    {
        return $this->team_1_score;
    }

    public function setTeam1Score(?int $team_1_score): self
    {
        $this->team_1_score = $team_1_score;

        return $this;
    }

    public function getTeam2Score(): ?int
    {
        return $this->team_2_score;
    }

    public function setTeam2Score(?int $team_2_score): self
    {
        $this->team_2_score = $team_2_score;

        return $this;
    }

    public function isPlayed(): ?bool
    {
        return $this->played;
    }

    public function setPlayed(?bool $played): self
    {
        $this->played = $played;

        return $this;
    }
}
