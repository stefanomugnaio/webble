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
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('telephone')
            ->add('adresse')
            ->add('code_postal')
            ->add('ville')
            // champ interne pour savoir quelle offre a été choisie
            ->add('offre', HiddenType::class, [
                'required' => false,
            ])
            ->add('contrat_maintenance', CheckboxType::class, [
                'required' => false,
                'label' => 'Contrat de maintenance annuelle (360 € HT / an)',
                'help' => 'Maintenance, petites évolutions, mises à jour de sécurité. Engagement 12 mois.',
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
