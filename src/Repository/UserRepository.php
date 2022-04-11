<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\AuthService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, AuthService $authService)
    {
        $this->authService = $authService;
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getAllStudents($promotion){

        $user = $this->authService->isAuthenticatedUser();

        $qb = $this->createQueryBuilder('u')
            ->select('u.id', 'campus.name as campusName ', 'u.firstName', 'u.lastName', 'al.name as actualYear', 'al.year')
            ->join('u.userExtended', 'ux')
            ->join('ux.actualLevel' ,' al')
            ->join('u.campus', 'campus')
            ->join('u.role', 'role')

            /* On récupère tous les étudiants du campus */
            ->where('campus.id = :campusId')
            ->setParameter('campusId', $user->getCampus()->getId())
            ->andWhere('role.id = 12')
            ->orderBy('u.firstName', 'ASC')
            ->orderBy('al.year', 'ASC');

            /* Si aucune promotion n'est sélectionné, on affiche tous les étudiants du campus */
            if($promotion > 0){
                $qb->andWhere('al.year = :promotionSelected')
                    ->setParameter(':promotionSelected', $promotion);
            }

            ;

        return $qb->getQuery()->getResult();

    }


    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}