<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateType::class)
            ->add('duree')
            ->add('dateLimiteInscription', DateType::class)
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('centreFormation', EntityType::class, [
                'choice_label'=>'nom',
                'label' =>'Centre de formation : ',
                'class' =>Site::class
            ])
            ->add('lieu', EntityType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
