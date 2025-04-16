<?php

namespace App\Controller;

//use App\Card\Card;
//use App\Card\DeckOfCards;
//use App\Card\DeckWithJokers;
//use App\Card\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TwentyOneGameController extends AbstractController
{
    /*#[Route("/session", name: "showSession")]
    public function show(SessionInterface $session): Response
    {
        $sessionData = $session->all();


        return $this->render('session.html.twig', [
            'sessionData' => $sessionData,
        ]);
    }

    #[Route("/session/delete", name: "deleteSession")]
    public function delete(SessionInterface $session): Response
    {
        $session->clear();

        $this->addFlash(
            'notice',
            'Nu är sessionen raderad'
        );

        return $this->redirectToRoute('showSession');
    }*/

    #[Route("/game", name: "game_start")]
    public function gameStart(): Response
    {
        return $this->render('game_start.html.twig');
    }

    #[Route("/game/doc", name: "game_doc")]
    public function gameDoc(): Response
    {
        return $this->render('game_doc.html.twig');
    }
}
