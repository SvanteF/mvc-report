<?php

namespace App\Controller;

/*
use App\Card\Card;
use App\Dice\DiceHand;
use App\Dice\DiceGraphic;
*/

use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardGameController extends AbstractController
{
    #[Route("/session", name: "showSession")]
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
    }

    #[Route("/card", name: "card_start")]
    public function cardStart(
        Request $request,
        SessionInterface $session
    ): Response
    {
        return $this->render('card.html.twig');
    }

    #[Route("/card/deck", name: "card_deck")]
    public function cardDeck(
        Request $request,
        SessionInterface $session
    ): Response
    {
        $deck = new DeckOfCards();
        $allCards = $deck->getDeck();

        $session->set("deck_of_cards", $allCards);

        return $this->render('deck.html.twig', ['allCards' => $allCards]);
    }
}
