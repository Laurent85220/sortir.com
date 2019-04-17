<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('organisateur', EntityType::class, [
                'choice_label'=>'nom',
                'label' =>'Organisateur: ',
                'class' =>Utilisateur::class
            ])
            ->add('dateHeureDebut', DateTimeType::class)
            ->add('duree')
            ->add('dateLimiteInscription', DateTimeType::class)
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('centreFormation', EntityType::class, [
                'choice_label'=>'nom',
                'label' =>'Ville organisatrice: ',
                'class' =>Site::class
            ])
            ->add('lieu', EntityType::class, [
                'choice_label'=>'nom',
                'label' =>'Lieu: ',
                'class' =>Lieu::class
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
