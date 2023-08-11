<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PanelController extends AbstractController
{
    #[Route('/panel', name: 'app_panel_index')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $breadcrumbs['items'] = [
            [
                "title" => "Panel",
                "url" => $this->generateUrl('app_panel_index')
            ]
        ];
        $breadcrumbs['last'] = "Anasayfa";
        return $this->render('panel/index.html.twig', [
            'controller_name' => 'PanelController',
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

}
