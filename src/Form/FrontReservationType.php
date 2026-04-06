<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrontReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $today = (new \DateTimeImmutable('today'))->format('Y-m-d');
        $isLocation = (bool) $options['is_location'];

        // validation: form front reservation mriguel bech agriculteur ma yod5olch dates wala quantite ghalta
        if ($isLocation) {
            $builder
                ->add('dateDebut', DateType::class, [
                    'label' => 'Date debut',
                    'widget' => 'single_text',
                    'help' => 'Choisissez une date de debut a partir d aujourd hui.',
                    'attr' => [
                        'min' => $today,
                    ],
                ])
                ->add('dateFin', DateType::class, [
                    'label' => 'Date fin',
                    'widget' => 'single_text',
                    'help' => 'La date de fin doit etre posterieure ou egale a la date de debut.',
                    'attr' => [
                        'min' => $today,
                    ],
                ]);
        }

        $builder
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantite',
                'help' => 'Saisissez une quantite valide, sans depasser le stock disponible.',
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => false,
                'help' => 'Vous pouvez ajouter une note courte pour preciser votre demande.',
                'attr' => [
                    'rows' => 3,
                    'maxlength' => 1000,
                    'placeholder' => 'Precisez vos besoins ou les details de livraison si necessaire.',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'is_location' => true,
        ]);

        $resolver->setAllowedTypes('is_location', 'bool');
    }
}
