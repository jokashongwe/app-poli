<?php

namespace App\Form\Type;

use App\Entity\Circonscription;
use App\Entity\Federation;
use App\Entity\Province;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BureauVoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("code", TextType::class)
            ->add("address", TextType::class)
            ->add("commune", TextType::class)
            ->add("territoire", TextType::class)
            ->add("codeCentre", TextType::class)
            ->add("cironscription", EntityType::class, [
                'class' => Circonscription::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'required' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer une diffusion']);
        ;
    }
}