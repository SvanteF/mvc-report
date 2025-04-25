<?php

namespace App\Card;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Game21
{
    private $betting;
    private $winner;
    private $deck;
    private $drawCards = [];
    private $bankCards = [];
    private $playerGamePoints = 0;
    private $bankGamePoints = 0;


    public function __construct(Betting $betting, ?DeckOfCards $deck)
    {
        $this->betting = $betting;
        $this->deck = $deck ?? (new DeckOfCards());
        if ($deck === null) {
            $this->deck->shuffleAndGetDeck();
        }   
    }

    public function saveToSession(SessionInterface $session): void
    {
        $session->set('game21', $this);
    }

    public function getNewCard($who): void
    {
        $card = $this->deck->drawCard();
        /*if ($who === 'player') {
            $this->drawCards[] = $card[0];
        } else {
            $this->bankCards[] = $card[0];
        }*/

        //Perform when we are out of cards in the deck
        if ($card === null) {
            $this->deck = new DeckOfCards();
            $this->deck->shuffleAndGetDeck();
            $card = $this->deck->drawCard();
        }
    
        //Change due to lint complain

        switch ($who) {
            case 'player':
                $this->drawCards[] = $card[0];
                break;
            case 'bank':
                $this->bankCards[] = $card[0];
                break;
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
                $this->playerGamePoints += ($this->playerGamePoints + 14 <= 21) ? 14 : 1;
                return;
            }
            $this->playerGamePoints += $pointsTable[$cardValue];
            return;
        }
        if ($who === 'bank') {
            if ($cardValue === 'A') {
                $this->bankGamePoints += ($this->bankGamePoints + 14 <= 21) ? 14 : 1;
                return;
            }
        }
        $this->bankGamePoints += $pointsTable[$cardValue];
        return;
    }

    public function getPlayerGamePoints(): int
    {
        return $this->playerGamePoints;
    }

    public function getBankGamePoints(): int
    {
        return $this->bankGamePoints;
    }

    public function gameOver($session, $who): bool
    {
        //Get the game mode(smart or dumb)
        $gameMode = $session->get('gameMode');

        //var_dump($who);
        //var_dump($gameMode);
      
        if ($this->playerGamePoints === 21 || $this->bankGamePoints > 21) {
            $this->winner = 'player';
            $this->betting->clearBet($this->winner, $session);
            return true;
        }

        if ($this->playerGamePoints > 21) {
            $this->winner = 'bank';
            $this->betting->clearBet($this->winner, $session);
            return true;
        }

        if ($gameMode === 'dumb' && $who === 'bank' && $this->bankGamePoints >= 17) {
            if ($this->bankGamePoints >= $this->playerGamePoints) {
                $this->winner = 'bank';
                $this->betting->clearBet($this->winner, $session);
                return true;
            }
            if ($this->bankGamePoints < $this->playerGamePoints) {
                $this->winner = 'player';
                $this->betting->clearBet($this->winner, $session);
                return true;
            }

            $this->winner = 'draw';
            $this->betting->clearBet($this->winner, $session);
            return true;
        }

        if ($gameMode === 'smart' && $who === 'bank') {
            $inverseRisk = $this->getFatProbability('bank');

            //var_dump($this->bankGamePoints);
            //var_dump($this->playerGamePoints);
            //var_dump($inverseRisk);

            if ($this->bankGamePoints >= $this->playerGamePoints) {
                $this->winner = 'bank';
                $this->betting->clearBet($this->winner, $session);
                return true;
            }
            if ($inverseRisk < 30) {
                $this->winner = 'player';
                $this->betting->clearBet($this->winner, $session);
                return true;
            }
        }
        return false;
    }

    public function getBanksCardsAsString(): array
    {
        $result = [];
        foreach ($this->bankCards as $card) {
            $result[] = $card->getAsString();
        }
        return $result;
    }

    public function getWinner(): string
    {
        return $this->winner;
    }
    // Add for smart function ()
    public function getDeck(): DeckOfCards
    {
        //$this->deck->getNumberOfCards();
        return $this->deck;
    }

    //Player is deafult
    public function getFatProbability($who = 'player'): float
    {
        //Setting A to 1 since we only want to avoid "getting fat"
        $pointsTable = [
            'A' => 1,
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

        //Get array of number of remaing cards of each value
        $number = $this->deck->getNumberOfCards();

        //Get the amount of cards left in the Deck
        $cardsLeft = array_sum($number);

        //Doesn't work with aces...
        //$okCard = 21 - $this->playerGamePoints;

        $playersPoints = 0;
        $banksPoints = 0;

        if ($who === 'player'){
            foreach ($this->drawCards as $card) {
                $value = $card->getValue();
                $playersPoints += $pointsTable[$value];
            }
            $okCard = 21 - $playersPoints;
        }

        if ($who === 'bank') {
            foreach ($this->bankCards as $card) {
                $value = $card->getValue();
                $banksPoints += $pointsTable[$value];
            }
            $okCard = 21 - $banksPoints;
        }

        $numberOfOkCards = 0;

        foreach ($number as $value=>$countValues) {
            $cardPoints = $pointsTable[$value];
            if ($cardPoints <= $okCard) {
                $numberOfOkCards += $countValues;
            }
        }

        //Avoid devide by zero
        if ($cardsLeft === 0) {
            return 0.0;
        }
        //var_dump($banksPoints);
        //var_dump($okCard);
        //var_dump($numberOfOkCards);
        //var_dump($cardsLeft);
        //var_dump($number);
        //var_dump(round($numberOfOkCards / $cardsLeft * 100, 1));

        //Return the propability of not getting fat in percent
        return round($numberOfOkCards / $cardsLeft * 100, 1);
    }  
}
