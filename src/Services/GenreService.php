<?php

namespace App\Services;

use App\Entity\Genre;
use App\Repository\GenreRepository;

class GenreService {
    public function __construct(private GenreRepository $genreRepository) {
    }

    /**
     * @return array<Genre>
     */
    public function getAllGenres(): array {
        return $this->genreRepository->findAll();
    }

    public function getGenreById($id): Genre|null {
        return $this->genreRepository->find($id);
    }

    /**
     * @return array<Genre>
     */
    public function getAllGenresOrderByIdAsc(): array {
        return $this->genreRepository->findAllOrderByIdAsc();
    }

    /**
     * @return array<Genre>
     */
    public function getAllGenresOrderByIdDesc(): array {
        return $this->genreRepository->findAllOrderByIdDesc();
    }

    /**
     * @return array<Genre>
     */
    public function getAllGenresOrderByNameDesc(): array {
        return $this->genreRepository->findAllOrderByNameDesc();
    }

    /**
     * @return array<Genre>
     */
    public function getAllGenresOrderByNameAsc(): array {
        return $this->genreRepository->findAllOrderByNameAsc();
    }

    public function save(Genre $genre): void {
        $this->genreRepository->save($genre);
    }

    public function delete(Genre $genre): void {
        $this->genreRepository->delete($genre);
    }
}
