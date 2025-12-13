<?php

declare(strict_types=1);

namespace App\Controller\admin;

use App\Entity\Genre;
use App\Form\admin\GenreFormType;
use App\Services\GenreService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GenreController extends AbstractController {
    public function __construct(private GenreService $genreService, private LoggerInterface $logger) {
    }

    #[Route('/admin/genres', name: 'app_admin_genres')]
    public function genres(): Response {
        $genres = $this->genreService->getAllGenresOrderByIdAsc();

        return $this->render('admin/genre/genres.html.twig', [
            'genres' => $genres,
        ]);
    }

    #[Route('/admin/genres/add', name: 'app_admin_genres_add')]
    public function addGenre(Request $request): Response {
        $genre = new Genre();
        $form = $this->createForm(GenreFormType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->genreService->save($genre);
            $this->addFlash('success', 'Genre added successfully!');

            return $this->redirectToRoute('app_admin_genres');
        }

        return $this->render('admin/genre/add_genre.html.twig', [
            'addGenreForm' => $form,
        ]);
    }

    #[Route('/admin/genres/edit/{id}', name: 'app_admin_genres_edit')]
    public function editGenre(int $id, Request $request): Response {
        $genre = $this->genreService->getGenreById($id);

        if (!$genre) {
            return $this->redirectToRoute('app_admin_genres');
        }

        $form = $this->createForm(GenreFormType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->genreService->save($genre);
            $this->addFlash('success', 'Genre edited successfully!');
        }

        return $this->render('admin/genre/edit_genre.html.twig', [
            'editGenreForm' => $form,
        ]);
    }

    #[Route('/admin/genres/delete/{id}', name: 'app_admin_genres_delete')]
    public function deleteGenre(int $id): Response {
        $genre = $this->genreService->getGenreById($id);

        try {
            $this->genreService->delete($genre);

            $this->addFlash('success', 'Genre deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', "Failed to delete genre: " . $e->getMessage());
        } finally {
            return $this->redirectToRoute('app_admin_genres');
        }
    }
}
