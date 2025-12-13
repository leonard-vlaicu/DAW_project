<?php

namespace App\Services;

use App\Entity\Book;
use App\Repository\BookRepository;

class BookService {
    public function __construct(private BookRepository $bookRepository) {
    }

    /**
     * @return array<Book>
     */
    public function getAllBooks(): array {
        return $this->bookRepository->findAll();
    }

    public function getBookById($id): Book|null {
        return $this->bookRepository->find($id);
    }

    public function save(Book $book): void {
        $this->bookRepository->save($book);
    }

    public function delete(Book $book): void {
        $this->bookRepository->delete($book);
    }

    public function getBookByIsbn($isbn): ?Book {
        return $this->bookRepository->findOneByIsbn($isbn);
    }

    /**
     * @return array<Book>
     */
    public function getAllOrderByIdAsc(): array {
        return $this->bookRepository->findAllOrderByIdAsc();
    }
}
