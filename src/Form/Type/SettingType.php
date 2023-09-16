<?php

namespace App\Form\Type;

use App\Entity\Federation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("president", TextType::class)
            ->add('photoPresident', FileType::class, [
                'label' => 'Photo du Président',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Sélectionner une bonne image SVP',
                    ])
                ],
            ])
            ->add("nomparti", TextType::class)
            ->add("messageEnregistrement", TextType::class)
            ->add("facebookURL", UrlType::class)
            ->add("twitterURL", UrlType::class)
            ->add('logo', FileType::class, [
                'label' => 'Logo',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Sélectionner une bonne image SVP',
                    ])
                ],
            ])
            ->add("slogan", TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Enregistrement']);
        ;
    }
}