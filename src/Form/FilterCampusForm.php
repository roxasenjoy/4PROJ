<?php

namespace App\Form;


use App\Entity\Campus;
use App\Entity\StudyLevel;
use App\Service\AuthService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterCampusForm extends AbstractType
{

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('campus', EntityType::class, [
            'class' => Campus::class,
            'choice_label' => 'name',
            'label' => false,
            'required' => true,
            'mapped' => false,
            'expanded' => true,
            'multiple' => true,
            'attr' => [
                'placeholder' => 'Campus',
                'class' => 'contentFilterCampus '
            ],
        ])

        ->add('submit', SubmitType::class, [
            'label' => 'Filtrer',
            'attr' => [
                'class' => 'zoom'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Campus::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}