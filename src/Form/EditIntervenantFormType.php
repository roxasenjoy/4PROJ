<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Role;
use App\Entity\StudyLevel;
use App\Entity\Subject;
use App\Entity\User;
use App\Service\AuthService;
use App\Service\GlobalService;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EditIntervenantFormType extends AbstractType
{

    public function __construct(GlobalService $globalService, AuthService $authService)
    {
        $this->globalService = $globalService;
        $this->authService = $authService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user = $options['data'];

        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom',
                ]
            ])

            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom'
                ]
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
                'data' => $user->getCampus(),
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

            ->add('subjects', CollectionType::class, [
                'entry_type' => _addSubjectFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'mapped' => false,
                'label' => false
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
