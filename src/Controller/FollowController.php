<?php


namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FollowController
 * @package App\Controller
 * @Security("is_granted('ROLE_USER')")
 */
class FollowController extends AbstractController
{
    /**
     * @Route("/follow/{username}", name="follow")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function follow(User $user)
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        if ($user->getId() !== $currentUser->getId()) {
            $currentUser->follow($user);
            $this->getDoctrine()->getManager()->flush();
        }


        return $this->redirectToRoute('micro_post_user', ['username' => $user->getUsername()]);
    }

    /**
     * @Route("/unfollow/{username}", name="unfollow")
     * @param User $user
     */
    public function unfollow(User $user)
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        $currentUser->getFollowing()->removeElement($user);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('micro_post_user', ['username' => $user->getUsername()]);
    }
}