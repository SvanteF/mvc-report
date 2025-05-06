<?php

namespace App\Controller;

use App\Entity\Library;
use App\Repository\LibraryRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }

    #[Route('/library/create', name: 'book_create_get', methods: ["GET"])]
    public function createBookGet(): Response
    {
        return $this->render('library/create.html.twig');
    }

    #[Route('/library/create', name: 'book_create', methods: ["POST"])]
    public function createBookPost(
        ManagerRegistry $doctrine,
        Request $request
    ): Response {

        $title = $request->request->get('title');
        $isbn = $request->request->get('isbn');
        $author = $request->request->get('author');

        $entityManager = $doctrine->getManager();

        $book = new Library();
        $book->setTitle($title);
        $book->setIsbn($isbn);
        $book->setAuthor($author);

        // Tell Doctrine you want to (eventually) save the Product
        $entityManager->persist($book);

        // Execute the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->render('library/index.html.twig');
    }

    #[Route('/library/view/{id}', name: 'book_by_id', methods: ["GET"])]
    public function showBookById(
        LibraryRepository $LibraryRepository,
        int $id
    ): Response {
        $book = $LibraryRepository->find($id);

        $data = [
            'book' => $book
        ];

        return $this->render('library/view_one.html.twig', $data);
    }
    
    #[Route('/library/view', name: 'library_view_all', methods: ["GET"])]
    public function viewAllBooks(
        LibraryRepository $LibraryRepository
    ): Response {
        $library = $LibraryRepository->findAll();

        $data = [
            'library' => $library
        ];

        return $this->render('library/view_all.html.twig', $data);
    }
}
