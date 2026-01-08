<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextField::new('category', 'Catégorie'),
            TextField::new('author', 'Auteur'),
            TextField::new('read_time', 'Temps de lecture'),
            TextField::new('image', 'URL de l\'image'),
            TextareaField::new('excerpt', 'Extrait'),
            TextEditorField::new('content', 'Contenu'),
            TextField::new('formattedDate', 'Date de publication')->hideOnForm(),
            TextField::new('date', 'Date de publication')
                ->setFormType(\Symfony\Component\Form\Extension\Core\Type\DateTimeType::class)
                ->setFormTypeOptions([
                    'widget' => 'single_text',
                    'html5' => true,
                    'input' => 'datetime'
                ])
                ->onlyOnForms(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER);
    }
}
