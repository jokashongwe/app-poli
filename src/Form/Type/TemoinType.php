<?php

namespace App\Form\Type;

use App\Entity\BureauVote;
use App\Entity\Candidat;
use App\Entity\Circonscription;
use App\Entity\Federation;
use App\Entity\Membre;
use App\Entity\Province;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TemoinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("accreditation", TextType::class)
            ->add("membre", EntityType::class, [
                'class' => Membre::class,
                'choice_label' => 'noidentification',
                'multiple' => false,
                'required' => true
            ])
            ->add("bureauVote", EntityType::class, [
                'class' => BureauVote::class,
                'choice_label' => 'code',
                'multiple' => false,
                'required' => true
            ])
            ->add("circonscription", EntityType::class, [
                'class' => Circonscription::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'required' => true
            ])
            ->add("candidat", EntityType::class, [
                'class' => Candidat::class,
                'choice_label' => 'codeCENI',
                'multiple' => false,
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer une diffusion']);
        ;
    }
}