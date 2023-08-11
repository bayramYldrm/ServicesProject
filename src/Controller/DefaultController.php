<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{
    #[Route('/default', name: 'app_default')]
    #[IsGranted('ROLE_PROTOKOL_VIEW')]
    public function index(): Response
    {
         $breadcrumbs['items'] = [
            [
                "title" => "Home",
                "url" => $this->generateUrl('app_default')
            ]
        ];
        $breadcrumbs['last'] = "Anasayfa";
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

}
