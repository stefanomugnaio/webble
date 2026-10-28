<?php

namespace App\Form;

use App\Entity\ParametreOffre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminParametreOffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('papierEnteteUrl', UrlType::class, [
            'label' => 'Lien du papier à en-tête',
            'required' => false,
            'help' => 'Utilisez une URL publique HTTPS vers une image PNG ou JPG au format A4. Elle sera placée en fond de chaque page du PDF.',
            'attr' => ['placeholder' => 'https://webble.fr/images/papier-entete.png'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => ParametreOffre::class]); }
}
