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


class AddIntervenantForm extends AbstractType
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

            ->add('birthday', BirthdayType::class, [
                'attr' => [
                    'placeholder' => 'Anniversaire'
                ]
            ])






        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
