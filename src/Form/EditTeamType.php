<?php

namespace App\Form;

use App\Entity\Jeu;
use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('jeu', EntityType::class , array(
                'class' => Jeu::class,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'submit'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
            'required' => false,
        ]);
    }
}
