<?php
namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('status', TextType::class, [
                'label' => 'Status',
                'required' => false,
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'Priority',
                'required' => false,
            ])
            ->add('created_at', DateTimeType::class, [
                'label' => 'Created At',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['readonly' => true],
                'data' => $options['data']->getCreatedAt() ?? new \DateTimeImmutable('Europe/Istanbul'), // Default value handling
            ])
            ->add('updated_at', DateTimeType::class, [
                'label' => 'Updated At',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['readonly' => true],
                'data' => $options['data']->getUpdatedAt() ?? new \DateTimeImmutable('Europe/Istanbul'), // Default value handling
            ])
            ->add('file_path', FileType::class, [
                'label' => 'File (PDF file)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'application/pdf',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
