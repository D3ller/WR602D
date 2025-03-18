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
    private ParameterBagInterface $params;


    public function __construct(
        private HttpClientInterface $client,
        Security                    $security,
        EntityManagerInterface      $em,
        FileRepository              $fileRepository,
        ParameterBagInterface       $params
    )
    {
        $this->security = $security;
        $this->em = $em;
        $this->fileRepository = $fileRepository;
        $this->params = $params;
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

        $ntfy = $this->client->request('POST', 'https://ntfy.sh/archivecorefr', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'pdfCount' => $pdfCount,
                'pdfMax' => $pdfMax,
            ]),
        ]);

        if ($pdfCount >= $pdfMax) {
            return [
                'message' => "Limite de génération de PDFs atteinte pour ce mois, augmentez votre abonnement",
                'statusCode' => 429
            ];
        }

        $response = $this->client->request('POST', $gotenBerg_URL . '/forms/chromium/convert/url', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'body' => (['url' => $url]),
        ]);

        if ($response->getStatusCode() !== 200) {
            return [
                'message' => "An error occurred during conversion",
                'statusCode' => $response->getStatusCode(),
                'error' => $response->getContent(false)
            ];
        } else {

            $file = new File();
            $file->setName('/pdf/' . time() . '.pdf');
            $user = $this->security->getUser();

            $file->setAccount($user);
            $file->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($file);
            $this->em->flush();

            return $response->getContent();
        }

    }

    /**
     * @throws TransportExceptionInterface
     */
    public function htmlToPDF(string $html)
    {
        $gotenBerg_URL = $_ENV["GOTENBERG_API_URL"];

        $htmlDirectory = ('kernel.project_dir') . '/public/html/';

        $filesystem = new Filesystem();
        if (!$filesystem->exists($htmlDirectory)) {
            $filesystem->mkdir($htmlDirectory, 0777);
        }

        $filename = time() . '.html';
        $filePath = $htmlDirectory . $filename;
        file_put_contents($filePath, $html);

        return $filePath;

    }
}
