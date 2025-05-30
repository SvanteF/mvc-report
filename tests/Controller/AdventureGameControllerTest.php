<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdventureGameControllerTest extends WebTestCase
{
    /**
     * Test GET route /proj
     */
    public function testAdventureLoadStartPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj');

        $this->assertResponseIsSuccessful();

        // Check that a form exists
        $this->assertSelectorExists('form');

    }

    /**
     * Test POST route /proj/game/new without name
     */
    public function testAdventureNewGameEmptyName(): void
    {
        $client = static::createClient();
        $client->request('POST', '/proj/game/new', [
            'name' => ''
        ]);

        // Verify that an emty name redirects to /proj
        $this->assertResponseRedirects('/proj');
        
        // Verify the right output of the flash message.
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-warning', 'Du glömde ditt namn');
    }

    /**
     * Test POST route /proj/game/new with name
     */
    public function testAdventureNewGameWithName(): void
    {
        $client = static::createClient();
        $client->request('POST', '/proj/game/new', [
            'name' => 'Test Name'
        ]);

        // Verify that play.html.twig is loaded
        $this->assertResponseIsSuccessful();

        // Verify that the name is present
        $this->assertSelectorTextContains('body', 'Test Name');
    }

    /**
     * Test GET route /proj/game with an active session
     */
    public function testAdventureGamePlayWithActiveSession(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/game/new', [
            'name' => 'Test Name'
        ]);

        $client->request('GET', '/proj/game');

        // Verify that play.html.twig is loaded
        $this->assertResponseIsSuccessful();

        // Verify that the name is present
        $this->assertSelectorTextContains('body', 'Test Name');
    }

    /**
     * Test GET route /proj/game without an active session
     */
    public function testAdventureGamePlayWithoutActiveSession(): void
    {
        $client = static::createClient();

        $client->request('GET', '/proj/game');

        // Verify that an emty name redirects to /proj
        $this->assertResponseRedirects('/proj');
    }

     /**
     * Test GET route /proj/game/move/{where} 
     */
    public function testAdventureGameMove(): void
    {
        $client = static::createClient();

        // Create a game to get an active session
        $client->request('POST', '/proj/game/new', [
            'name' => 'Test Name'
        ]);

        // Move to Viggo room (norr)
        $client->request('GET', '/proj/game/move/norr');

        // Verify that the player is not is Viggo's room
        $this->assertSelectorTextContains('h3.glow-yellow', 'Viggos rum');

        // Verify that Hallen now is an option
        $this->assertSelectorTextContains('body', 'Hallen (syd)');


        // Verify that play.html.twig is loaded
        $this->assertResponseIsSuccessful();
    }
}