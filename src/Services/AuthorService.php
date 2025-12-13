<?php

namespace App\Services;

use App\Entity\Author;
use App\Repository\AuthorRepository;

class AuthorService {
    public function __construct(private AuthorRepository $authorRepository) {
    }

    public function getAllAuthors(): array {
        return $this->authorRepository->findAll();
    }

    public function getAuthorById($id): Author|null {
        return $this->authorRepository->find($id);
    }

    public function save(Author $author): void {
        $this->authorRepository->save($author);
    }

    public function delete(Author $author): void {
        $this->authorRepository->delete($author);
    }

    /**
     * @return array<Author>
     */
    public function getAllAuthorsOrderByIdAsc(): array {
        return $this->authorRepository->findAllOrderByIdAsc();
    }

    /**
     * @return array<Author>
     */
    public function getAllAuthorsOrderByIdDesc(): array {
        return $this->authorRepository->findAllOrderByIdDesc();
    }
}
