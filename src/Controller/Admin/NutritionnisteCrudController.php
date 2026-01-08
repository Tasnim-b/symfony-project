<?php

namespace App\Controller\Admin;

use App\Entity\Nutritionniste;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class NutritionnisteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Nutritionniste::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom', 'Nom complet'),
            TextField::new('specialite', 'Spécialité'),
            TextField::new('ville', 'Ville'),
            TextareaField::new('adresse', 'Adresse'),
            TextField::new('telephone', 'Téléphone'),
            EmailField::new('email', 'Email'),
            UrlField::new('siteWeb', 'Site Web')->hideOnIndex(),
            NumberField::new('tarifConsultation', 'Tarif (€)'),
            TextEditorField::new('description', 'Description')->hideOnIndex(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER);
    }
}
