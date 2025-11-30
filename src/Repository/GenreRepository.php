<?php

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GenreRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Genre::class);
    }

    public function delete(Genre $genre): void {
        $this->getEntityManager()->remove($genre);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array<Genre>
     */
    public function findAllOrderByNameAsc(): array {
        return $this->createQueryBuilder('g')
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Genre>
     */
    public function findAllOrderByNameDesc(): array {
        return $this->createQueryBuilder('g')
            ->orderBy('g.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Genre>
     */
    public function findAllOrderByIdAsc(): array {
        return $this->createQueryBuilder('g')
            ->orderBy('g.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Genre>
     */
    public function findAllOrderByIdDesc(): array {
        return $this->createQueryBuilder('g')
            ->orderBy('g.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Genre $genre): void {
        $this->getEntityManager()->persist($genre);
        $this->getEntityManager()->flush();
    }
}
