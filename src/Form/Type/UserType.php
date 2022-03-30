<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class,[
                'label'=> "username"
            ])
            ->add("nom", TextType::class, [
                'label' => "nom"
            ])
            ->add("postnom", TextType::class, [
                "label" => "postnom"
            ])
            ->add("prenom", TextType::class, [
                "label" => "prenom"
            ])
            ->add("password", PasswordType::class, [
                "label" => "password"
            ])
            ->add("roles", ChoiceType::class, [
                'choices' => [
                    "Accès Restraint" => 'ROLE_USER',
                    "Gestion Membre" => "ROLE_MEMBRE",
                    "Gestion Cotisation" => "ROLE_COTISATION",
                    "Gestion Difusion" => "ROLE_DIFFUSION",
                    "Administrateur" => "ROLE_ADMIN"
            ],
                'multiple' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter un utilisateur']);
        ;

    }
}