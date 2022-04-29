<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Role;
use App\Entity\StudyLevel;
use App\Entity\Subject;
use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use App\Service\AuthService;
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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;


class _addIntervenantFormType extends AbstractType
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

            ->add('email', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => false,
                'query_builder' => function(UserRepository $user){
                    return $user->getAllTeacher();
                },
                'attr' => [
                    'class' => 'emailUser selectNewCours'
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
