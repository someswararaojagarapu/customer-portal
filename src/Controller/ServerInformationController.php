<?php

namespace App\CustomerPortal\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ServerInformationController extends AbstractController
{
    #[Route('/server/information/view', name: 'server_information_view')]
    public function filterList() {
        return $this->render('base.html.twig', []);
    }
}
