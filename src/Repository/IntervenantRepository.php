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

    public function getIntervenant($idIntervenant){

        return $this->createQueryBuilder('i')
                ->join('i.user', 'user')
                ->andWhere('user.id = :idIntervenant')
                ->setParameter(':idIntervenant', $idIntervenant)
                ->getQuery()
                ->getResult();

    }

    /**
     * Affiche tous les intervenants de l'étudiant en fonction du campus et de l'année des sujets
     * @param $user
     * @return float|int|mixed|string
     */
    public function getIntervenants($user){

        $qb = $this->createQueryBuilder('intervenant')
            ->select('user.id', 'user.firstName', 'user.lastName', 'subject.name', 'campus.name as campusName')
            ->join('intervenant.user', 'user')
            ->join('intervenant.subject', 'subject')
            ->join('user.userExtended', 'ux')
            ->join('user.campus', 'campus')
            ->join('subject.level', 'level')

            ->where('campus.id = :campusId')
            ->setParameter(':campusId', $user->getCampus()->getId())

            ->andWhere('level.year = :year')
            ->setParameter(':year', $user->getUserExtended()->getActualLevel()->getYear())

        ;

        return $qb->getQuery()->getResult();
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
            ->join('subject.level', 'sl')
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

    /**
     * Détermine tous les cours enseignés par un intervenant + vérifie si c'est bien son cours
     * @param $intervenantId
     * @param $coursId
     * @return float|int|mixed|string
     */
    public function getAllowedSubjectPerIntervenant($intervenantId, $coursId = null){
        $qb = $this->createQueryBuilder('intervenant')
            ->select('subject.id','subject.name',  'subject.fullName','level.name as levelName','level.year as levelYear')

            ->join('intervenant.user', 'user')
            ->join('intervenant.subject', 'subject')
            ->join('subject.level', 'level')
            ->join('user.userExtended', 'ux')
            ->join('intervenant.campus', 'campus')
        ;

        if($intervenantId){
            $qb ->where('user.id = :userId')
                ->setParameter(':userId', $intervenantId);
        }

        if($coursId){
            $qb ->andWhere('subject.id = :subjectId')
                ->setParameter(':subjectId', $coursId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Obtenir tous les sujets en fonction de l'id de l'intervenant
     * @param $intervenantId
     * @return float|int|mixed|string
     */
    public function getSubjectByIntervenant($intervenantId){


        $qb = $this->createQueryBuilder('intervenant')
            ->select('user.id', 'user.firstName', 'user.lastName',
                'campus.name as campusName', 'subject.id as idSubject',
                'campus.id as idCampus')

            ->join('intervenant.user', 'user')
            ->join('intervenant.subject', 'subject')
            ->join('user.userExtended', 'ux')
            ->join('intervenant.campus', 'campus')
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
