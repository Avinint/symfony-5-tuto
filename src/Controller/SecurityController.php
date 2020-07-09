<?php


namespace App\Controller;

use App\Entity\User;
use App\Mailer\Mailer;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;
class SecurityController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct( UserRepository $userRepository, Mailer $mailer, TokenGenerator $tokenGenerator)
    {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

//    /**
//     * @Route("/login", name="security_login")
//     * @param AuthenticationUtils $authUtils
//     * @return Response
//     */
//    public function login(AuthenticationUtils $authUtils)
//    {
//        return $this->render('security/login.html.twig', [
//            'last_username' => $authUtils->getLastUsername(),
//            'error' => $authUtils->getLastAuthenticationError()
//        ]);
//    }

    /**
     * @Route("/confirm/remind/{username}", name="security_remind_confirmation")
     * @param User $user
     */
    public function remindConfirmation(User $user)
    {
        return $this->render('security/remind_confirmation.html.twig', [
            'error' => 'Account not active',
            'username' => $user->getUsername()
        ]);
    }

    /**
     * @Route("/confirm/send/{username}", name="security_send_confirmation")
     * @param User $user
     * @return RedirectResponse
     */
    public function sendConfirmation(User $user)
    {
        $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken(30));
        $this->mailer->sendConfirmationEmail($user);
        $this->getDoctrine()->getManager()->flush($user);
        return new RedirectResponse($this->get('router')->generate('micro_post_index'));
    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     * @param string $token
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function confirm(string $token, EntityManagerInterface $entityManager)
    {
        $user = $this->userRepository->findOneBy([
            'confirmationToken' => $token
        ]);
        if (!$user instanceof User) {
            throw new AuthenticationException();
        }

        $user->setEnabled(true);
        $user->setConfirmationToken('');
        $entityManager->flush();


        return $this->render('security/confirmation.html.twig',
            ['user' => $user]);
    }

    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}