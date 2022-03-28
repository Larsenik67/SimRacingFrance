<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {


        $fields = [
            TextField::new('nom', 'Nom de pilote'),
            TextField::new('email', 'Email'),
            AssociationField::new('team', 'Team'),
            ArrayField::new('roleTeam', 'Role au sein de la team'),
            DateTimeField::new('ban', "Bannis jusqu'au"),
            ArrayField::new('jeu', 'Jeu(x)'),
        ];

        return $fields;
    }
}
