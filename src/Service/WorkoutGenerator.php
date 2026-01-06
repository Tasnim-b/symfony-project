<?php
// src/Service/WorkoutGenerator.php

namespace App\Service;

use App\Entity\WorkoutProfile;

class WorkoutGenerator
{
    public function generateWorkout(WorkoutProfile $profile): string
    {
        $workout = $this->generateHeader($profile);

        // Ajouter les conseils poids si disponible
        if ($profile->getWeight() !== null && $profile->getWeight() > 0) {
            $workout .= $this->calculateBMITips($profile->getWeight());
        }

        $workout .= $this->generateMotivationMessage($profile);
        $workout .= $this->generateProfileAnalysis($profile);
        $workout .= $this->generateWeeklyProgram($profile);
        $workout .= $this->generateExerciseDetails($profile);
        $workout .= $this->generateWarmupCoolDown($profile);
        $workout .= $this->generateNutritionTips($profile);
        $workout .= $this->generateRecoveryTips($profile);
        $workout .= $this->generateProgressTracking($profile);
        $workout .= $this->generateEncouragement($profile);

        return $workout;
    }

    private function generateHeader(WorkoutProfile $profile): string
    {
        $header = "## 🏆 **PROGRAMME WORKOUT SUR MESURE**\n\n";
        $header .= "**Date de création :** " . date('d/m/Y') . "\n";
        $header .= "**Pour :** " . $profile->getGender() . ", " . $profile->getAge() . " ans\n";

        if ($profile->getWeight() !== null && $profile->getWeight() > 0) {
            $header .= "**Poids :** " . $profile->getWeight() . " kg\n";
        }

        $header .= "**Niveau :** " . $profile->getCurrentLevel() . "\n";
        $header .= "**Objectif :** " . $profile->getPrimaryGoal() . "\n";
        $header .= "**Séances/semaine :** " . $profile->getSessionsPerWeek() . "\n";
        $header .= "**Durée/séance :** " . $profile->getSessionDuration() . " min\n\n";
        $header .= "---\n\n";

        return $header;
    }

    private function calculateBMITips(float $weight): string
    {
        $tips = "### ⚖️ **ANALYSE DE VOTRE PROFIL**\n\n";

        if ($weight < 55) {
            $tips .= "📊 **Votre poids:** " . $weight . " kg (Poids léger)\n";
            $tips .= "💡 **Recommandation:** \n";
            $tips .= "- Focus sur la prise de masse musculaire\n";
            $tips .= "- Nutrition riche en protéines (1.6-2g/kg)\n";
            $tips .= "- Exercices composés avec charges progressives\n";
            $tips .= "- Repos suffisant entre les séances\n\n";
        } elseif ($weight >= 55 && $weight < 75) {
            $tips .= "📊 **Votre poids:** " . $weight . " kg (Poids idéal)\n";
            $tips .= "✅ **Parfait!** Vous êtes dans la zone optimale pour :\n";
            $tips .= "- Développer force et endurance\n";
            $tips .= "- Sculpter votre physique\n";
            $tips .= "- Améliorer vos performances\n";
            $tips .= "- Atteindre vos objectifs rapidement\n\n";
        } elseif ($weight >= 75 && $weight < 90) {
            $tips .= "📊 **Votre poids:** " . $weight . " kg (Poids solide)\n";
            $tips .= "🎯 **Stratégie recommandée:** \n";
            $tips .= "- Combiner cardio et musculation\n";
            $tips .= "- Focus sur la définition musculaire\n";
            $tips .= "- Nutrition équilibrée avec déficit calorique modéré\n";
            $tips .= "- Exercices avec bonnes amplitudes\n\n";
        } else {
            $tips .= "📊 **Votre poids:** " . $weight . " kg (Force maximale)\n";
            $tips .= "💪 **Atout majeur:** Vous avez un potentiel de force exceptionnel!\n";
            $tips .= "- Priorité aux exercices de force\n";
            $tips .= "- Technique impeccable pour prévenir les blessures\n";
            $tips .= "- Cardio modéré pour l'endurance\n";
            $tips .= "- Nutrition adaptée à l'effort intense\n\n";
        }

        return $tips;
    }

