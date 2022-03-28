<?php

namespace App\Controller\Admin;

use App\Entity\Jeu;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class JeuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Jeu::class;
    }

    public function configureFields(string $pageName): iterable
    {

        $fields = [
            TextField::new('nom', 'Nom'),
            ImageField::new('icone', 'Icone')->onlyOnIndex()->setBasePath('public\img\jeux'),
            ImageField::new('icone', 'Icone')->onlyOnForms()->setUploadDir('public\img\jeux')->setFormTypeOption('allow_delete', false),
        ];

        return $fields;
    }
    
}
