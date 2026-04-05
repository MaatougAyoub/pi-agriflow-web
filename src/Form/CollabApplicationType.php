<?php

<<<<<<< HEAD
declare(strict_types=1);

=======
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
namespace App\Form;

use App\Entity\CollabApplication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
<<<<<<< HEAD
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
=======
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollabApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
<<<<<<< HEAD
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
=======
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
                    'placeholder' => 'Ex: 20 305 177 ou +21620305177',
                    'class' => 'form-control',
                ],
                'help' => 'Les espaces et le préfixe + sont acceptés ; seuls les chiffres sont conservés.',
            ])
            ->add('yearsOfExperience', IntegerType::class, [
                'label' => 'Années d\'expérience',
                'required' => false,
                'empty_data' => 0,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 50,
                ],
            ])
            ->add('expectedSalary', NumberType::class, [
                'label' => 'Salaire attendu par jour (DT)',
                'required' => false,
                'empty_data' => '0',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 50.00',
                    'min' => 0,
                    'step' => '0.01',
                ],
                'invalid_message' => 'Veuillez entrer un montant numérique valide.',
            ])
            ->add('motivation', TextareaType::class, [
                'label' => 'Lettre de motivation',
                'attr' => [
                    'placeholder' => 'Décrivez vos motivations, votre expérience pertinente...',
                    'class' => 'form-control',
                    'rows' => 6,
                ],
            ])
        ;

        $builder->get('phone')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            if (\is_string($data)) {
                $event->setData(preg_replace('/\D+/', '', $data) ?? '');
            }
        });
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollabApplication::class,
        ]);
    }
}
