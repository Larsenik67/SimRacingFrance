<?php

namespace App\Controller\Admin;

use App\Entity\Messages;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MessagesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Messages::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            
            AssociationField::new('user', 'Auteur'),
            AssociationField::new('sujet', 'Sujet'),
            TextEditorField::new('contenu', "Message"),
            DateTimeField::new('dateTime', "Date et heure"),
            BooleanField::new('statut', 'Désactiver le message'),
        ];
    }
    
}
