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

    const ROLE_STUDENT = 12;
    const ROLE_VISITEUR = 13;
    const ROLE_PROFESSEUR = 14;
    const ROLE_PEDAGO = 15;
    const ROLE_ADMIN = 16;

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


    /**
     * Compte le nombre d'étudiant qui se trouve dans le campus de l'utilisateur connecté
     * @param $user
     * @return float|int|mixed|string
     */
    public function countStudent($user){
        $qb = $this->createQueryBuilder('u')

            ->select('count(role.id) as totalStudent')

            /** Jointure de tous les éléments  **/
            ->join('u.role', 'role')
            ->join('u.campus', 'campus')
            ->join('u.userExtended', 'ux')

            /** Conditions **/
            ->where('role.id = :roleStudent') // Condition : Le rôle de l'étudiant est 12
            ->setParameter(':roleStudent', self::ROLE_STUDENT)

            /** On regroupe les étudiants par rôle */
            ->groupBy('role.id');

        if($user->getRoles()[0] != 'ROLE_ADMIN')
        {
            $qb ->andWhere('campus.id = :campusId')
                ->setParameter(':campusId', $user->getCampus()->getId());
        }



        return $qb->getQuery()->getResult();
    }

    /**
     * Utilisé à l'endroit suivant : /admin/cours
     * Permet d'afficher les élèves en fonction du filtre sélectionné
     *
     * @param $promotion
     * @return float|int|mixed|string
     */
    public function getAllStudentsPerPromotion($promotion, $filterCampus, $researchBar){

        $user = $this->authService->isAuthenticatedUser();

        $qb = $this ->createQueryBuilder('u')
                    ->select('u.id', 'campus.name as campusName ', 'u.firstName', 'u.lastName', 'al.name as actualYear', 'al.year')
                    ->join('u.userExtended', 'ux')
                    ->join('ux.actualLevel' ,' al')
                    ->join('u.campus', 'campus')
                    ->join('u.role', 'role');



        if($filterCampus)
        {
            $qb     ->andWhere('campus.id IN (:campusId)')
                    ->setParameter(':campusId', $filterCampus);
        }

            $qb     ->andWhere('role.id = :roleStudent')
                    ->setParameter(':roleStudent', self::ROLE_STUDENT)
                    ->addOrderBy('al.year', 'ASC')
                    ->addOrderBy('u.firstName', 'ASC');

            /* Si aucune promotion n'est sélectionné, on affiche tous les étudiants du campus */
            if($promotion > 0){
                $qb ->andWhere('al.year = :promotionSelected')
                    ->setParameter(':promotionSelected', $promotion);
            }


        /* Recherche via la barre de recherche */
        if($researchBar){
            $qb ->andWhere('u.lastName LIKE :research OR u.firstName LIKE :research')
                ->setParameter('research', '%' . $researchBar . '%');
        }

        /* On récupère tous les étudiants du campus */
        // Si ADMIN on récupere tous les campus
        if($user->getRoles()[0] != 'ROLE_ADMIN')
        {
            $qb ->andWhere('campus.id = :campusId')
                ->setParameter(':campusId', $user->getCampus()->getId());
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * Utilisé à l'endroit suivant : /admin/cours/details/{id}
     * Obtenir tous les étudiants se trouvant sur les campus sélectionné + en fonction de l'id de la matière
     * @param $filter
     * @return float|int|mixed|string
     */
    public function getAllStudentPerCours($filter, $coursId){

        $qb = $this->createQueryBuilder('u')

            ->select('u.id', 'u.firstName', 'u.lastName', 'campus.name as campusName', 'ug.grade', 'ug.status', 'subject.id as subjectId')

            /** Jointure de tous les éléments  **/
            ->join('u.role', 'role')
            ->join('u.campus', 'campus')
            ->join('u.userExtended', 'ux')
            ->join('ux.actualLevel' ,' al')
            ->join('u.userGrades', 'ug')
            ->join('ug.subject', 'subject')
            ->join('subject.level', 'levelSubject')

            /** Conditions **/
            ->where('role.id = :roleStudent') // Condition : Le rôle de l'étudiant est 12
            ->andWhere('al.year = levelSubject.year') // Condition : Le niveau du sujet est égal au niveau de l'étudiant
            ->andWhere('subject.id = :coursId') // Condition : Le cours est égal à $coursId

            ->setParameter(':roleStudent', self::ROLE_STUDENT)
            ->setParameter(':coursId', $coursId)

            ->addOrderBy('ug.status', 'ASC')
            ->addOrderBy('u.firstName', 'ASC');

            if($filter){
                $qb->andWhere('campus.id IN (:filter)')
                    ->setParameter('filter', $filter);
            }

        return $qb->getQuery()->getResult();

    }

    public function getAllStudentsPerLevel($filter, $cours){

        $user = $this->authService->isAuthenticatedUser();

        $qb = $this->createQueryBuilder('u')
            ->select('u.id', 'campus.name as campusName ', 'u.firstName', 'u.lastName', 'al.name as actualYear', 'al.year')
            ->join('u.userExtended', 'ux')
            ->join('ux.actualLevel' ,' al')
            ->join('u.campus', 'campus')
            ->join('u.role', 'role')

            /* On récupère tous les étudiants du campus */
            ->where('role.id = :roleStudent')
            ->setParameter(':roleStudent', self::ROLE_STUDENT)

            ->andWhere('al.year = :coursYear')
            ->setParameter(':coursYear', $cours->getLevel()->getYear());

            if($user->getRoles()[0] != "ROLE_ADMIN"){
                $qb->andWhere('campus.id = :campusId')
                    ->setParameter(':campusId', $user->getCampus()->getId());
            }



            $qb->addOrderBy('campus.name', 'ASC')
                ->addOrderBy('u.firstName', 'ASC');

        if($filter){
            $qb->andWhere('campus.id IN (:filter)')
                ->setParameter('filter', $filter);
        }

        return $qb->getQuery()->getResult();
    }


    /**
     * Return tous les utilisateurs pour le query_builder de la création d'un cours
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryGetAllTeacher($user){

        $qb = $this->createQueryBuilder('u')
            ->join('u.campus', 'campus')
            ->join('u.role', 'role')
            ->where('role.id = :professeur')
            ->setParameter(':professeur', self::ROLE_PROFESSEUR)
            ->andWhere('campus.id = :campusId')
            ->setParameter(':campusId', $user->getCampus()->getId())
            ->orderBy('u.email', 'DESC');

        return $qb;
    }

    /**
     * @return float|int|mixed|string
     */
    public function getAllTeacher($filterCampus = [], $research = null){

        $user = $this->authService->isAuthenticatedUser();


        $qb = $this->createQueryBuilder('u')
                    ->select('u.id', 'u.firstName', 'u.lastName', 'campus.name as campusName')
                    ->join('u.campus', 'campus')
                    ->join('u.role', 'role')
                    ->andWhere('role.id = :teacherRole')
                    ->setParameter(':teacherRole', self::ROLE_PROFESSEUR)
                    ->orderBy('campus.name', 'ASC');

        if($filterCampus){
            $qb->andWhere('campus.id IN (:filterCampus)')
                ->setParameter(':filterCampus', $filterCampus);
        }

        if($user->getRoles()[0] != "ROLE_ADMIN"){
            $qb->andWhere('campus.id = :campusId')
                ->setParameter(':campusId', $user->getCampus()->getId());
        }

        if($research){
            $qb ->andWhere('u.lastName LIKE :research OR u.firstName LIKE :research')
                ->setParameter('research', '%' . $research . '%');
        }




        return $qb->getQuery()->getResult();

    }

}