    private function generateMotivationMessage(WorkoutProfile $profile): string
    {
        $messages = [
            'Débutant' => [
                "## 🌟 **BIENVENUE DANS VOTRE TRANSFORMATION !**\n\n",
                "Chaque expert était un jour débutant. Vous venez de prendre la décision la plus importante : **COMMENCER** ! 🚀\n\n",
                "**Votre super-pouvoir :** La constance. Chaque séance vous rapproche de la version la plus forte de vous-même.\n\n",
                "**Notre promesse :** Nous vous guiderons pas à pas vers le succès.\n\n"
            ],
            'Intermédiaire' => [
                "## ⚡ **PRÊT POUR LA PROCHAINE ÉTAPE ?**\n\n",
                "Les bases sont posées, maintenant nous allons construire l'excellence. Votre progression ne fait que commencer ! 💪\n\n",
                "**Votre atout :** L'expérience. Vous savez déjà écouter votre corps, c'est votre plus grande force.\n\n",
                "**Notre mission :** Vous faire atteindre de nouveaux sommets.\n\n"
            ],
            'Avancé' => [
                "## 🏆 **NIVEAU EXPERT ACTIVÉ !**\n\n",
                "Vous recherchez la perfection, et nous allons vous y conduire. Chaque détail compte pour exceller. 🔥\n\n",
                "**Votre force :** La discipline. C'est ce qui sépare les bons des extraordinaires.\n\n",
                "**Notre objectif :** Pousser vos limites avec précision et sécurité.\n\n"
            ]
        ];

        $level = $profile->getCurrentLevel();
        return implode('', $messages[$level] ?? $messages['Débutant']);
    }

    private function generateProfileAnalysis(WorkoutProfile $profile): string
    {
        $analysis = "### 🔍 **ANALYSE DÉTAILLÉE DE VOTRE PROFIL**\n\n";

        // Analyse selon l'âge
        $age = $profile->getAge();
        if ($age < 25) {
            $analysis .= "**Âge (<25 ans) :** Votre récupération est optimale ! Profitez-en pour progresser rapidement.\n";
        } elseif ($age < 40) {
            $analysis .= "**Âge (25-39 ans) :** Parfait équilibre entre énergie et maturité musculaire.\n";
        } elseif ($age < 55) {
            $analysis .= "**Âge (40-54 ans) :** Focus sur la mobilité et la prévention des blessures.\n";
        } else {
            $analysis .= "**Âge (55+ ans) :** Priorité à la santé articulaire et à la longévité.\n";
        }

        // Analyse motivation
        $motivation = $profile->getMotivationLevel();
        $analysis .= "\n**Motivation :** ";
        switch($motivation) {
            case 'Débutant':
                $analysis .= "Nous construirons des habitudes solides jour après jour.\n";
                break;
            case 'Motivé':
                $analysis .= "Votre engagement est votre meilleur allié pour des résultats durables.\n";
                break;
            case 'Intense':
                $analysis .= "Prêt à repousser les limites ? Nous avons ce qu'il vous faut !\n";
                break;
            case 'Compétiteur':
                $analysis .= "L'esprit de compétition vous mènera à l'excellence.\n";
                break;
        }

        // Analyse équipement
        $equipment = $profile->getEquipmentAvailable();
        $analysis .= "\n**Équipement :** ";
        switch($equipment) {
            case 'Aucun':
                $analysis .= "Programme basé sur le poids du corps - efficace et accessible.\n";
                break;
            case 'Basique':
                $analysis .= "Utilisation optimale de votre matériel pour des résultats maximum.\n";
                break;
            case 'Complet':
                $analysis .= "Nous exploiterons tout votre équipement pour varier les stimuli.\n";
                break;
            case 'Salle':
                $analysis .= "Accès à tous les équipements pour une progression optimale.\n";
                break;
        }

        $analysis .= "\n---\n\n";

        return $analysis;
    }

