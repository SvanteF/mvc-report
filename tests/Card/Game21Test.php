<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;


/**
 * Test class for class Game21
 */
class Game21Test extends TestCase
{

    private Game21 $game21;

    /** 
     * @var \App\Card\Betting&\PHPUnit\Framework\MockObject\MockObject $betting 
     */
    private \PHPUnit\Framework\MockObject\MockObject $betting;

    /** 
     * @var \App\Card\DeckOfCards&\PHPUnit\Framework\MockObject\MockObject $deck
     */
    private \PHPUnit\Framework\MockObject\MockObject $deck;

    /**
     * Construct required objects to avoid replicated code.
     */
    protected function setUp(): void
    {
        $this->betting = $this->createMock(Betting::class);
        
        $this->deck = $this->createMock(DeckOfCards::class);        

        $this->game21 = new Game21($this->betting, $this->deck);
    }

    /**
     * Verify instance of class Game21
     */
    public function testGame21Construct(): void
    {
        // Test with existing deck
        $this->assertInstanceOf("App\Card\Game21", $this->game21);

        // Test with non existing deck
        $game21 = new Game21($this->betting, null);
        $this->assertInstanceOf("App\Card\Game21", $game21);

    }

    /**
     * Verify that save to Session works by comparing the initated 
     * object with the one saved in the session
     */
    public function testGame21SaveToSession(): void
    {
        // Create a new session with Symfony
        $sessionStorage = new MockArraySessionStorage();
        $session = new Session($sessionStorage); 

        // Call saveToSession
        $this->game21->saveToSession($session);
        
        // Check if the session contains the same object a was initiated
        $this->assertSame($this->game21, $session->get('game21')); 
    }

    /**
     * Verify that the player and bank get 1 new card with getNewCard
     */
    public function testGame21GetNewCard(): void
    {
        // Construct a Card
        $card = new Card('2', '♥');

        // Mock that one card is drawn from the full deck
        $this->deck->method('drawCard')->willReturn([$card, 51]);

        // Check getNewCard for player
        $this->game21->getNewCard('player');
        $drawCards = $this->game21->getDrawCards();
        $this->assertCount(1, $drawCards);
        $this->assertSame('2', $drawCards[0]->getValue());
        $this->assertSame('♥', $drawCards[0]->getColor());
        $this->assertEquals(2, $this->game21->getPlayerGamePoints());

        // Check getNewCard for bank
        $this->game21->getNewCard('bank');
        $drawCards = $this->game21->getDrawCards();
        $this->assertCount(1, $drawCards);
        $this->assertSame('2', $drawCards[0]->getValue());
        $this->assertSame('♥', $drawCards[0]->getColor());
        $this->assertEquals(2, $this->game21->getPlayerGamePoints());
    }

    /**
     * Verify that ...
     */
    public function testGame21GetNewCardWhenDeckIsEmpty(): void
    {
        // Mock an empty deck
        $this->deck->method('getNumberOfCards')->willReturn([
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
        ]);

        // Test for player
        $this->game21->getNewCard('player');
        $drawCards = $this->game21->getDrawCards();

        // Check that a card was drawn by the player despite the deck initiallt being empty
        $this->assertCount(1, $drawCards);

    }
}
