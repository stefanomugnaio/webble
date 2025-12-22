<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class DocumentUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', ChoiceType::class, [
                'label' => 'Client',
                'choices' => $options['clients'],
                'choice_label' => fn (Client $client) =>
                    $client->getPrenom().' '.$client->getNom().' ('.$client->getEmail().')',
                'choice_value' => 'id',
                'placeholder' => 'Sélectionner un client',
                'constraints' => [
                    new NotBlank(),
                ],
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom du document',
                'constraints' => [
                    new NotBlank(),
                ],
            ])

            ->add('fichier', FileType::class, [
                'label' => 'Fichier PDF',
                'mapped' => false,
                'constraints' => [
                    new File(
                        mimeTypes: ['application/pdf'],
                        mimeTypesMessage: 'Uniquement des fichiers PDF'
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'clients' => [],
        ]);
    }
}
