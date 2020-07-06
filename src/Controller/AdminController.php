<?php


namespace App\Controller;


use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Form\AjouterLieuType;
use App\Form\AjouterSiteType;
use App\Form\AjoutUtilisateurType;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/admin", name="admin_")
 */

 class AdminController extends AbstractController
{


//ROUTE INTERFACE ADMIN
     /**
      * @Route("", name="interface")
      */
     public function dashboard()
     {
         return $this->render('admin/interfaceAdmin.html.twig', []);
     }



//GESTION DES LIEUX
    /**
     * @Route("/gestionLieux", name="lieu")
     */
    public function gestionDesLieux(Request $request, LieuRepository $lieuRepository) {

        if ( $request->get('champ_recherche_lieu'))
        {
            $lieux =   $lieuRepository->afficherlieuDontLeNomContient( $request->get('champ_recherche_lieu'));
        }
        else{
            $lieux = $lieuRepository->findAll();
        }

        return $this->render('admin/gestionLieu.html.twig', [
            'lieux' =>$lieux
        ]);
    }



//GESTION DES SITES
     /**
      * @Route("/gestionSite", name="site")
      */
     public function gestionDesSites (Request $request, SiteRepository $siteRepository) {

         if ( $request->get('champ_recherche_site'))
         {
             $sites =   $siteRepository->afficherSiteDontLeNomContient( $request->get('champ_recherche_site'));
         }
         else{
             $sites = $siteRepository->findAll();
         }

         return $this->render('admin/gestionSite.html.twig', [
             'sites' =>$sites
         ]);
     }


//AJOUT ET MODIF DUN  LIEU
    /**
     * @Route("/ajoutLieu", name="ajoutLieu")
     *  @Route("/{id}/editLieu", name="editLieu")
     */

    public function ajoutLieu(Lieu $lieu = null, Request $request) {

        $id  = $this->getUser();

        if(!$lieu) {
            $lieu = new Lieu();
        }

        $formAjoutLieu = $this->createForm(AjouterLieuType::class, $lieu);
        $formAjoutLieu->handleRequest($request);

        if($formAjoutLieu->isSubmitted() && $formAjoutLieu->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $lieu ->getId($id);
            $manager->persist($lieu);
            $manager->flush();
            return $this->redirectToRoute("admin_lieu");
        }

        return $this->render('sortie/AjouterUnLieu.html.twig', [
            'formAjoutLieu' => $formAjoutLieu->createView(),
            'lieu' => $lieu
        ]);
    }



// SUPPRESSION DUN LIEU
    /**
     * @Route("/supprimerLieu/{id}", name="supprimer_lieu")
     */

    public function supprimerLieu ($id, LieuRepository $repoLieu) {


        $lieu = $repoLieu->find($id);

        $sorties =  $lieu ->getSorties();


        if(count($sorties) == 0 ) {

            $manager = $this->getDoctrine()->getManager();
            $this->addFlash('success', "Le lieu '". $lieu->getNom() . "' a bien été supprimé" );
            $manager->remove($lieu);
            $manager->flush();
        } else {
            $this->addFlash('danger', "Le lieu '". $lieu->getNom() . "' ne peut pas être supprimé, il est associé à des sorties" );
        }

        return $this->redirectToRoute('admin_lieu');
    }




    //SUPPRESSION DUN SITE
    /**
     * @Route("/supprimerSite/{id}", name="supprimer_site")
     */

    public function supprimerSite ($id, SiteRepository $repoSite) {

        $site = $repoSite->find($id);

        $sorties = $site ->getSorties();
        $participants = $site ->getParticipant();

        if(count($sorties) == 0 && count($participants) == 0) {

            $manager = $this->getDoctrine()->getManager();
            $this->addFlash('success', "Le site '". $site->getNom() . "' a bien été supprimé" );
            $manager->remove($site);
            $manager->flush();

        } elseif (count($sorties) <> 0) {
            $this->addFlash('danger', "Le site '". $site->getNom() . "' ne peut pas être supprimé, il est associé à des sorties/partcipants!" );
        }


        return $this->redirectToRoute('admin_site');
    }





    //AJOUT DUN SITE ET MODIFICATION DUN SITE
     /**
      * @Route("/ajoutSite", name="ajoutSite")
      *  @Route("/{id}/editSite", name="editSite")
      */

