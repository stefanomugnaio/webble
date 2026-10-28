<?php

namespace App\Form;

use App\Entity\OffreCommerciale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminOffreCommercialeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TextType::class, ['label' => 'Numéro de l’offre'])
            ->add('statut', ChoiceType::class, ['choices' => ['Brouillon' => 'brouillon', 'Envoyée' => 'envoyee', 'Acceptée' => 'acceptee', 'Refusée' => 'refusee']])
            ->add('dateEmission', DateType::class, ['label' => 'Date d’émission', 'widget' => 'single_text'])
            ->add('dateValidite', DateType::class, ['label' => 'Valable jusqu’au', 'widget' => 'single_text'])
            ->add('clientSociete', TextType::class, ['label' => 'Société', 'required' => false])
            ->add('clientNom', TextType::class, ['label' => 'Nom du client'])
            ->add('clientEmail', EmailType::class, ['label' => 'E-mail', 'required' => false])
            ->add('clientAdresse', TextareaType::class, ['label' => 'Adresse', 'required' => false, 'attr' => ['rows' => 3]])
            ->add('objet', TextType::class, ['label' => 'Objet de l’offre'])
            ->add('introduction', TextareaType::class, ['label' => 'Introduction', 'required' => false, 'attr' => ['rows' => 4]])
            ->add('tauxTva', NumberType::class, ['label' => 'TVA (%)', 'scale' => 2, 'html5' => true, 'attr' => ['min' => 0, 'step' => 0.01]])
            ->add('conditions', TextareaType::class, ['label' => 'Conditions et précisions', 'required' => false, 'attr' => ['rows' => 5]])
            ->add('lignes', CollectionType::class, ['entry_type' => LigneOffreType::class, 'allow_add' => true, 'allow_delete' => true, 'by_reference' => false, 'prototype' => true, 'label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => OffreCommerciale::class]); }
}
