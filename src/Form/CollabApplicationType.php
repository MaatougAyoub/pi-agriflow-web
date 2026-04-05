<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\CollabApplication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollabApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'attr'  => ['placeholder' => 'Votre nom et prénom'],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr'  => ['placeholder' => '+216 XX XXX XXX'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr'  => ['placeholder' => 'votre@email.com'],
            ])
            ->add('yearsOfExperience', null, [
                'label' => "Années d'expérience",
                'attr'  => ['min' => 0, 'max' => 50],
            ])
            ->add('motivation', TextareaType::class, [
                'label' => 'Lettre de motivation',
                'attr'  => ['rows' => 6, 'placeholder' => 'Expliquez pourquoi vous êtes le meilleur candidat...'],
            ])
            ->add('expectedSalary', NumberType::class, [
                'label'    => 'Salaire attendu (DT)',
                'scale'    => 2,
                'attr'     => ['min' => 0, 'step' => '0.01'],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollabApplication::class,
        ]);
    }
}
