<?php

declare(strict_types=1);

namespace App\Controller\admin;

use App\Entity\Author;
use App\Form\admin\AuthorFormType;
use App\Services\AuthorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManageAuthorsController extends AbstractController {
    public function __construct(private AuthorService $authorService) {
    }

    #[Route('/admin/authors', name: 'app_admin_authors')]
    public function authors(): Response {
        $authors = $this->authorService->getAllAuthorsOrderByIdAsc();

        return $this->render('admin/author/authors.html.twig', [
            'authors' => $authors
        ]);
    }

    #[Route('/admin/authors/add', name: 'app_admin_authors_add')]
    public function addAuthor(Request $request): Response {
        $author = new Author();
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->authorService->save($author);
            $this->addFlash('success', 'Author added successfully!');

            return $this->redirectToRoute('app_admin_authors');
        }

        return $this->render('admin/author/add_author.html.twig', [
            'addAuthorForm' => $form->createView()
        ]);
    }

    #[Route('/admin/authors/edit/{id}', name: 'app_admin_authors_edit')]
    public function editAuthor(int $id, Request $request): Response {
        $author = $this->authorService->getAuthorById($id);

        if (!$author) {
            return $this->redirectToRoute('app_admin_authors');
        }

        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->authorService->save($author);
            $this->addFlash('success', 'Author edited successfully!');
        }

        return $this->render('admin/author/edit_author.html.twig', [
            'editAuthorForm' => $form->createView()
        ]);
    }

    #[Route('/admin/authors/delete/{id}', name: 'app_admin_authors_delete')]
    public function deleteAuthor(int $id): Response {
        $author = $this->authorService->getAuthorById($id);

        try {
            $this->authorService->delete($author);
            $this->addFlash('success', 'Author deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', "Failed to delete author: " . $e->getMessage());
        } finally {
            return $this->redirectToRoute('app_admin_authors');
        }
    }
}
