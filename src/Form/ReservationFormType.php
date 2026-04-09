<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Enum\ReservationStatut;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // validation: hedha form admin, ywalli flexible ama mazal y7ot bornes sghar 3al champs
        $builder
            ->add('annonce', EntityType::class, [
                'class' => Annonce::class,
                'choice_label' => 'titre',
                'label' => 'Annonce',
                // nwarriw titre l annonce khir men ID bech l user yefhem
            ])
            ->add('clientId', IntegerType::class, [
                'label' => 'Reference client',
                'empty_data' => '0',
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date debut',
                'widget' => 'single_text',
                'help' => 'L administrateur peut ajuster la date, mais l ordre des dates reste obligatoire.',
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date fin',
                'widget' => 'single_text',
                'help' => 'La date de fin doit rester posterieure a la date de debut.',
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantite',
                'empty_data' => '0',
                'help' => 'La quantite doit rester strictement positive.',
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('statut', EnumType::class, [
                'class' => ReservationStatut::class,
                'label' => 'Statut',
                'choice_label' => static fn (ReservationStatut $statut): string => $statut->label(),
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => false,
                'help' => 'Zone optionnelle pour consulter ou ajuster la note associee a la reservation.',
                'attr' => [
                    'rows' => 4,
                    'maxlength' => 1000,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
