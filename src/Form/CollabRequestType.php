<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\CollabRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollabRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'       => 'Titre de la demande',
                'attr'        => ['placeholder' => 'Ex: Besoin d\'aide pour la récolte d\'olives'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'attr'  => ['rows' => 5, 'placeholder' => 'Décrivez la collaboration souhaitée...'],
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'attr'  => ['placeholder' => 'Ex: Sfax, Tunisie'],
            ])
            ->add('startDate', DateType::class, [
                'label'  => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label'  => 'Date de fin',
                'widget' => 'single_text',
            ])
            ->add('neededPeople', null, [
                'label' => 'Nombre de personnes recherchées',
                'attr'  => ['min' => 1, 'max' => 1000],
            ])
            ->add('salary', NumberType::class, [
                'label'  => 'Salaire total (DT)',
                'scale'  => 2,
                'attr'   => ['min' => 0, 'step' => '0.01'],
            ])
            ->add('salaryPerDay', NumberType::class, [
                'label'  => 'Salaire journalier (DT)',
                'scale'  => 2,
                'attr'   => ['min' => 0, 'step' => '0.01'],
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label'   => 'Statut',
                'choices' => CollabRequest::STATUSES,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollabRequest::class,
        ]);
    }
}
