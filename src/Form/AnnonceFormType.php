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
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5],
            ])
            ->add('type', EnumType::class, [
                'class' => AnnonceType::class,
                'label' => 'Type',
                'choice_label' => static fn (AnnonceType $type): string => $type->label(),
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
            ])
            ->add('unitePrix', TextType::class, [
                'label' => 'Unite du prix',
            ])
            ->add('categorie', TextType::class, [
                'label' => 'Categorie',
            ])
            ->add('imageUrl', UrlType::class, [
                'label' => 'URL image',
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Localisation',
            ])
            ->add('proprietaireId', IntegerType::class, [
                'label' => 'ID proprietaire',
            ])
            ->add('quantiteDisponible', IntegerType::class, [
                'label' => 'Quantite disponible',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
