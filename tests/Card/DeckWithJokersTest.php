<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertCount;

/**
 * Test class for class DeckWithJokers
 */
class DeckWithJokersTest extends TestCase 
{
    /**
     * Construct object and verify verify that the new deck contains 54 cards.
     */
    public function testDeckWithJokersCreate()
    {
        $deckWithJokers = new DeckWithJokers();
        // Assert the instance of DeckWithJokers
        $this->assertInstanceOf("\App\Card\DeckWithJokers", $deckWithJokers);

        $allCards = $deckWithJokers->getDeck();

        // Assert that the deck has 54 cards, including the 2 jokers
        $this->assertCount(54, $allCards);
    }
}