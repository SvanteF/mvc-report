<?php

namespace App\Adventure;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Room
 */
class RoomTest extends TestCase
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

    /**
     * Verify that a Thing is added to the Room and that it is retrieved with getThings()
     */
    public function testAddAndGetThingRoom(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];

        $room = new Room($name, $things, $closets);

        $thing = new Thing('laundry');

        $room->addThing($thing);

        // Verify that that getThings() return the object that was added
        $this->assertContains($thing, $room->getThings());

    }

    /**
     * Verify that a Thing is added to the Closet and that it is retrieved with getThings()
     */
    public function testRemoveThingsFromRoom(): void
    {
        $name = 'Hallen';
        $things = [];
        $closets = [];

        $room = new Room($name, $things, $closets);

        $thing = new Thing('laundry');


        // Verify that nothing can be removed if the room is empty
        $this->assertFalse($room->removeThing($thing));

        $room->addThing($thing);

        $this->assertTrue($room->removeThing($thing));
        $this->assertNotContains($thing, $room->getThings());
    }

    /**
     * Verify that a Closet can be added to a room and retrieved with getClosets()
     */
    public function testGetClosetFromRoom(): void
    {
        $name = 'Hallen';
        $things = [];
        $closet1 = new Closet(0);
        $closet2 = new Closet(1);


        $room = new Room($name, $things, [$closet1, $closet2]);

        // Get the closet
        $closets = $room->getClosets();

        //Verify getClosets()
        $this->assertCount(2, $closets);
        $this->assertSame($closet1, $closets[0]);
        $this->assertSame($closet2, $closets[1]);
    }

    /**
     * Verify doors can be set and retrieved
     */
    public function testsetAndGetDoorToRoom(): void
    {
        $hallen = new Room('Hallen');
        $grovkoket = new Room('Grovköket');

        // Add door between hallen and grovköket
        $hallen->setDoorTo('grovköket', $grovkoket);

        //Verify the Rooms are the same
        $this->assertSame($grovkoket, $hallen->getDoorTo('grovköket'));
    }
}
