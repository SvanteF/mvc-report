<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Key extends Thing
{
    public function __construct()
    {
        parent::__construct('key');
    }

}
