<?php

namespace App\Entity;

use App\Repository\ChampionshipMatchRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChampionshipMatchRepository::class)]
class ChampionshipMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $team_1 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $team_2 = null;

    #[ORM\ManyToOne]
    private ?Championship $championship = null;

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

    public function getChampionship(): ?Championship
    {
        return $this->championship;
    }

    public function setChampionship(?Championship $championship): self
    {
        $this->championship = $championship;

        return $this;
    }
}
