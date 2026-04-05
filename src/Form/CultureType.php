<?php

namespace App\Form;

use App\Entity\Culture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class CultureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedParcelleId = $options['selected_parcelle_id'];
        $selectedParcelleAvailableSurface = $selectedParcelleId ? ($options['surface_by_parcelle'][$selectedParcelleId] ?? null) : null;
        $superficieHelp = null !== $selectedParcelleAvailableSurface
            ? sprintf('Surface maximale disponible pour la parcelle selectionnee: %.0f m².', $selectedParcelleAvailableSurface)
            : 'Choisissez une parcelle pour verifier la surface disponible.';

        $builder
            ->add('parcelleId', ChoiceType::class, [
                'label' => 'Parcelle',
                'choices' => $options['parcelle_choices'],
                'placeholder' => 'Choisir une parcelle',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir une parcelle.']),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de la culture',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un nom de culture.']),
                ],
            ])
            ->add('typeCulture', TextType::class, [
                'label' => 'Type de culture',
                'required' => false,
            ])
            ->add('superficie', NumberType::class, [
                'label' => 'Superficie (m²)',
                'scale' => 2,
                'help' => $superficieHelp,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une superficie.']),
                    new Positive(['message' => 'La superficie doit etre superieure a 0.']),
                ],
            ])
            ->add('etat', TextType::class, [
                'label' => 'Etat',
                'required' => false,
            ])
            ->add('dateRecolte', DateType::class, [
                'label' => 'Date de recolte',
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de recolte doit etre superieure ou egale a aujourd hui.',
                    ]),
                ],
            ])
            ->add('recolteEstime', NumberType::class, [
                'label' => 'Recolte estimee',
                'scale' => 2,
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'La recolte estimee doit etre superieure ou egale a 0.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Culture::class,
            'parcelle_choices' => [],
            'surface_by_parcelle' => [],
            'selected_parcelle_id' => null,
        ]);

        $resolver->setAllowedTypes('parcelle_choices', 'array');
        $resolver->setAllowedTypes('surface_by_parcelle', 'array');
        $resolver->setAllowedTypes('selected_parcelle_id', ['null', 'int']);
    }
}
