<?php

// src/Form/UserProjectTaskType.php

namespace App\Form;

use App\Entity\Task;
use App\Entity\UserProjectTask;
use App\Repository\TaskRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProjectTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $project = $options['project'];

        $builder
            ->add('task', EntityType::class, [
                'class' => Task::class,
                'choice_label' => 'title',
                'query_builder' => function (TaskRepository $taskRepository) use ($project) {
                    return $taskRepository->createQueryBuilder('t')
                        ->leftJoin('t.userProjectTasks', 'upt')
                        ->andWhere('upt.project IS NULL OR upt.project != :project')
                        ->setParameter('project', $project);
                },
                'label' => 'Task',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Add Task',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserProjectTask::class,
            'project' => null,
        ]);
    }
}
