<?php

// src/Controller/UserProjectTaskController.php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\UserActiv;
use App\Entity\UserProjectTask;
use App\Form\UserProjectTaskType;
use App\Repository\TaskRepository;
use App\Repository\UserActivRepository;
use App\Repository\UserProjectTaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProjectTaskController extends AbstractController
{
    private LoggerInterface $logger; // Logger özelliğini tanımlayın

    public function __construct(LoggerInterface $logger) // Dependency Injection ile Logger'ı alın
    {
        $this->logger = $logger;
    }

    #[Route('/user/project/{projectId}/task/add', name: 'user_project_task_add', methods: ['GET', 'POST'])]
    public function addTaskToProject(
        Request $request,
        UserActivRepository $userActivRepository,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager,
        int $projectId
    ): Response {
        $project = $userActivRepository->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $userProjectTask = new UserProjectTask();
        $form = $this->createForm(UserProjectTaskType::class, $userProjectTask, [
            'project' => $project,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userProjectTask->setProject($project);
            $entityManager->persist($userProjectTask);
            $entityManager->flush();

            $this->addFlash('success', 'Task successfully added to the project.');

            return $this->redirectToRoute('user_project_task_list', ['projectId' => $projectId]);
        }

        return $this->render('user_project_task/add_task.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }

    #[Route('/user/project/{projectId}/tasks', name: 'user_project_task_list', methods: ['GET'])]
    public function listTasks(UserActivRepository $userActivRepository, int $projectId): Response
    {
        $project = $userActivRepository->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $tasks = $project->getUserProjectTasks();

        return $this->render('user_project_task/list_tasks.html.twig', [
            'project' => $project,
            'tasks' => $tasks,
        ]);
    }

    #[Route('/user/project/{projectId}/task/{taskId}/delete', name: 'user_project_task_delete', methods: ['POST'])]
    public function deleteTask(
        Request $request,
        UserProjectTaskRepository $userProjectTaskRepository,
        EntityManagerInterface $entityManager,
        int $projectId,
        int $taskId
    ): Response {
        // CSRF token kontrolü
        $csrfToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete' . $taskId, $csrfToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        // Görevi bul
        $userProjectTask = $userProjectTaskRepository->findOneByProjectAndTask($projectId, $taskId);

        if (!$userProjectTask) {
            throw $this->createNotFoundException('Task not found or does not belong to the specified project');
        }

        // Görevi sil
        $entityManager->remove($userProjectTask);
        $entityManager->flush();

        $this->addFlash('success', 'Task successfully deleted.');

        return $this->redirectToRoute('user_project_task_list', ['projectId' => $projectId]);
    }
}
