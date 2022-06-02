<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subject|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subject|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subject[]    findAll()
 * @method Subject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Subject $entity, bool $flush = true): void
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
    public function remove(Subject $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Obtenir tous les cours de l'étudiant + ceux qui sont validés.
     * @param $user
     * @return float|int|mixed|string
     */
    public function getAllLessonsByLevel($user){

        if($user->getUserExtended()){
            $qb = $this->createQueryBuilder('subject')
                ->select('subject.id','subject.name',  'subject.fullName','subject.points')
                ->join('subject.level', 'level')
                ->where('level.year = :userActualLevel')
                ->setParameter(':userActualLevel', $user->getUserExtended()->getActualLevel());
            return $qb->getQuery()->getResult();
        } else {
            return false;
        }
    }

    /**
     * Obtenir tous les cours dispo à SUPINFO
     * @param $user
     * @return float|int|mixed|string
     */
    public function getAllLessons(int $promotion = 0){

        $qb = $this->createQueryBuilder('subject')
            ->select('subject.id','subject.name',  'subject.fullName','level.name as levelName', 'level.year as levelYear')
            ->join('subject.level', 'level')
            ->orderBy('subject.name', 'ASC')
            ->orderBy('level.year', 'ASC');

        if($promotion){
            $qb->where('level.year = :promotion')
                ->setParameter('promotion', $promotion);
        }


        ;
        return $qb->getQuery()->getResult();
    }

    /**
     * Compte le nombre de cours disponibles
     * @param $user
     * @return float|int|mixed|string
     */
    public function countLesson(){
        $qb =   $this->createQueryBuilder('subject')
                ->select('count(subject) as totalLesson');
        return $qb->getQuery()->getResult();
    }

}
