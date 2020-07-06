<?php

namespace App\Controller;



use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CrudController extends AbstractController
{





    /**
     * @Route("/sinscrire/{id}", name="sinscrire_a_une_sortie")
     */
    public function iscriptionSortie($id,ParticipantRepository $participantRepository, SortieRepository $sortieRepository)
    {
        $currentUserId  = $this->getUser()->getSalt();
        $participant = $participantRepository->find($currentUserId);

        $sortie = $sortieRepository->find($id);
        $sortie->addParticipant($participant);

        $em = $this->getDoctrine()->getManager();
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('accueil');
        //TODO ajouter un message flash pour dire que linscription s'est bien effectuée

    }




    /**
     * @Route("/desisterSurUneSortie/{id}", name="desister_sur_une_sortie")
     */
    public function desister($id,ParticipantRepository $participantRepository, SortieRepository $sortieRepository)
    {
        $currentUserId  = $this->getUser()->getSalt();
        $participant = $participantRepository->find($currentUserId);

        $sortie = $sortieRepository->find($id);
        $sortie->removeParticipant($participant);

        $em = $this->getDoctrine()->getManager();
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('accueil');
        //TODO ajouter un message flash pour dire que linscription s'est bien effectuée

    }




    /**
     * @Route("/publierUneSortie/{id}", name="publier_une_sortie")
     */
    public function publierSortie($id,EtatRepository $etatRepository, SortieRepository $sortieRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $sortie = $sortieRepository->find($id);

        $currentDatetime = new \DateTime('now');

        if($sortie->getOrganisateur() == $this->getUser() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {

            if($sortie->getDateLimiteInscription() > $currentDatetime->modify('+ 15 hours') and $sortie->getDateLimiteInscription() < $sortie->getDateHeureDebut())
            {

                $etat = $etatRepository->findOneBy(['libelle'=>"Ouverte"]);
                $sortie->setEtat($etat);
                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Sortie a bien été publiée avec succès.');
                return $this->redirectToRoute("accueil");
            }
            else
            {
                $this->addFlash('danger', "La date limite d'inscription doit être supérieure à ". $currentDatetime->format("Y-m-d H:i").", modifiez la et rééssayez.");

                return $this->redirectToRoute("accueil");
            }

        }
        else
        {
            $this->addFlash('danger', 'Vous n\'êts pas l\'organisateur de la sortie!');
            return $this->redirectToRoute('accueil');
        }
    }


    
    
    /**
     * @Route("/annulerUneSortie/{id}", name="annuler_une_sortie")
     */
    public function annulerUneSortie($id, EtatRepository $etatRepository, SortieRepository $sortieRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $sortie = $sortieRepository->find($id);


        if($sortie->getOrganisateur() == $this->getUser() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {

            $etatOuvert = $etatRepository->findOneBy(['libelle'=>"Ouverte"]);
            $etatCree = $etatRepository->findOneBy(['libelle'=>"Créée"]);

            if($sortie->getEtat() === $etatOuvert or $sortie->getEtat() === $etatCree )
            {

                $etatAnnule = $etatRepository->findOneBy(['libelle'=>"Annulée"]);
                $sortie->setEtat($etatAnnule);
                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'La sortie a bien été annulée.');
                return $this->redirectToRoute("accueil");
            }
            else
            {
                $this->addFlash('danger', "Vous ne pouvez pas annuler cette sortie");

                return $this->redirectToRoute("accueil");
            }

        }
        else
        {
            $this->addFlash('danger', 'Vous n\'êts pas l\'organisateur de la sortie!');
            return $this->redirectToRoute('accueil');
        }

    }








}
