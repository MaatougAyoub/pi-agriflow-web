<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categorie', ChoiceType::class, [
                'label' => 'Categorie *',
                'choices' => [
                    'Technique' => 'TECHNIQUE',
                    'Access' => 'ACCESS',
                    'Paiement' => 'PAIMENT',
                    'Service' => 'SERVICE',
                    'Livraison' => 'DELIVERY',
                    'Autre' => 'AUTRE',
                ],
                'placeholder' => 'Choisir une categorie',
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez choisir une categorie.'),
                ],
            ])
            ->add('titre', TextType::class, [
                'label' => 'Titre *',
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer un titre.'),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description *',
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez entrer une description.'),
                ],
                'attr' => ['rows' => 5],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
