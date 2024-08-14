<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\UserActiv;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/comments')]
class CommentController extends AbstractController
{
    private CommentRepository $commentRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CommentRepository $commentRepository, EntityManagerInterface $entityManager)
    {
        $this->commentRepository = $commentRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/{userActiv}', name: 'comment_index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserActiv $userActiv, UserInterface $user): Response
    {
        $newComment = new Comment();
        $newComment->setUser($user);
        $newComment->setUserActiv($userActiv);
        $newComment->setCreatedAt(new \DateTimeImmutable());
        $newComment->setStatus('active');
        $newComment->setLikes(0);
        $newComment->setDislikes(0);
        $newComment->setReported(0);

        $newCommentForm = $this->createForm(CommentType::class, $newComment);
        $newCommentForm->handleRequest($request);

        if ($newCommentForm->isSubmitted() && $newCommentForm->isValid()) {
            $this->entityManager->persist($newComment);
            $this->entityManager->flush();
            return $this->redirectToRoute('comment_index', ['userActiv' => $userActiv->getId()], Response::HTTP_SEE_OTHER);
        }

        $comments = $this->entityManager->getRepository(Comment::class)->findBy(['userActiv' => $userActiv], ['createdAt' => 'DESC']);

        $editForms = [];
        $replyForms = [];

        foreach ($comments as $comment) {
            if ($this->isGranted('edit', $comment)) {
                $editForm = $this->createForm(CommentType::class, $comment, [
                    'action' => $this->generateUrl('comment_edit', ['id' => $comment->getId()]),
                    'method' => 'POST'
                ]);
                $editForms[$comment->getId()] = $editForm->createView();
            }

            if ($this->isGranted('reply', $comment)) {
                $replyForm = $this->createForm(CommentType::class, (new Comment())->setParent($comment), [
                    'action' => $this->generateUrl('comment_reply', ['id' => $comment->getId()]),
                    'method' => 'POST'
                ]);
                $replyForms[$comment->getId()] = $replyForm->createView();
            }
        }

        return $this->render('comment/index.html.twig', [
            'userActiv' => $userActiv,
            'comments' => $comments,
            'newCommentForm' => $newCommentForm->createView(),
            'editForms' => $editForms,
            'replyForms' => $replyForms,
        ]);
    }

    #[Route('/{id}/edit', name: 'comment_edit', methods: ['POST'])]
    public function edit(Request $request, Comment $comment): Response
    {
        $this->denyAccessUnlessGranted('edit', $comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            return $this->redirectToRoute('comment_index', ['userActiv' => $comment->getUserActiv()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment): Response
    {
        //$this->denyAccessUnlessGranted('delete', $comment);

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('comment_index', ['userActiv' => $comment->getUserActiv()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reply', name: 'comment_reply', methods: ['POST'])]
    public function reply(Request $request, Comment $parentComment, UserInterface $user): Response
    {
        $reply = new Comment();
        $reply->setUser($user);
        $reply->setUserActiv($parentComment->getUserActiv());
        $reply->setParent($parentComment);
        $reply->setCreatedAt(new \DateTimeImmutable());
        $reply->setStatus('active');
        $reply->setLikes(0);
        $reply->setDislikes(0);
        $reply->setReported(0);
        $reply->setContent($request->request->get('comment'));

        $form = $this->createForm(CommentType::class, $reply);
        $form->handleRequest($request);

        $newComment = new Comment();
        $newComment->setUser($user);
        $newComment->setUserActiv($parentComment->getUserActiv());
        $newComment->setCreatedAt(new \DateTimeImmutable());
        $newComment->setStatus('active');
        $newComment->setLikes(0);
        $newComment->setDislikes(0);
        $newComment->setReported(0);

        $newCommentForm = $this->createForm(CommentType::class, $newComment);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($reply);
            $this->entityManager->flush();

            return $this->redirectToRoute('comment_index', [
                'userActiv' => $parentComment->getUserActiv()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/index.html.twig', [
            'parentComment' => $parentComment,
            'form' => $form->createView(),
            'replyForm' => $form->createView(),
            'newCommentForm' => $newCommentForm->createView(),
            'comments' => $this->getComments($parentComment->getUserActiv())
        ]);
    }

    #[Route('/reply/{id}/delete', name: 'comment_reply_delete', methods: ['POST'])]
    public function deleteReply(Request $request, Comment $comment): Response
    {
        // Kullanıcının silme yetkisine sahip olduğunu kontrol edin
      //  $this->denyAccessUnlessGranted('delete', $comment);

        // CSRF token doğrulaması
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }

        // Yanıtın bağlı olduğu UserActiv'e yönlendir
        return $this->redirectToRoute('comment_index', ['userActiv' => $comment->getUserActiv()->getId()], Response::HTTP_SEE_OTHER);
    }


    private function getComments(UserActiv $userActiv)
    {
        return $this->commentRepository->findBy(['userActiv' => $userActiv, 'parent' => null], ['createdAt' => 'DESC']);
    }

    #[Route('/{id}/like', name: 'comment_like', methods: ['POST'])]
    public function like(Request $request, Comment $comment, UserInterface $user)
    {
        $userId = $user->getId();
        $likesUsersIds = array_map(fn($u) => $u->getId(), $comment->getLikesUsers()->toArray());
        $dislikesUsersIds = array_map(fn($u) => $u->getId(), $comment->getDislikesUsers()->toArray());

        if (in_array($userId, $dislikesUsersIds)) {
            $comment->setDislikes($comment->getDislikes() - 1);
            $dislikeUser = $this->entityManager->getRepository(User::class)->find($userId);
            if ($dislikeUser) {
                $comment->removeDislikeUser($dislikeUser);
            }
        }

        if (in_array($userId, $likesUsersIds)) {
            $comment->setLikes($comment->getLikes() - 1);
            $likeUser = $this->entityManager->getRepository(User::class)->find($userId);
            if ($likeUser) {
                $comment->removeLikeUser($likeUser);
            }
        } else {
            $comment->setLikes($comment->getLikes() + 1);
            $likeUser = $this->entityManager->getRepository(User::class)->find($userId);
            if ($likeUser) {
                $comment->addLikeUser($likeUser);
            }
        }

        $this->entityManager->flush();
        return $this->redirectToRoute('comment_index',['userActiv'=>$comment->getUserActiv()->getId()]);
    }

    #[Route('/{id}/dislike', name: 'comment_dislike', methods: ['POST'])]
    public function dislike(Request $request, Comment $comment, UserInterface $user): JsonResponse
    {
        $userId = $user->getId();
        $likesUsersIds = array_map(fn($u) => $u->getId(), $comment->getLikesUsers()->toArray());
        $dislikesUsersIds = array_map(fn($u) => $u->getId(), $comment->getDislikesUsers()->toArray());

        if (in_array($userId, $likesUsersIds)) {
            $comment->setLikes($comment->getLikes() - 1);
            $likeUser = $this->entityManager->getRepository(User::class)->find($userId);
            if ($likeUser) {
                $comment->removeLikeUser($likeUser);
            }
        }

        if (in_array($userId, $dislikesUsersIds)) {
            $comment->setDislikes($comment->getDislikes() - 1);
            $dislikeUser = $this->entityManager->getRepository(User::class)->find($userId);
            if ($dislikeUser) {
                $comment->removeDislikeUser($dislikeUser);
            }
        } else {
            $comment->setDislikes($comment->getDislikes() + 1);
            $dislikeUser = $this->entityManager->getRepository(User::class)->find($userId);
            if ($dislikeUser) {
                $comment->addDislikeUser($dislikeUser);
            }
        }

        $this->entityManager->flush();

        return new JsonResponse(['success' => true, 'dislikes' => $comment->getDislikes()]);
    }

    #[Route('/{id}/report', name: 'comment_report', methods: ['POST'])]
    public function report(Request $request, Comment $comment, UserInterface $user): JsonResponse
    {
        $userId = $user->getId();

        if ($comment->getReportedUsers()->contains($user)) {
            $comment->removeReportedUser($user);
        } else {
            $comment->addReportedUser($user);
        }

        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }



}
