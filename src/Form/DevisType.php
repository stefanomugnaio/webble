<?php

namespace App\Form;

use App\Entity\Devis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
            ])

            ->add('prenom', null, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
            ])

            ->add('email', null, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    'class' => 'form-control',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
            ])

            ->add('telephone', null, [
                'label' => 'Téléphone',
                'attr' => [
                    'class' => 'form-control',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
            ])

            ->add('adresse', null, [
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'form-control',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
            ])

            ->add('code_postal', null, [
                'label' => 'Code postal',
                'attr' => [
                    'class' => 'form-control',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
            ])

            ->add('ville', null, [
                'label' => 'Ville',
                'attr' => [
                    'class' => 'form-control',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
            ])

            // OFFRE (hidden, gérée côté controller/service)
            ->add('offre', HiddenType::class)

            // MAINTENANCE
            ->add('contrat_maintenance', CheckboxType::class, [
                'label' => 'Contrat de maintenance annuelle (360 € HT / an)',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'row_attr' => [
                    'class' => 'form-check',
                ],
            ])

            // RGPD
            ->add('rgpd', CheckboxType::class, [
                'label' => false, // label géré dans le Twig
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'row_attr' => [
                    'class' => 'form-check mt-2',
                ],
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter la politique de confidentialité.'
                    ),
                ],
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
