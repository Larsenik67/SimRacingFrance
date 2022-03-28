<?php

namespace App\Controller\Admin;

use App\Entity\Sujet;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SujetCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sujet::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            
            AssociationField::new('user', 'Auteur'),
            TextField::new('titre', 'Titre'),
            TextEditorField::new('description', 'Description'),
            TextEditorField::new('contenu', 'Contenu du sujet'),
            DateTimeField::new('dateTime', 'Date et heure'),
            BooleanField::new('statut', 'Désactiver le sujet'),
            BooleanField::new('closed', 'Fermer le sujet'),
            AssociationField::new('team', 'Team'),
            ArrayField::new('messages', 'Réponse(s)')
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
    return $actions

        ->disable(Action::NEW);
    }
    
}
