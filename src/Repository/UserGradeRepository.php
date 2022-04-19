<?php

namespace App\Repository;

use App\Entity\UserGrade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserGrade|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGrade|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGrade[]    findAll()
 * @method UserGrade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGrade::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UserGrade $entity, bool $flush = true): void
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
    public function remove(UserGrade $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Obtenir toutes les notes de l'étudiant
     *
     * @param $userId
     * @return float|int|mixed|string
     */
    public function getGradesByUser($userId){

        $qb = $this->createQueryBuilder('ug')
            ->select('subject.id as subjectId', 'ug.grade', 'ug.status', 'subject.name')
            ->join('ug.subject', 'subject')
            ->where('ug.user = :user')
            ->setParameter(':user', $userId)
            ->orderBy('ug.date', 'DESC');

        return $qb->getQuery()->getResult();

    }

    public function getTotalEctsPerUser($userId){
        $qb = $this->createQueryBuilder('ug')
            ->select('SUM(subject.points)')
            ->join('ug.subject', 'subject')
            ->where('ug.user = :user')
            ->andWhere('ug.grade >= 10')
            ->setParameter(':user', $userId);

        return $qb->getQuery()->getResult();
    }

    public function hasUserGrade($user, $cours){

        $qb = $this->createQueryBuilder('ug')
                    ->select('ug.id')
                    ->join('ug.user', 'user')
                    ->join('ug.subject', 'subject')

                    ->where('subject.id = :cours')
                    ->setParameter(':cours', $cours->getId())

                    ->andWhere('user.id = :user')
                    ->setParameter('user', $user->getId());

        return $qb->getQuery()->getResult();

    }

    // /**
    //  * @return UserGrade[] Returns an array of UserGrade objects
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
    public function findOneBySomeField($value): ?UserGrade
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
