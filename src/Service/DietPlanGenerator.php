<?php
// src/Service/DietPlanGenerator.php

namespace App\Service;

use App\Entity\DietForm;

class DietPlanGenerator
{
    public function generateDietPlan(DietForm $dietForm): string
    {
        $goal = $dietForm->getGoal();
        $gender = $dietForm->getGender();
        $activityLevel = $dietForm->getActivityLevel();
        $allergies = $dietForm->getAllergies();

        // Logique de génération du plan alimentaire
        $plan = "## Votre Plan Nutritionnel Personnalisé\n\n";

        // Calcul des calories
        $calories = $this->calculateDailyCalories($dietForm);
        $dietForm->setDailyCalories($calories);

        $plan .= "**Calories quotidiennes recommandées :** " . $calories . " kcal\n\n";

        // Répartition des macronutriments
        if ($goal === 'weight_loss') {
            $plan .= "**Objectif : Perte de poids**\n";
            $plan .= "- Protéines : 30% (" . round($calories * 0.3 / 4) . "g)\n";
            $plan .= "- Lipides : 25% (" . round($calories * 0.25 / 9) . "g)\n";
            $plan .= "- Glucides : 45% (" . round($calories * 0.45 / 4) . "g)\n\n";
        } elseif ($goal === 'muscle_gain') {
            $plan .= "**Objectif : Prise de masse musculaire**\n";
            $plan .= "- Protéines : 35% (" . round($calories * 0.35 / 4) . "g)\n";
            $plan .= "- Lipides : 25% (" . round($calories * 0.25 / 9) . "g)\n";
            $plan .= "- Glucides : 40% (" . round($calories * 0.4 / 4) . "g)\n\n";
        } else {
            $plan .= "**Objectif : Maintien**\n";
            $plan .= "- Protéines : 25% (" . round($calories * 0.25 / 4) . "g)\n";
            $plan .= "- Lipides : 30% (" . round($calories * 0.3 / 9) . "g)\n";
            $plan .= "- Glucides : 45% (" . round($calories * 0.45 / 4) . "g)\n\n";
        }

        // Plan de repas
        $plan .= "## Plan de Repas Journalier\n\n";
        $plan .= "**Petit-déjeuner (7h-8h) :**\n";
        $plan .= "- 2 œufs entiers + 1 blanc d'œuf\n";
        $plan .= "- 60g d'avoine\n";
        $plan .= "- 1 fruit frais\n\n";

        $plan .= "**Collation (10h30) :**\n";
        $plan .= "- 1 yaourt grec\n";
        $plan .= "- 10 amandes\n\n";

        $plan .= "**Déjeuner (13h) :**\n";
        $plan .= "- 150g de protéine (poulet, poisson, tofu)\n";
        $plan .= "- 150g de légumes verts\n";
        $plan .= "- 100g de glucides complets (riz, quinoa)\n\n";

        $plan .= "**Collation (16h30) :**\n";
        $plan .= "- 1 fruit\n";
        $plan .= "- 1 portion de fromage blanc\n\n";

        $plan .= "**Dîner (20h) :**\n";
        $plan .= "- 120g de protéine légère\n";
        $plan .= "- 200g de légumes\n";
        $plan .= "- 50g de glucides\n\n";

        // Recommandations personnalisées
        $plan .= "## Recommandations Personnalisées\n\n";

        if ($allergies) {
            $plan .= "**Attention aux allergies :** " . $allergies . "\n";
            $plan .= "Évitez ces aliments et remplacez-les par des alternatives sûres.\n\n";
        }

        if ($dietForm->getMedicalHistory()) {
            $plan .= "**Considérations médicales :**\n";
            $plan .= "Consultez votre médecin avant de commencer ce régime.\n\n";
        }

        $plan .= "**Hydratation :**\n";
        $plan .= "Buvez au moins " . $dietForm->getWaterConsumption() . "L d'eau par jour.\n";
        $plan .= "Augmentez à " . ($dietForm->getWaterConsumption() + 0.5) . "L les jours d'entraînement.\n\n";

        $plan .= "## Conseils Importants\n\n";
        $plan .= "1. Mangez toutes les 3-4 heures\n";
        $plan .= "2. Buvez un grand verre d'eau avant chaque repas\n";
        $plan .= "3. Dormez 7-8 heures par nuit\n";
        $plan .= "4. Pratiquez une activité physique régulière\n";

        return $plan;
    }

    public function calculateDailyCalories(DietForm $dietForm): float
    {
        $weight = $dietForm->getWeight();
        $height = $dietForm->getHeight();
        $age = 30; // Par défaut, vous pourriez ajouter un champ âge

        // Formule de Harris-Benedict
        if ($dietForm->getGender() === 'male') {
            $bmr = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
        } else {
            $bmr = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
        }

        // Facteur d'activité
        $activityFactors = [
            'sedentary' => 1.2,
            'lightly_active' => 1.375,
            'moderately_active' => 1.55,
            'very_active' => 1.725,
            'extra_active' => 1.9
        ];

        $activityLevel = $dietForm->getActivityLevel();
        $tdee = $bmr * ($activityFactors[$activityLevel] ?? 1.2);

        // Ajustement selon l'objectif
        $goal = $dietForm->getGoal();
        if ($goal === 'weight_loss') {
            $tdee -= 500; // Déficit calorique
        } elseif ($goal === 'muscle_gain') {
            $tdee += 300; // Surplus calorique
        }

        return round($tdee);
    }
}
