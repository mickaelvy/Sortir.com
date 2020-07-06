<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\MonProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class MonProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="mon_profil")
     */

    public function profil(Request $request,ParticipantRepository $participantRepository)
    {
        $id  = $this->getUser()->getSalt();
        $participant = $participantRepository->find($id);

        $profilForm = $this->createForm(MonProfilType::class, $participant);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid())
        {

            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->flush();

          return $this->redirectToRoute('accueil', [
              'partcipant' => $participant
          ]);//pour voir cette route faut  aller dans AccueilController.php

        }

        return $this->render('profil/MonProfil.html.twig', [
            "profilForm"=>$profilForm->createView(),
            'participant' => $participant
        ]);

    }



    /**
     * @Route("/afficheUser/{id}", name="afficher_user")
     */

    public function showUser($id){
        $repo=$this->getDoctrine()->getRepository(Participant::class);
        $participant = $repo->find($id);
        return $this->render('profil/afficherUnProfil.html.twig', [
            'participant' =>$participant
        ]);
    }
}
