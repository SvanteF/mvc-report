<?php

namespace App\Adventure;

//use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Room
{
    private string $name;

    /**
     * @var array<string, Room>
     */
    private array $doorTo = [];

    /**
     * @var Thing[]
     */
    private array $things;

    /**
     * @var Closet[]
     */
    private array $closets = [];

    /**
     * @param Thing[] $things
     * @param Closet[] $closets
     */
    public function __construct(string $name, array $things = [], array $closets = []) 
    {
        $this->name = $name;
        $this->things = $things;
        $this->closets = $closets;
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

    /**
     * @return Thing[]
     */
    public function getThings(): array
    {
        return $this->things;
    }

    /**
     * @return Closet[]
     */
    public function getClosets(): array
    {
        return $this->closets;
    }

    public function setDoorTo(string $where, Room $room): void
    {
        $this->doorTo[$where] = $room;
    }

    public function getDoorTo(string $where): ?Room
    {
        return $this->doorTo[$where] ?? null;
    }
}