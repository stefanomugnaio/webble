<?php

namespace App\Form;

use App\Entity\LigneOffre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneOffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextareaType::class, ['label' => 'Détail de la tâche', 'attr' => ['rows' => 2]])
            ->add('quantite', NumberType::class, ['label' => 'Quantité', 'scale' => 2, 'html5' => true, 'attr' => ['min' => 0.01, 'step' => 0.01]])
            ->add('prixUnitaire', MoneyType::class, ['label' => 'Prix unitaire HT', 'currency' => 'EUR', 'scale' => 2, 'attr' => ['min' => 0, 'step' => 0.01]])
            ->add('offerte', CheckboxType::class, ['label' => 'Offerte', 'required' => false])
            ->add('position', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => LigneOffre::class]); }
}
