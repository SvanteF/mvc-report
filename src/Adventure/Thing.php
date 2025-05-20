<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Thing
{
    private string $type;
    private static int $idCounter = 1;
    private int $id;
    private bool $visible = true;


    public function __construct(string $type)
    {
        $this->type = $type;
        $this->id = self::$idCounter;
        self::$idCounter++;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setVisibility(bool $visible): void
    {
        $this->visible = $visible;
    }

    public function getVisibility(): bool
    {
        return $this->visible;
    }

    public function getId(): int
    {
        return $this->id;
    }

}
