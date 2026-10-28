<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Document;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du document',
                'attr' => ['class' => 'form-control'],
                'constraints' => [new NotBlank()],
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => fn (Client $client) => sprintf(
                    '%s %s (%s)',
                    $client->getPrenom(),
                    $client->getNom(),
                    $client->getEmail()
                ),
                'label' => 'Client',
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Document::class]);
    }
}
