<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminClientType extends AbstractType
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
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('pays', TextType::class, $this->field('Pays'))
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => ['Client' => 'ROLE_USER', 'Administrateur' => 'ROLE_ADMIN'],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('notification_document', CheckboxType::class, [
                'label' => 'Notifier lors de l’ajout d’un document',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
                'row_attr' => ['class' => 'form-check mb-3'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $options['password_required'] ? 'Mot de passe' : 'Nouveau mot de passe',
                'mapped' => false,
                'required' => $options['password_required'],
                'help' => $options['password_required'] ? null : 'Laissez vide pour conserver le mot de passe actuel.',
                'attr' => ['class' => 'form-control', 'autocomplete' => 'new-password'],
                'constraints' => $options['password_required']
                    ? [new NotBlank(), new Length(min: 8, max: 4096)]
                    : [new Length(min: 8, max: 4096)],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'password_required' => false,
        ]);
        $resolver->setAllowedTypes('password_required', 'bool');
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
