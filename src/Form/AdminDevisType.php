<?php

namespace App\Form;

use App\Entity\Devis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminDevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, $this->field('Nom'))
            ->add('prenom', TextType::class, $this->field('Prénom'))
            ->add('email', EmailType::class, [
                ...$this->field('Adresse e-mail'),
                'constraints' => [new NotBlank(), new Email()],
            ])
            ->add('telephone', TextType::class, $this->field('Téléphone'))
            ->add('adresse', TextType::class, $this->field('Adresse'))
            ->add('code_postal', IntegerType::class, $this->field('Code postal'))
            ->add('ville', TextType::class, $this->field('Ville'))
            ->add('offre', ChoiceType::class, [
                'label' => 'Offre',
                'choices' => [
                    'Tranquille' => 'Tranquille',
                    'Sérieuse' => 'Sérieuse',
                    'Pro' => 'Pro',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du projet',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 7],
            ])
            ->add('contrat_maintenance', CheckboxType::class, $this->checkbox('Abonnement Webble+'))
            ->add('domaine', CheckboxType::class, $this->checkbox('Assistance nom de domaine'))
            ->add('hebergement', CheckboxType::class, $this->checkbox('Configuration de l’hébergement'))
            ->add('rgpd', CheckboxType::class, $this->checkbox('Consentement RGPD accepté'))
            ->add('cgv', CheckboxType::class, $this->checkbox('Conditions générales acceptées'));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Devis::class]);
    }

    private function field(string $label): array
    {
        return [
            'label' => $label,
            'attr' => ['class' => 'form-control'],
            'constraints' => [new NotBlank()],
        ];
    }

    private function checkbox(string $label): array
    {
        return [
            'label' => $label,
            'required' => false,
            'attr' => ['class' => 'form-check-input'],
            'row_attr' => ['class' => 'form-check mb-3'],
        ];
    }
}
