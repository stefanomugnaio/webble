<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminContactType extends AbstractType
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
            ->add('sujet', TextType::class, $this->field('Sujet'))
            ->add('num_contrat', TextType::class, [
                'label' => 'Numéro de contrat',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['class' => 'form-control', 'rows' => 7],
            ])
            ->add('date_creation', DateType::class, [
                'label' => 'Date de réception',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Contact::class]);
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
