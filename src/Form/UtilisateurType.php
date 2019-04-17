<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('pseudo')
            ->add('telephone')
            ->add('ville')
            ->add('cp')
            ->add('centreFormation', EntityType::class, [
                'choice_label'=>'nom',
                'label' =>'Centre de formation : ',
                'class' =>Site::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'attr' => [
                'novalidate' => 'novalidate'
                ]
        ]);
    }
}
