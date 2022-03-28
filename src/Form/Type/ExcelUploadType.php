<?php

namespace App\Form\Type;

use App\Entity\Federation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class ExcelUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attachement', FileType::class, [
                'label' => 'Fichier Excel',
                'mapped' => false,
                'required' => true,

                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ],
                        'mimeTypesMessage' => 'Sélectionner un fichier Excel valide SVP',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Importer']);
    }
}