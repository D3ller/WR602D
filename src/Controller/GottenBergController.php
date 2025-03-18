<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use App\Service\Gotenberg;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class GottenBergController extends AbstractController
{
    #[Route('/generate/url', name: 'app_gotten_berg_url')]
    public function generateFromUrl(Gotenberg $gotenberg, Request $request): Response
    {
        $url = $request->query->get('url');

        if ($request->isMethod('GET') && $url) {
            try {
                $pdfContent = $gotenberg->getPDFbyURL(urldecode($url));

                if (!is_string($pdfContent)) {
                    $this->addFlash('error', $pdfContent['message']);
                    if($pdfContent['statusCode'] === 429) {
                        return $this->redirectToRoute('app_subscription');
                    } else {
                        return $this->redirectToRoute('app_gotten_berg_url');
                    }
                }

                if (!$pdfContent) {
                    throw new \Exception('Erreur lors de la génération du PDF.');
                }

                $pdfDirectory = $this->getParameter('kernel.project_dir') . '/public/pdf/';

                $filesystem = new Filesystem();
                if (!$filesystem->exists($pdfDirectory)) {
                    $filesystem->mkdir($pdfDirectory, 0777);
                }

                $safeFilename = time();
                $filename = $safeFilename . '.pdf';
                $filePath = $pdfDirectory . $filename;

                file_put_contents($filePath, $pdfContent);

                $publicUrl = '/pdf/' . $filename;

                return new RedirectResponse($publicUrl);
            } catch (\Exception $e) {
                return new Response('Erreur : ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $form = $this->createFormBuilder()
            ->add('url', null, ['required' => true, 'label' => false, 'attr' => ['placeholder' => 'https://google.com']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->getData()['url'];

            return $this->redirectToRoute('app_gotten_berg_url', [
                'url' => $url
            ]);
        }

        return $this->render('gotten_berg/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/generate/html', name: 'app_gotten_berg_html')]
    public function generateFromHtml(Gotenberg $gotenberg, Request $request): Response
    {

        $form = $this->createFormBuilder()
            ->add('html', TextareaType::class, ['required' => true, 'label' => false, 'attr' => ['class' => 'form-control', 'placeholder' => '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>My PDF</title>
  </head>
  <body>
    <h1>Hello world!</h1>
  </body>
</html>']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $html = $form->getData()['html'];
            return $gotenberg->htmlToPDF($html);
        }

        return $this->render('gotten_berg/html.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
