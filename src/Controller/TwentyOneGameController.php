<?php

namespace App\Controller;

//use App\Card\Card;
//use App\Card\DeckOfCards;
//use App\Card\DeckWithJokers;
//use App\Card\Player;

use App\Card\Game21;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TwentyOneGameController extends AbstractController
{
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

    #[Route("/game/21/1", name: "game_21_1_get", methods: ["GET"])]
    public function game211Get(): Response {

        return $this->render('game_21_1.html.twig');
    }

    #[Route("/game/21/1", name: "game_21_1_post", methods: ["POST"])]
    public function game211Post(): Response {

        return $this->redirectToRoute('game_21_2_get');
    }

    #[Route("/game/21/2", name: "game_21_2_get", methods: ["GET"])]
    public function game212Get(
        SessionInterface $session
    ): Response {

        $game21 = new Game21();
        $game21->getNewCard('player');

        $game21->saveToSession($session);

        return $this->render('game_21_2.html.twig', [
            'playersCards' => $game21->getPlayersCardsAsString(),
            'playerPoints' => $game21->getPlayerGamePoints(),
        ]);
    }

    #[Route("/game/21/2", name: "game_21_2_post", methods: ["POST"])]
    public function game212Post(
        SessionInterface $session
    ): Response {
        // $playerBet = $request->request->get('playerBet');

        // $session->set('playerBet', $playerBet);

        $game21 = $session->get('game21');

        $game21->getNewCard('player');

        $game21->saveToSession($session);

        $gameOver = $game21->gameOver();

        if ($gameOver) {
            return $this->render('game_over.html.twig', [
                'playerPoints' => $game21->getPlayerGamePoints(),
            ]);
        }

        return $this->render('game_21_2.html.twig', [
         'playersCards' => $game21->getPlayersCardsAsString(),
         'playerPoints' => $game21->getPlayerGamePoints(),
         ]);
    }

    #[Route("/game/21/3", name: "game_21_3_post", methods: ["POST"])]
    public function game213Post(
        SessionInterface $session
    ): Response {
        // $playerBet = $request->request->get('playerBet');

        // $session->set('playerBet', $playerBet);

        $game21 = $session->get('game21');

        $game21->getNewCard('bank');

        $game21->saveToSession($session);

        $gameOver = $game21->gameOver();

        if ($gameOver) {
            return $this->render('game_over.html.twig', [
                'playerPoints' => $game21->getPlayerGamePoints(),
                'bankPoints' => $game21->getBankGamePoints(),
            ]);
        }

        return $this->render('game_21_3.html.twig', [
         'playersCards' => $game21->getPlayersCardsAsString(),
         'playerPoints' => $game21->getPlayerGamePoints(),
         'banksCards' => $game21->getBanksCardsAsString(),
         'bankPoints' => $game21->getBankGamePoints(),
         ]);
    }
}
