<?php

namespace App\Controller;

//use App\Card\Card;
use App\Card\DeckOfCards;
use App\Card\DeckWithJokers;
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
    public function cardStart(): Response
    {
        return $this->render('card.html.twig');
    }

    #[Route("/card/deck", name: "card_deck")]
    public function cardDeck(
        SessionInterface $session
    ): Response
    {
        $deck = new DeckOfCards();

        $session->set("deck_of_cards", $deck);

        $allCards = $deck->getDeck();

        return $this->render('deck.html.twig', [
            'allCards' => $allCards
        ]);
    }

    #[Route("/card/deck/shuffle", name: "shuffle_deck")]
    public function shuffleDeck(
        SessionInterface $session
    ): Response
    {
        //$deck = new DeckOfCards();
        $deck = $session->get("deck_of_cards");

        $deck->shuffleAndGetDeck();

        $session->set("deck_of_cards", $deck);

        return $this->render('shuffle.html.twig', [
            'shuffleCards' => $deck->getDeck()
        ]);
    }
    
    #[Route("/card/deck/draw", name: "draw_from_deck")]
    public function drawFromDeck(
        SessionInterface $session
    ): Response
    {
        $deck = $session->get("deck_of_cards");

        if ($deck === null) {
        
            $this->addFlash(
                'notice',
                'Det finns ingen kortlek så vi skapade en'
            );
            return $this->redirectToRoute('card_deck');
        }

        $drawRes = $deck->drawCard();

        if ($drawRes === null) {
            $this->addFlash(
                'warning',
                'Leken är tom'
            );

            return $this->redirectToRoute('card_start'); 
        }

        [$drawCard, $cardsLeft] = $drawRes;

        $session->set("deck_of_cards", $deck);

        $cardsLeft = count ($deck->getDeck());

        return $this->render('draw.html.twig', [
            'drawCard' => $drawCard,
            'cardsLeft' => $cardsLeft,
        ]);
    }

    #[Route("/card/deck/draw/{num<\d+>}", name: "draw_many_from_deck_get", methods: ["GET"])]
    public function drawManyFromDeckGet(
        SessionInterface $session,
        int $num
    ): Response    
    {
        if ($num > 52) {
            throw new \Exception("There are maximum 52 cards");
        }

        $deck = $session->get("deck_of_cards");

        if ($deck === null) {
        
            $this->addFlash(
                'notice',
                'Det finns ingen kortlek så vi skapade en'
            );
            return $this->redirectToRoute('card_deck');
        }

        $cardHand = [];
        for ($i = 1; $i <= $num; $i++) {
            $drawRes = $deck->drawCard();

            if ($drawRes === null) {
                $this->addFlash(
                    'warning',
                    'Leken är tom'
                );
    
                return $this->redirectToRoute('card_start'); 
            }
    
            [$drawCard, $cardsLeft] = $drawRes;
        
            $cardHand[] = $drawCard;
        }

        $cardsLeft = count ($deck->getDeck());
        $session->set("deck_of_cards", $deck);


        return $this->render('draw_many.html.twig', [
            'cardHand' => $cardHand,
            'cardsLeft' => $cardsLeft,
            'num' => $num,
        ]);
    }

    #[Route("/card/deck/draw/", name: "draw_many_from_deck_post", methods: ["POST"])]
    public function drawManyFromDeckPost(
        Request $request,
        SessionInterface $session
    ): Response    
    {
        $num = $request->request->get('num');

        if ($num > 52) {
            throw new \Exception("There are maximum 52 cards");
        }

        $deck = $session->get("deck_of_cards");

        $cardHand = [];
        for ($i = 1; $i <= $num; $i++) {
            $drawRes = $deck->drawCard();

            if ($drawRes === null) {
                $this->addFlash(
                    'warning',
                    'Leken är tom'
                );
    
                return $this->redirectToRoute('card_start'); 
            }
    
            [$drawCard, $cardsLeft] = $drawRes;
        
            $cardHand[] = $drawCard;
        }

        $cardsLeft = count ($deck->getDeck());
        $session->set("deck_of_cards", $deck);


        return $this->render('draw_many.html.twig', [
            'cardHand' => $cardHand,
            'cardsLeft' => $cardsLeft,
            'num' => $num,
        ]);
    }

    #[Route("/card/deck/jokers", name: "deck_with_jokers")]
    public function deckWithJokers(
        SessionInterface $session
    ): Response
    {
        $deckWithJokers = new DeckWithJokers();

        $session->set("deck_of_cards_with_jokers", $deckWithJokers);
        $session->set("deck_of_cards", $deckWithJokers);


        $allCards = $deckWithJokers->getDeck();

        return $this->render('deck_with_jokers.html.twig', [
            'allCards' => $allCards,
        ]);
    }
}
