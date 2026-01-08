<?php

namespace App\Form;

use App\Entity\Nutritionniste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class NutritionnisteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom complet',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Dr. Nom Prénom',
                    'class' => 'form-control'
                ]
            ])
            ->add('specialite', TextType::class, [
                'label' => 'Spécialité',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nutrition Sportive, Diététique Thérapeutique...',
                    'class' => 'form-control'
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Tunis, Sfax, Sousse...',
                    'class' => 'form-control'
                ]
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Adresse complète du cabinet',
                    'class' => 'form-control'
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => 'XX XXX XXX',
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'exemple@domaine.com',
                    'class' => 'form-control'
                ]
            ])
            ->add('siteWeb', UrlType::class, [
                'label' => 'Site web',
                'required' => true,
                'attr' => [
                    'placeholder' => 'https://www.votresite.com',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Description des services, spécialités, approche...',
                    'class' => 'form-control'
                ]
            ])
            ->add('tarifConsultation', NumberType::class, [
                'label' => 'Tarif consultation (TND)',
                'required' => false,
                'scale' => 2,
                'attr' => [
                    'placeholder' => 'Ex: 80.00',
                    'class' => 'form-control',
                    'step' => '0.01'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Nutritionniste::class,
        ]);
    }
}
