<?php

namespace App\Form;

use App\Entity\CollabApplication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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
                'attr' => [
                    'placeholder' => 'Votre nom et prénom',
                    'class' => 'form-control',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'votre.email@example.com',
                    'class' => 'form-control',
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Ex: 20305177',
                    'class' => 'form-control',
                ],
            ])
            ->add('yearsOfExperience', IntegerType::class, [
                'label' => 'Années d\'expérience',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                ],
            ])
            ->add('expectedSalary', NumberType::class, [
                'label' => 'Salaire attendu par jour (DT)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 50.00',
                    'min' => 0,
                    'step' => '0.01',
                ],
            ])
            ->add('motivation', TextareaType::class, [
                'label' => 'Motivation',
                'attr' => [
                    'placeholder' => 'Décrivez vos motivations...',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollabApplication::class,
        ]);
    }
}
