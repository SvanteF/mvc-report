<?php

namespace App\Controller;

use App\Adventure\Game;

use App\Entity\PlayerEntity;
use App\Entity\Highscore;
use App\Repository\PlayerRepository;
use App\Repository\HighscoreRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdventureGameController extends AbstractController
{
    #[Route("/proj", name: "adventure_start")]
    public function adventureStart(
        SessionInterface $session
    ): Response {
        $previousName = $session->get('previousName', '');
        return $this->render('adventure/start.html.twig', [
            'previousName' => $previousName,
        ]);
    }

    /*#[Route("/adventure/game/new", name: "adventure_new_game")]
    public function newGame(SessionInterface $session): Response
    {
        $session->remove('Game');
        return $this->redirectToRoute('adventure_play');
    }*/

    #[Route("/proj/game/new", name: "adventure_play", methods: ["POST"])]
    public function gameNew(
        ManagerRegistry $doctrine,
        Request $request,
        SessionInterface $session
    ): Response {
        $name = (string) ($request->request->get('name'));
        $name = trim($name);

          if ($name == '') {
            $this->addFlash('warning', 'Du glömde ditt namn, vänligen skriv in det innan du börjar');
            return $this->redirectToRoute('adventure_start');
        }

        // Create a new PlayerEntitity with every new game
        $playerEntity = new PlayerEntity();
        $playerEntity->setName($name);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($playerEntity);
        $entityManager->flush();


        $game = new Game($name);
        $session->set('previousName', $name);
        $session->set('player_id', $playerEntity->getId());
        $session->set('Game', $game);
        
        $player = $game->getPlayer();

        return $this->render('adventure/play.html.twig', [
            'room' => $player->getCurrentRoom(),
            'player' => $player,
        ]);
    }

    
    #[Route("/proj/game", name: "adventure_game", methods: ["GET"])]
    public function gamePlay(
        SessionInterface $session
        ): Response {

        $game = $session->get('Game');

        if (!$game) {
            return $this->redirectToRoute('adventure_start');
        }

        $player = $game->getPlayer();

        return $this->render('adventure/play.html.twig', [
            'room' => $player->getCurrentRoom(),
            'player' => $player,
        ]);
    }

    #[Route("/proj/game/move/{where}", name: "adventure_move")]
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


    #[Route("/proj/game/collect/{thingId}/{closetId}", name: "adventure_collect", methods: ["POST"], defaults: ["closetId" => null])]
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
            return $this->redirectToRoute('adventure_game');
        } 

        $thing = $room->getThingById($thingId);
        if ($thing) {
            $player->collectThingFromRoom($thing);
        }
        $session->set('Game', $game);

        return $this->redirectToRoute('adventure_game');
    }

    #[Route("/proj/game/unlock/{closetId}", name: "adventure_unlock", methods: ["POST"])]
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

            return $this->redirectToRoute('adventure_game');
        } 

        $this->addFlash(
            'warning',
            'Det var fel nyckel, garderoben är fortfarande låst'
        );
        
        $session->set('Game', $game);

        return $this->redirectToRoute('adventure_game');
    }

    #[Route("/proj/game/over", name: "adventure_game_over")]
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

    #[Route("/proj/about/database", name: "adventure_about_database")]
    public function adventureAboutDatabase(
    ): Response {
        return $this->render('adventure/database.html.twig');
    }

    #[Route("/proj/quick", name: "adventure_quick")]
    public function adventureQuick(): Response {
        return $this->render('adventure/quick.html.twig');
    }

    #[Route('/proj/entity', name: 'app_proj_entity')]
    public function index(): Response
    {
        return $this->render('adventure/index.html.twig', [
            'controller_name' => 'AdventureEntityController',
        ]);
    }

   #[Route('/proj/entity/delete', name: 'player_entity_reset', methods: ["POST"])]
    public function resetDatabase(
        PlayerRepository $playerRepository,
        HighscoreRepository $highscoreRepository
    ): Response {
        $playerRepository->resetPlayer();
        $highscoreRepository->resetHighscore();

        return $this->redirectToRoute('adventure_start');
    }

}
