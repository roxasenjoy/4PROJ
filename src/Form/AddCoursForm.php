<?php

namespace App\Form;

use App\Entity\StudyLevel;
use App\Entity\Subject;
use App\Service\GlobalService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AddCoursForm extends AbstractType
{

    public function __construct(GlobalService $globalService)
    {
        $this->globalService = $globalService;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {



        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Diminutif',
                ]
            ])

            ->add('fullName', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom complet'
                ]
            ])

            ->add('points', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Crédits ECTS'
                ],

                'choices' => $this->globalService->generatePoints(20)
            ])

            ->add('year', EntityType::class, [
                'class' => StudyLevel::class,
                'choice_label' => 'name',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Niveau d\'étude actuel'
                ],
                'query_builder' => function(EntityRepository $level){
                    $level =  $level
                        ->createQueryBuilder('l')
                        ->where('l.id >= 1')
                        ->andWhere('l.id <= 5');
                    return $level;
                }
            ])

            ->add('intervenants', CollectionType::class, [
                'entry_type' => _addIntervenantFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'mapped' => false,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subject::class
        ]);
    }
}
