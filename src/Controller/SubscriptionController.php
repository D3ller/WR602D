<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription')]
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {

        return $this->render(
            'subscription/index.html.twig', [
            'controller_name' => 'SubscriptionController',
            'subscriptions' => $subscriptionRepository->findAll(),
            ]
        );
    }

    #[Route('/subscription/{id}/subscribe', name: 'app_subscription_subscribe')]
    public function subscribe(int $id, SubscriptionRepository $subscriptionRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour souscrire à un abonnement.');
            return $this->redirectToRoute('app_login');
        }

        $subscription = $subscriptionRepository->find($id);
        if (!$subscription) {
            $this->addFlash('error', 'Abonnement introuvable.');
            return $this->redirectToRoute('app_subscription');
        }

        $user->setSubscription($subscription);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre abonnement a été mis à jour avec succès.');

        return $this->redirectToRoute('app_subscription');
    }
}
