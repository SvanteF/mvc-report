<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Thing
{
    private string $type;
    private bool $visible = true;

    public function __construct(string $type) 
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setVisiblity(bool $visible): void 
    {
        $this->visible = $visible;
    }

    public function getVisibility(): bool
    {
        return $this->visible;
    }

}