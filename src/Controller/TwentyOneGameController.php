<?php

namespace App\Controller;

//use App\Card\Card;
//use App\Card\DeckOfCards;
//use App\Card\DeckWithJokers;
//use App\Card\Player;

use App\Card\Betting;
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
    public function game211Get(
        SessionInterface $session
    ): Response {
        $betting = new Betting();

        $betting->saveToSession($session);

        return $this->render('game_21_1.html.twig', [
            'playerFunds' => $betting->getPlayerFunds(),
            'bankFunds' => $betting->getBankFunds(),
        ]);
    }

    /*#[Route("/game/21/1", name: "game_21_1_post", methods: ["POST"])]
    public function game211Post(
        SessionInterface $session
    ): Response {

        return $this->redirectToRoute('game_21_2_get');
    }*/

    #[Route("/game/21/2", name: "game_21_2_get", methods: ["GET"])]
    public function game212Get(
        SessionInterface $session
    ): Response {

        $betting = $session->get('betting');

        $game21 = new Game21($betting);
        $game21->getNewCard('player');
        $game21->saveToSession($session);

        return $this->render('game_21_2.html.twig', [
            'playersCards' => $game21->getPlayersCardsAsString(),
            'playerPoints' => $game21->getPlayerGamePoints(),
            'bet' => $betting->getBet(),
            'playerFunds' => $betting->getPlayerFunds(),
            'bankFunds' => $betting->getBankFunds(),
            'betText' => 'Inget bet lagt än',
        ]);
    }

    #[Route("/game/21/2", name: "game_21_2_post", methods: ["POST"])]
    public function game212Post(
        Request $request,
        SessionInterface $session
    ): Response {
        $game21 = $session->get('game21');

        $betting = $session->get('betting');

        if ($betting->getBet() == 0) {
            $bet = $request->request->get('playersBet');
            if ($bet > $betting->getPlayerFunds() || $bet > $betting->getBankFunds()) {
                $bet = 0;

                return $this->render('game_21_2.html.twig', [
                    'playersCards' => $game21->getPlayersCardsAsString(),
                    'playerPoints' => $game21->getPlayerGamePoints(),
                    //'bet' => $betting->getBet(),
                    'bet' => $bet,
                    'bankFunds' => $betting->getBankFunds(),
                    'playerFunds' => $betting->getPlayerFunds(),
                    'betText' => 'Bet är för högt, det måste vara max saldot för bank eller player',
                    ]);
            }

            $betting->makeBet($bet);
        }

        $game21->getNewCard('player');
        $game21->saveToSession($session);

        $betting->saveToSession($session);

        if ($game21->gameOver($session)) {
            if ($betting->getBankFunds() === 0 || $betting->getPlayerFunds() === 0) {
                return $this->render('game_over_betting.html.twig', [
                    'winner' => $game21->getWinner(),
                ]);
            }
            return $this->render('game_over.html.twig', [
                'playerPoints' => $game21->getPlayerGamePoints(),
                'playersCards' => $game21->getPlayersCardsAsString(),
                'bankPoints' => $game21->getBankGamePoints(),
                'banksCards' => $game21->getBanksCardsAsString(),
                'winner' => $game21->getWinner(),
            ]);
        }

        return $this->render('game_21_2.html.twig', [
         'playersCards' => $game21->getPlayersCardsAsString(),
         'playerPoints' => $game21->getPlayerGamePoints(),
         'bet' => $betting->getBet(),
         'bankFunds' => $betting->getBankFunds(),
         'playerFunds' => $betting->getPlayerFunds(),
         ]);
    }

    #[Route("/game/21/3", name: "game_21_3_post", methods: ["POST"])]
    public function game213Post(
        SessionInterface $session
    ): Response {
        $betting = $session->get('betting');

        $game21 = $session->get('game21');

        $game21->getNewCard('bank');

        $game21->saveToSession($session);

        if ($game21->gameOver($session)) {
            if ($betting->getBankFunds() === 0 || $betting->getPlayerFunds() === 0) {
                return $this->render('game_over_betting.html.twig', [
                    'winner' => $game21->getWinner(),
                ]);
            }
            return $this->render('game_over.html.twig', [
                'playerPoints' => $game21->getPlayerGamePoints(),
                'playersCards' => $game21->getPlayersCardsAsString(),
                'bankPoints' => $game21->getBankGamePoints(),
                'banksCards' => $game21->getBanksCardsAsString(),
                'winner' => $game21->getWinner(),
            ]);
        }

        return $this->render('game_21_3.html.twig', [
         'playersCards' => $game21->getPlayersCardsAsString(),
         'playerPoints' => $game21->getPlayerGamePoints(),
         'banksCards' => $game21->getBanksCardsAsString(),
         'bankPoints' => $game21->getBankGamePoints(),
         'bankFunds' => $betting->getBankFunds(),
         'playerFunds' => $betting->getPlayerFunds(),
         'bet' => $betting->getBet(),
         ]);
    }
}
