<?php

namespace App\Form;

use App\Entity\Formation;
use App\Enum\FormationLibelle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

           ->add('libelle', EnumType::class, [
                'class' => FormationLibelle::class,
                'choice_label' => function (FormationLibelle $choice) {
                    return $choice->label();
                },
                'placeholder' => 'Choisir une formation',
                'label' => 'Nom de la formation',
                'attr' => ['class' => 'form-select'],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner une formation.')
                ],
            ])

            ->add('prix', NumberType::class, [
                'label' => 'Prix (HT)',
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner le prix.'),
                    new Positive(message: 'Le prix doit être supérieur à 0.')
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5
                ],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner une description.'),
                    new Length(
                        min: 10,
                        minMessage: 'La description doit contenir au moins {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en heures)',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner la durée.'),
                    new Positive(message: 'La durée doit être positive.')
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
