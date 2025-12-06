<?php

declare(strict_types=1);

namespace App\Controller\admin;

use App\Entity\Book;
use App\Form\admin\BookFormType;
use App\Services\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController {
    public function __construct(private BookService $bookService) {
    }

    #[Route('/admin/books', name: 'app_admin_books')]
    public function books(): Response {
        $books = $this->bookService->getAllOrderByIdAsc();

        return $this->render('admin/books/books.html.twig', [
            'books' => $books
        ]);
    }

    #[Route('/admin/books/add', name: 'app_admin_books_add')]
    public function addBook(Request $request): Response {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->save($book);
            $this->addFlash('success', 'Book added successfully!');

            return $this->redirectToRoute('app_admin_books');
        }

        return $this->render('admin/books/add_book.html.twig', [
            'addBookForm' => $form
        ]);
    }

    #[Route('/admin/books/edit/{id}', name: 'app_admin_books_edit')]
    public function editBook(int $id, Request $request): Response {
        $book = $this->bookService->getBookById($id);

        if (!$book) {
            return $this->redirectToRoute('app_admin_books');
        }

        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->save($book);
            $this->addFlash('success', 'Book edited successfully!');
        }

        return $this->render('admin/books/edit_book.html.twig', [
            'editBookForm' => $form
        ]);
    }

    #[Route('/admin/books/delete/{id}', name: 'app_admin_books_delete')]
    public function deleteBook(int $id): Response {
        $book = $this->bookService->getBookById($id);

        try {
            $this->bookService->delete($book);
            $this->addFlash('success', 'Book deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', "Failed to delete book: " . $e->getMessage());
        } finally {
            return $this->redirectToRoute('app_admin_books');
        }
    }
}
