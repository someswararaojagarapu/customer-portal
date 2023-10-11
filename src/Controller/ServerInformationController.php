<?php

namespace App\CustomerPortal\Controller;

use App\CustomerPortal\Manager\FileReaderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ServerInformationController extends AbstractController
{
    #[Route('/server/information/view', name: 'server_information_view')]
    public function serverInformationView(FileReaderManager $fileReaderManager) {
        try {
            $serverInfoJson = $fileReaderManager->readJson();

            return $this->render('server_information.html.twig', ['serverInfoJson' => $serverInfoJson]);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
