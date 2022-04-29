<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Subject;

use App\Service\AuthService;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('name', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => false,
                'attr' => [
                    'class' => 'campusNameSelected selectNewCours'
                ],
            ])

            ->add('test', EntityType::class, [
                'class' => Subject::class,
                'choice_name' => 'name',
                'label' => false,
                'attr' => [
                    'class' => 'emailUser selectNewCours'
                ],

            ])


        ;
    }

}
