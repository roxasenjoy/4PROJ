<?php

namespace App\Repository;

use App\Entity\Intervenant;
use App\Service\AuthService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Intervenant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intervenant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intervenant[]    findAll()
 * @method Intervenant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntervenantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, AuthService $authService)
    {
        parent::__construct($registry, Intervenant::class);
        $this->authService = $authService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Intervenant $entity, bool $flush = true): void
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
    public function remove(Intervenant $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getIntervenants($user){
        return $this->createQueryBuilder('intervenant')
            ->select('user.id', 'user.firstName', 'user.lastName', 'subject.name', 'sl.year as actualYear')
            ->join('intervenant.user', 'user')
            ->join('intervenant.subject', 'subject')
            ->join('user.userExtended', 'ux')
            ->join('user.campus', 'campus')
            ->join('ux.actualLevel', 'sl')
            ->where('campus.id = :campusId')
            ->andWhere('sl.year = :userActualLevel')
            ->setParameter(':userActualLevel', $user->getUserExtended()->getActualLevel()->getYear())
            ->setParameter('campusId', $user->getCampus()->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * On récupère le nom/prenom de l'intervenant, son campus
     *
     * @param $filter
     * @param $coursId
     * @return float|int|mixed|string
     */
    public function getIntervenantsPerCampus($filter, $coursId){

        $user = $this->authService->isAuthenticatedUser();

        $qb = $this->createQueryBuilder('intervenant')
            ->select('user.id', 'user.firstName', 'user.lastName', 'campus.name as campusName')

            ->join('intervenant.user', 'user')
            ->join('intervenant.subject', 'subject')
            ->join('user.userExtended', 'ux')
            ->join('intervenant.campus', 'campus')
            ->join('ux.actualLevel', 'sl')
            ->where('subject.id = :coursId')
            ->setParameter(':coursId', $coursId)
        ;

            if($filter){
                $qb ->andWhere('campus.id IN (:campusId)')
                    ->setParameter(':campusId', $filter);
            }

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Intervenant[] Returns an array of Intervenant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Intervenant
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
