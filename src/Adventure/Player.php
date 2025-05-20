<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Player
{
    private Room $currentRoom;

    private string $playerName;

    /**
     * @var Laundry[]
     */
    private array $basket = [];

    /**
     * @var Key[]
     */
    private array $pocket = [];

    public function __construct(Room $startRoom, string $playerName)
    {
        $this->currentRoom = $startRoom;
        $this->playerName = $playerName;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->playerName;
    }

    /**
     * @return Laundry[]
     */
    public function getBasket(): array
    {
        return $this->basket;
    }


    public function emptyBasket(): void
    {
        $this->basket = [];
    }

    /**
     * @return Key[]
     */
    public function getPocket(): array
    {
        return $this->pocket;
    }

    public function emptyPocket(): void
    {
        $this->pocket = [];
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
        if ($this->currentRoom->removeThing($thing)) {
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
        if ($closet->removeThing($thing)) {
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
    /*public function unlockCloset(Closet $closet): bool
    {
        foreach ($this->pocket as $position => $key) {
            if ($closet->unlock($key)) {
                unset($this->pocket[$position]);
                $this->pocket = array_values($this->pocket);
                return true;
            }
        }
        return false;
    }*/

    public function useKeyOnCloset(int $keyId, Closet $closet): bool
    {
        foreach ($this->pocket as $index => $key) {
            //Right key
            if ($key->getId() === $keyId) {
                if ($closet->unlock($key)) {
                    unset($this->pocket[$index]);
                    $this->pocket = array_values($this->pocket);
                    return true;
                }
                // Wrong key
                return false;
            }
        }
        // No key in the pocket
        return false;
    }


    /**
     * @return int
     */
    public function getLaundryCount(): int
    {
        return count($this->basket);
    }

    /**
     * @return Room
     */
    public function getCurrentRoom(): Room
    {
        return $this->currentRoom;
    }

    public function setCurrentRoom(Room $room): void
    {
        $this->currentRoom = $room;
    }
}
