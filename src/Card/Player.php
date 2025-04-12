<?php

namespace App\Card;

class Player
{
    private $name;
    private $cardHand = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function giveCard(Card $card): void
    {
        $this->cardHand[] = $card;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCardHand(): array
    {
        return $this->cardHand;
    }
}
