<?php

namespace App\Form\Type;

use App\Entity\BureauVote;
use App\Entity\Candidat;
use App\Entity\Circonscription;
use App\Entity\Federation;
use App\Entity\Membre;
use App\Entity\Province;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("membre", EntityType::class, [
                'class' => Membre::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('m')
                        ->where('m.visible is null');
                },
                'choice_label' => 'noidentification',
                'multiple' => false,
                'required' => true
            ])
            ->add("typeElection", ChoiceType::class, [
                'choices' => [
                    'Provincial' => 'PROVINCIAL', 
                    'National' => 'NATIONAL', 
                    'Communal' => 'COMMUNAL', 
                    'Présidentiel' => 'PRESIDENTIEL'
                ]
            ])
            ->add("regroupement", TextType::class, ['required' => false])
            ->add("parti", TextType::class)
            ->add("codeCENI", TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Envoyer une diffusion']);
    }
}
