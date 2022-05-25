<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Role;
use App\Entity\StudyLevel;
use App\Service\AuthService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EditStudentFormType extends AbstractType
{

    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

       $user = $options['data'];

        $builder
            ->add('firstName', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prénom'
                ],
                'empty_data' => $user->getFirstName(),

            ])

            ->add('lastName', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom'
                ],
                'empty_data' => $user->getLastName(),
            ])

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Nom du campus'
                ],

                'query_builder' => function(EntityRepository $campusRepo){
                    $campusRepo =  $campusRepo
                                        ->createQueryBuilder('c');
                                        if($this->authService->isAuthenticatedUser()->getRoles()[0] != 'ROLE_ADMIN'){
                                            $campusRepo->where('c.id = :idCampus')
                                                ->setParameter('idCampus',
                                                    $this->authService
                                                        ->isAuthenticatedUser()
                                                        ->getCampus()
                                                        ->getId()
                                                );
                                        }
                    return $campusRepo;

                },
                'empty_data' => $user->getCampus()->getId(),
            ])

//            ->add('role', EntityType::class, [
//                'class' => Role::class,
//                'attr' => [
//                    'placeholder' => 'Rôle'
//                ],
//                'query_builder' => function(EntityRepository $roleRepository){
//                    $roleRepository =  $roleRepository->createQueryBuilder('r');
//
//                    // Si le user connecté, est un membre de l'équipe pédago.
//                    // On enlève le rôle admin car ils n'ont pas la possibilité de le mettre
//                    if($this->authService->isAuthenticatedUser()->getRole()->getId() === 15){
//                        $roleRepository = $roleRepository->where('r.id != :idRole')
//                            ->setParameter('idRole', 16);
//                    }
//
//                    // Si ce n'est pas le cas, le user connecté, il a accès à tous.
//
//                    return $roleRepository;
//
//                },
//                'choice_label' => 'name',
//                'mapped' => false,
//                'required' => true
//            ])

            ->add('email', EmailType::class, [
                'required'   => false,
                'attr' => [
                    'placeholder' => 'Adresse email'
                ],
                'empty_data' => $user->getEmail(),
            ])
            ->add('address', TextType::class, [
                'required'   => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Adresse'
                ],
                'empty_data' => $user->getUserExtended()->getAddress(),
                'data' => $user->getUserExtended()->getAddress()
            ])

            ->add('region', ChoiceType::class, [
                'choices' => [
                    'Auvergne-Rhônes-Alpes' => 'Auvergne-Rhônes-Alpes',
                    'Bourgogne-Franche-Comté' => 'Bourgogne-Franche-Comté',
                    'Bretagne' => 'Bretagne',
                    'Centre-Val de Loire' => 'Centre-Val de Loire',
                    'Corse' => 'Corse',
                    'Grand Est' => 'Grand Est',
                    'Hauts de France' => 'Hauts de France',
                    'Île de France' => 'Île de France',
                    'Normandie' => 'Normandie',
                    'Nouvelle-Aquitaine' => 'Nouvelle-Aquitaine',
                    'Occitanie' => 'Occitanie',
                    'Pays de la Loire' => 'Pays de la Loire',
                    'Provence-Alpes-Côte d\'Azur' => 'Provence-Alpes-Côte d\'Azur',
                    'Guadeloupe' => 'Guadeloupe',
                    'Martinique' => 'Martinique',
                    'Guyane' => 'Guyane',
                    'La Réunion' => 'La Réunion',
                    'Mayotte' => 'Mayotte'
                ],
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Région'
                ],
                'data' => $user->getUserExtended()->getRegion()
            ])

            ->add('actualLevelName', EntityType::class, [
                'class' => StudyLevel::class,
                'choice_label' => 'name',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Niveau d\'étude actuel'
                ],
                'empty_data' => $user->getUserExtended()->getActualLevel()->getId(),
                'data' => $user->getUserExtended()->getActualLevel(),
                'query_builder' => function(EntityRepository $level){
                    $level =  $level
                        ->createQueryBuilder('l')
                        ->where('l.id >= 1')
                        ->andWhere('l.id <= 5');
                    return $level;

                },
            ])

            ->add('hasProContract', ChoiceType::class, [
                'choices' => [
                    'Oui, il possède une alternance/stage' => true,
                    'Non, il ne possède pas d\'alternance/stage' => false
                ],
                'required' => true,
                'mapped' => false,
                'data' => $user->getUserExtended()->getHasProContract(),
            ])

            ->add('isHired', ChoiceType::class, [
                'choices' => [
                    'Oui, il est embauché' => true,
                    'Non, il est toujours en recherche de travail' => false
                ],
                'required' => true,
                'mapped' => false,
                'data' => $user->getUserExtended()->getIsHired(),
            ])




        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
