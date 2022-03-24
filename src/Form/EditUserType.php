<?php

namespace App\Form;

use App\Entity\Jeu;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   

        $builder
            ->add('nom')
            ->add('email')
            ->add('jeu', EntityType::class , array(
                'class' => Jeu::class,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'submit'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'required' => false,
        ]);
    }
}
