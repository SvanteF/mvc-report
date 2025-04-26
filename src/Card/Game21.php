<?php

namespace App\Card;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Game21
{
    private Betting $betting;
    private string $winner;
    private DeckOfCards $deck;

    /**
    *@var Card[] 
    */    
    private array $drawCards = [];

    /**
     * 
    *@var Card[] 
    */
    private array $bankCards = [];

    private int $playerGamePoints = 0;
    private int $bankGamePoints = 0;


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

    public function getNewCard(string $who): void
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

    /**
    * @return string[]
    */
    public function getPlayersCardsAsString(): array
    {
        $result = [];
        foreach ($this->drawCards as $card) {
            $result[] = $card->getAsString();
        }
        return $result;
    }

    public function getPoints(Card $card, string $who): void
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

    private function genericWin(SessionInterface $session): bool
    {
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
        return false;
    }

    private function dumbWin(SessionInterface $session, string $who, string $gameMode): bool
    {
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
        
        return false;
    }

    private function smartWin(SessionInterface $session, string $who, string $gameMode): bool
    {
        if ($gameMode === 'smart' && $who === 'bank') {
            $inverseRisk = $this->getFatProbability('bank');

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

    public function gameOver(SessionInterface $session, string $who): bool
    {
        //Get the game mode(smart or dumb)
        $gameMode = $session->get('gameMode');

        if ($this->genericWin($session)) {
            return true;
        }

        if ($this->dumbWin($session, $who, $gameMode)) {
            return true;
        }

        if ($this->smartWin($session, $who, $gameMode)) {
            return true;
        }

        return false;
    }

    /**
    * @return string[]
    */
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
    public function getFatProbability(string $who = 'player'): float
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
        $okCard = 0;

        if ($who === 'player') {
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

        foreach ($number as $value => $countValues) {
            $cardPoints = $pointsTable[$value];
            if ($cardPoints <= $okCard) {
                $numberOfOkCards += $countValues;
            }
        }

        //Avoid devide by zero
        if ($cardsLeft === 0) {
            return 0.0;
        }

        //Return the propability of not getting fat in percent
        return round($numberOfOkCards / $cardsLeft * 100, 1);
    }
}
