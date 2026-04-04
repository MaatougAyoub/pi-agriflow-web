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
        $builder
            ->add('annonce', EntityType::class, [
                'class' => Annonce::class,
                'choice_label' => 'titre',
                'label' => 'Annonce',
                // nwarriw titre l annonce khir men ID bech l user yefhem
            ])
            ->add('clientId', IntegerType::class, [
                'label' => 'Reference client',
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date debut',
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date fin',
                'widget' => 'single_text',
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantite',
            ])
            ->add('statut', EnumType::class, [
                'class' => ReservationStatut::class,
                'label' => 'Statut',
                'choice_label' => static fn (ReservationStatut $statut): string => $statut->label(),
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => false,
                'attr' => ['rows' => 4],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
