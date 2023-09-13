<?php

namespace App\Form\Type;

use App\Entity\Federation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Province;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FederationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom de la Fédération",
            ])
            ->add('federation', EntityType::class, [
                'class' => Federation::class,
                'choice_label' => "nom",
                'placeholder' => 'Choisissez une option',
                'multiple' => false,
                'required' => false,
            ])
            ->add('province', EntityType::class, [
                'class' => Province::class,
                'choice_label' => 'nom',
                'multiple' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer une Fédération']);
    }
}