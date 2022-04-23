<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Role;
use App\Entity\StudyLevel;
use App\Service\AuthService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AddStudentFormType extends AbstractType
{

    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prénom'
                ]

            ])

            ->add('lastName', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom du campus'
                ],

                'query_builder' => function(EntityRepository $campusRepo){
                    $campusRepo =  $campusRepo
                                        ->createQueryBuilder('c')
                                        ->where('c.id = :idCampus')
                                        ->setParameter('idCampus',
                                            $this->authService
                                                ->isAuthenticatedUser()
                                                ->getCampus()
                                                ->getId()
                                        );
                    return $campusRepo;

                }
            ])

            ->add('role', EntityType::class, [
                'class' => Role::class,
                'attr' => [
                    'placeholder' => 'Rôle'
                ],
                'query_builder' => function(EntityRepository $roleRepository){
                    $roleRepository =  $roleRepository->createQueryBuilder('r');

                    // Si le user connecté, est un membre de l'équipe pédago.
                    // On enlève le rôle admin car ils n'ont pas la possibilité de le mettre
                    if($this->authService->isAuthenticatedUser()->getRole()->getId() === 15){
                        $roleRepository = $roleRepository->where('r.id IN (:idRole)')
                            ->setParameter('idRole', [12,13]);
                    }


                    // Si ce n'est pas le cas, le user connecté, il a accès à tous.

                    return $roleRepository;

                },
                'choice_label' => 'name',
                'required' => true
            ])

            ->add('email', EmailType::class, [
                'required'   => true,
                'attr' => [
                    'placeholder' => 'Adresse email'
                ]
            ])
            ->add('address', TextType::class, [
                'required'   => true,
                'attr' => [
                    'placeholder' => 'Adresse'
                ]
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
                'attr' => [
                    'placeholder' => 'Région'
                ]
            ])

            ->add('actualLevelName', EntityType::class, [
                'class' => StudyLevel::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Dernier niveau d\'étude'
                ],
                'query_builder' => function(EntityRepository $level){
                    $level =  $level
                        ->createQueryBuilder('l')
                        ->where('l.id >= 1')
                        ->andWhere('l.id <= 5');
                    return $level;

                },
            ])

            ->add('lastLevelName', EntityType::class, [
                'class' => StudyLevel::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Année d\'entrée à SUPINFO'
                ],
                'query_builder' => function(EntityRepository $level){
                    $level =  $level
                        ->createQueryBuilder('l')
                        ->where('l.id >= 6');
                    return $level;

                },
            ])

            ->add('yearEntry', IntegerType::class, [
                'attr' => [
                        'placeholder' => 'Année d\'entrée'
                    ]
            ])

            ->add('yearExit', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Année de sortie'
                ]
            ])

            ->add('birthday', BirthdayType::class, [
                'attr' => [
                    'placeholder' => 'Anniversaire'
                ]
            ])

            ->add('hasProContract', ChoiceType::class, [
                'choices' => [
                    'Non, il ne possède pas d\'alternance/stage' => false,
                    'Oui, il possède une alternance/stage' => true
                ],
                'required' => true,
            ])

            ->add('isHired', ChoiceType::class, [
                'choices' => [
                    'Non, il est toujours en recherche de travail' => false,
                    'Oui, il est embauché' => true,

                ],
                'required' => true,
            ])




        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
