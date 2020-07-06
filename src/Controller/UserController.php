<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\AjoutUtilisateurType;
use App\Form\MonProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */

    public function login(AuthenticationUtils $authenticationUtils/*, Request $request*/): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        // get the login error if there is one

/*        $cookies = $request->cookies;

        if ($cookies->has('REMEMBERME'))
        {
            var_dump($cookies->get('REMEMBERME'));
        }*/

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/encoderMotDePasse")
     */
    public function EncodeUnString(UserPasswordEncoderInterface $encoder){
        $participant = new Participant();
        $enc = $encoder->encodePassword($participant, "123");
        return new Response($enc);
    }




}
