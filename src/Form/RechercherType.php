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
                'label'=>'Centre de formation : ',
                'placeholder'=>'Tous',
//                'preferred_choices'=>$options['site'], // TODO: comment récupérer le centre de l'utilisateur?
                'class'=> Site::class,
            ])
            ->add('champ_recherche', SearchType::class, [
                'empty_data'=>'',
                'trim'=>true,
                'label'=>'mot clé : '
            ])
            ->add('date_debut', DateType::class, [
                'label'=>'Entre ',
                'widget'=>'single_text',
                'html5'=>'true',
//                'format'=>'d M y',
//                'placeholder'=>['day'=>'jour', 'month'=>'mois', 'year'=>'année'],
            ])
            ->add('date_fin', DateType::class, [
                'label'=>' Et ',
                'widget'=>'single_text',
                'html5'=>'true',
//                'format'=>'d M y',
//                'placeholder'=> ['day'=>'jour', 'month'=>'mois', 'year'=>'année'],
            ])
            ->add('organisees', CheckboxType::class, [
                'label'=>'Sorties dont je suis l\'organisateur / trice',
            ])
            ->add('inscrit', CheckboxType::class, [
                'label'=>'Sorties auxquelles je suis inscrit/e',
            ])
            ->add('non_inscrit', CheckboxType::class, [
                'label'=>'Sorties auxquelles je ne suis pas inscrit/e',
            ])
            ->add('passees', CheckboxType::class, [
                'label'=>'Sorties passées',
            ])
        // essai non concluant avec un ChoiceType
//            ->add('radio', ChoiceType::class, [
//                'label'=>'Filtres : ',
//                'multiple'=>'false',
//                'expanded'=>'true',
//                'choices'=> [
//                    'Sorties dont je suis l\'organisateur / trice'=> 'organisees',
//                    'Sorties auxquelles je suis inscrit/e'=>'inscrit',
//                    'Sorties auxquelles je ne suis pas inscrit/e'=>'non_inscrit',
//                    'Sorties passées'=>'passees'
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
            ],
        ]);
    }
}
