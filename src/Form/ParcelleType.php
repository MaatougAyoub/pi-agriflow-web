<?php

namespace App\Form;

use App\Entity\Parcelle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ParcelleType extends AbstractType
{
    private const TYPE_TERRE_CHOICES = [
        'ARGILEUSE' => 'ARGILEUSE',
        'SABLEUSE' => 'SABLEUSE',
        'LIMONEUSE' => 'LIMONEUSE',
        'CALCAIRE' => 'CALCAIRE',
        'HUMIFERE' => 'HUMIFERE',
        'SALINE' => 'SALINE',
        'MIXTE' => 'MIXTE',
        'AUTRE' => 'AUTRE',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la parcelle',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un nom de parcelle.']),
                ],
            ])
            ->add('superficie', NumberType::class, [
                'label' => 'Superficie (m²)',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une superficie.']),
                    new Positive(['message' => 'La superficie doit etre superieure a 0.']),
                ],
            ])
            ->add('typeTerre', ChoiceType::class, [
                'label' => 'Type de terre',
                'choices' => self::TYPE_TERRE_CHOICES,
                'placeholder' => 'Choisir un type de terre',
                'required' => false,
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Localisation',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une localisation.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parcelle::class,
        ]);
    }
}
