<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Book::class);
    }

    public function delete(Book $book): void {
        $this->getEntityManager()->remove($book);
        $this->getEntityManager()->flush();
    }

    public function save(Book $book): void {
        $this->getEntityManager()->persist($book);
        $this->getEntityManager()->flush();
    }

    public function findOneByIsbn($isbn): ?Book {
        return $this->createQueryBuilder('b')
            ->where('b.isbn = :isbn')
            ->setParameter('isbn', $isbn)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<Book>
     */
    public function findAllOrderByIdAsc(): array {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
