<?php

namespace App\Form;

use App\Entity\Jeu;
use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   

        $builder
            ->add('nom')
            ->add('description')
            ->add('nbPlace')
            ->add('dateTime')
            ->add('jeu', EntityType::class , array(
                'class' => Jeu::class,
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'submit'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
            'required' => false,
        ]);
    }
}