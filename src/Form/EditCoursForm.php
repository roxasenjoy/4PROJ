<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Role;
use App\Entity\StudyLevel;
use App\Entity\Subject;
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


class EditCoursForm extends AbstractType
{

    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $subject = $options['data'];

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Diminutif',
                    'maxlength' => 5
                ],
                'empty_data' => $subject->getName(),
                'data' => $subject->getName(),
            ])

            ->add('fullName', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom complet'
                ],
                'empty_data' => $subject->getFullName(),
                'data' => $subject->getFullName(),
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

                },
                'empty_data' => $subject->getLevel(),
                'data' => $subject->getLevel(),
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subject::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
