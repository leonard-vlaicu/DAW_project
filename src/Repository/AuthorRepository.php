<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AuthorRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Author::class);
    }

    public function delete(Author $author): void {
        $this->getEntityManager()->remove($author);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array<Author>
     */
    public function findAllOrderByIdAsc(): array {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Author>
     */
    public function findAllOrderByIdDesc(): array {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Author $author): void {
        $this->getEntityManager()->persist($author);
        $this->getEntityManager()->flush();
    }
}
