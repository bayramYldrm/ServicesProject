<?php

namespace App\Controller;

use App\Entity\UserActiv;
use App\Form\UserActiv1Type;
use App\Repository\UserActivRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_project_index', methods: ['GET'])]
    public function index(UserActivRepository $userActivRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'user_activs' => $userActivRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userActiv = new UserActiv();
        $form = $this->createForm(UserActiv1Type::class, $userActiv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // IP adresini ve tarih-saat bilgisini otomatik olarak ayarla
            $userActiv->setIp($request->getClientIp());

            // Saat dilimi ile tarih ve saat bilgisi ayarla
            $timezone = new \DateTimeZone('Europe/Istanbul');
            $userActiv->setCreatedAt(new \DateTime('now', $timezone));

            $entityManager->persist($userActiv);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/new.html.twig', [
            'user_activ' => $userActiv,
            'form' => $form,
        ]);
    }

    #[Route('/search', name: 'app_project_search', methods: ['GET'])]
    public function search(Request $request, UserActivRepository $userActivRepository): Response
    {
        $query = $request->query->get('query');
        $results = $userActivRepository->findByQuery($query);

        return $this->render('project/search.html.twig', [
            'query' => $query,
            'results' => $results,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(UserActiv $userActiv): Response
    {
        // Servisleri almak için gerekli metod
        $services = $userActiv->getUserActivServices(); // Servisleri döndüren metod, entity yapılandırmanıza bağlı olarak değişebilir

        return $this->render('project/show.html.twig', [
            'user_activ' => $userActiv,
            'services' => $services,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserActiv $userActiv, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserActiv1Type::class, $userActiv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/edit.html.twig', [
            'user_activ' => $userActiv,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, UserActiv $userActiv, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userActiv->getId(), $request->request->get('_token'))) {
            $entityManager->remove($userActiv);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/project/ajax_save', name: 'app_project_ajax_save', methods: ['POST'])]
    public function saveProject(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $owner = $data['owner'] ?? '';
        $projectName = $data['projectName'] ?? '';

        if (!$owner || !$projectName) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Owner and Project Name are required!'
            ], Response::HTTP_BAD_REQUEST);
        }

        $userActiv = new UserActiv();
        $userActiv->setOwner($owner);
        $userActiv->setProjectName($projectName);
        $userActiv->setIp($request->getClientIp());

        $timezone = new \DateTimeZone('Europe/Istanbul');
        $userActiv->setCreatedAt(new \DateTime('now', $timezone));

        $entityManager->persist($userActiv);
        $entityManager->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Project successfully saved!'
        ]);
    }


    // src/Controller/ProjectController.php

    #[Route('/projects/data', name: 'app_project_data', methods: ['GET'])]
    public function getData(UserActivRepository $userActivRepository): JsonResponse
    {
        $projects = $userActivRepository->findAll();
        $data = [];

        foreach ($projects as $project) {
            $data[] = [
                'id' => $project->getId(),
                'owner' => $project->getOwner(),
                'projectName' => $project->getProjectName(),
                'createdAt' => $project->getCreatedAt()->format('Y-m-d H:i:s'),
                'ip' => $project->getIp(),
            ];
        }

        return new JsonResponse(['projects' => $data]);
    }



}
