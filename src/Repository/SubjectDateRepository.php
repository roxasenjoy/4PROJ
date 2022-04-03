<?php

namespace App\Repository;

use App\Entity\SubjectDate;
use App\Service\GlobalService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubjectDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubjectDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubjectDate[]    findAll()
 * @method SubjectDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectDateRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        GlobalService $globalService,
    )
    {
        $this->globalService = $globalService;
        parent::__construct($registry, SubjectDate::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SubjectDate $entity, bool $flush = true): void
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
    public function remove(SubjectDate $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function getAgendaByUser($userId){

        // Récupérer les prochains cours de l'étudiant
        // On se base sur l'année des cours et l'année de l'étudiant, si c'est pareil on lui affiche.
        return $this->createQueryBuilder('sd')
            ->select('subject.name', 'sd.date_begin', 'sd.date_end')
            ->join('sd.user', 'user')
            ->join('sd.subject', 'subject')
            ->where('user.id = :userId')
            ->setParameter(':userId', $userId)
            ->andWhere('sd.date_begin > :todayDate')
            ->setParameter(':todayDate', $this->globalService->getTodayDate())
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;


    }

    // /**
    //  * @return SubjectDate[] Returns an array of SubjectDate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubjectDate
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