    private function generateWeeklyProgram(WorkoutProfile $profile): string
    {
        $sessions = $profile->getSessionsPerWeek();
        $goal = $profile->getPrimaryGoal();

        $program = "## 📅 **PLANNING HEBDOMADAIRE DÉTAILLÉ**\n\n";
        $program .= "### 🗓️ **Structure : " . $sessions . " séances par semaine**\n\n";

        $schedules = [
            2 => [
                ["Lundi", "💪 **FULL BODY FORCE**", "Séance complète - Développement global", "Squats, Pompes, Rowing, Planche"],
                ["Jeudi", "🔥 **CARDIO & TONIFICATION**", "Endurance et renforcement", "HIIT, Fentes, Gainage, Cardio"]
            ],
            3 => [
                ["Lundi", "🏋️‍♂️ **HAUT DU CORPS**", "Pectoraux, Dos, Épaules", "Développé couché, Tractions, Épaules"],
                ["Mercredi", "🦵 **BAS DU CORPS**", "Jambes, Fessiers, Abdos", "Squats, Fentes, Soulevé de terre"],
                ["Vendredi", "⚡ **FULL BODY CIRCUIT**", "Circuit complet - Brûle calories", "Circuit training 45min"]
            ],
            4 => [
                ["Lundi", "🏋️‍♂️ **PUSH DAY**", "Pectoraux, Épaules, Triceps", "Développé, Pompes, Épaules"],
                ["Mardi", "🔥 **PULL DAY**", "Dos, Biceps, Posture", "Tractions, Rowing, Curls"],
                ["Jeudi", "🦵 **LEGS DAY**", "Jambes complètes", "Squats, Presse, Mollets"],
                ["Vendredi", "💨 **CARDIO & CORE**", "Endurance + Gainage", "Cardio 30min, Abdos variés"]
            ],
            5 => [
                ["Lundi", "🏋️‍♂️ **CHEST FOCUS**", "Pectoraux intensifs", "Développé décliné, Écarté, Pompes"],
                ["Mardi", "🔥 **BACK FOCUS**", "Dos et posture", "Tractions lestées, Rowing T, Hyperextension"],
                ["Mercredi", "🦵 **LEG DAY POWER**", "Force jambes", "Squats lourds, Soulevé de terre, Fentes"],
                ["Jeudi", "⚡ **SHOULDERS & ARMS**", "Épaules et bras", "Développé militaire, Curls, Triceps"],
                ["Vendredi", "💨 **FUNCTIONAL & CARDIO**", "Mobilité + Endurance", "Circuit fonctionnel, Cardio"]
            ]
        ];

        $selected = $schedules[$sessions] ?? $schedules[3];

        foreach ($selected as $day) {
            $program .= "#### 📍 **" . $day[0] . " : " . $day[1] . "**\n";
            $program .= "📝 **Objectif :** " . $day[2] . "\n";
            $program .= "🎯 **Exercices clés :** " . $day[3] . "\n";
            $program .= "⏱️ **Durée :** " . $profile->getSessionDuration() . " minutes\n";
            $program .= "💪 **Intensité :** " . $this->getIntensityForGoal($goal, $profile->getCurrentLevel()) . "\n\n";
        }

        $program .= "### 💡 **CONSEILS DE PROGRAMMATION**\n";
        $program .= "- **Échauffement :** 10-15min avant chaque séance\n";
        $program .= "- **Récupération :** 48h minimum entre les mêmes groupes musculaires\n";
        $program .= "- **Progressivité :** Augmentez les charges de 5% chaque 2 semaines\n";
        $program .= "- **Adaptation :** Écoutez votre corps, un jour de repos supplémentaire n'est pas un échec\n\n";

        $program .= "---\n\n";

        return $program;
    }

    private function getIntensityForGoal(string $goal, string $level): string
    {
        $intensities = [
            'Prise de masse musculaire' => '8-12 répétitions, repos 60-90s, charges lourdes',
            'Perte de poids' => '12-20 répétitions, repos 30-45s, circuits intensifs',
            'Tonification' => '12-15 répétitions, repos 45-60s, contrôle des mouvements',
            'Force' => '4-6 répétitions, repos 2-3min, charges maximales',
            'Bien-être' => '10-15 répétitions, repos 60s, focus sur la technique'
        ];

        $base = $intensities[$goal] ?? 'Adapté à votre niveau';

        if ($level === 'Débutant') {
            return $base . " (début progressif)";
        } elseif ($level === 'Avancé') {
            return $base . " (niveau avancé)";
        }

        return $base;
    }

