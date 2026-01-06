<?php
// src/Controller/DietController.php

namespace App\Controller;

use App\Form\DietPlanType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DietController extends AbstractController
{
    #[Route('/plan-nutritionnel', name: 'diet_plan')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(DietPlanType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Stocker dans la session
            $request->getSession()->set('diet_data', $data);

            // Rediriger vers les résultats
            return $this->redirectToRoute('diet_results');
        }

        return $this->render('diet/plan.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/resultats-regime', name: 'diet_results')]
    public function results(Request $request): Response
    {
        $data = $request->getSession()->get('diet_data');

        if (!$data) {
            return $this->redirectToRoute('diet_plan');
        }

        $dietPlan = $this->calculateDietPlan($data);

        return $this->render('diet/results.html.twig', [
            'dietPlan' => $dietPlan,
            'userData' => $data,
        ]);
    }

    private function calculateDietPlan(array $data): array
    {
        $gender = $data['gender'] ?? 'male';
        $weight = floatval($data['weight']);
        $height = floatval($data['height']);
        $age = 30;
        $activityLevel = $data['activityLevel'];
        $goal = $data['goal'];
        $occupation = $data['occupation'];

        // 1. Calculer le BMR
        $bmr = $gender === 'female'
            ? (10 * $weight) + (6.25 * $height) - (5 * $age) - 161
            : (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;

        // 2. TDEE avec facteur d'activité
        $activityMultipliers = [
            'sedentary' => 1.2,
            'mixte' => 1.45,
            'physique' => 1.75,
        ];

        $tdee = $bmr * ($activityMultipliers[$activityLevel] ?? 1.2);

        // 3. Ajuster selon l'objectif
        $goalMultipliers = [
            'weight_loss' => 0.85,
            'muscle_gain' => 1.15,
            'maintenance' => 1.0,
        ];

        $dailyCalories = $tdee * ($goalMultipliers[$goal] ?? 1.0);

        // 4. Macronutriments détaillés
        $macroRatios = $this->getDetailedMacros($goal, $activityLevel);

        $proteinGrams = round(($dailyCalories * $macroRatios['protein']) / 4);
        $carbsGrams = round(($dailyCalories * $macroRatios['carbs']) / 4);
        $fatGrams = round(($dailyCalories * $macroRatios['fat']) / 9);

        // 5. Générer le plan de repas détaillé
        $mealPlan = $this->generateDetailedMealPlan($dailyCalories, $macroRatios, $data);

        // 6. Recommandations personnalisées
        $recommendedWater = round($weight * 0.035, 1);

        return [
            'summary' => [
                'dailyCalories' => round($dailyCalories),
                'bmr' => round($bmr),
                'tdee' => round($tdee),
                'goal' => $this->getGoalLabel($goal),
                'activityLevel' => $this->getActivityLabel($activityLevel),
                'occupation' => $this->getOccupationLabel($occupation),
            ],
            'macros' => [
                'protein' => [
                    'grams' => $proteinGrams,
                    'calories' => round($proteinGrams * 4),
                    'percentage' => $macroRatios['protein'] * 100,
                    'role' => 'Construction musculaire, réparation tissulaire',
                ],
                'carbs' => [
                    'grams' => $carbsGrams,
                    'calories' => round($carbsGrams * 4),
                    'percentage' => $macroRatios['carbs'] * 100,
                    'role' => 'Énergie, fonctionnement cérébral',
                ],
                'fat' => [
                    'grams' => $fatGrams,
                    'calories' => round($fatGrams * 9),
                    'percentage' => $macroRatios['fat'] * 100,
                    'role' => 'Hormones, absorption vitamines',
                ],
            ],
            'mealPlan' => $mealPlan,
            'recommendations' => [
                'hydration' => [
                    'current' => $data['waterConsumption'] ?? 1.5,
                    'recommended' => $recommendedWater,
                    'tips' => $recommendedWater > ($data['waterConsumption'] ?? 1.5)
                        ? "Buvez " . ($recommendedWater - $data['waterConsumption']) . "L d'eau supplémentaire par jour"
                        : "Votre hydratation est optimale, continuez ainsi!",
                ],
                'mealFrequency' => $goal === 'weight_loss'
                    ? '5-6 repas équilibrés par jour (3 principaux + 2-3 collations)'
                    : '4-5 repas répartis (3 principaux + 1-2 collations)',
                'timing' => $this->getMealTiming($goal, $occupation),
                'supplements' => $this->getSupplementRecommendations($goal, $activityLevel),
                'tips' => $this->getDetailedTips($data),
            ],
            'shoppingList' => $this->generateShoppingList($mealPlan),
            'recipes' => $this->getRecipeCollection($goal, $data['allergies'] ?? ''),
            'createdAt' => new \DateTime(),
        ];
    }

    private function getDetailedMacros(string $goal, string $activity): array
    {
        $ratios = [
            'weight_loss' => [
                'sedentary' => ['protein' => 0.40, 'carbs' => 0.35, 'fat' => 0.25],
                'mixte' => ['protein' => 0.35, 'carbs' => 0.40, 'fat' => 0.25],
                'physique' => ['protein' => 0.30, 'carbs' => 0.45, 'fat' => 0.25],
            ],
            'muscle_gain' => [
                'sedentary' => ['protein' => 0.30, 'carbs' => 0.40, 'fat' => 0.30],
                'mixte' => ['protein' => 0.30, 'carbs' => 0.45, 'fat' => 0.25],
                'physique' => ['protein' => 0.25, 'carbs' => 0.50, 'fat' => 0.25],
            ],
            'maintenance' => [
                'sedentary' => ['protein' => 0.25, 'carbs' => 0.40, 'fat' => 0.35],
                'mixte' => ['protein' => 0.25, 'carbs' => 0.45, 'fat' => 0.30],
                'physique' => ['protein' => 0.20, 'carbs' => 0.50, 'fat' => 0.30],
            ],
        ];

        return $ratios[$goal][$activity] ?? $ratios['maintenance']['mixte'];
    }

    private function generateDetailedMealPlan(float $calories, array $ratios, array $userData): array
    {
        $goal = $userData['goal'];
        $allergies = strtolower($userData['allergies'] ?? '');

        if ($goal === 'weight_loss') {
            $meals = [
                ['name' => 'Petit-déjeuner', 'time' => '7h-8h', 'calorie_ratio' => 0.25],
                ['name' => 'Collation matinale', 'time' => '10h-11h', 'calorie_ratio' => 0.10],
                ['name' => 'Déjeuner', 'time' => '12h30-13h30', 'calorie_ratio' => 0.30],
                ['name' => 'Collation après-midi', 'time' => '16h-17h', 'calorie_ratio' => 0.10],
                ['name' => 'Dîner', 'time' => '19h-20h', 'calorie_ratio' => 0.25],
            ];
        } else {
            $meals = [
                ['name' => 'Petit-déjeuner', 'time' => '7h-8h', 'calorie_ratio' => 0.25],
                ['name' => 'Collation pré-entraînement', 'time' => '10h', 'calorie_ratio' => 0.15],
                ['name' => 'Déjeuner', 'time' => '13h', 'calorie_ratio' => 0.30],
                ['name' => 'Collation post-entraînement', 'time' => '17h', 'calorie_ratio' => 0.15],
                ['name' => 'Dîner', 'time' => '20h', 'calorie_ratio' => 0.15],
            ];
        }

        $mealPlan = [];
        foreach ($meals as $meal) {
            $mealCalories = round($calories * $meal['calorie_ratio']);

            $mealPlan[] = [
                'name' => $meal['name'],
                'time' => $meal['time'],
                'calories' => $mealCalories,
                'protein_grams' => round($mealCalories * $ratios['protein'] / 4),
                'carbs_grams' => round($mealCalories * $ratios['carbs'] / 4),
                'fat_grams' => round($mealCalories * $ratios['fat'] / 9),
                'suggestions' => $this->getMealSuggestions($meal['name'], $userData),
                'tips' => $this->getMealTips($meal['name'], $goal),
                'recipes' => $this->getMealRecipes($meal['name'], $goal, $allergies),
            ];
        }

        return $mealPlan;
    }

    private function getMealSuggestions(string $mealName, array $userData): array
    {
        $allergies = strtolower($userData['allergies'] ?? '');
        $goal = $userData['goal'];

        $suggestions = [
            'Petit-déjeuner' => [
                'Smoothie protéiné vert (épinards, banane, protéine vanille, lait d\'amande) - 350 kcal',
                'Omelette aux 3 légumes (2 œufs, épinards, champignons, poivrons) + avocat - 400 kcal',
                'Porridge protéiné (avoine, protéine chocolat, fruits rouges, noix) - 380 kcal',
                'Toast complet au saumon fumé, fromage frais et concombres - 320 kcal',
                'Bowl petit-déjeuner (yaourt grec, granola maison, fruits de saison) - 300 kcal',
            ],
            'Collation matinale' => [
                'Yaourt grec 0% avec 1 cuillère de miel et 10 amandes - 180 kcal',
                'Barre protéinée maison (dattes, noix, protéine) - 200 kcal',
                'Fruits frais (pomme + 1 cuillère de beurre d\'amande) - 160 kcal',
                'Fromage blanc aux myrtilles et graines de chia - 150 kcal',
            ],
            'Déjeuner' => [
                'Poulet grillé (150g) avec quinoa (100g cuit) et légumes grillés - 450 kcal',
                'Salade complète (thon, avocat, œuf dur, quinoa, tomates) - 480 kcal',
                'Buddha bowl (lentilles corail, patate douce, chou kale, avocat) - 420 kcal',
                'Wrap protéiné (galette complète, dinde, houmous, crudités) - 400 kcal',
                'Poisson blanc vapeur avec riz basmati et ratatouille - 430 kcal',
            ],
            'Collation après-midi' => [
                'Shaker protéiné (protéine vanille + eau) + fruit - 180 kcal',
                'Cottage cheese avec tomates cerises et basilic - 160 kcal',
                'Œufs durs (2) avec tranches de concombre - 140 kcal',
                'Mix de noix (noix, noisettes, amandes - 30g) - 200 kcal',
            ],
            'Dîner' => [
                'Saumon au four (150g) avec brocoli vapeur et patate douce - 380 kcal',
                'Filet de dinde (120g) avec purée de chou-fleur et haricots verts - 350 kcal',
                'Sauté de tofu aux légumes et riz sauvage - 320 kcal',
                'Omelette aux champignons et épinards avec salade verte - 300 kcal',
                'Soupe de légumes protéinée (lentilles, légumes variés) - 280 kcal',
            ],
        ];

        // Filtrer selon les allergies
        $filtered = [];
        foreach ($suggestions[$mealName] ?? [] as $suggestion) {
            if (!$this->hasAllergyConflict($suggestion, $allergies)) {
                $filtered[] = $suggestion;
            }
        }

        return array_slice($filtered, 0, 4);
    }

    private function getMealRecipes(string $mealName, string $goal, string $allergies): array
    {
        $recipes = [
            'Petit-déjeuner' => [
                [
                    'name' => 'Smoothie Power Breakfast',
                    'ingredients' => ['1 banane', '1 poignée d\'épinards', '30g protéine vanille', '250ml lait d\'amande', '1 c.à.c graines de lin'],
                    'preparation' => 'Mixer tous les ingrédients pendant 1 minute',
                    'time' => '5 min',
                ],
                [
                    'name' => 'Omelette Fitness',
                    'ingredients' => ['2 œufs', '1 blanc d\'œuf', '50g épinards', '50g champignons', '30g fromage râpé léger'],
                    'preparation' => 'Faire revenir les légumes, ajouter les œufs battus, cuire à feu doux',
                    'time' => '10 min',
                ],
            ],
            'Déjeuner' => [
                [
                    'name' => 'Bowl Quinoa Poulet',
                    'ingredients' => ['150g poulet', '100g quinoa cuit', '100g brocoli', '50g carottes', '1/2 avocat', 'vinaigrette citron'],
                    'preparation' => 'Cuire le poulet et les légumes, assembler avec le quinoa et l\'avocat',
                    'time' => '20 min',
                ],
            ],
            'Dîner' => [
                [
                    'name' => 'Saumon aux Herbes',
                    'ingredients' => ['150g saumon', '200g patate douce', '150g haricots verts', 'citron', 'herbes de Provence'],
                    'preparation' => 'Cuire le saumon au four avec les herbes, servir avec patate douce vapeur',
                    'time' => '25 min',
                ],
            ],
        ];

        return $recipes[$mealName] ?? [];
    }

    private function hasAllergyConflict(string $food, string $allergies): bool
    {
        if (empty($allergies)) return false;

        $allergyList = explode(',', $allergies);
        $keywords = [
            'lactose' => ['lait', 'yaourt', 'fromage', 'crème', 'cottage'],
            'gluten' => ['blé', 'seigle', 'orge', 'pain', 'pâtes', 'avoine (si contamination)'],
            'fruits à coque' => ['noix', 'amandes', 'noisettes', 'cajou', 'beurre d\'amande'],
            'crustacés' => ['crevette', 'crabe', 'homard'],
            'œufs' => ['œuf', 'omelette'],
            'soja' => ['soja', 'tofu', 'tempeh'],
        ];

        foreach ($allergyList as $allergy) {
            $allergy = trim(strtolower($allergy));
            if (isset($keywords[$allergy])) {
                foreach ($keywords[$allergy] as $keyword) {
                    if (stripos($food, $keyword) !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function getMealTips(string $mealName, string $goal): string
    {
        $tips = [
            'Petit-déjeuner' => $goal === 'weight_loss'
                ? 'Consommez dans l\'heure qui suit le réveil pour relancer le métabolisme'
                : 'Idéalement 1h avant l\'entraînement si vous vous entraînez le matin',
            'Déjeuner' => 'Mangez lentement (minimum 20 minutes) pour une meilleure digestion',
            'Dîner' => $goal === 'weight_loss'
                ? 'Terminez votre repas 3 heures avant de vous coucher'
                : 'Incluez des protéines lentes (caséine) pour la nuit',
        ];

        return $tips[$mealName] ?? 'Mâchez bien chaque bouchée pour une meilleure satiété';
    }

    private function generateShoppingList(array $mealPlan): array
    {
        $commonItems = [
            'Protéines' => ['Poulet', 'Œufs', 'Poisson blanc', 'Tofu', 'Lentilles'],
            'Légumes' => ['Brocoli', 'Épinards', 'Poivrons', 'Tomates', 'Oignons', 'Ail'],
            'Féculents' => ['Quinoa', 'Riz complet', 'Patate douce', 'Avoine'],
            'Fruits' => ['Bananes', 'Pommes', 'Baies congelées', 'Citrons'],
            'Graisses saines' => ['Avocats', 'Noix', 'Huile d\'olive', 'Graines de lin'],
            'Produits laitiers' => ['Yaourt grec 0%', 'Fromage blanc', 'Lait d\'amande'],
            'Épicerie' => ['Protéine en poudre', 'Épices', 'Vinaigre balsamique'],
        ];

        return $commonItems;
    }

    private function getRecipeCollection(string $goal, string $allergies): array
    {
        $recipes = [
            [
                'name' => 'Soupe de lentilles protéinée',
                'calories' => 280,
                'protein' => 18,
                'carbs' => 35,
                'fat' => 7,
                'time' => '30 min',
                'difficulty' => 'Facile',
            ],
            [
                'name' => 'Bowl de quinoa au poulet et avocat',
                'calories' => 420,
                'protein' => 32,
                'carbs' => 45,
                'fat' => 12,
                'time' => '25 min',
                'difficulty' => 'Moyen',
            ],
            [
                'name' => 'Omelette aux légumes du soleil',
                'calories' => 320,
                'protein' => 25,
                'carbs' => 15,
                'fat' => 18,
                'time' => '15 min',
                'difficulty' => 'Facile',
            ],
            [
                'name' => 'Smoothie bowl énergétique',
                'calories' => 350,
                'protein' => 22,
                'carbs' => 40,
                'fat' => 10,
                'time' => '10 min',
                'difficulty' => 'Facile',
            ],
        ];

        return array_slice($recipes, 0, 3);
    }

    private function getMealTiming(string $goal, string $occupation): string
    {
        $timings = [
            'weight_loss' => [
                'sedentary' => 'Petit-déj 8h, Collation 11h, Déj 13h, Collation 16h, Dîner 19h',
                'mixte' => 'Petit-déj 7h30, Collation 10h30, Déj 12h30, Collation 16h, Dîner 20h',
                'physique' => 'Petit-déj 7h, Collation 10h, Déj 13h, Collation 17h, Dîner 20h30',
            ],
            'muscle_gain' => [
                'sedentary' => 'Petit-déj 8h, Collation 11h, Déj 13h, Collation 17h, Dîner 20h',
                'mixte' => 'Petit-déj 7h, Pré-workout 10h, Post-workout 13h, Collation 17h, Dîner 20h30',
                'physique' => 'Petit-déj 6h30, Pré-workout 9h30, Post-workout 12h30, Collation 16h, Dîner 20h',
            ],
        ];

        return $timings[$goal][$occupation] ?? 'Adaptez vos horaires à votre rythme de vie';
    }

    private function getSupplementRecommendations(string $goal, string $activity): array
    {
        $supplements = [];

        if ($goal === 'muscle_gain') {
            $supplements[] = 'Protéine en poudre (whey ou végétale) - 1 dose post-entraînement';
        }

        if ($activity === 'physique') {
            $supplements[] = 'BCAA - pendant l\'entraînement si > 1h';
            $supplements[] = 'Créatine monohydrate - 5g par jour';
        }

        $supplements[] = 'Multivitamines - 1 comprimé par jour';
        $supplements[] = 'Oméga-3 - 1-2g par jour';

        if ($goal === 'weight_loss') {
            $supplements[] = 'Thé vert - 3 tasses par jour';
        }

        return $supplements;
    }

    private function getDetailedTips(array $data): array
    {
        $tips = [];

        // Selon l'objectif
        if ($data['goal'] === 'weight_loss') {
            $tips[] = 'Buvez 500ml d\'eau 30 minutes avant chaque repas principal';
            $tips[] = 'Augmentez votre consommation de fibres (légumes à chaque repas)';
            $tips[] = 'Évitez les calories liquides (sodas, jus, alcool)';
            $tips[] = 'Dormez 7-8 heures par nuit - le manque de sommeil augmente la faim';
            $tips[] = 'Tenez un journal alimentaire pendant 2 semaines';
        } elseif ($data['goal'] === 'muscle_gain') {
            $tips[] = 'Consommez 20-30g de protéines dans les 30 minutes post-entraînement';
            $tips[] = 'Augmentez progressivement vos calories (100-200kcal/semaine)';
            $tips[] = 'Priorisez les exercices composés (squat, développé couché, tractions)';
            $tips[] = 'Reposez-vous 48h entre les séances pour le même groupe musculaire';
        }

        // Selon l'occupation
        if ($data['occupation'] === 'sedentary') {
            $tips[] = 'Faites 10 minutes de marche après chaque repas';
            $tips[] = 'Utilisez un tracker d\'activité pour viser 7500 pas/jour minimum';
            $tips[] = 'Levez-vous toutes les heures pour vous étirer';
        } elseif ($data['occupation'] === 'physique') {
            $tips[] = 'Hydratez-vous régulièrement pendant la journée (petites gorgées)';
            $tips[] = 'Prévoyez des collations pratiques à emporter';
            $tips[] = 'Étirez-vous 10 minutes le soir pour la récupération';
        }

        // Conseils généraux
        $tips[] = 'Pesez vos aliments pendant 2 semaines pour développer votre "œil nutritionnel"';
        $tips[] = 'Préparez vos repas à l\'avance le dimanche (meal prep)';
        $tips[] = 'Écoutez votre corps : mangez quand vous avez faim, arrêtez quand vous êtes rassasié';
        $tips[] = 'Variez vos sources de protéines (animales et végétales)';

        return array_slice($tips, 0, 8);
    }

    private function getGoalLabel(string $goal): string
    {
        $labels = [
            'weight_loss' => 'Perte de poids',
            'muscle_gain' => 'Prise de masse musculaire',
            'maintenance' => 'Maintien du poids',
        ];

        return $labels[$goal] ?? 'Objectif personnalisé';
    }

    private function getActivityLabel(string $activity): string
    {
        $labels = [
            'sedentary' => 'Sédentaire',
            'mixte' => 'Mixte (sédentaire/actif)',
            'physique' => 'Physique (actif)',
        ];

        return $labels[$activity] ?? 'Sédentaire';
    }

    private function getOccupationLabel(string $occupation): string
    {
        return $this->getActivityLabel($occupation);
    }
}
