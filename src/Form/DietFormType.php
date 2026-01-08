<?php
// src/Form/DietFormType.php - VERSION SIMPLIFIÉE

namespace App\Form;

use App\Entity\DietForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DietFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('goal', ChoiceType::class, [
                'label' => 'Objectif',
                'choices' => [
                    'Perte de poids' => 'weight_loss',
                    'Prise de masse' => 'muscle_gain',
                    'Maintien' => 'weight_maintenance'
                ],
                'attr' => ['class' => 'form-select']
            ])

            ->add('weight', NumberType::class, [
                'label' => 'Poids (kg)',
                'attr' => ['class' => 'form-control']
            ])

            ->add('height', NumberType::class, [
                'label' => 'Taille (cm)',
                'attr' => ['class' => 'form-control']
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Générer le plan',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DietForm::class,
        ]);
    }
}

