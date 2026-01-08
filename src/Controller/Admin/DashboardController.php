<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Nutritionniste;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        $stats = [
            'total_users' => $this->entityManager->getRepository(User::class)->count([]),
            'verified_users' => $this->entityManager->getRepository(User::class)->count(['isVerified' => true]),
            'total_articles' => $this->entityManager->getRepository(Article::class)->count([]),
            'total_nutritionists' => $this->entityManager->getRepository(Nutritionniste::class)->count([]),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span class="text-primary">Health</span><span class="text-secondary">Fit</span> Admin')
            ->setFaviconPath('build/images/favicon-admin.ico')
            ->renderSidebarMinimized(false);
    }

    public function configureAssets(): \EasyCorp\Bundle\EasyAdminBundle\Config\Assets
    {
        return parent::configureAssets()
            ->addCssFile('assets/styles/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Article::class);
        yield MenuItem::linkToCrud('Nutritionnistes', 'fas fa-user-md', Nutritionniste::class);
    }

    public function configureUserMenu(\Symfony\Component\Security\Core\User\UserInterface $user): \EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu
    {
        if (!$user instanceof User) {
            return parent::configureUserMenu($user);
        }

        return parent::configureUserMenu($user)
            ->setName($user->getFullName())
            ->setAvatarUrl($user->getProfileImageUrl());
    }
}
