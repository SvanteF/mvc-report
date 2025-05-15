<?php

namespace App\Adventure;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Player
 */
class PlayerTest extends TestCase
{
    /**
     * Construct object and verify that an instance is created
     */
    public function testCreateRoom(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];

        $room = new Room($name, $things, $closets);

        $this->assertInstanceOf("\App\Adventure\Room", $room);

        $this->assertSame('Hallen', $room->getName());
    }

   
}
