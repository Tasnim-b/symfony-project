<?php
// src/Controller/AdminController.php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
// #[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        // Statistiques
        $totalArticles = $entityManager->getRepository(Article::class)->count([]);
        $totalUsers = $entityManager->getRepository(User::class)->count([]);
        $recentArticles = $entityManager->getRepository(Article::class)
            ->findBy([], ['createdAt' => 'DESC'], 5);

        // Articles par catégorie
        $articlesByCategory = $entityManager->createQuery(
            'SELECT a.category, COUNT(a.id) as count
             FROM App\Entity\Article a
             WHERE a.category IS NOT NULL
             GROUP BY a.category'
        )->getResult();

        return $this->render('admin/dashboard.html.twig', [
            'totalArticles' => $totalArticles,
            'totalUsers' => $totalUsers,
            'recentArticles' => $recentArticles,
            'articlesByCategory' => $articlesByCategory,
        ]);
    }

    #[Route('/articles', name: 'app_admin_articles')]
    public function articles(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/{id}/toggle-status', name: 'app_admin_article_toggle_status', methods: ['POST'])]
    public function toggleArticleStatus(Article $article, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('toggle-status'.$article->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_admin_articles');
        }

        // Basculer le statut de publication
        // Adaptez cette logique selon vos champs d'entité
        if (method_exists($article, 'setPublished')) {
            $article->setPublished(!$article->isPublished());
            $status = $article->isPublished() ? 'publié' : 'dépublié';
            $message = "Article $status avec succès";
        } elseif (method_exists($article, 'setIsActive')) {
            $article->setIsActive(!$article->getIsActive());
            $status = $article->getIsActive() ? 'activé' : 'désactivé';
            $message = "Article $status avec succès";
        } else {
            // Si vous n'avez pas de champ de statut, ajoutez-en un dans votre entité
            $this->addFlash('error', 'Fonctionnalité non disponible - Ajoutez un champ de statut à l\'entité Article');
            return $this->redirectToRoute('app_admin_articles');
        }

        $entityManager->flush();
        $this->addFlash('success', $message);

        return $this->redirectToRoute('app_admin_articles');
    }

    #[Route('/articles/{id}/delete-admin', name: 'app_admin_article_delete', methods: ['POST'])]
    public function deleteArticleAdmin(Article $article, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article supprimé avec succès');
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('app_admin_articles');
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}/toggle-role', name: 'app_admin_user_toggle_role', methods: ['POST'])]
    public function toggleUserRole(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('toggle-role'.$user->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_admin_users');
        }

        // Empêcher l'auto-modification (l'admin ne peut pas se retirer ses propres droits)
        if ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier vos propres rôles');
            return $this->redirectToRoute('app_admin_users');
        }

        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            // Retirer le rôle admin
            $user->setRoles(array_values(array_diff($roles, ['ROLE_ADMIN'])));
            $message = 'Rôle admin retiré avec succès';
        } else {
            // Ajouter le rôle admin
            $user->setRoles(array_merge($roles, ['ROLE_ADMIN']));
            $message = 'Rôle admin ajouté avec succès';
        }

        $entityManager->flush();
        $this->addFlash('success', $message);

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/statistics', name: 'app_admin_statistics')]
    public function statistics(EntityManagerInterface $entityManager): Response
    {
        // Statistiques détaillées
        $articlesPerMonth = $entityManager->createQuery(
            "SELECT DATE_FORMAT(a.createdAt, '%Y-%m') as month,
                    COUNT(a.id) as count
             FROM App\Entity\Article a
             WHERE a.createdAt >= DATE_SUB(CURRENT_DATE(), 6, 'MONTH')
             GROUP BY month
             ORDER BY month DESC"
        )->getResult();

        $popularCategories = $entityManager->createQuery(
            'SELECT a.category,
                    COUNT(a.id) as total,
                    SUM(a.likes) as totalLikes,
                    SUM(a.comments) as totalComments
             FROM App\Entity\Article a
             WHERE a.category IS NOT NULL
             GROUP BY a.category
             ORDER BY total DESC'
        )->getResult();

        $topArticles = $entityManager->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->orderBy('a.likes', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->render('admin/statistics.html.twig', [
            'articlesPerMonth' => $articlesPerMonth,
            'popularCategories' => $popularCategories,
            'topArticles' => $topArticles,
        ]);
    }

    #[Route('/settings', name: 'app_admin_settings')]
    public function settings(Request $request): Response
    {
        // Ici vous pouvez gérer les paramètres du site
        // Pour l'instant, une page simple

        return $this->render('admin/settings.html.twig');
    }

    #[Route('/test', name: 'app_admin_test')]
    public function test(): Response
    {
        return new Response('TEST ADMIN - ACCÈS AUTORISÉ !');
    }
}
