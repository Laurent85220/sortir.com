<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class InscriptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=>'Email :'
            ])


            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label'=>'Mot de passe :',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label'=>'Nom :'
            ])
            ->add('prenom', TextType::class, [
                'label'=>'Prenom :'
            ])
            ->add('pseudo', TextType::class, [
                'label'=>'Pseudo :'
            ])
            ->add('telephone', TelType::class, [
                'label'=>'Téléphone :'
            ])
            ->add('ville', TextType::class, [
                'label'=>'Ville :'
            ])
            ->add('cp', TelType::class, [
                'label'=>'Code Postal :'
            ])
            ->add('centreFormation', EntityType::class, [
                'choice_label'=>'nom',
                'label' =>'Centre de formation : ',
                'class' =>Site::class
            ])
            ->add('file', FileType::class,)
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
