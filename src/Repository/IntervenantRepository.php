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
            ->select('user.id', 'user.firstName', 'user.lastName', 'subject.name', 'sl.year as actualYear', 'campus.name as campusName')
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
    public function getIntervenantsPerCampus($filter = null, $coursId = null){

        $qb = $this->createQueryBuilder('intervenant')
            ->select('user.id', 'user.firstName', 'user.lastName',
                'campus.name as campusName', 'subject.id as idSubject',
                'campus.id as idCampus')

            ->join('intervenant.user', 'user')
            ->join('intervenant.subject', 'subject')
            ->join('user.userExtended', 'ux')
            ->join('intervenant.campus', 'campus')
            ->join('ux.actualLevel', 'sl')
        ;

            if($coursId){
                $qb ->where('subject.id = :coursId')
                    ->setParameter(':coursId', $coursId);
            }

            if($filter){
                $qb ->andWhere('campus.id IN (:campusId)')
                    ->setParameter(':campusId', $filter);
            }

        return $qb->getQuery()->getResult();
    }

    public function getSubjectByIntervenant($intervenantId){

        $qb = $this->createQueryBuilder('intervenant')
            ->select('user.id', 'user.firstName', 'user.lastName',
                'campus.name as campusName', 'subject.id as idSubject',
                'campus.id as idCampus')

            ->join('intervenant.user', 'user')
            ->join('intervenant.subject', 'subject')
            ->join('user.userExtended', 'ux')
            ->join('intervenant.campus', 'campus')
            ->join('ux.actualLevel', 'sl')
        ;

        if($intervenantId){
            $qb ->where('user.id = :userId')
                ->setParameter(':userId', $intervenantId);
        }

        return $qb->getQuery()->getResult();
    }

    public function selectIntervenant($idIntervenant, $idCampus, $idSubject){

        $qb =   $this->createQueryBuilder('intervenant')
                ->select('intervenant.id', 'user.firstName', 'user.lastName')
                ->join('intervenant.user', 'user')
                ->join('intervenant.subject', 'subject')
                ->join('intervenant.campus', 'campus')

                ->where('campus.id = :idCampus')
                ->andWhere('subject.id = :idSubject')

                ->setParameter(':idCampus', $idCampus)
                ->setParameter(':idSubject', $idSubject);



        if($idIntervenant !== null){
            $qb->andWhere('user.id = :idIntervenant')
                ->setParameter(':idIntervenant', $idIntervenant);
        }

        return $qb->getQuery()->getResult();

    }
}
