<?php

namespace App\Form;

use App\Entity\Devis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // -----------------------------
            // CHAMPS TEXTES (UN PAR UN)
            // -----------------------------

            ->add('nom', null, [
                'attr'       => ['class' => 'form-control fs-5'],
                'label_attr' => ['class' => 'fs-5'],
            ])

            ->add('prenom', null, [
                'attr'       => ['class' => 'form-control fs-5'],
                'label_attr' => ['class' => 'fs-5'],
            ])

            ->add('email', null, [
                'attr'       => ['class' => 'form-control fs-5'],
                'label_attr' => ['class' => 'fs-5'],
            ])

            ->add('telephone', null, [
                'attr'       => ['class' => 'form-control fs-5'],
                'label_attr' => ['class' => 'fs-5'],
            ])

            ->add('adresse', null, [
                'attr'       => ['class' => 'form-control fs-5'],
                'label_attr' => ['class' => 'fs-5'],
            ])

            ->add('code_postal', null, [
                'attr'       => ['class' => 'form-control fs-5'],
                'label_attr' => ['class' => 'fs-5'],
            ])

            ->add('ville', null, [
                'attr'       => ['class' => 'form-control fs-5'],
                'label_attr' => ['class' => 'fs-5'],
            ])

            // -----------------------------
            // HIDDEN FIELD
            // -----------------------------

            ->add('offre', HiddenType::class)

            // -----------------------------
            // CHECKBOX MAINTENANCE
            // -----------------------------

            ->add('contrat_maintenance', CheckboxType::class, [
                'required'   => false,
                'label'      => 'Contrat de maintenance annuelle (360 € HT / an)',
                'attr'       => ['class' => 'form-check-input fs-5'],
                'label_attr' => ['class' => 'fs-5 ms-2'],
            ])

            // -----------------------------
            // CHECKBOX RGPD (PAS DE LABEL)
            // -----------------------------

            ->add('rgpd', CheckboxType::class, [
                'required'   => true,
                'label'      => false,
                'attr'       => ['class' => 'form-check-input fs-5'],
                'label_attr' => ['class' => 'fs-5'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
