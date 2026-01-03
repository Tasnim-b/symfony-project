<?php

namespace App\Controller;

use App\Entity\Nutritionniste;
use App\Form\NutritionnisteType;
use App\Repository\NutritionnisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NutritionnisteController extends AbstractController
{
    #[Route('/nutritionnistes', name: 'app_nutritionniste')]
    public function index(NutritionnisteRepository $repository): Response
    {
        // 1. Récupérer les nutritionnistes STATIQUES
        $staticNutritionnistes = $this->getStaticNutritionnistes();

        // 2. Récupérer les nutritionnistes de la BASE DE DONNÉES
        $dbNutritionnistes = $repository->findAll();

        // 3. Convertir les entités BD en tableau
        $dbNutritionnistesArray = [];
        foreach ($dbNutritionnistes as $nutri) {
            $dbNutritionnistesArray[] = [
                'id' => 'db_' . $nutri->getId(), // db_1, db_2, etc.
                'nom' => $nutri->getNom(),
                'specialite' => $nutri->getSpecialite(),
                'ville' => $nutri->getVille(),
                'adresse' => $nutri->getAdresse(),
                'telephone' => $nutri->getTelephone(),
                'email' => $nutri->getEmail(),
                'site_web' => $nutri->getSiteWeb(),
                'description' => $nutri->getDescription(),
                'tarif' => $nutri->getTarifConsultation(),
                'source' => 'database' // Marqueur pour savoir que c'est de la BD
            ];
        }

        // 4. FUSIONNER les deux tableaux
        $allNutritionnistes = array_merge($staticNutritionnistes, $dbNutritionnistesArray);

        // 5. Trier par ville et nom
        usort($allNutritionnistes, function($a, $b) {
            if ($a['ville'] === $b['ville']) {
                return strcmp($a['nom'], $b['nom']);
            }
            return strcmp($a['ville'], $b['ville']);
        });

        // 6. Envoyer à la vue
        return $this->render('nutritionniste/index.html.twig', [
            'nutritionnistes' => $allNutritionnistes,
        ]);
    }

    #[Route('/nutritionnistes/ajouter', name: 'app_nutritionniste_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer un nouveau nutritionniste (vide)
        $nutritionniste = new Nutritionniste();

        // Créer le formulaire
        $form = $this->createForm(NutritionnisteType::class, $nutritionniste);

        // Traiter la soumission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // SAUVEGARDER dans la base de données
            $entityManager->persist($nutritionniste);
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', "✅ Nutritionniste '{$nutritionniste->getNom()}' ajouté avec succès !");

            // Rediriger vers la liste
            return $this->redirectToRoute('app_nutritionniste');
        }

        // Afficher le formulaire
        return $this->render('nutritionniste/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // AJOUTEZ CETTE MÉTHODE POUR LA SUPPRESSION ↓
    #[Route('/nutritionnistes/supprimer/{id}', name: 'app_nutritionniste_supprimer')]
    public function supprimer(int $id, NutritionnisteRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $nutritionniste = $repository->find($id);

        if (!$nutritionniste) {
            $this->addFlash('error', 'Nutritionniste non trouvé !');
            return $this->redirectToRoute('app_nutritionniste');
        }

        $entityManager->remove($nutritionniste);
        $entityManager->flush();

        $this->addFlash('success', '✅ Nutritionniste supprimé avec succès !');

        return $this->redirectToRoute('app_nutritionniste');
    }

    /**
     * Retourne les 16 nutritionnistes STATIQUES
     */
    private function getStaticNutritionnistes(): array
    {
        return [
            [
                'id' => 1,
                'nom' => 'Dr. Amira Ben Salah',
                'specialite' => 'Nutrition Sportive',
                'ville' => 'Tunis',
                'adresse' => '15 Avenue Habib Bourguiba, Tunis',
                'telephone' => '71 234 567',
                'email' => 'contact@nutrition-sportive.tn',
                'site_web' => 'www.nutrition-sportive.tn',
                'description' => 'Spécialiste en nutrition pour sportifs de haut niveau et amateurs.',
                'tarif' => 80.00,
                'source' => 'static'
            ],
            [
                'id' => 2,
                'nom' => 'Dr. Sami Karray',
                'specialite' => 'Diététique Thérapeutique',
                'ville' => 'Sfax',
                'adresse' => 'Rue Hédi Chaker, Sfax',
                'telephone' => '74 456 789',
                'email' => 'dr.karray@nutrition.tn',
                'site_web' => null,
                'description' => 'Expert en nutrition thérapeutique pour diabétiques.',
                'tarif' => 70.00,
                'source' => 'static'
            ],
            [
                'id' => 3,
                'nom' => 'Dr. Leila Trabelsi',
                'specialite' => 'Nutrition Pédiatrique',
                'ville' => 'Sousse',
                'adresse' => 'Centre Médical Sousse Medical, Sousse',
                'telephone' => '73 345 678',
                'email' => 'leila.trabelsi@pediatrie.tn',
                'site_web' => 'www.nutrition-pediatrique.tn',
                'description' => 'Spécialisée dans la nutrition des enfants.',
                'tarif' => 75.00,
                'source' => 'static'
            ],
            [
                'id' => 4,
                'nom' => 'Dr. Hichem Ben Ammar',
                'specialite' => 'Perte de Poids',
                'ville' => 'Nabeul',
                'adresse' => 'Clinique Les Oliviers, Nabeul',
                'telephone' => '72 567 890',
                'email' => 'h.benammar@regime.tn',
                'site_web' => null,
                'description' => 'Programmes de perte de poids personnalisés.',
                'tarif' => 85.00,
                'source' => 'static'
            ],
            [
                'id' => 5,
                'nom' => 'Dr. Nadia Chaabane',
                'specialite' => 'Nutrition Végétarienne',
                'ville' => 'Tunis',
                'adresse' => 'Lac 2, Tunis',
                'telephone' => '98 765 432',
                'email' => 'nadia.chaabane@vege-nutrition.tn',
                'site_web' => 'www.vege-nutrition-tunisie.tn',
                'description' => 'Accompagnement pour régimes végétariens.',
                'tarif' => 90.00,
                'source' => 'static'
            ],
            [
                'id' => 6,
                'nom' => 'Dr. Mohamed Ghanmi',
                'specialite' => 'Nutrition du Sportif',
                'ville' => 'Monastir',
                'adresse' => 'Complexe Sportif, Monastir',
                'telephone' => '73 987 654',
                'email' => 'm.ghanmi@elite-sport.tn',
                'site_web' => null,
                'description' => 'Nutritionniste officiel des équipes nationales.',
                'tarif' => 120.00,
                'source' => 'static'
            ],
            [
                'id' => 7,
                'nom' => 'Dr. Salma Abid',
                'specialite' => 'Nutrition Thérapeutique',
                'ville' => 'Bizerte',
                'adresse' => 'Avenue 7 Novembre, Bizerte',
                'telephone' => '72 123 456',
                'email' => 'salma.abid@nutrition.tn',
                'site_web' => null,
                'description' => 'Maladies chroniques et troubles digestifs.',
                'tarif' => 65.00,
                'source' => 'static'
            ],
            [
                'id' => 8,
                'nom' => 'Dr. Karim Ferchichi',
                'specialite' => 'Nutrition Sportive',
                'ville' => 'Gabès',
                'adresse' => 'Rue Ali Belhouane, Gabès',
                'telephone' => '75 654 321',
                'email' => 'k.ferchichi@sport-nutrition.tn',
                'site_web' => 'www.sport-nutrition-gabes.tn',
                'description' => 'Nutrition pour sportifs.',
                'tarif' => 75.00,
                'source' => 'static'
            ],
            [
                'id' => 9,
                'nom' => 'Dr. Sonia Mahjoub',
                'specialite' => 'Nutrition Prénatale',
                'ville' => 'Ariana',
                'adresse' => 'Clinique Ennasr, Ariana',
                'telephone' => '70 111 222',
                'email' => 'sonia.mahjoub@prenatal.tn',
                'site_web' => 'www.nutrition-prenatale.tn',
                'description' => 'Nutrition pendant la grossesse.',
                'tarif' => 95.00,
                'source' => 'static'
            ],
            [
                'id' => 10,
                'nom' => 'Dr. Youssef Hammami',
                'specialite' => 'Nutrition Gériatrique',
                'ville' => 'Tunis',
                'adresse' => 'Rue du Liban, El Menzah',
                'telephone' => '71 333 444',
                'email' => 'y.hammami@senior-nutrition.tn',
                'site_web' => null,
                'description' => 'Nutrition pour personnes âgées.',
                'tarif' => 85.00,
                'source' => 'static'
            ],
            [
                'id' => 11,
                'nom' => 'Dr. Imen Bouslimi',
                'specialite' => 'Troubles du Comportement Alimentaire',
                'ville' => 'Sfax',
                'adresse' => 'Centre Médical Sfax Plaza',
                'telephone' => '74 555 666',
                'email' => 'i.bouslimi@tca-consult.tn',
                'site_web' => 'www.tca-nutrition-sfax.tn',
                'description' => 'Prise en charge des TCA.',
                'tarif' => 100.00,
                'source' => 'static'
            ],
            [
                'id' => 12,
                'nom' => 'Dr. Riadh Chebbi',
                'specialite' => 'Nutrition du Sportif',
                'ville' => 'Kairouan',
                'adresse' => 'Complexe Sportif Kairouan',
                'telephone' => '77 777 888',
                'email' => 'r.chebbi@sport-kairouan.tn',
                'site_web' => null,
                'description' => 'Nutritionniste pour clubs sportifs.',
                'tarif' => 70.00,
                'source' => 'static'
            ],
            [
                'id' => 13,
                'nom' => 'Dr. Fatma Zouari',
                'specialite' => 'Diététique Thérapeutique',
                'ville' => 'Mahdia',
                'adresse' => 'Rue de la République, Mahdia',
                'telephone' => '73 999 000',
                'email' => 'f.zouari@nutrition-mahdia.tn',
                'site_web' => 'www.nutrition-mahdia.tn',
                'description' => 'Diabète, hypertension.',
                'tarif' => 65.00,
                'source' => 'static'
            ],
            [
                'id' => 14,
                'nom' => 'Dr. Ali Ben Youssef',
                'specialite' => 'Nutrition Sportive',
                'ville' => 'Ben Arous',
                'adresse' => 'Centre Sportif Ben Arous',
                'telephone' => '79 123 789',
                'email' => 'ali.benyoussef@performance.tn',
                'site_web' => null,
                'description' => 'Coach nutritionnel pour athlètes.',
                'tarif' => 110.00,
                'source' => 'static'
            ],
            [
                'id' => 15,
                'nom' => 'Dr. Samira Gharbi',
                'specialite' => 'Nutrition Végétarienne',
                'ville' => 'Hammamet',
                'adresse' => 'Zone Touristique, Hammamet',
                'telephone' => '72 456 123',
                'email' => 's.gharbi@vege-hammamet.tn',
                'site_web' => 'www.vegetarien-hammamet.tn',
                'description' => 'Consultations en anglais et français.',
                'tarif' => 95.00,
                'source' => 'static'
            ],
            [
                'id' => 16,
                'nom' => 'Dr. Tarek Saadi',
                'specialite' => 'Perte de Poids',
                'ville' => 'Gafsa',
                'adresse' => 'Avenue Farhat Hached, Gafsa',
                'telephone' => '76 321 654',
                'email' => 't.saadi@minceur-gafsa.tn',
                'site_web' => null,
                'description' => 'Programme minceur adapté.',
                'tarif' => 60.00,
                'source' => 'static'
            ]
        ];
    }
}
