<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Event\CommentEvent;
use App\EventSubscriber\CommentSubscriber;
use App\Form\CommentType;
use App\Handler\Form\CommentHandler;
use App\Handler\Form\ForgotPasswordHandler;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PostController extends AbstractController
{

    public function __construct(
        private CommentRepository  $commentRepository,
        private PaginatorInterface $paginator
    )
    {
    }


    #[Route('/tricks/details/{slug}/{id}', name: 'tricks.show', requirements: ['slug' => '[a-z0-9\-]*'])]
    public function show(
        Post           $post,
        Request        $request,
        CommentHandler $commentHandler
    ): Response
    {
        $comments = $this->paginator->paginate(
            $this->commentRepository->findBy(['post' => $post], ['created_at' => 'DESC']),
            $request->query->getInt('page', 1),
            10
        );

        $commentsCount = count($this->commentRepository->findBy(['post' => $post], ['created_at' => 'DESC']));

        $comment = new Comment();
        $comment->setPost($post);

        if ($commentHandler->handle($request, $comment)) {
            $this->addFlash('success', 'Votre commentaire est bien envoyé');
            return $this->redirectToRoute('tricks.show', [
                'slug' => $post->getSlug(),
                'id' => $post->getId()
            ]);
        }

        return $this->render('post/show.html.twig', [
            'current_menu' => 'tricks',
            'post' => $post,
            'form' => $commentHandler->createView(),
            'comments' => $comments,
            'commentsCount' => $commentsCount
        ]);
    }

    #[Route('/loadmoreComments/{id}', name: 'comment.loadmore')]
    public function loadMoreComment
    (
        Post    $post,
        Request $request

    ): Response
    {
        $comments = $this->paginator->paginate(
            $this->commentRepository->findBy(['post' => $post], ['created_at' => 'DESC']),
            $request->query->getInt('page'),
            10
        );

        return $this->render('comment/_loadMore.html.twig', [
            'comments' => $comments
        ]);
    }


}
