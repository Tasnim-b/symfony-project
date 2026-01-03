<?php

namespace App\DataFixtures;

use App\Entity\Nutritionniste;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NutritionnisteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $nutritionnistes = [
            [
                'nom' => 'Amira Ben Salah',
                'specialite' => 'Nutrition Sportive',
                'ville' => 'Tunis',
                'adresse' => '15 Avenue Habib Bourguiba, Tunis',
                'telephone' => '71 234 567',
                'email' => 'contact@nutrition-sportive.tn',
                'site_web' => 'www.nutrition-sportive.tn',
                'description' => 'Spécialiste en nutrition pour sportifs de haut niveau et amateurs. Accompagnement personnalisé pour la performance.',
                'tarif_consultation' => 80.00
            ],
            [
                'nom' => 'Sami Karray',
                'specialite' => 'Diététique Thérapeutique',
                'ville' => 'Sfax',
                'adresse' => 'Rue Hédi Chaker, Sfax',
                'telephone' => '74 456 789',
                'email' => 'dr.karray@nutrition.tn',
                'site_web' => null,
                'description' => 'Expert en nutrition thérapeutique pour diabétiques, maladies cardiaques et troubles métaboliques.',
                'tarif_consultation' => 70.00
            ],
            [
                'nom' => 'Leila Trabelsi',
                'specialite' => 'Nutrition Pédiatrique',
                'ville' => 'Sousse',
                'adresse' => 'Centre Médical Sousse Medical, Sousse',
                'telephone' => '73 345 678',
                'email' => 'leila.trabelsi@pediatrie.tn',
                'site_web' => 'www.nutrition-pediatrique.tn',
                'description' => 'Spécialisée dans la nutrition des enfants et adolescents. Prise en charge des troubles alimentaires juvéniles.',
                'tarif_consultation' => 75.00
            ],
            [
                'nom' => 'Hichem Ben Ammar',
                'specialite' => 'Perte de Poids',
                'ville' => 'Nabeul',
                'adresse' => 'Clinique Les Oliviers, Nabeul',
                'telephone' => '72 567 890',
                'email' => 'h.benammar@regime.tn',
                'site_web' => null,
                'description' => 'Programmes de perte de poids personnalisés avec suivi régulier et coaching nutritionnel.',
                'tarif_consultation' => 85.00
            ],
            [
                'nom' => 'Nadia Chaabane',
                'specialite' => 'Nutrition Végétarienne',
                'ville' => 'Tunis',
                'adresse' => 'Lac 2, Tunis',
                'telephone' => '98 765 432',
                'email' => 'nadia.chaabane@vege-nutrition.tn',
                'site_web' => 'www.vege-nutrition-tunisie.tn',
                'description' => 'Accompagnement nutritionnel pour régimes végétariens et végétaliens. Planification des repas équilibrés.',
                'tarif_consultation' => 90.00
            ],
            [
                'nom' => 'Mohamed Ghanmi',
                'specialite' => 'Nutrition du Sportif',
                'ville' => 'Monastir',
                'adresse' => 'Complexe Sportif, Monastir',
                'telephone' => '73 987 654',
                'email' => 'm.ghanmi@elite-sport.tn',
                'site_web' => null,
                'description' => 'Nutritionniste officiel de plusieurs équipes nationales. Expertise en nutrition pour la performance sportive.',
                'tarif_consultation' => 120.00
            ],
            [
                'nom' => 'Salma Abid',
                'specialite' => 'Nutrition Thérapeutique',
                'ville' => 'Bizerte',
                'adresse' => 'Avenue 7 Novembre, Bizerte',
                'telephone' => '72 123 456',
                'email' => 'salma.abid@nutrition.tn',
                'site_web' => null,
                'description' => 'Prise en charge nutritionnelle des maladies chroniques et troubles digestifs.',
                'tarif_consultation' => 65.00
            ],
            [
                'nom' => 'Karim Ferchichi',
                'specialite' => 'Nutrition Sportive',
                'ville' => 'Gabès',
                'adresse' => 'Rue Ali Belhouane, Gabès',
                'telephone' => '75 654 321',
                'email' => 'k.ferchichi@sport-nutrition.tn',
                'site_web' => 'www.sport-nutrition-gabes.tn',
                'description' => 'Nutrition pour sportifs amateurs et professionnels. Optimisation des performances.',
                'tarif_consultation' => 75.00
            ]
        ];

        foreach ($nutritionnistes as $data) {
            $nutritionniste = new Nutritionniste();
            $nutritionniste->setNom($data['nom']);
            $nutritionniste->setSpecialite($data['specialite']);
            $nutritionniste->setVille($data['ville']);
            $nutritionniste->setAdresse($data['adresse']);
            $nutritionniste->setTelephone($data['telephone']);
            $nutritionniste->setEmail($data['email']);
            $nutritionniste->setSiteWeb($data['site_web']);
            $nutritionniste->setDescription($data['description']);
            $nutritionniste->setTarifConsultation($data['tarif_consultation']);

            $manager->persist($nutritionniste);
        }

        $manager->flush();
    }
}
