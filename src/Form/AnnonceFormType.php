<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Annonce;
use App\Enum\AnnonceStatut;
use App\Enum\AnnonceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // validation: houni n7ot controles saisie bech data mta3 annonce tod5ol nadhiya
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'empty_data' => '',
                'attr' => [
                    'data-ai-assistant-target' => 'titreField',
                    'minlength' => 3,
                    'maxlength' => 150,
                    'placeholder' => 'Ex: Tracteur New Holland T5',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'empty_data' => '',
                'help' => 'Minimum 20 caracteres pour decrire l offre clairement.',
                'attr' => [
                    'data-ai-assistant-target' => 'descriptionField',
                    'rows' => 6,
                    'minlength' => 20,
                    'maxlength' => 2000,
                    'placeholder' => 'Decrivez clairement l offre, l etat du produit et les conditions.',
                ],
            ])
            ->add('type', EnumType::class, [
                'class' => AnnonceType::class,
                'label' => 'Type',
                'choice_label' => static fn (AnnonceType $type): string => $type->label(),
                'attr' => [
                    'data-ai-assistant-target' => 'typeField',
                ],
            ])
            ->add('statut', EnumType::class, [
                'class' => AnnonceStatut::class,
                'label' => 'Statut',
                'choice_label' => static fn (AnnonceStatut $statut): string => $statut->label(),
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'TND',
                'divisor' => 1,
                'empty_data' => '0',
                'help' => 'Saisissez un montant en dinars tunisiens.',
                'attr' => [
                    'min' => 0.01,
                    'step' => '0.01',
                ],
            ])
            ->add('unitePrix', TextType::class, [
                'label' => 'Unite du prix',
                'empty_data' => '',
                'help' => 'Exemples: jour, piece, semaine.',
                'attr' => [
                    'data-ai-assistant-target' => 'unitePrixField',
                    'maxlength' => 20,
                    'minlength' => 2,
                    'placeholder' => 'Ex: jour, piece, semaine',
                ],
            ])
            ->add('categorie', TextType::class, [
                'label' => 'Categorie',
                'empty_data' => '',
                'attr' => [
                    'data-ai-assistant-target' => 'categorieField',
                    'minlength' => 2,
                    'maxlength' => 120,
                    'placeholder' => 'Materiel, Fruits, Irrigation...',
                ],
            ])
            ->add('imageUrl', UrlType::class, [
                'label' => 'URL image',
                'empty_data' => '',
                'help' => 'Ajoutez un lien complet du style https://...',
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => 'https://...',
                ],
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Localisation',
                'empty_data' => '',
                'attr' => [
                    'data-ai-assistant-target' => 'localisationField',
                    'minlength' => 2,
                    'maxlength' => 120,
                    'placeholder' => 'Nabeul, Sousse, Ariana...',
                ],
            ])
            ->add('quantiteDisponible', IntegerType::class, [
                'label' => 'Quantite disponible',
                'empty_data' => '0',
                'attr' => [
                    'min' => 1,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