    private function generateExerciseDetails(WorkoutProfile $profile): string
    {
        $details = "## 🏋️‍♂️ **DÉTAIL TECHNIQUE DES EXERCICES**\n\n";

        $equipment = $profile->getEquipmentAvailable();
        $environment = $profile->getWorkoutEnvironment();

        $details .= "### 🏠 **ENVIRONNEMENT ADAPTÉ**\n";
        $details .= "**Lieu :** " . $environment . "\n";
        $details .= "**Équipement :** " . $equipment . "\n\n";

        $details .= "### 📋 **SÉANCE TYPE - FULL BODY**\n\n";

        $exercises = $this->getExercisesForEnvironment($environment, $equipment);

        foreach ($exercises as $index => $ex) {
            $details .= "#### " . ($index + 1) . ". **" . $ex['name'] . "**\n";
            $details .= "📊 **Séries/Reps :** " . $ex['sets'] . "\n";
            $details .= "🎯 **Objectif :** " . $ex['goal'] . "\n";
            $details .= "💡 **Technique :** " . $ex['technique'] . "\n";
            $details .= "⏱️ **Repos :** " . $ex['rest'] . "\n\n";
        }

        // Adaptations pour limitations
        if ($profile->getPhysicalLimitations()) {
            $details .= "### ⚠️ **ADAPTATIONS SPÉCIFIQUES**\n";
            $details .= "Pour **" . $profile->getPhysicalLimitations() . "** :\n";
            $details .= "- Éviter les impacts et mouvements brusques\n";
            $details .= "- Privilégier les amplitudes confortables\n";
            $details .= "- Augmenter progressivement l'intensité\n";
            $details .= "- Écouter les signaux de douleur (≠ inconfort musculaire)\n";
            $details .= "- Consulter un professionnel de santé si nécessaire\n\n";
        }

        $details .= "### 🔄 **VARIATIONS PROGRESSIVES**\n";
        $details .= "- **Semaines 1-2 :** Maîtrise technique\n";
        $details .= "- **Semaines 3-4 :** Augmentation volume\n";
        $details .= "- **Semaines 5-6 :** Augmentation intensité\n";
        $details .= "- **Semaines 7-8 :** Introduction variations\n";
        $details .= "- **Semaines 9-12 :** Consolidation performance\n\n";

        $details .= "---\n\n";

        return $details;
    }

    private function getExercisesForEnvironment(string $environment, string $equipment): array
    {
        if ($equipment === 'Aucun' || $environment === 'Domicile') {
            return [
                [
                    'name' => 'Squats au Poids du Corps',
                    'sets' => '3x15-20',
                    'goal' => 'Jambes et fessiers',
                    'technique' => 'Dos droit, genoux alignés avec pieds',
                    'rest' => '45s'
                ],
                [
                    'name' => 'Pompes Adaptées',
                    'sets' => '3xMax',
                    'goal' => 'Pectoraux et triceps',
                    'technique' => 'Corps gainé, descente contrôlée',
                    'rest' => '60s'
                ],
                [
                    'name' => 'Fentes Alternées',
                    'sets' => '3x10 par jambe',
                    'goal' => 'Équilibre et puissance',
                    'technique' => 'Genou avant à 90°, buste droit',
                    'rest' => '45s'
                ],
                [
                    'name' => 'Superman',
                    'sets' => '3x12',
                    'goal' => 'Dos et posture',
                    'technique' => 'Contracter le dos, tenir 2s',
                    'rest' => '30s'
                ],
                [
                    'name' => 'Planche',
                    'sets' => '3x30-60s',
                    'goal' => 'Gainage complet',
                    'technique' => 'Ventre rentré, fessiers serrés',
                    'rest' => '60s'
                ]
            ];
        }

        // Pour salle ou équipement complet
        return [
            [
                'name' => 'Squats avec Barre',
                'sets' => '4x8-12',
                'goal' => 'Force globale',
                'technique' => 'Barre sur trapèzes, dos neutre',
                'rest' => '90s'
            ],
            [
                'name' => 'Développé Couché',
                'sets' => '4x8-10',
                'goal' => 'Pectoraux puissance',
                'technique' => 'Omoplates serrées, contrôle descente',
                'rest' => '90s'
            ],
            [
                'name' => 'Rowing Barre',
                'sets' => '3x10-12',
                'goal' => 'Épaisseur du dos',
                'technique' => 'Dos droit, tirer avec les coudes',
                'rest' => '75s'
            ],
            [
                'name' => 'Fentes Marchées avec Haltères',
                'sets' => '3x12 par jambe',
                'goal' => 'Stabilité et masse',
                'technique' => 'Grand pas, genou léger au sol',
                'rest' => '60s'
            ],
            [
                'name' => 'Développé Épaules',
                'sets' => '3x10-12',
                'goal' => 'Épaules complètes',
                'technique' => 'Contrôle strict, pas d\'élan',
                'rest' => '75s'
            ]
        ];
    }

