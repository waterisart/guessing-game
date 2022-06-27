<?php

namespace App\Entity;

use App\Repository\RoundRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoundRepository::class)]
class Round
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $number_to_guess;

    #[ORM\Column(type: 'string')]
    private $winner;

    #[ORM\Column(type: 'boolean')]
    private $is_finished;


    #[ORM\ManyToMany(targetEntity: Player::class, mappedBy: 'rounds', inversedBy: 'players', cascade: null, fetch: 'LAZY', orphanRemoval: false, indexBy: 'id')]
    private $players;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberToGuess(): ?int
    {
        return $this->number_to_guess;
    }

    public function setNumberToGuess(int $number_to_guess): self
    {
        $this->number_to_guess = $number_to_guess;

        return $this;
    }

    public function getWinner(): string 
    {
        return $this->winner;
    }

    public function setWinner(string $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getIsFinished(): bool 
    {
        return $this->is_finished;
    }

    public function setIsFinished(bool $isFinished): self
    {
        $this->is_finished = $isFinished;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }
    
    public function setPlayers(Collection $players): self
    {
        $this->players = $players;
        return $this;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
        }

        return $this;
    }
}
