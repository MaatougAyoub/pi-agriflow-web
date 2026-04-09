<?php

namespace App\Form;

use App\Entity\CollabRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollabRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            if (!\is_array($data)) {
                return;
            }
            foreach (['title', 'description', 'location'] as $field) {
                if (isset($data[$field]) && \is_string($data[$field])) {
                    $data[$field] = trim($data[$field]);
                }
            }
            $event->setData($data);
        });

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la demande',
                'attr' => [
                    'placeholder' => 'Ex: Récolte des tomates',
                    'class' => 'form-control',
                    'minlength' => 3,
                    'maxlength' => 255,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez le travail recherché, les compétences souhaitées...',
                    'class' => 'form-control',
                    'rows' => 5,
                    'minlength' => 20,
                    'maxlength' => 10000,
                ],
                'help' => 'Minimum 20 caractères, maximum 10 000.',
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'attr' => [
                    'placeholder' => 'Ex: Tunis, Sousse, Sfax...',
                    'class' => 'form-control',
                    'minlength' => 2,
                    'maxlength' => 100,
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'help' => 'Lors d’une nouvelle demande, la date ne peut pas être dans le passé.',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'help' => 'Doit être strictement après la date de début.',
            ])
            ->add('neededPeople', IntegerType::class, [
                'label' => 'Nombre de personnes recherchées',
                'empty_data' => 1,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 50,
                ],
            ])
            ->add('salary', NumberType::class, [
                'label' => 'Salaire par jour (DT)',
                'required' => true,
                'empty_data' => '0',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 50.00',
                    'min' => 0,
                    'max' => 99999.99,
                    'step' => '0.01',
                ],
                'help' => 'Entre 0 et 99 999,99 DT (0 = bénévolat ou à préciser avec l’employeur).',
                'invalid_message' => 'Veuillez entrer un montant numérique valide.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollabRequest::class,
            'validation_groups' => static function (FormInterface $form): array {
                $data = $form->getData();
                if ($data instanceof CollabRequest && null !== $data->getId()) {
                    return ['Default', 'collab_edit'];
                }

                return ['Default', 'collab_create'];
            },
        ]);
    }
}
