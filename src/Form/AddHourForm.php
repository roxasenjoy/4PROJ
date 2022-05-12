<?php

namespace App\Form;


use App\Entity\SubjectDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AddHourForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('dateBegin', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'choice',
                'date_format' => 'dd-MM-yyyy HH:mm',
                'html5' => false,
                'attr' => [
                    'class' => 'date'
                ],
                'placeholder' => [
                    'year' => 'Années',
                    'month' => 'Mois',
                    'day' => 'Jours',
                    'minute' => 'Minutes',
                    'hour' => 'Heures'
                ],
            ])

            ->add('dateEnd', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'choice',
                'date_format' => 'dd-MM-yyyy HH:mm',
                'html5' => false,
                'attr' => [
                    'class' => 'date'
                ],
                'placeholder' => [
                    'year' => 'Années',
                    'month' => 'Mois',
                    'day' => 'Jours',
                    'minute' => 'Minutes',
                    'hour' => 'Heures'
                ],
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubjectDate::class
        ]);
    }
}
