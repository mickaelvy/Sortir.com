<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordChangeController extends AbstractController
{
    /**
     * @Route("/ChangerMotDePasse", name="password_change")
     */
    public function index()
    {
        return $this->render('password_change/modifierPassword.html.twig');

    }


    /**
     * @Route("/Subimission", name="passwordChangeSubmission")
     */

    public function submission(Request $request, ParticipantRepository $participantRepository, UserPasswordEncoderInterface $encoder )
    {
        $id  = $this->getUser()->getSalt();
        $participant = $participantRepository->find($id);

        $password = $request->request->get('newPassword');
        $cfmPassword = $request->request->get('cnfPassword');

        if ($password==$cfmPassword)
        {
            $enc = $encoder->encodePassword($participant,$password );
            $participant->setPasswword($enc);

            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->flush();

            return $this->redirectToRoute('accueil');//pour voir cette route aller dans AccueilController.php
        }
        else{
            $this->addFlash('danger', 'Les mots de passes saisis ne correspondent pas.');
            return $this->redirectToRoute('password_change');

        }

        //return $this->render('accueil/accueil.html.twig');

    }


}
