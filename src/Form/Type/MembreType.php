<?php

namespace App\Form\Type;

use App\Entity\Federation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Province;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom",
            ])
            ->add('prenom', TextType::class, [
                'label' => "Prénom",
            ])
            ->add('postnom', TextType::class, [
                'label' => "Postnom",
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'required' => false
            ])
            ->add('datenaissance', DateType::class, [
                'label' => "Date de naissance (Optionel)",
                'widget' => 'single_text',
            ])
            ->add('adresse', TextType::class, [
                'label' => "Adresse de résidence",
            ])
            ->add('genre', ChoiceType::class, [
                'label' => "Sexe",
                'choices' => ["Homme" => "Homme", "Femme" => "Femme"],
            ])
            ->add('federation', EntityType::class, [
                'class' => Federation::class,
                'choice_label' => 'nom'
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('numerocarte', TextType::class, [
                'label' => 'Numéro de Carte d\'identité (Optionnel)',
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter un membre']);
    }
}