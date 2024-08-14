<?php

namespace App\Controller;

use App\Entity\UserActiv;
use App\Entity\Service;
use App\Entity\UserActivService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserActivServiceController extends AbstractController
{
    #[Route('/user/activ/service', name: 'app_user_activ_service')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $userActivs = $entityManager->getRepository(UserActiv::class)->findAll();
        return $this->render('user_activ_service/index.html.twig', [
            'userActivs' => $userActivs,
        ]);
    }

    #[Route('/user/activ/service/add/{id}', name: 'add_service_form')]
    public function addServiceForm(int $id, EntityManagerInterface $entityManager): Response
    {
        $userActiv = $entityManager->getRepository(UserActiv::class)->find($id);
        $services = $entityManager->getRepository(Service::class)->findAll();

        if (!$userActiv) {
            throw $this->createNotFoundException('UserActiv not found');
        }

        return $this->render('user_activ_service/add_service.html.twig', [
            'userActiv' => $userActiv,
            'services' => $services,
        ]);
    }

    #[Route('/user/activ/service/add', name: 'add_service_to_user', methods: ['POST'])]
    public function addServiceToUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userActivId = $request->request->get('userActivId');
        $serviceId = $request->request->get('serviceId');

        $userActiv = $entityManager->getRepository(UserActiv::class)->find($userActivId);
        $service = $entityManager->getRepository(Service::class)->find($serviceId);

        if (!$userActiv || !$service) {
            return new Response('UserActiv or Service not found.', Response::HTTP_NOT_FOUND);
        }

        $userActivService = new UserActivService();
        $userActivService->setUserActiv($userActiv);
        $userActivService->setService($service);

        $entityManager->persist($userActivService);
        $entityManager->flush();

        return $this->redirectToRoute('edit_services', ['id' => $userActivId]);
    }

    #[Route('/user/activ/service/edit/{id}', name: 'edit_services')]
    public function editServices(int $id, EntityManagerInterface $entityManager): Response
    {
        $userActiv = $entityManager->getRepository(UserActiv::class)->find($id);
        $userActivServices = $entityManager->getRepository(UserActivService::class)->findBy(['userActiv' => $userActiv]);

        if (!$userActiv) {
            throw $this->createNotFoundException('UserActiv not found');
        }

        return $this->render('user_activ_service/edit_services.html.twig', [
            'userActiv' => $userActiv,
            'userActivServices' => $userActivServices,
        ]);
    }

    #[Route('/user/activ/service/remove', name: 'remove_service_from_user', methods: ['POST'])]
    public function removeServiceFromUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userActivId = $request->request->get('userActivId');
        $serviceId = $request->request->get('serviceId');

        $userActiv = $entityManager->getRepository(UserActiv::class)->find($userActivId);
        $service = $entityManager->getRepository(Service::class)->find($serviceId);

        if (!$userActiv || !$service) {
            return new Response('UserActiv or Service not found.', Response::HTTP_NOT_FOUND);
        }

        $userActivService = $entityManager->getRepository(UserActivService::class)->findOneBy([
            'userActiv' => $userActiv,
            'service' => $service,
        ]);

        if ($userActivService) {
            $entityManager->remove($userActivService);
            $entityManager->flush();
        }

        return $this->redirectToRoute('edit_services', ['id' => $userActivId]);
    }
}
