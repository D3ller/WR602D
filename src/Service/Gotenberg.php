<?php

namespace App\Service;

use App\Entity\File;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Gotenberg
{
    private Security $security;
    private EntityManagerInterface $em;
    private FileRepository $fileRepository;


    public function __construct(
        private HttpClientInterface $client,
        Security                    $security,
        EntityManagerInterface      $em,
        FileRepository              $fileRepository,
    ) {
        $this->security = $security;
        $this->em = $em;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public
    function getPDFbyURL(string $url): array|string
    {
        $gotenBerg_URL = $_ENV["GOTENBERG_API_URL"];
        $user = $this->security->getUser();

        if (!$user) {
            return ['message' => "User not authenticated", 'statusCode' => 403];
        }

        $subscription = $user->getSubscription();
        $pdfMax = $subscription ? $subscription->getMaxPdf() : 0;
        $pdfCount = $this->fileRepository->countUserFilesThisMonth($user->getId());

        if ($pdfCount >= $pdfMax) {
            return [
                'message' => "Limite de génération de PDFs atteinte pour ce mois, augmentez votre abonnement",
                'statusCode' => 429
            ];
        }

        $response = $this->client->request(
            'POST', $gotenBerg_URL . '/forms/chromium/convert/url', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'body' => ['url' => $url],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            $result = [
                'message' => "An error occurred during conversion",
                'statusCode' => $statusCode,
                'error' => $response->getContent(false)
            ];
        } else {
            $file = new File();
            $file->setName('/pdf/' . time() . '.pdf');
            $file->setAccount($user);
            $file->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($file);
            $this->em->flush();

            $result = $response->getContent();
        }

        return $result;
    }

    /**
     * @throws TransportExceptionInterface
     */
}
