<?php
// src/Form/DietPlanType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DietPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Profil personnel
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Homme' => 'male',
                    'Femme' => 'female',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('weight', NumberType::class, [
                'label' => 'Poids Actuel (kg)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 30,
                    'max' => 300,
                    'step' => 0.5,
                    'placeholder' => 'Ex: 70.5',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('height', NumberType::class, [
                'label' => 'Taille (cm)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 100,
                    'max' => 250,
                    'step' => 1,
                    'placeholder' => 'Ex: 175',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('occupation', ChoiceType::class, [
                'label' => 'Occupation Quotidienne',
                'choices' => [
                    'Sédentaire (bureau, peu de marche)' => 'sedentary',
                    'Mixte (alternance bureau/activité)' => 'mixte',
                    'Physique (travail actif, beaucoup de marche)' => 'physique',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])

            // Objectifs nutritionnels
            ->add('goal', ChoiceType::class, [
                'label' => 'Objectif Principal',
                'choices' => [
                    'Perte de poids' => 'weight_loss',
                    'Prise de masse musculaire' => 'muscle_gain',
                    'Maintien du poids' => 'maintenance',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('activityLevel', ChoiceType::class, [
                'label' => "Niveau d'Activité Physique",
                'choices' => [
                    'Débutant (1-2 séances/semaine)' => 'beginner',
                    'Intermédiaire (3-4 séances/semaine)' => 'intermediate',
                    'Avancé (5+ séances/semaine)' => 'advanced',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])

            // Informations de santé
            ->add('medicalHistory', TextareaType::class, [
                'label' => 'Antécédents Médicaux (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Ex: Diabète type 2, hypertension, problèmes thyroïdiens...'
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('allergies', TextareaType::class, [
                'label' => 'Allergies ou Intolérances Alimentaires (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Ex: Lactose, gluten, fruits à coque, crustacés, œufs, soja...'
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('waterConsumption', NumberType::class, [
                'label' => "Consommation Quotidienne d'Eau (litres)",
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.5,
                    'placeholder' => 'Ex: 1.5',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])

            // Bouton de soumission
            ->add('submit', SubmitType::class, [
                'label' => 'Générer mon plan nutritionnel',
                'attr' => [
                    'class' => 'btn-submit',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas besoin de data_class pour un formulaire simple
        ]);
    }
}
