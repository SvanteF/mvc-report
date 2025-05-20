<?php

namespace App\Adventure;

class Room
{
    private string $name;
    private string $roomInfo;
    //private string $image;

    /**
     * @var array<string, Room>
     */
    private array $doorTo = [];

    /**
     * @var Thing[]
     */
    private array $things = [];

    /**
     * @var Closet[]
     */
    private array $closets = [];

    /**
     * @param Thing[] $things
     * @param Closet[] $closets
     */
    public function __construct(string $name, array $things = [], array $closets = [], string $roomInfo = "")
    {
        $this->name = $name;
        $this->things = $things;
        $this->closets = $closets;
        $this->roomInfo = $roomInfo;
    }

    public function addThing(Thing $thing): void
    {
        $this->things[] = $thing;
    }

    public function removeThing(Thing $thing): bool
    {
        $position = array_search($thing, $this->things, true);
        if ($position !== false) {
            unset($this->things[$position]);
            $this->things = array_values($this->things);
            return true;
        }
        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRoomInfo(): string
    {
        return $this->roomInfo;
    }

    /**
     * @return Thing[]
     */
    public function getThings(): array
    {
        return $this->things;
    }

    /**
     * @return ?Thing
     */
    public function getThingById(int $id): ?Thing
    {
        foreach ($this->things as $thing) {
            if ($thing->getId() === $id) {
                return $thing;
            }
        }
        return null;
    }

    /**
     * @return Closet[]
     */
    public function getClosets(): array
    {
        return $this->closets;
    }

    /**
     * @return ?Closet
     */
    public function getClosetById(int $id): ?Closet
    {
        foreach ($this->closets as $closet) {
            if ($closet->getId() === $id) {
                return $closet;
            }
        }
        return null;
    }

    public function setDoorTo(string $where, Room $room): void
    {
        $this->doorTo[$where] = $room;
    }

    public function getDoorTo(string $where): ?Room
    {
        return $this->doorTo[$where] ?? null;
    }

    /**
     * @return array<string, Room>
     */
    public function getAvailableDoors(): array
    {
        return $this->doorTo;
    }
}
