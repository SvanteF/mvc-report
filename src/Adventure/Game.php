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

    public function __construct(string $playerName)
    {

        /**
         * Info about the rooms
         */
        $infoHallen = "Här kommer text som handlar om hallen";
        $infoViggosRum = "Här kommer text som handlar om Viggos rum";
        $infoAmeliesRum = "Här kommer text som handlar om Amélies rum";
        $infoFabiansRum = "Här kommer text som handlar om Fabians rum";
        $infoGrovkok = "Här kommer text som handlar om grovköket";

        /**
         * Room images
         */
        $imageHallen = "img/uml.png";
        $imageViggosRum = "img/uml.png";
        $imageAmeliesRum = "img/uml.png";
        $imageFabiansRum = "img/uml.png";
        $imageGrovkok = "img/uml.png";


        /**
         * Create 2 keys
         */
        $keys = [];
        for ($i = 0; $i < 2; $i++) {
            $keys[] = new Key();
        }

        /**
         * Create 6 pieces of laundry
         */
        $laundries = [];
        for ($i = 0; $i < 6; $i++) {
            $laundries[] = new Laundry();
        }

        /**
         * Create 3 closets
         */
        $closets = [];
        /*for ($i = 0; $i < 3; $i++) {
            $closets[] = new Closet($i);
        }*/
        $closets[] = new Closet(1);
        $closets[] = new Closet(2);
        $closets[] = new Closet(3, 2);


        /**
         * Add laundry to closets
         */
        $closets[0]->addThing($laundries[0]);
        $closets[2]->addThing($laundries[5]);


        /**
         * Add key to closet
         */
        $closets[1]->addThing($keys[0]);

        /**
         * Create all 5 rooms
         */
        $hallen = new Room('Hallen', [$laundries[1]], [], $infoHallen, $imageHallen);
        $viggosRoom = new Room('Viggos rum', [$laundries[2], $keys[1]], [$closets[0]], $infoViggosRum, $imageViggosRum);
        $ameliesRoom = new Room('Amélies rum', [$laundries[3]], [$closets[1]], $infoAmeliesRum, $imageAmeliesRum);
        $fabiansRoom = new Room('Fabians rum', [$laundries[4]], [$closets[2]], $infoFabiansRum, $imageFabiansRum);
        $grovkok = new Room('Grovkök', [], [], $infoGrovkok, $imageGrovkok);

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