    private function generateWarmupCoolDown(WorkoutProfile $profile): string
    {
        $warmup = "## 🔥 **PROTOCOLE ÉCHAUFFEMENT COMPLET**\n\n";
        $warmup .= "### ❗ **NE JAMAIS NÉGLIGER CETTE ÉTAPE**\n\n";

        $warmup .= "#### 1️⃣ **CARDIO LÉGER (5 minutes)**\n";
        $warmup .= "- Vélo, marche rapide ou corde à sauter\n";
        $warmup .= "- Objectif : Augmenter température corporelle\n";
        $warmup .= "- Indicateur : Légère transpiration\n\n";

        $warmup .= "#### 2️⃣ **MOBILITÉ ARTICULAIRE (5 minutes)**\n";
        $warmup .= "- Rotations chevilles/genoux/hanches/épaules/cou\n";
        $warmup .= "- Cercles de bras avant/arrière\n";
        $warmup .= "- Rotations du buste\n";
        $warmup .= "- Flexions latérales\n\n";

        $warmup .= "#### 3️⃣ **ACTIVATION MUSCULAIRE (5 minutes)**\n";
        $warmup .= "- Squats au corps (2x15)\n";
        $warmup .= "- Pompes inclinées (2x10)\n";
        $warmup .= "- Planche (2x30s)\n";
        $warmup .= "- Fentes sans charge (2x10 par jambe)\n\n";

        $warmup .= "### ❄️ **PROTOCOLE RETOUR AU CALME**\n\n";
        $warmup .= "#### 1️⃣ **ÉTIREMENTS STATIQUES (8 minutes)**\n";
        $warmup .= "- Quadriceps : 30s par jambe\n";
        $warmup .= "- Ischio-jambiers : 30s par jambe\n";
        $warmup .= "- Pectoraux : 30s chaque côté\n";
        $warmup .= "- Dos : 30s\n";
        $warmup .= "- Épaules : 30s chaque côté\n\n";

        $warmup .= "#### 2️⃣ **RÉCUPÉRATION ACTIVE (5 minutes)**\n";
        $warmup .= "- Marche lente\n";
        $warmup .= "- Respiration profonde ventrale\n";
        $warmup .= "- Hydratation progressive\n\n";

        $warmup .= "#### 3️⃣ **JOURNAL DE BORD (2 minutes)**\n";
        $warmup .= "- Notez vos performances\n";
        $warmup .= "- Évaluez votre énergie (1-10)\n";
        $warmup .= "- Identifiez les difficultés\n";
        $warmup .= "- Planifiez la prochaine séance\n\n";

        $warmup .= "---\n\n";

        return $warmup;
    }

