<?php

namespace App\Form\Type;

use App\Entity\Federation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Province;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $organisation = $_SERVER['organisation_x'];
        $builder
            ->add('code', TextType::class, [
                'label' => "Code",
            ])
            ->add('name',  TextType::class, [
                'label' => "Nom",
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer un groupe']);
    }
}