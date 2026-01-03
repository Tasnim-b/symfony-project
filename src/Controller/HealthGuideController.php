<?php
// src/Controller/HealthGuideController.php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HealthGuideController extends AbstractController
{
    #[Route('/guide-sante', name: 'app_guide_sante')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupère tous les articles depuis la base
        $articles = $entityManager->getRepository(Article::class)->findAll();

        // Si pas d'articles, en créer quelques-uns
        if (empty($articles)) {
            $this->createSampleArticles($entityManager);
            $articles = $entityManager->getRepository(Article::class)->findAll();
        }

        return $this->render('health_guide/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/articles/{id}', name: 'app_article_show')]
    public function showArticle(Article $article): Response
    {
        // Vérifier si le contenu est une URL externe
        $isExternalUrl = $this->isExternalUrl($article->getContent());

        if ($isExternalUrl) {
            // Rediriger directement vers l'URL externe
            return $this->redirect($article->getContent());
        }

        return $this->render('health_guide/article.html.twig', [
            'article' => $article
        ]);
    }

    private function createSampleArticles(EntityManagerInterface $entityManager): void
    {
        $articlesData = [
            [
                'title' => '💧 Les 8 verres d\'eau par jour : Mythe ou réalité ?',
                'excerpt' => 'Découvrez la vérité sur l\'hydratation quotidienne et ses bienfaits pour la santé...',
                'content' => 'https://www.doctissimo.fr/sante/nutrition/hydratation/eau-bienfaits',
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Hydratation',
                'author' => 'Dr. Sophie Martin',
                'date' => new \DateTime('2024-10-15'),
                'likes' => 245,
                'comments' => 42,
                'shares' => 18,
                'read_time' => '4 min'
            ],
            [
                'title' => '😴 Le secret d\'un sommeil réparateur',
                'excerpt' => '7 astuces simples pour améliorer la qualité de votre sommeil et votre santé globale...',
                'content' => 'https://www.passeportsante.net/fr/Actualites/Dossiers/DossierComplexe.aspx?doc=conseils-pour-mieux-dormir',
                'image' => 'https://images.unsplash.com/photo-1548600916-dc8492f8e845?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Sommeil',
                'author' => 'Dr. Jean Dupont',
                'date' => new \DateTime('2024-10-14'),
                'likes' => 189,
                'comments' => 31,
                'shares' => 12,
                'read_time' => '5 min'
            ],
            [
                'title' => '🥗 5 super-aliments pour booster votre immunité',
                'excerpt' => 'Ces aliments accessibles peuvent renforcer votre système immunitaire naturellement...',
                'content' => 'https://www.lanutrition.fr/bien-dans-son-assiette/les-aliments-sante/les-super-aliments',
                'image' => 'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Nutrition',
                'author' => 'Nutritionniste Claire',
                'date' => new \DateTime('2024-10-13'),
                'likes' => 312,
                'comments' => 56,
                'shares' => 24,
                'read_time' => '6 min'
            ],
            [
                'title' => '🏃‍♀️ 30 minutes de marche par jour : Les bénéfices incroyables',
                'excerpt' => 'Découvrez comment une simple marche quotidienne peut transformer votre santé...',
                'content' => 'https://www.santemagazine.fr/sante/sante-pratique/marche-a-pied-bienfaits-170984',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Exercice',
                'author' => 'Coach Sportif Marc',
                'date' => new \DateTime('2024-10-12'),
                'likes' => 156,
                'comments' => 28,
                'shares' => 9,
                'read_time' => '3 min'
            ],
            [
                'title' => '🧠 10 techniques de méditation pour réduire le stress',
                'excerpt' => 'Apprenez des méthodes simples de méditation pour apaiser votre esprit au quotidien...',
                'content' => 'https://www.psychologies.com/Bien-etre/Meditation/Articles-et-dossiers/10-exercices-de-meditation-pour-debutants',
                'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Mental',
                'author' => 'Pr. Antoine Leclerc',
                'date' => new \DateTime('2024-10-11'),
                'likes' => 278,
                'comments' => 45,
                'shares' => 21,
                'read_time' => '7 min'
            ],
            [
                'title' => '🍏 Le régime méditerranéen : La clé d\'une longue vie en bonne santé',
                'excerpt' => 'Pourquoi le régime méditerranéen est considéré comme l\'un des plus sains au monde...',
                'content' => 'https://www.futura-sciences.com/sante/dossiers/nutrition-regime-mediterraneen-veritable-atout-sante-1530/',
                'image' => 'https://images.unsplash.com/photo-1493770348161-369560ae357d?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Nutrition',
                'author' => 'Dr. Elena Rossi',
                'date' => new \DateTime('2024-10-10'),
                'likes' => 198,
                'comments' => 33,
                'shares' => 15,
                'read_time' => '8 min'
            ],
            [
                'title' => '💪 Renforcement musculaire sans matériel : Le guide complet',
                'excerpt' => 'Exercices efficaces que vous pouvez faire à la maison pour développer votre masse musculaire...',
                'content' => 'https://www.musculation.com/wikibody/renforcement-musculaire-sans-materiel/',
                'image' => 'https://images.unsplash.com/photo-1534367507877-0edd93bd013b?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Exercice',
                'author' => 'Coach Sarah',
                'date' => new \DateTime('2024-10-09'),
                'likes' => 324,
                'comments' => 67,
                'shares' => 29,
                'read_time' => '10 min'
            ],
            [
                'title' => '🩺 Prévention des maladies cardiovasculaires : Les gestes qui sauvent',
                'excerpt' => 'Comment réduire votre risque de maladies cardiaques grâce à des habitudes simples...',
                'content' => 'https://www.fedecardio.org/Je-m-informe/Prevention',
                'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Prévention',
                'author' => 'Dr. Thomas Moreau',
                'date' => new \DateTime('2024-10-08'),
                'likes' => 187,
                'comments' => 29,
                'shares' => 11,
                'read_time' => '6 min'
            ],
            [
                'title' => '🌿 Les bienfaits des plantes médicinales sur la santé',
                'excerpt' => 'Découvrez comment les plantes traditionnelles peuvent soutenir votre santé naturelle...',
                'content' => 'https://www.plantes-et-sante.fr/',
                'image' => 'https://images.unsplash.com/photo-1516557070061-b0e87b5c6f8d?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Conseils',
                'author' => 'Herboriste Léa',
                'date' => new \DateTime('2024-10-07'),
                'likes' => 231,
                'comments' => 38,
                'shares' => 17,
                'read_time' => '5 min'
            ],
            [
                'title' => '🧘‍♂️ Yoga pour débutants : Les postures essentielles',
                'excerpt' => 'Initiez-vous au yoga avec ces postures de base bénéfiques pour le corps et l\'esprit...',
                'content' => 'https://www.yogajournal.fr/yoga-pour-debutants/',
                'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Exercice',
                'author' => 'Prof. de Yoga Ananda',
                'date' => new \DateTime('2024-10-06'),
                'likes' => 276,
                'comments' => 52,
                'shares' => 24,
                'read_time' => '9 min'
            ],
            [
                'title' => '🥑 Les graisses saines : Amies ou ennemies de votre santé ?',
                'excerpt' => 'Tout ce que vous devez savoir sur les différentes graisses et leurs effets sur la santé...',
                'content' => 'https://www.lanutrition.fr/bien-dans-son-assiette/les-macronutriments/les-lipides',
                'image' => 'https://images.unsplash.com/photo-1529312266912-b33cfce2eefd?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Nutrition',
                'author' => 'Dr. Nutrition',
                'date' => new \DateTime('2024-10-05'),
                'likes' => 192,
                'comments' => 34,
                'shares' => 14,
                'read_time' => '6 min'
            ],
            [
                'title' => '🏊‍♂️ La natation : Le sport complet par excellence',
                'excerpt' => 'Pourquoi la natation est considérée comme l\'un des sports les plus bénéfiques pour le corps...',
                'content' => 'https://www.santemagazine.fr/sport/sports-aquatiques/natation-bienfaits-sante-170992',
                'image' => 'https://images.unsplash.com/photo-1530549387789-4c1017266635?w=1200&auto=format&fit=crop&q=80',
                'category' => 'Exercice',
                'author' => 'Coach Aquatique',
                'date' => new \DateTime('2024-10-04'),
                'likes' => 203,
                'comments' => 41,
                'shares' => 16,
                'read_time' => '5 min'
            ]
        ];

        foreach ($articlesData as $data) {
            $article = new Article();
            $article->setTitle($data['title']);
            $article->setExcerpt($data['excerpt']);
            $article->setContent($data['content']);
            $article->setImage($data['image']);
            $article->setCategory($data['category']);
            $article->setAuthor($data['author']);
            $article->setDate($data['date']);
            $article->setLikes($data['likes']);
            $article->setComments($data['comments']);
            $article->setShares($data['shares']);
            $article->setReadTime($data['read_time']);
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($article);
        }

        $entityManager->flush();
    }

    #[Route('/article/nouveau', name: 'app_article_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $article = new Article();
            $article->setTitle($request->request->get('title'));
            $article->setExcerpt($request->request->get('excerpt'));
            $article->setContent($request->request->get('content'));
            $article->setCategory($request->request->get('category'));
            $article->setAuthor($request->request->get('author'));
            $article->setReadTime($request->request->get('read_time'));

            $imageUrl = $request->request->get('image');
            if (empty($imageUrl)) {
                $defaultImages = [
                    'Nutrition' => 'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=1200&auto=format&fit=crop&q=80',
                    'Exercice' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1200&auto=format&fit=crop&q=80',
                    'Sommeil' => 'https://images.unsplash.com/photo-1548600916-dc8492f8e845?w=1200&auto=format&fit=crop&q=80',
                    'Mental' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&auto=format&fit=crop&q=80',
                    'Hydratation' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&auto=format&fit=crop&q=80',
                    'Prévention' => 'https://images.unsplash.com/photo-1584467735871-8db9ac8d0916?w=1200&auto=format&fit=crop&q=80',
                    'Conseils' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1200&auto=format&fit=crop&q=80'
                ];
                $category = $article->getCategory();
                $imageUrl = $defaultImages[$category] ?? 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&auto=format&fit=crop&q=80';
            }
            $article->setImage($imageUrl);

            $article->setDate(new \DateTime());
            $article->setLikes(0);
            $article->setComments(0);
            $article->setShares(0);
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', '🎉 Article publié avec succès !');

            return $this->redirectToRoute('app_guide_sante');
        }

        return $this->render('health_guide/new.html.twig');
    }

    #[Route('/articles/{id}/delete', name: 'app_article_delete', methods: ['DELETE'])]
    public function deleteArticle(Article $article, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($article);
            $entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Article supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }

    #[Route('/articles/{id}/supprimer', name: 'app_article_delete_get', methods: ['GET'])]
    public function deleteArticleGet(Article $article, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article supprimé avec succès !');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression');
        }

        return $this->redirectToRoute('app_guide_sante');
    }

    #[Route('/articles/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function editArticle(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $article->setTitle($request->request->get('title'));
            $article->setExcerpt($request->request->get('excerpt'));
            $article->setContent($request->request->get('content'));
            $article->setCategory($request->request->get('category'));
            $article->setAuthor($request->request->get('author'));
            $article->setReadTime($request->request->get('read_time'));

            $imageUrl = $request->request->get('image');
            if (!empty($imageUrl)) {
                $article->setImage($imageUrl);
            }

            $article->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->flush();

            $this->addFlash('success', 'Article modifié avec succès !');
            return $this->redirectToRoute('app_guide_sante');
        }

        return $this->render('health_guide/edit.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * Vérifie si une chaîne est une URL externe
     */
    private function isExternalUrl(string $content): bool
    {
        return preg_match('/^https?:\/\//', $content) === 1;
    }
}