    private function generateNutritionTips(WorkoutProfile $profile): string
    {
        $water = $profile->getDailyWaterIntake();
        $weight = $profile->getWeight();
        $goal = $profile->getPrimaryGoal();

        $tips = "## 🥗 **STRATÉGIE NUTRITIONNELLE OPTIMALE**\n\n";

        $tips .= "### 💧 **HYDRATATION INTELLIGENTE**\n";
        $tips .= "**Actuel :** " . $water . "L/jour\n";

        // Calculer l'eau recommandée
        $recommended = 2.5; // Valeur par défaut
        if ($weight !== null && $weight > 0) {
            $recommended = round($weight * 0.035, 1);
        }

        $diff = $recommended - $water;

        if ($diff > 0.5) {
            $tips .= "🎯 **Objectif :** " . $recommended . "L/jour (+" . $diff . "L)\n";
            $tips .= "💡 **Plan d'action :**\n";
            $tips .= "- 1 verre d'eau au réveil\n";
            $tips .= "- 1 bouteille de 500ml pendant l'entraînement\n";
            $tips .= "- 1 verre à chaque repas\n";
            $tips .= "- 1 verre avant le coucher\n\n";
        } else {
            $tips .= "✅ **Excellent !** Continuez sur cette lancée.\n\n";
        }

        $tips .= "### 🍽️ **TIMING NUTRITIONNEL PRÉCIS**\n";
        $tips .= "**Avant l'entraînement (1h30-2h) :**\n";
        $tips .= "- Repas équilibré protéines + glucides complexes\n";
        $tips .= "- Ex: Poulet + riz brun + légumes\n\n";

        $tips .= "**30min avant :**\n";
        $tips .= "- Fruit facile à digérer (banane, pomme)\n";
        $tips .= "- Café (si toléré) pour l'énergie\n\n";

        $tips .= "**Pendant l'entraînement :**\n";
        $tips .= "- Eau uniquement (sauf séance >90min)\n\n";

        $tips .= "**Dans les 30min après :**\n";
        $tips .= "- Protéines rapides + glucides simples\n";
        $tips .= "- Ex: Shaker protéiné + banane\n\n";

        $tips .= "**Repas post-entraînement (1-2h après) :**\n";
        $tips .= "- Repas complet protéines + glucides + légumes\n\n";

        $tips .= "### 🎯 **APPORTS RECOMMANDÉS**\n";
        if ($weight !== null && $weight > 0) {
            $protein = $goal === 'Prise de masse musculaire' ? round($weight * 2) : round($weight * 1.6);
            $tips .= "**Protéines :** " . $protein . "g/jour (" . round($weight * 0.032) . "g/kg)\n";
            $tips .= "**Glucides :** Adaptés à votre activité\n";
            $tips .= "**Lipides :** 0.8-1g/kg de poids\n\n";
        } else {
            $tips .= "**Pour des recommandations précises :** Renseignez votre poids dans vos préférences.\n\n";
        }

        $tips .= "### 🚫 **À ÉVITER ABSOLUMENT**\n";
        $tips .= "- S'entraîner à jeun prolongé (>3h)\n";
        $tips .= "- Boissons sucrées pendant l'effort\n";
        $tips .= "- Sauter le repas post-entraînement\n";
        $tips .= "- Excès d'alcool avant/suite à l'entraînement\n\n";

        $tips .= "---\n\n";

        return $tips;
    }

    private function generateRecoveryTips(WorkoutProfile $profile): string
    {
        $sleep = $profile->getSleepQuality();
        $stress = $profile->getStressLevel();

        $tips = "## 😴 **STRATÉGIE DE RÉCUPÉRATION OPTIMISÉE**\n\n";

        $tips .= "### 💤 **QUALITÉ DE SOMMEIL**\n";
        $tips .= "**Votre niveau :** " . $sleep . "\n\n";

        $sleepStrategies = [
            'Insuffisant' => [
                "🎯 **PRIORITÉ ABSOLUE :** Atteindre 7-8h de sommeil",
                "🕐 **Coucher régulier :** Même heure ±30min",
                "📵 **Déconnexion :** Écrans OFF 1h avant le coucher",
                "🌡️ **Environnement :** Chambre à 18°C, obscurité totale",
                "☕ **Stimulants :** Pas de caféine après 14h"
            ],
            'Moyen' => [
                "✨ **AMÉLIORATION :** Ajouter 30min de sommeil",
                "🧘 **Routine :** 10min de lecture/méditation avant de dormir",
                "💧 **Hydratation :** Limiter l'eau 1h avant le coucher",
                "🏃 **Activité :** Exercice terminé 3h avant le sommeil"
            ],
            'Bon' => [
                "✅ **EXCELLENT :** Votre récupération est optimale",
                "🔒 **Maintenez :** Continuez vos bonnes habitudes",
                "📊 **Suivi :** Notez votre énergie au réveil (1-10)",
                "🔄 **Adaptation :** Augmentez l'intensité progressivement"
            ],
            'Excellent' => [
                "🏆 **EXCEPTIONNEL :** Votre sommeil est parfait",
                "⚡ **Atout :** Récupération maximale pour progression rapide",
                "🎯 **Objectif :** Utilisez cet avantage pour repousser vos limites",
                "💪 **Performance :** Sommeil = Votre complément n°1"
            ]
        ];

        foreach ($sleepStrategies[$sleep] ?? $sleepStrategies['Moyen'] as $strategy) {
            $tips .= "- " . $strategy . "\n";
        }
        $tips .= "\n";

        $tips .= "### 🧘 **GESTION DU STRESS**\n";
        $tips .= "**Niveau actuel :** " . $stress . "/10\n\n";

        if ($stress >= 8) {
            $tips .= "⚠️ **ATTENTION ÉLEVÉE :** Stress impactant la récupération\n";
            $tips .= "🧠 **Techniques :**\n";
            $tips .= "- Méditation 10min matin/soir\n";
            $tips .= "- Journaling 5min avant le coucher\n";
            $tips .= "- Respiration 4-7-8 (4s inspire, 7s retient, 8s expire)\n";
            $tips .= "- Marche nature 20min/jour\n\n";
        } elseif ($stress >= 5) {
            $tips .= "📊 **NIVEAU MODÉRÉ :** Gestion nécessaire\n";
            $tips .= "🔄 **Actions :**\n";
            $tips .= "- Pauses actives toutes les 90min de travail\n";
            $tips .= "- Limitation écrans soirée\n";
            $tips .= "- Activités relaxantes (musique, dessin)\n\n";
        } else {
            $tips .= "✅ **BON ÉQUILIBRE :** Stress bien géré\n";
            $tips .= "💚 **Maintenance :** Continuez vos pratiques actuelles\n\n";
        }

        $tips .= "### 🔄 **RÉCUPÉRATION ACTIVE**\n";
        $tips .= "**Jours de repos :**\n";
        $tips .= "- 🚶 **Marche légère :** 30-45min\n";
        $tips .= "- 🧘 **Étirements :** 15min de mobilité\n";
        $tips .= "- 🛀 **Détente :** Bain chaud avec sel d'Epsom\n";
        $tips .= "- 🎵 **Relaxation :** Musique calme, lecture\n";
        $tips .= "- 💆 **Auto-massage :** Rouleau foam 10min\n\n";

        $tips .= "---\n\n";

        return $tips;
    }

