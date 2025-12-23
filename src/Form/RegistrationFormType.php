<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // -------------------------------
            // Identité
            // -------------------------------
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom'
                ],
            ])

            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre prénom'
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'email@exemple.com'
                ],
            ])

            // -------------------------------
            // Téléphone
            // -------------------------------
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Optionnel'
                ],
            ])

            // -------------------------------
            // Pays
            // -------------------------------
            ->add('pays', TextType::class, [
                'label' => 'Pays',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'France'
                ],
            ])

            // -------------------------------
            // Notifications documents (CHAMP MANQUANT)
            // -------------------------------
            ->add('notification_document', CheckboxType::class, [
                'label' => 'Recevoir une notification lors de l’ajout d’un document',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
            ])

            // -------------------------------
            // Conditions générales
            // -------------------------------
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "J'accepte les conditions générales",
                'mapped' => false,
                'constraints' => [
                    new IsTrue(message: 'Vous devez accepter les conditions.'),
                ],
                'attr' => [
                    'class' => 'form-check-input'
                ],
            ])

            // -------------------------------
            // Mot de passe
            // -------------------------------
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                    'placeholder' => '********'
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un mot de passe.'),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        max: 4096
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
