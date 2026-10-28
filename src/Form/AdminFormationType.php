<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class AdminFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Nom de la formation',
                'attr' => ['class' => 'form-control'],
                'constraints' => [new NotBlank(), new Length(max: 100)],
            ])
            ->add('slug', TextType::class, [
                'label' => 'Identifiant URL',
                'required' => false,
                'help' => 'Laissez vide pour le générer automatiquement depuis le nom.',
                'attr' => ['class' => 'form-control', 'placeholder' => 'decouverte-informatique'],
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix HT',
                'scale' => 2,
                'attr' => ['class' => 'form-control', 'min' => 0, 'step' => '0.01'],
                'constraints' => [new NotBlank(), new Positive()],
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée en heures',
                'attr' => ['class' => 'form-control', 'min' => 1],
                'constraints' => [new NotBlank(), new Positive()],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 7],
                'constraints' => [new NotBlank(), new Length(max: 255)],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Formation::class]);
    }
}
