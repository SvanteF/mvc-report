<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Key extends Thing
{
    private int $keyId;

    public function __construct(int $keyId)
    {
        parent::__construct('key');
        $this->keyId = $keyId;
    }

    public function getId(): int
    {
        return $this->keyId;
    }

}