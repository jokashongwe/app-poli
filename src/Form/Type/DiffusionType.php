<?php

namespace App\Form\Type;

use App\Entity\Federation;
use App\Entity\Province;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DiffusionType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("titre", TextType::class, [
                'mapped' => false,
            ])
            ->add("contenu", TextareaType::class, [
                'mapped' => false,
            ])
            ->add("province", EntityType::class, [
                'class' => Province::class,
                'choice_label' => 'nom',
                'mapped' => false,
                'multiple' => true
            ])
            ->add("federation", EntityType::class, [
                'class' => Federation::class,
                'mapped' => false,
                'choice_label' => 'nom',
                'multiple' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer une diffusion']);
        ;
    }
}