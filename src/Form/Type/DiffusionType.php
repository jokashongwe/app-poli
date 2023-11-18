<?php

namespace App\Form\Type;

use App\Entity\Federation;
use App\Entity\Province;
use App\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DiffusionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $organisation = $_SERVER['organisation_x'];
        $builder
            ->add("content", TextareaType::class, ['required' => false])
            ->add("tags", EntityType::class, [
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) use ($organisation): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->where("t.organisation = :organisation")
                        ->setParameter('organisation', $organisation);
                },
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer une diffusion']);
        ;
    }
}