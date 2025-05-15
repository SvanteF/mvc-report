<?php

namespace App\Adventure;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Laundry extends Thing
{
    public function __construct()
    {
        parent::__construct('laundry');
    }

}