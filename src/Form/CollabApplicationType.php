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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollabApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            if (!\is_array($data)) {
                return;
            }
            foreach (['fullName', 'email', 'motivation'] as $field) {
                if (isset($data[$field]) && \is_string($data[$field])) {
                    $data[$field] = trim($data[$field]);
                }
            }
            if (isset($data['phone']) && \is_string($data['phone'])) {
                $data['phone'] = preg_replace('/\D+/', '', $data['phone']) ?? '';
            }
            // Évite null / chaîne vide sur IntegerType (TypeError sur le setter)
            if (!isset($data['yearsOfExperience']) || $data['yearsOfExperience'] === '' || $data['yearsOfExperience'] === null) {
                $data['yearsOfExperience'] = 0;
            } elseif (is_numeric($data['yearsOfExperience'])) {
                $data['yearsOfExperience'] = (int) $data['yearsOfExperience'];
            }
            $event->setData($data);
        });

        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'attr' => [
                    'placeholder' => 'Votre nom et prénom',
                    'class' => 'form-control',
                    'minlength' => 3,
                    'maxlength' => 255,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'votre.email@example.com',
                    'class' => 'form-control',
                    'maxlength' => 100,
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Ex: 20 305 177 ou +21620305177',
                    'class' => 'form-control',
                    'inputmode' => 'numeric',
                    'autocomplete' => 'tel',
                ],
                'help' => 'Les espaces et le préfixe + sont acceptés ; seuls les chiffres sont conservés (8 à 20 chiffres).',
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
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 50.00 ou 0',
                    'min' => 0,
                    'max' => 99999.99,
                    'step' => '0.01',
                ],
                'help' => 'Indiquez 0 si vous acceptez le salaire affiché sur l’offre.',
                'invalid_message' => 'Veuillez entrer un montant numérique valide.',
            ])
            ->add('motivation', TextareaType::class, [
                'label' => 'Lettre de motivation',
                'attr' => [
                    'placeholder' => 'Décrivez vos motivations, votre expérience pertinente...',
                    'class' => 'form-control',
                    'rows' => 6,
                    'minlength' => 30,
                    'maxlength' => 8000,
                ],
                'help' => 'Minimum 30 caractères, maximum 8 000.',
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
