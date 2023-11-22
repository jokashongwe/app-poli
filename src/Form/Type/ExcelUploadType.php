<?php

namespace App\Form\Type;

use App\Entity\Federation;
use App\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class ExcelUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $organisation = $_SERVER['organisation_x'];
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
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'label' => 'Groupe',
                'query_builder' => function (EntityRepository $er) use ($organisation): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->where("t.organisation = :organisation")
                        ->setParameter('organisation', $organisation);
                },
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Importer']);
    }
}