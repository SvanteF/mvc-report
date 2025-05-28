<?php

namespace App\Entity;

use App\Repository\HighscoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HighscoreRepository::class)]
class Highscore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $score;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $created;

    #[ORM\ManyToOne(targetEntity: PlayerEntity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private PlayerEntity $player;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;
        return $this;
    }

    public function getPlayer(): PlayerEntity
    {
        return $this->player;
    }

    public function setPlayer(PlayerEntity $player): self
    {
        $this->player = $player;
        return $this;
    }

}
