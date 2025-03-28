<?php

namespace App\Controller;

use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(FileRepository $fileRepository): Response
    {
        $user = $this->getUser();
        $pdfCount = $fileRepository->countUserFilesThisMonth($user->getId());

        return $this->render(
            'dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $user,
            'pdfCount' => $pdfCount,
            ]
        );
    }

    #[Route('/history', name: 'app_dashboard_historique')]
    public function historique(): Response
    {
        return $this->render(
            'dashboard/history.html.twig', [
            'controller_name' => 'DashboardController',
            ]
        );
    }
}
