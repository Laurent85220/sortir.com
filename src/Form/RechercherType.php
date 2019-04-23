<?php

namespace App\Form;

use App\Entity\Site;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class,[
                'choice_label'=>'nom',
                'required'=>'false',
                'label'=>'Centre de formation : ',
                'placeholder'=>'pour filtrer en fonction de son centre de formation',
//                'preferred_choices'=>$centreParDefaut, TODO: comment récupérer le centre de l'utilisateur?
                'class'=> Site::class,
            ])
            ->add('champ_recherche', SearchType::class, [
                'required'=>'false',
                'empty_data'=>'',
                'label'=>'Le nom de la sortie contient : '
            ])
            ->add('date_debut', DateType::class, [
                'label'=>'Entre ',
                'widget'=>'choice',
                'format'=>'d M y',
                'placeholder'=>['day'=>'jour', 'month'=>'mois', 'year'=>'année'],
            ])
            ->add('date_fin', DateType::class, [
                'label'=>' et ',
                'widget'=>'choice',
                'format'=>'d M y',
                'placeholder'=> ['day'=>'jour', 'month'=>'mois', 'year'=>'année'],
            ])
            ->add('sortiesOrganisees', CheckboxType::class, [
                'label'=>'Sorties dont je suis l\'organisateur / trice',
                'value'=>'true',
            ])
            ->add('mesSorties', CheckboxType::class, [
                'label'=>'Sorties auxquelles je suis inscrit/e',
                'value'=>'true',
            ])
            ->add('AutresSortiesEnCours', CheckboxType::class, [
                'label'=>'Sorties auxquelles je ne suis pas inscrit/e',
                'value'=>'true',
            ])
            ->add('SortiesPassees', CheckboxType::class, [
                'label'=>'Sorties passées',
                'value'=>'true',
            ])
//            ->add('filtres_sorties', ChoiceType::class, [
//                'label'=>'Filtres : ',
//                'multiple'=>'true',
//                'expanded'=>'true',
//                'choices'=> [
//                    'Sorties dont je suis l\'organisateur / trice'=> 'sortiesOrganisees',
//                    'Sorties auxquelles je suis inscrit/e'=>'mesSorties',
//                    'Sorties auxquelles je ne suis pas inscrit/e'=>'AutresSortiesEnCours',
//                    'Sorties passées'=>'SortiesPassees'
//                ]
//            ])
            ->add('btn_rechercher', SubmitType::class,[
                'label'=>'Rechercher',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr'=> [
                'novalidate'=>'novalidate',
            ]
        ]);
    }
}
