<?php

namespace App\Card;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Game21
{
    private $deck;
    private $drawCards = [];
    private $bankCards = [];
    private $playerGamePoints = 0;
    private $bankGamePoints = 0;


    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffleAndGetDeck();
    }

    public function saveToSession(SessionInterface $session): void
    {
        $session->set('game21', $this);
    }

    public function getNewCard($who): void
    {
        $card = $this->deck->drawCard();
        if ($who === 'player') {
            $this->drawCards[] = $card[0];
        } else {
            $this->bankCards[] = $card[0];
        }
        $this->getPoints($card[0], $who);
        
    }

    public function getPlayersCardsAsString(): array
    {
        $result = [];
        foreach ($this->drawCards as $card) {
            $result[] = $card->getAsString();
        }
        return $result;
    }

    public function getPoints($card, $who): void
    {
        $cardValue = $card->getValue();

        $pointsTable = [
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            '10' => 10,
            '♞' => 11,
            '♛' => 12,
            '♚' => 13
        ];

        if ($who === 'player') {
            if ($cardValue === 'A') {
                $this->playerGamePoints += ($this->playerGamePoints + 14 <= 21 ) ? 14 : 1;
            } else {
                $this->playerGamePoints += $pointsTable[$cardValue];
            }
        } else {
            if ($cardValue === 'A') {
                $this->bankGamePoints += ($this->bankGamePoints + 14 <= 21 ) ? 14 : 1;
            } else {
                $this->bankGamePoints += $pointsTable[$cardValue];
            }
        }
    }

    public function getPlayerGamePoints(): int
    {
        return $this->playerGamePoints;
    }

    public function getBankGamePoints(): int
    {
        return $this->bankGamePoints;
    }

    public function gameOver(): bool
    {
        return ($this->playerGamePoints >= 21 || $this->bankGamePoints >= 17);
    }

    public function getBanksCardsAsString(): array
    {
        $result = [];
        foreach ($this->bankCards as $card) {
            $result[] = $card->getAsString();
        }
        return $result;
    }
}
