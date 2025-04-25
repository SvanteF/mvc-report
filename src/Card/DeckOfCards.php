<?php

namespace App\Card;

class DeckOfCards
{
    protected $deck = [];

    public function __construct()
    {
        $colors = ['♥', '♣', '♦', '♠'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', '♞', '♛', '♚', 'A'];

        foreach ($colors as $color) {
            foreach ($values as $value) {
                $this->deck[] = new Card($value, $color);
            }
        }
    }

    public function shuffleAndGetDeck(): array
    {
        shuffle($this->deck);

        return $this->deck;
    }

    public function drawCard(): ?array
    {
        if (count($this->deck) === 0) {
            return null;
        }

        $randomPos = array_rand($this->deck);
        $card = $this->deck[$randomPos];

        unset($this->deck[$randomPos]);
        $this->deck = array_values($this->deck);

        return [$card, count($this->deck)];
    }

    public function getDeck(): array
    {
        return $this->deck;
    }

    public function getNumberOfCards(): array
    {
        $number = [
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
            '7' => 0,
            '8' => 0,
            '9' => 0,
            '10' => 0,
            '♞' => 0,
            '♛' => 0,
            '♚' => 0,
            'A' => 0
        ];
        foreach ($this->deck as $card) {
            $value = $card->getValue();            
            $number[$value]++;
        }
        return $number;
    }
}