    private function generateProgressTracking(WorkoutProfile $profile): string
    {
        $tracking = "## 📊 **SYSTÈME DE SUIVI & PROGRESSION**\n\n";

        $tracking .= "### 🎯 **MÉTRIQUES ESSENTIELLES À SUIVRE**\n\n";
        $tracking .= "#### 1️⃣ **PERFORMANCES (À NOTER APRÈS CHAQUE SÉANCE)**\n";
        $tracking .= "- Charges utilisées pour chaque exercice\n";
        $tracking .= "- Nombre de répétitions réalisées\n";
        $tracking .= "- Durée des séries et repos\n";
        $tracking .= "- Niveau d'énergie pendant la séance (1-10)\n\n";

        $tracking .= "#### 2️⃣ **SENSATIONS & BIEN-ÊTRE (QUOTIDIEN)**\n";
        $tracking .= "- Qualité du sommeil (1-10)\n";
        $tracking .= "- Niveau d'énergie générale\n";
        $tracking .= "- Récupération entre les séances\n";
        $tracking .= "- Motivation et humeur\n\n";

        $tracking .= "#### 3️⃣ **MESURES CORPORELLES (TOUS LES 15 JOURS)**\n";
        $tracking .= "- Tour de taille (au nombril)\n";
        $tracking .= "- Tour de bras (contracté)\n";
        $tracking .= "- Tour de cuisse (mi-cuisse)\n";
        $tracking .= "- Tour de poitrine\n";
        $tracking .= "- Poids (même heure, mêmes conditions)\n\n";

        $tracking .= "#### 4️⃣ **PHOTOS PROGRESSIVES (TOUS LES 30 JOURS)**\n";
        $tracking .= "- Face avant, contracté\n";
        $tracking .= "- Face avant, relâché\n";
        $tracking .= "- Profil, contracté\n";
        $tracking .= "- Mêmes conditions (lumière, heure, vêtements)\n\n";

        $tracking .= "### 📅 **PLAN DE PROGRESSION SUR 12 SEMAINES**\n\n";
        $tracking .= "#### 🟢 **PHASE 1 : ACQUISITION (Semaines 1-4)**\n";
        $tracking .= "- Maîtrise technique parfaite\n";
        $tracking .= "- Établissement des routines\n";
        $tracking .= "- Adaptation neuromusculaire\n";
        $tracking .= "- Objectif : Habitudes solides\n\n";

        $tracking .= "#### 🟡 **PHASE 2 : PROGRESSION (Semaines 5-8)**\n";
        $tracking .= "- Augmentation charges (+5-10%)\n";
        $tracking .= "- Réduction temps de repos\n";
        $tracking .= "- Introduction de variations\n";
        $tracking .= "- Objectif : Performance croissante\n\n";

        $tracking .= "#### 🔴 **PHASE 3 : OPTIMISATION (Semaines 9-12)**\n";
        $tracking .= "- Charges maximales adaptées\n";
        $tracking .= "- Techniques avancées\n";
        $tracking .= "- Évaluation complète\n";
        $tracking .= "- Objectif : Résultats visibles\n\n";

        $tracking .= "### 🏆 **OBJECTIFS RÉALISTES**\n";
        $goal = $profile->getPrimaryGoal();

        $objectives = [
            'Prise de masse musculaire' => "+1-2kg de muscle, +10-20% sur les charges",
            'Perte de poids' => "-2-4kg de graisse, +15% endurance",
            'Tonification' => "Muscles définis, meilleure posture, +10% force",
            'Force' => "+20-30% charges principales, technique impeccable",
            'Bien-être' => "+30% énergie, meilleur sommeil, -40% stress"
        ];

        $tracking .= "**En 3 mois, vous pouvez raisonnablement attendre :**\n";
        $tracking .= "🎯 " . ($objectives[$goal] ?? "Amélioration significative de votre condition physique") . "\n\n";

        $tracking .= "---\n\n";

        return $tracking;
    }

