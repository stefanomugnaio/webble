<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre nom.'),
                ],
            ])

            ->add('prenom', null, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre prénom.'),
                ],
            ])

            ->add('email', null, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre adresse e-mail.'),
                    new Email(message: 'Veuillez saisir une adresse e-mail valide.'),
                ],
            ])

            ->add('sujet', ChoiceType::class, [
                'label' => 'Sujet',
                'choices' => [
                    'Demande d\'informations' => 'informations',
                    'Devis pour un site web : Formule Pro' => 'devis',
                    'Problème technique' => 'technique',
                    'Développement spécifique' => 'specifique',
                    'Support / Assistance' => 'support',
                    'J\'ai perdu mon mot de passe (Espace client)' => 'password',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Choisissez un sujet...',
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner un sujet.'),
                ],
            ])

            // ✅ Nouveau champ numéro de contrat
            ->add('num_contrat', null, [
                'label' => 'Numéro de contrat',
                'required' => false, // on gère le "obligatoire" à la main selon le sujet
                'constraints' => [
                    new Length(
                        max: 50,
                        maxMessage: 'Le numéro de contrat ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('description', null, [
                'label' => 'Votre message',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre message.'),
                    new Length(
                        min: 10,
                        max: 2000,
                        minMessage: 'Votre message doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Votre message ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('recaptchaToken', HiddenType::class, [
                'mapped' => false,
            ])

            //DATE DE CRÉATION EN HIDDEN
            ->add('date_creation', HiddenType::class)
        ;

        // ✅ TRANSFORMATION STRING ⇄ DATETIME
        $builder->get('date_creation')->addModelTransformer(
            new CallbackTransformer(
                // DateTime -> string (affichage)
                function ($dateTime) {
                    return $dateTime instanceof \DateTimeInterface
                        ? $dateTime->format('Y-m-d H:i:s')
                        : '';
                },
                // string -> DateTime (soumission)
                function ($dateString) {
                    return $dateString
                        ? new \DateTime($dateString)
                        : new \DateTime();
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