     public function ajoutSite(Site $site = null, Request $request) {

         $id  = $this->getUser();

         if(!$site) {
             $site = new Site();
         }

         $formAjoutSite = $this->createForm(AjouterSiteType::class, $site);
         $formAjoutSite->handleRequest($request);

         if($formAjoutSite->isSubmitted() && $formAjoutSite->isValid()){
             $manager = $this->getDoctrine()->getManager();
             $site ->getId($id);
             $manager->persist($site);
             $manager->flush();
             return $this->redirectToRoute("admin_site");
         }

         return $this->render('admin/ajouterUnSite.html.twig', [
             'formAjoutSite' => $formAjoutSite->createView(),
             'site' => $site
         ]);
     }







//AJOUT DUN NOUVEAU UTILISATEUR
    /**
     * @Route("/ajoutUtilisateur", name="ajoutUtilisateur")
     */
    public function ajoutUtilisateur(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $participant = new Participant();
        $utilisateurForm = $this->createForm(AjoutUtilisateurType::class, $participant);
        $utilisateurForm->handleRequest($request);

        if ($utilisateurForm->isSubmitted() && $utilisateurForm->isValid())
        {
            //Si le checkbox administrateur est coché c'est role attribué est = "ROLE_ADMIN" sinon  role = "ROLE_USER"
          $role[] = $participant->getAdministrateur()? "ROLE_ADMIN" : "ROLE_USER";
            $participant->setRoles($role);
            //Encodage du mot de passe
            $enc = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setPasswword($enc);

            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->flush();

            $this->addFlash('success', " L'utilisateur ".$participant->getPseudo()."a été ajouté avec succès.");
            return $this->redirectToRoute('admin_listeUtilisateurs');
        }

        return $this->render('admin/ajouterUtilisateur.html.twig',[
            "utilisateurForm"=>$utilisateurForm->createView()]);
    }



//ENVOI DE LA LISTE DE TOUS LES PARTICIPANTS
    /**
     * @Route("/listeDesUtisateurs", name="listeUtilisateurs")
     */
    public function listeDesUtisateurs(ParticipantRepository $participantRepository)
    {
        $tousLesParticipants = $participantRepository->findBy([], ["nom" => "ASC"]);
        return $this->render('admin/listeUtilisateurs.html.twig', [
            'tousLesParticipants' => $tousLesParticipants
        ]);
    }




//DESACTIVTAION ET ACTIVATION DUN PARTICIPANT
    /**
     * @Route("/{id}/desactiver", name="desactiverOuActiverUnParticipant")
     */
    public function DesactiverActiverUnParticipant($id, ParticipantRepository $participantRepository)
    {
        $participant = $participantRepository->find($id);

        //S'assurer ici que c'est pas un administrateur qui désactive un autre administrateur
        if ($participant->getAdministrateur())
        {
            $this->addFlash('danger', "Vous ne pouvez pas bannir un administrateur comme vous.");
            return $this->redirectToRoute('admin_listeUtilisateurs');
        }

        else{
            $em = $this->getDoctrine()->getManager();
            $participant->setActif(!$participant->getActif());
            $em->persist($participant);
            $em->flush();
            $statusCompte = $participant->getActif() ? "activé" : "désactivé";
            $this->addFlash('success', "Le compte de l'utilisateur : ".$participant->getPseudo()." a bien été $statusCompte !");
            return $this->redirectToRoute('admin_listeUtilisateurs');
        }

    }



//
////SUPPRESSION DUN UTILISATEUR
//    /**
//     * @Route("/supprimerUtilisateur/{id}", name="supprimerUtilisateur")
//     */
//    public function supprimerUtilisateur ($id, ParticipantRepository $participantRepository, SortieRepository $sortieRepository)
//    {
//
//        $registeredSorties = $sortieRepository->registeredSorties($id);
//        $participant = $participantRepository->find($id);
//        $ParticipantSite = $participant->getSite();
//        $sorties = $sortieRepository->findAll();
//
//        if($participant == null) {
//            throw $this->createNotFoundException("L'utilisateur n'éxiste pas ou a déjà été supprimé");
//        }
//
//        elseif (count($participant->getSorties()) > 0)
//        {
//            $this->addFlash('danger', "Le participant '". $participant->getPseudo() . "' ne peut pas être supprimé, il a une ou plusieurs sorties en ligne." );
//            return $this->redirectToRoute('admin_listeUtilisateurs');
//        }
//
//        else(
//
//            foreach ($sorties as $sortie){
//                $participantsBySortie = $sortie->getParticipants();
//                foreach ($participantsBySortie as $participantBysortie) {
//                    if ($participantBysortie === $participant) {
//                        $this->addFlash('danger', "Le participant '" . $participant->getPseudo() . "' ne peut pas être supprimé, il est inscrit à une ou plusieurs sorties en ligne.");
//                        return $this->redirectToRoute('admin_listeUtilisateurs');
//                    }
//                    else(
//
//                )
//                }}
//
//
//        )
//
//}



}

