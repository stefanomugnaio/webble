<?php

namespace App\Form;

use App\Entity\DevisFormation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class DevisFormationType extends AbstractType
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

            ->add('sessionFormation', SessionFormationType::class)

            ->add('rgpd', CheckboxType::class, [
                'label' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter la politique de confidentialité.'
                    ),
                ],
            ])

            ->add('recaptchaToken', HiddenType::class, [
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisFormation::class,
        ]);
    }
}
