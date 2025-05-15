<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Closet
{
    private bool $locked;

    private ?int $keyId = null;

    /**
     * @var Thing[]
     */
    private array $things = [];

    public function __construct(?int $keyId = null)
    {
        $this->keyId = $keyId;
        $this->locked = $keyId !== null;
    }

    /*public function lock(): void
    {
        $this->locked = true;
    }*/

    /**
     * Unlock a door
     *
     * @return bool
     */
    public function unlock(Key $key): bool
    {
        if ($this->locked && $key->getId() === $this->keyId) {
            $this->locked = false;
            return true;
        }
        return false;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function addThing(Thing $thing): void
    {
        $this->things[] = $thing;
    }

    public function removeThing(Thing $thing): bool
    {
        //Only allow things to be removed if the door is unlocked
        if ($this->locked) {
            return false;
        }

        $position = array_search($thing, $this->things, true);
        if ($position !== false) {
            unset($this->things[$position]);
            $this->things = array_values($this->things);
            return true;
        }
        return false;
    }

    /**
     * @return Thing[]
     */
    public function getThings(): array
    {
        return $this->things;
    }

}
