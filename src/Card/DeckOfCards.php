<?php
namespace App\Card;

use App\Dice\Dice;

class DeckOfCards
{
    private $deck = [];

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

    public function getDeck(): array
    {
        return $this->deck;
    }
}