<?php

namespace App\Form;

use App\Entity\DevisFormation;
use App\Entity\Formation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminDevisFormationType extends AbstractType
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
            ->add('code_postal', TextType::class, $this->field('Code postal'))
            ->add('ville', TextType::class, $this->field('Ville'))
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'libelle',
                'label' => 'Formation',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('dateSouhaitee', DateType::class, [
                'label' => 'Journée choisie',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('date_demande', DateType::class, [
                'label' => 'Date de la demande',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('rgpd', CheckboxType::class, [
                'label' => 'Consentement RGPD accepté',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
                'row_attr' => ['class' => 'form-check mb-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => DevisFormation::class]);
    }

    private function field(string $label): array
    {
        return [
            'label' => $label,
            'attr' => ['class' => 'form-control'],
            'constraints' => [new NotBlank()],
        ];
    }
}
