<?php

namespace App\Controller;

use App\Entity\StudyLevel;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\UserExtended;
use App\Entity\UserGrade;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotesController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $em,
        AuthService $authService
    )
    {
        $this->em = $em;
        $this->authService = $authService;
    }

    /**
     * Obtenir les notes de l'étudiant concerné
     * @return Response
     */
    #[Route('/notes', name: 'app_notes')]
    public function getUserNote(): Response
    {

        $user = $this->authService->isAuthenticatedUser();

        $allLessons = $this->em->getRepository(Subject::class)->getAllLessonsByLevel($user);
        $grades = $this->em->getRepository(UserGrade::class)->getGradesByUser($user->getId());

        $combined = array();

            /**
             * On combine les deux array qu'on vient de récuperer pour ne former plus qu'un
             */

        if($allLessons){
            foreach ($allLessons as $lesson) {
                $comb = array(
                    'id' => $lesson['id'],
                    'fullName' => $lesson['fullName'],
                    'points' => $lesson['points'],
                    'note' => '--',
                    'status' => null);

                foreach ($grades as $grade) {
                    if ($grade['subjectId'] == $lesson['id']) {
                        $comb['note'] = $grade['grade'];
                        $comb['status'] = $grade['status'];
                        break;
                    }
                }

                $combined[] = $comb;
            }
        }


        return $this->render('notes/notes.html.twig', [
            'notes' => $combined
        ]);


    }
}
