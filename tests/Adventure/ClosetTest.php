<?php

namespace App\Adventure;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Closet
 */
class ClosetTest extends TestCase
{
    /**
     * Construct object and verify that an instance is created
     */
    public function testCreateClosetWithParameter(): void
    {
        $keyId = 0;
        $closet = new Closet($keyId);

        $this->assertInstanceOf("\App\Adventure\Closet", $closet);
        
    }

    /**
     * Construct object and verify that an instance is created 
     */
    public function testCreateClosetWithoutParameter(): void
    {
        $closet = new Closet();

        $this->assertInstanceOf("\App\Adventure\Closet", $closet);
        
    }

    /**
     * Verify that lock status can be read, that it can be unlocked with the right key and that the status changes to unlocked
     */
    public function testUnlockClosetWithRightKey(): void
    {
        $keyId = 0;
        $key = new Key($keyId);
        $closet = new Closet($keyId);

        // Verify lock status is locked if a key is used
        $this->assertSame(true, $closet->isLocked());

        // Verify closet is unlocked with right key
        $this->assertTrue($closet->unlock($key));

        // Verify lock status is now unlocked if a key was used
        $this->assertSame(false, $closet->isLocked());

    }

    /**
     * Verify that lock status can be read, that it won´t be unlocked with the wrong key and that the status does not change to unlocked afterwards
     */
    public function testUnlockClosetWithWrongKey(): void
    {
        $keyId = 0;
        $anotherKeyId = 1;
        $key = new Key($keyId);
        $closet = new Closet($anotherKeyId);

        // Verify lock status is locked if a key is used
        $this->assertSame(true, $closet->isLocked());

        // Verify closet is not unlocked with wrong key
        $this->assertFalse($closet->unlock($key));

        // Verify lock status is still locked
        $this->assertSame(true, $closet->isLocked());

    }

    /**
     * Verify that a Thing is added to the Closet and that it is retrieved with getThings()
     */
    public function testAddAndGetThingCloset(): void
    {
        $keyId = 0;
        $closet = new Closet($keyId);

        $thing = new Thing('key');

        $closet->addThing($thing);

        // Verify that that getThings() return the object that was added
        $this->assertContains($thing, $closet->getThings());

    }

     /**
     * Verify that a Thing is removed from Closet it it is open.
     */
    public function testRemoveThingsFromCloset(): void
    {
        // Add Thing to the closet
        $keyId = 0;
        $key = new Key($keyId);
        $closet = new Closet($keyId);
        $thing = new Thing('key');

        //Verify that Things can not be removed when the door is locked
        $this->assertFalse($closet->removeThing($thing));


        //Unlock closet so Things can be removed
        $closet->unlock($key);

        // Verify that nothing can be removed if the closet is empty
        $this->assertFalse($closet->removeThing($thing));

        // Add a thing
        $closet->addThing($thing);

        $this->assertTrue($closet->removeThing($thing));
        $this->assertNotContains($thing, $closet->getThings());
    }
}
