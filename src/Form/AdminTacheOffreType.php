<?php

namespace App\Form;

use App\Entity\TacheOffre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminTacheOffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, ['label' => 'Libellé'])
            ->add('quantite', NumberType::class, ['label' => 'Quantité par défaut', 'scale' => 2, 'html5' => true, 'attr' => ['min' => 0.01, 'step' => 0.01]])
            ->add('prixUnitaire', MoneyType::class, ['label' => 'Prix unitaire HT', 'currency' => 'EUR', 'scale' => 2, 'attr' => ['min' => 0, 'step' => 0.01]])
            ->add('offerte', CheckboxType::class, ['label' => 'Cette tâche est offerte par défaut', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => TacheOffre::class]); }
}
