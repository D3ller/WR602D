<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    #[Route('/auth/register', name: 'app_register')]
    public function index(): Response
    {
        return $this->render(
            'register/index.html.twig', [
            'controller_name' => 'RegisterController',
            ]
        );
    }
}
