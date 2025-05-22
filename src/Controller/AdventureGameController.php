<?php

namespace App\Controller;

use App\Adventure\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdventureGameController extends AbstractController
{
    #[Route("/proj", name: "adventure_start")]
    public function adventureStart(
    ): Response {
        return $this->render('adventure/start.html.twig');
    }

    #[Route("/adventure/game/new", name: "adventure_new_game")]
    public function newGame(SessionInterface $session): Response
    {
        $session->remove('Game');
        return $this->redirectToRoute('adventure_play');
    }

    #[Route("/adventure/game/play", name: "adventure_play")]
    public function gameStart(
        SessionInterface $session
    ): Response {
        $game = $session->get('Game');
        if (!$game) {
            $game = new Game('hoppjerka');
        }

        $player = $game->getPlayer();

        $session->set('Game', $game);

        return $this->render('adventure/play.html.twig', [
            'room' => $player->getCurrentRoom(),
            'player' => $player,
        ]);
    }

    #[Route("/adventure/game/move/{where}", name: "adventure_move")]
    public function gameMove(
        string $where,
        SessionInterface $session
    ): Response {
        $game = $session->get('Game');
        $player = $game->getPlayer();
        $currentRoom = $player->getCurrentRoom();
        $nextRoom = $currentRoom->getDoorTo($where);

        $player->setCurrentRoom($nextRoom);

        $session->set('Game', $game);

        return $this->render('adventure/play.html.twig', [
            'room' => $player->getCurrentRoom(),
            'player' => $player,
        ]);
    }

    #[Route("/adventure/game/collect/{thingId}/{closetId}", name: "adventure_collect", methods: ["POST"], defaults: ["closetId" => null])]
    public function gameCollect(
        int $thingId,
        ?int $closetId,
        SessionInterface $session
    ): Response {
        $game = $session->get('Game');
        $player = $game->getPlayer();
        $room = $player->getCurrentRoom();

        if ($closetId !== null) {
            $closet = $room->getClosetById($closetId);
            $thing = $closet->getThingById($thingId);
            if ($thing) {
                $player->collectThingFromCloset($closet, $thing);
            }
            $session->set('Game', $game);
            return $this->redirectToRoute('adventure_play');
        } 

        $thing = $room->getThingById($thingId);
        if ($thing) {
            $player->collectThingFromRoom($thing);
        }
        $session->set('Game', $game);

        return $this->redirectToRoute('adventure_play');
    }

    #[Route("/adventure/game/unlock/{closetId}", name: "adventure_unlock", methods: ["POST"])]
    public function unlockCloset(
        int $closetId,
        Request $request,
        SessionInterface $session
    ): Response {
        $keyId = $request->request->get('keyId');

        $game = $session->get('Game');
        $player = $game->getPlayer();
        $room = $player->getCurrentRoom();
        $closet = $room->getClosetById($closetId);

        $success = false;
        if ($closet->isLocked()) {
            $success = $player->useKeyOnCloset($keyId, $closet);
        }

        if ($success) {
            $this->addFlash(
                'success',
                'Rätt nyckel! Garderoben är nu öppen'
            );
            $session->set('Game', $game);

            return $this->redirectToRoute('adventure_play');
        } 

        $this->addFlash(
            'warning',
            'Det var fel nyckel, garderoben är fortfarande låst'
        );
        
        $session->set('Game', $game);

        return $this->redirectToRoute('adventure_play');
    }

    #[Route("/adventure/game/over", name: "adventure_game_over")]
    public function gameOver(
        SessionInterface $session
    ): Response
    {
        $session->remove('Game');
        return $this->render('adventure/over.html.twig');
    }

     #[Route("/proj/about", name: "adventure_about")]
    public function adventureAbout(
    ): Response {
        return $this->render('adventure/about.html.twig');
    }
}