    private function generateEncouragement(WorkoutProfile $profile): string
    {
        $encouragement = "## 🌈 **MOTIVATION & ACCOMPAGNEMENT**\n\n";

        $quotes = [
            "💪 **Le succès n'est pas final, l'échec n'est pas fatal : c'est le courage de continuer qui compte.** - Winston Churchill",
            "🚀 **Vous ne verrez pas de changement chaque jour, mais chaque jour compte.**",
            "🌟 **La seule mauvaise séance est celle que vous ne faites pas.**",
            "🔥 **Ne comparez pas votre chapitre 1 au chapitre 20 de quelqu'un d'autre.**",
            "🌈 **Le progrès, pas la perfection. Chaque petit pas compte.**",
            "🎯 **Vous êtes plus fort que vous ne le pensez. Faites-nous confiance.**",
            "⚡ **La discipline vous mènera là où la motivation ne peut pas aller.**",
            "💚 **Votre santé est un investissement, pas une dépense.**"
        ];

        $randomQuote = $quotes[array_rand($quotes)];

        $encouragement .= $randomQuote . "\n\n";

        $encouragement .= "### 📱 **RAPPELS QUOTIDIENS**\n";
        $encouragement .= "1. **Hydratez-vous** dès le réveil (1 grand verre d'eau)\n";
        $encouragement .= "2. **Bougez** au moins 30 minutes chaque jour\n";
        $encouragement .= "3. **Écoutez** votre corps (douleur ≠ inconfort musculaire)\n";
        $encouragement .= "4. **Célébrez** chaque petite victoire\n";
        $encouragement .= "5. **Dormez** comme un champion (7-8h)\n";
        $encouragement .= "6. **Respirez** profondément 3x par jour\n";
        $encouragement .= "7. **Notez** vos progrès dans votre journal\n\n";

        $encouragement .= "### 🤝 **NOTRE ENGAGEMENT ENVERS VOUS**\n";
        $encouragement .= "Nous croyons en vous. Ce programme a été créé avec soin, expertise et passion spécifiquement pour **VOUS**.\n\n";

        $encouragement .= "**Suivez-le** avec constance\n";
        $encouragement .= "**Adaptez-le** si nécessaire\n";
        $encouragement .= "**Écoutez** votre corps\n";
        $encouragement .= "Mais surtout : **PERSÉVÉREZ**\n\n";

        $encouragement .= "**Votre coach dévoué,**\n";
        $encouragement .= "L'équipe Healfit 💚\n\n";

        $encouragement .= "---\n";
        $encouragement .= "*Programme généré le " . date('d/m/Y à H:i') . "*\n";
        $encouragement .= "*© Healfit - Votre succès, notre passion*\n";
        $encouragement .= "*Pour toute question : contact@healfit.com*\n";

        return $encouragement;
    }
}
