<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Player
{

    private Room $currentRoom;

    /**
     * @var Laundry[]
     */
    private array $basket = []; 

    /**
     * @var Key[]
     */
    private array $pocket = [];

    public function __construct(Room $startRoom)
    {
        $this->currentRoom = $startRoom;
        $this->basket = [];
        $this->pocket = [];
    }

    public function addLaundryToBasket(Laundry $laundry): void
    {
        $this->basket[] = $laundry;
    }

    public function addKeyToPocket(Key $key): void
    {
        $this->pocket[] = $key;
    }

    /**
     * @return Room
     */
    public function getCurrentRoom(): Room
    {
        return $this->currentRoom;
    }

    /**
     * @return Laundry[]
     */
    public function getBasket(): array
    {
        return $this->basket;
    }

    /**
     * @return Key[]
     */
    public function getPocket(): array
    {
        return $this->pocket;
    }

    /**
     * Move the player
     * 
     * 
     */
    public function move(string $where): bool
    {
        $nextRoom = $this->currentRoom->getDoorTo($where);
        if ($nextRoom !== null) {
            $this->currentRoom = $nextRoom;
            return true;
        }
        return false;
    }

    /**
     * Collect a thing from a room
     * 
     * @return bool
     */
    public function collectThingFromRoom(Thing $thing): bool
    { 
        if($this->currentRoom->removeThing($thing)) {
            if ($thing instanceof Key) {
                $this->pocket[] = $thing;
            } elseif ($thing instanceof Laundry) {
                $this->basket[] = $thing;
            }
            return true;
        }
        return false;
    }

    /**
     * Collect a thing from a closet
     * 
     * @return bool
     */
    public function collectThingFromCloset(Closet $closet, Thing $thing): bool
    { 
        if($closet->removeThing($thing)) {
            if ($thing instanceof Key) {
                $this->pocket[] = $thing;
            } elseif ($thing instanceof Laundry) {
                $this->basket[] = $thing;
            }
            return true;
        }
        return false;
    }

    /**
     * Unlock a closet with a key
     * 
     * @return bool
     */
    public function unlockCloset(Closet $closet): bool
    {
        foreach ($this->pocket as $key) {
            if ($closet->unlock($key)) {
                return true;
            }

        }
        return false;
    }
}