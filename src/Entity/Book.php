<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    const STATUS = [
        "save" => "Réservation enregistrée",
        "payment_process" => "Paiement",
        "valid" => "Equipe validée"
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Player::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $players;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $team_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?int $goal_for = null;

    #[ORM\Column(nullable: true)]
    private ?int $score_against = null;

    #[ORM\Column(nullable: true)]
    private ?int $victories = null;

    #[ORM\Column(nullable: true)]
    private ?int $draw = null;

    #[ORM\Column(nullable: true)]
    private ?int $defeat = null;

    #[ORM\Column(nullable: true)]
    private ?int $points = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    public function __construct()
    {
        $this->status = 'payment_process';
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setBook($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getBook() === $this) {
                $player->setBook(null);
            }
        }

        return $this;
    }

    public function getTeamName(): ?string
    {
        return $this->team_name;
    }

    public function setTeamName(?string $team_name): self
    {
        $this->team_name = $team_name;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getGoalFor(): ?int
    {
        return $this->goal_for;
    }

    public function setGoalFor(?int $goal_for): self
    {
        $this->goal_for = $goal_for;

        return $this;
    }

    public function getScoreAgainst(): ?int
    {
        return $this->score_against;
    }

    public function setScoreAgainst(?int $score_against): self
    {
        $this->score_against = $score_against;

        return $this;
    }

    public function getVictories(): ?int
    {
        return $this->victories;
    }

    public function setVictories(?int $victories): self
    {
        $this->victories = $victories;

        return $this;
    }

    public function getDraw(): ?int
    {
        return $this->draw;
    }

    public function setDraw(?int $draw): self
    {
        $this->draw = $draw;

        return $this;
    }

    public function getDefeat(): ?int
    {
        return $this->defeat;
    }

    public function setDefeat(?int $defeat): self
    {
        $this->defeat = $defeat;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
