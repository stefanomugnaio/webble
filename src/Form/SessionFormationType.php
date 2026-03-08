<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\SessionFormation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class SessionFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('date_debut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner la date de début.')
                ],
            ])

            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner la date de fin.')
                ],
            ])

            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Disponible' => 'disponible',
                    'Complet' => 'complet',
                    'Annulé' => 'annule',
                ],
                'attr' => ['class' => 'form-select'],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner un statut.')
                ],
            ])

            ->add('duree', NumberType::class, [
                'label' => 'Durée (heures)',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1
                ],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner la durée.'),
                    new Positive(message: 'La durée doit être un nombre positif.')
                ],
            ])

            ->add('heure_debut', TimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner l\'heure de début.')
                ],
            ])

            ->add('heure_fin', TimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner l\'heure de fin.')
                ],
            ])

            ->add('bloc', IntegerType::class, [
                'label' => 'Bloc',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1
                ],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner le bloc.'),
                    new Positive(message: 'Le bloc doit être un nombre positif.')
                ],
            ])

            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'label' => 'Formation',
                'choice_label' => function (Formation $libelle) {
                    return $libelle->getLibelle();
                    
                },
                'placeholder' => 'Choisissez une formation',
                'attr' => ['class' => 'form-select'],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner une session.')
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SessionFormation::class,
        ]);
    }
}
