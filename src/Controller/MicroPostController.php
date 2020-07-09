<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/micro-post", name="micro_post")
 */
class MicroPostController extends AbstractController
{
    /**
     * @Route("/", name="_index")
     * @return Response
     */
    public function index()
    {
        $currentUser = $this->getUser();
        $usersToFollow  = [];
        $microPostRepository = $this->getDoctrine()->getRepository(MicroPost::class);
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        if ($currentUser instanceof  User) {
            $posts = $microPostRepository->findAllByUsers( $currentUser->getFollowing());
            $usersToFollow = count($posts) === 0 ? $userRepository->findAllWithMoreThan5PostsExceptUser($currentUser) : [];
        } else {
            $posts = $microPostRepository->findBy([], ['time' => 'DESC']);
        }
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts,
            'usersToFollow' => $usersToFollow
        ]);
    }

    /**
     * @Route("/edit/{id}", name="_edit")
     * @Security("is_granted('edit', microPost)", message="Access denied")
     * @param MicroPost $microPost
     * @param Request $request
     */
    public function edit(MicroPost $microPost, Request $request)
    {
        //$this->denyAccessUnlessGranted('edit', $microPost);
        $form = $this->get('form.factory')->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return new RedirectResponse($this->get('router')->generate('micro_post_index'));
        }

        return $this->render('micro_post/add.html.twig', ['form' => $form->createView()
        ]);
    }

    /**
     * @Route("/add", name="_add")
     * @Security("is_granted('ROLE_USER')")
     * @param Request $request
     * @return RedirectResponse
     */
    public function add(Request $request)
    {
        $microPost = new MicroPost();
        $microPost->setUser($this->getUser());
        $form = $this->get('form.factory')->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($microPost);
            $entityManager->flush();

            return new RedirectResponse($this->get('router')->generate('micro_post_index'));
        }

        return $this->render('micro_post/add.html.twig', ['form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="_show")
     * @param MicroPost $post
     * @return Response
     */
    public function show(MicroPost $post)
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="_delete")
     * @Security("is_granted('delete', post)", message="Access denied")
     * @param MicroPost $post
     */
    public function delete(MicroPost $post)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Post was deleted');

        return new RedirectResponse(
            $this->get('router')->generate('micro_post_index')
        );
    }

    /**
     * @Route("/user/{username}/posts", name="_user")
     * @param User $user
     * @return Response
     */
    public function userPosts(User $user)
    {
        return $this->render('micro_post/user-posts.html.twig', [
//            'posts' => $this->getDoctrine()->getRepository(MicroPost::class)->findBy(['user'=> $user], ['time' => 'DESC']),
            'posts' => $user->getPosts(),
            'user' => $user
        ]);
    }
}

