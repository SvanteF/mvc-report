<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Game
{
    /**
     * @var Room[]
     */
    private array $rooms = [];
    private Player $player;

    /**
     * Info about the rooms
     */
    private string $infoHallen = "Du befinner dig i hallen. Härifrån kan du nå alla sovrum. Finns det smutskläder här? Plocka upp dem i så fall.";
    private string $infoViggosRum = "Här kommer text som handlar om Viggos rum";
    private string $infoAmeliesRum = "Här kommer text som handlar om Amélies rum";
    private string $infoFabiansRum = "Här kommer text som handlar om Fabians rum";
    private string $infoGrovkok = "Här kommer text som handlar om grovköket";

    /**
     * Room images
     */
    private string $imageHallen = "img/hallen.png";
    private string $imageViggosRum = "img/uml.png";
    private string $imageAmeliesRum = "img/uml.png";
    private string $imageFabiansRum = "img/uml.png";
    private string $imageGrovkok = "img/uml.png";

    public function __construct(string $playerName)
    {
        /**
         * Create 5 keys
         */
        $keys = [];
        for ($i = 0; $i < 5; $i++) {
            $keys[] = new Key();
        }

        /**
         * Create 15 pieces of laundry
         */
        $laundries = [];
        for ($i = 0; $i < 15; $i++) {
            $laundries[] = new Laundry();
        }

        /**
         * Create 5 closets
         */
        $closets = [];
        /*for ($i = 0; $i < 3; $i++) {
            $closets[] = new Closet($i);
        }*/
        $closets[] = new Closet(1, 1); // $closets[0]
        $closets[] = new Closet(2, 2); // $closets[1]
        $closets[] = new Closet(3, 3); // $closets[2]
        $closets[] = new Closet(4, 4); // $closets[3]
        $closets[] = new Closet(5, 5); // $closets[4]


        /**
         * Add laundry to closets
         */
        $closets[0]->addThing($laundries[0]);
        $closets[1]->addThing($laundries[1]);
        $closets[1]->addThing($laundries[2]);
        $closets[4]->addThing($laundries[3]);
        $closets[4]->addThing($laundries[4]);


        /**
         * Add key to closet
         */
        //
        $closets[1]->addThing($keys[4]);
        $closets[2]->addThing($keys[3]);
        $closets[3]->addThing($keys[0]);
        $closets[4]->addThing($keys[2]);


        /**
         * Create all 5 rooms
         */
        $hallen = new Room('Hallen', [$laundries[5], $laundries[6], $laundries[7]], [], $this->infoHallen, $this->imageHallen);
        $viggosRoom = new Room('Viggos rum', [$laundries[8]], [$closets[0], $closets[1]], $this->infoViggosRum, $this->imageViggosRum);
        $ameliesRoom = new Room('Amélies rum', [$laundries[9], $laundries[10]], [$closets[2]], $this->infoAmeliesRum, $this->imageAmeliesRum);
        $fabiansRoom = new Room('Fabians rum', [$laundries[11], $laundries[12], $laundries[13], $keys[1]], [$closets[3], $closets[4]], $this->infoFabiansRum, $this->imageFabiansRum);
        $grovkok = new Room('Grovkök', [$laundries[14]], [], $this->infoGrovkok, $this->imageGrovkok);

        /**
         * Connect hallen to all other rooms
         */
        $hallen->setDoorTo('norr', $viggosRoom);
        $hallen->setDoorTo('öst', $ameliesRoom);
        $hallen->setDoorTo('syd', $fabiansRoom);
        $hallen->setDoorTo('väst', $grovkok);

        /**
         * Connect all other rooms to hallen
         */
        $viggosRoom->setDoorTo('syd', $hallen);
        $ameliesRoom->setDoorTo('väst', $hallen);
        $fabiansRoom->setDoorTo('norr', $hallen);
        $grovkok->setDoorTo('öst', $hallen);


        $this->rooms = [$hallen, $viggosRoom, $ameliesRoom, $fabiansRoom, $grovkok];

        /**
         * Add player and start position
         */
        $this->player = new Player($hallen, $playerName);
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return Room[]
     */
    public function getRooms(): array
    {
        return $this->rooms;
    }
}
