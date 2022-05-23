<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Subject;

use App\Service\AuthService;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;



class _addSubjectFormType extends AbstractType
{

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => false,
                'attr' => [
                    'class' => 'campusNameSelected selectNewCours'
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

            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_name' => 'name',
                'label' => false,
                'attr' => [
                    'class' => 'emailUser selectNewCours'
                ],

            ]);
    }

}
