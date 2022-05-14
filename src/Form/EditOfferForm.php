<?php

namespace App\Form;


use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EditOfferForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Titre de l'offre"
                ]
            ])

            ->add('company', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Nom de l'entreprise"
                ]
            ])

            ->add('type_contract', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Temps pleins' => 'Temps Pleins',
                    'Intérim'       => 'Intérim',
                    'Temps Partiel' => 'Temps Partiel',
                    'Freelance'     => 'Freelance',
                    'Apprentissage' => 'Apprentissage',
                    'Contrat pro'   => 'Contrat pro',
                    'Stage'         => 'Stage'
                ],
            ])

            ->add('experience', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    '< 2 ans' => "< 2 ans",
                    '2 ans à 4 ans' => "2 ans à 4 ans",
                    '4 ans à 6 ans' => "4 ans à 6 ans",
                    '> 6 ans' => "> 6 ans"
                ],
            ])

            ->add('salaire', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Salaire potentiel"
                ]
            ])



            ->add('location', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Adresse où se trouve l'entreprise"
                ]
            ])

            ->add('description', TextareaType::class, [
                'label' => false,

                'attr' => [
                    'placeholder' => "Description du poste de l'entreprise",
                    'cols' => 100,
                    'row' => 100,
                    'style' => 'width:100%; height: 200px; font-size: 1em; color: $blue;'
                ]
            ])

            ->add('profil', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Profil recherché pour le poste",
                    'cols' => 100,
                    'row' => 100,
                    'style' => 'width:100%; height: 200px; font-size: 1em; color: $blue;'
                ]
            ])

            ->add('website', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Lien vers le site internet de l'entreprise"
                ]
            ])



        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class
        ]);
    }
}
