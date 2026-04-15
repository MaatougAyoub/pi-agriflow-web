<?php

namespace App\Form;

use App\Entity\CollabRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollabRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la demande',
                'attr' => [
                    'placeholder' => 'Ex: Récolte des tomates',
                    'class' => 'form-control',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez le travail recherché...',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'attr' => [
                    'placeholder' => 'Ex: Tunis, Sousse, Sfax...',
                    'class' => 'form-control',
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('neededPeople', IntegerType::class, [
                'label' => 'Nombre de personnes recherchées',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                ],
            ])
            ->add('salary', NumberType::class, [
                'label' => 'Salaire par jour (DT)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 50.00',
                    'min' => 0,
                    'step' => '0.01',
                ],
            ])
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollabRequest::class,
        ]);
    }
}
