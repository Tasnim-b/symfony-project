<?php

namespace App\Form;

use App\Entity\WorkoutProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

class WorkoutProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ÉTAPE 1: PROFIL
            ->add('gender', ChoiceType::class, [
                'label' => '👤 Votre Genre',
                'choices' => [
                    '👨 Homme' => 'Homme',
                    '👩 Femme' => 'Femme',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'gender-radio',
                    'data-step' => '1',
                ],
            ])
            ->add('age', IntegerType::class, [
                'label' => '📅 Votre Âge (ans)',
                'attr' => [
                    'min' => 15,
                    'max' => 80,
                    'placeholder' => 'Ex: 28',
                    'class' => 'form-control',
                    'data-step' => '1',
                ],
            ])
            ->add('weight', NumberType::class, [
                'label' => '⚖️ Votre Poids (kg)',
                'required' => false,
                'scale' => 1,
                'attr' => [
                    'min' => 30,
                    'max' => 200,
                    'step' => 0.5,
                    'placeholder' => 'Ex: 70.5',
                    'class' => 'form-control',
                    'data-step' => '1',
                ],
            ])

            // ÉTAPE 2: OBJECTIFS
            ->add('currentLevel', ChoiceType::class, [
                'label' => '📊 Votre Niveau',
                'choices' => [
                    '🚀 Débutant' => 'Débutant',
                    '⚡ Intermédiaire' => 'Intermédiaire',
                    '🏆 Avancé' => 'Avancé',
                ],
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-step' => '2',
                ],
            ])
            ->add('primaryGoal', ChoiceType::class, [
                'label' => '🎯 Votre Objectif',
                'choices' => [
                    '💪 Prise de Masse Musculaire' => 'Prise de masse musculaire',
                    '🔥 Perte de Poids' => 'Perte de poids',
                    '✨ Tonification & Définition' => 'Tonification',
                    '🏋️‍♂️ Force & Performance' => 'Force',
                    '🌿 Santé & Bien-être' => 'Bien-être',
                ],
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-step' => '2',
                ],
            ])

            // ÉTAPE 3: PRÉFÉRENCES
            ->add('sessionsPerWeek', ChoiceType::class, [
                'label' => '🗓️ Séances par Semaine',
                'choices' => [
                    '2 séances' => 2,
                    '3 séances' => 3,
                    '4 séances' => 4,
                    '5 séances' => 5,
                    '6+ séances' => 6,
                ],
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-step' => '3',
                ],
            ])
            ->add('preferredSchedule', ChoiceType::class, [
                'label' => '⏰ Horaires Préférés',
                'choices' => [
                    '🌅 Matin (6h-9h)' => 'Matin',
                    '☀️ Midi (11h-14h)' => 'Midi',
                    '🌇 Après-midi (14h-18h)' => 'Après-midi',
                    '🌃 Soir (18h-21h)' => 'Soir',
                ],
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'schedule-checkboxes',
                    'data-step' => '3',
                ],
            ])
            ->add('sessionDuration', ChoiceType::class, [
                'label' => '⏱️ Durée d\'une Séance',
                'choices' => [
                    '30 minutes' => 30,
                    '45 minutes' => 45,
                    '60 minutes' => 60,
                    '75 minutes' => 75,
                    '90+ minutes' => 90,
                ],
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-step' => '3',
                ],
            ])

            // ÉTAPE 4: SANTÉ & ENVIRONNEMENT
            ->add('workoutEnvironment', ChoiceType::class, [
                'label' => '🏠 Lieu d\'Entraînement',
                'choices' => [
                    '🏠 À domicile' => 'Domicile',
                    '🏋️‍♂️ En salle de sport' => 'Salle',
                    '🌳 En extérieur' => 'Exterieur',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'environment-radio',
                    'data-step' => '4',
                ],
            ])
            ->add('equipmentAvailable', ChoiceType::class, [
                'label' => '🛠️ Équipement Disponible',
                'choices' => [
                    'Aucun équipement' => 'Aucun',
                    'Matériel basique' => 'Basique',
                    'Matériel complet' => 'Complet',
                    'Accès à une salle' => 'Salle',
                ],
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-step' => '4',
                ],
            ])
            ->add('motivationLevel', ChoiceType::class, [
                'label' => '🌟 Niveau de Motivation',
                'choices' => [
                    '🌟 Je débute' => 'Débutant',
                    '🚀 Je suis motivé' => 'Motivé',
                    '🔥 Je veux me dépasser' => 'Intense',
                    '🏆 Compétiteur' => 'Compétiteur',
                ],
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-step' => '4',
                ],
            ])
            ->add('stressLevel', RangeType::class, [
                'label' => '😌 Niveau de Stress (1-10)',
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'value' => 5,
                    'class' => 'stress-slider',
                    'data-step' => '4',
                ],
                'required' => false,
            ])
            ->add('sleepQuality', ChoiceType::class, [
                'label' => '😴 Qualité de Sommeil',
                'choices' => [
                    '😴 Insuffisant (<6h)' => 'Insuffisant',
                    '😪 Moyen (6-7h)' => 'Moyen',
                    '😌 Bon (7-8h)' => 'Bon',
                    '😊 Excellent (>8h)' => 'Excellent',
                ],
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-step' => '4',
                ],
            ])
            ->add('dailyWaterIntake', NumberType::class, [
                'label' => '💧 Eau par jour (Litre)',
                'scale' => 1,
                'attr' => [
                    'min' => 0.5,
                    'max' => 5,
                    'step' => 0.5,
                    'placeholder' => 'Ex: 2.0',
                    'class' => 'form-control',
                    'data-step' => '4',
                ],
            ])
            ->add('physicalLimitations', TextareaType::class, [
                'label' => '⚠️ Blessures/Limitations (Optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Problème de genou, dos fragile...',
                    'rows' => 3,
                    'class' => 'form-control',
                    'data-step' => '4',
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => '🏋️‍♂️ GÉNÉRER MON WORKOUT PERSONNALISÉ',
                'attr' => [
                    'class' => 'btn-generate-workout',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkoutProfile::class,
        ]);
    }
}
