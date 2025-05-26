<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Card\Player;
use App\Repository\LibraryRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdventureControllerJson extends AbstractController
{

    #[Route("/proj/api", name: "adventure_api")]
    public function projApi(): Response
    {
        return $this->render('/adventure/api.html.twig');
    }
}
