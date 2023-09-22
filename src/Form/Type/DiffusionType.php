<?php

namespace App\Form\Type;

use App\Entity\Federation;
use App\Entity\Province;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DiffusionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("content", TextareaType::class)
            ->add("federations", EntityType::class, [
                'class' => Federation::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer une diffusion']);
        ;
    }
}