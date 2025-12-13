<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\User\SignatureType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findOneByPasswordSignature($signature): ?User {
        $type = SignatureType::PASSWORD;

        return $this->createQueryBuilder('u')
            ->innerJoin('u.userSignature', 'us')
            ->addSelect('us')
            ->andWhere('us.signature = :signature')
            ->andWhere('us.type = :type')
            ->setParameter('signature', $signature)
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneorNullResult();
    }


    public function findOneByEmailSignature($signature): ?User {
        $type = SignatureType::EMAIL;

        return $this->createQueryBuilder('u')
            ->innerJoin('u.userSignature', 'us')
            ->addSelect('us')
            ->andWhere('us.signature = :signature')
            ->andWhere('us.type = :type')
            ->setParameter('signature', $signature)
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneorNullResult();
    }

    public function save(User|UserInterface $user): void {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
