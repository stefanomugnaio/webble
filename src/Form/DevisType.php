<?php

namespace App\Form;

use App\Entity\Devis;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-1'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre nom.'),
                    new Length(
                        min: 2,
                        max: 100,
                        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('prenom', null, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-1'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre prénom.'),
                    new Length(
                        min: 2,
                        max: 100,
                        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('email', null, [
                'label' => 'Adresse e-mail',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-1'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre adresse e-mail.'),
                    new Email(message: 'Veuillez saisir une adresse e-mail valide.'),
                ],
            ])

            ->add('telephone', null, [
                'label' => 'Téléphone',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-1'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre numéro de téléphone.'),
                    new Regex(
                        pattern: '/^[0-9+\s().-]{8,20}$/',
                        message: 'Veuillez saisir un numéro de téléphone valide.'
                    ),
                ],
            ])

            ->add('adresse', null, [
                'label' => 'Adresse',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-1'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre adresse.'),
                    new Length(
                        min: 5,
                        max: 255,
                        minMessage: 'L’adresse doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'L’adresse ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('code_postal', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-1'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner le code postal.'),
                    new Regex(
                        pattern: '/^\d{4,5}$/',
                        message: 'Veuillez saisir un code postal valide.'
                    ),
                ],
            ])

            ->add('ville', null, [
                'label' => 'Ville',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-1'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner la ville.'),
                    new Length(
                        min: 2,
                        max: 100,
                        minMessage: 'La ville doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'La ville ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            // OFFRE (hidden)
            ->add('offre', HiddenType::class)

            // MAINTENANCE
            ->add('contrat_maintenance', CheckboxType::class, [
                'label' => 'Contrat de maintenance annuelle (360 € HT / an)',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
                'row_attr' => ['class' => 'form-check'],
            ])

            // RGPD
            ->add('rgpd', CheckboxType::class, [
                'label' => false,
                'attr' => ['class' => 'form-check-input'],
                'row_attr' => ['class' => 'form-check mt-2'],
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
            'data_class' => Devis::class,
        ]);
    }
}
