<?php


namespace App\Controller;


use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\AjouterLieuType;
use App\Form\CreationSortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/sortie", name="sortie_")
 */

class SortieController extends AbstractController
{
    /**
     * @Route("/new", name="create")
     * @Route("/{id}/edit", name="edit")
     */

//Création et modification d'une sortie

    public function form (Sortie $sortie = null,Request $request,EtatRepository $etatRepository,ParticipantRepository $participantRepository)
    {

        $organisateur = $participantRepository->find($this->getUser()->getSalt());
// si je n'ai pas de sortie, je veux en créer une nouvelle
        if(!$sortie) {
            $sortie = new Sortie();
        }


        $form = $this->createForm(CreationSortieType::class, $sortie);
        $form ->handleRequest($request);

//Le cas où c'est le bontton 'Enregistrer'  qui est cliqué.
        if($form->isSubmitted() && $form->isValid() && $request->get('enregistrer'))
        {
            $etatCree = $etatRepository->findOneBy(['libelle'=>"Créée"]);
            $sortie->setEtat($etatCree);
            $manager = $this->getDoctrine()->getManager();
            $sortie -> setOrganisateur($organisateur);
            $manager->persist($sortie);
            $manager->flush();

            return $this->redirectToRoute('accueil');
        }

//Le cas où c'est le bontton 'Publier'  qui est cliqué'
        if($form->isSubmitted() && $form->isValid() && $request->get('publier'))
        {
            $etatPublie = $etatRepository->findOneBy(['libelle'=>"Ouverte"]);
            $sortie->setEtat($etatPublie);
            $manager = $this->getDoctrine()->getManager();
            $sortie -> setOrganisateur($organisateur);
            $manager->persist($sortie);

            if ($sortie->getDateLimiteInscription() <  $sortie->getDateHeureDebut()) {
                 $manager->flush();
                 return $this->redirectToRoute('accueil');
             }
             else
             {
                 $this->addFlash('danger', "La date limite d'inscription doit être inférieure à la date de la sortie");
             }

        }


        return $this -> render ('sortie/creerSortie.html.twig', [
            'formSortie' => $form ->createView(),
            'editMode' =>$sortie->getId() !== null,
            //On renvoie 'sortie' pour préremplir le formulaire le cas d'une modif
            'sortie'=>$sortie,
        ]);
    }


//Affichage d'une sortie
    /**
     * @Route("/details/sortie/{id}", name="afficher")
     */

    public function showSortie($id){
        $repo=$this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $repo->find($id);
        $participants = $sortie->getParticipants();
        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' =>$sortie,
            "participants" => $participants
        ]);
    }


//Inscription
    /**
     * @Route("/sinscrire/{id}", name="sinscrire")
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



//Desistement
    /**
     * @Route("/desisterSurUneSortie/{id}", name="Sedesister")
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



//Publication
    /**
     * @Route("/publierUneSortie/{id}", name="publier")
     */
    public function publierSortie($id,EtatRepository $etatRepository, SortieRepository $sortieRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $sortie = $sortieRepository->find($id);
        $currentDatetime = new \DateTime('now');

        if($sortie->getOrganisateur() == $this->getUser() or $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {

            if($sortie->getDateLimiteInscription() > $currentDatetime and $sortie->getDateLimiteInscription() < $sortie->getDateHeureDebut())
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




//Annulation
    /**
     * @Route("/annulerUneSortie/{id}", name="annuler")
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

//AJOUT LIEU PAR LUTILISATEUR
    /**
     * @Route("/ajoutLieu", name="ajoutLieu")
     */

    public function ajoutLieu( Request $request) {

        $lieu = new Lieu();
        $formAjoutLieu = $this->createForm(AjouterLieuType::class, $lieu);
        $formAjoutLieu->handleRequest($request);

        if($formAjoutLieu->isSubmitted() && $formAjoutLieu->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($lieu);
            $manager->flush();
            return $this->redirectToRoute("sortie_create");
        }

        return $this->render('sortie/AjouterUnLieu.html.twig', [
            'formAjoutLieu' => $formAjoutLieu->createView(),
            'lieu' => $lieu
        ]);
    }


//Suppression sortie
    /**
     * @Route("/supprimerSortie/{id}", name="supprimer")
     */
    public function supprimerSortie(EntityManagerInterface $em,$id)
    {
        //TODO : faire plus de tests  et de cas pour une suppression plus correcte.
        $sortie = $em->getRepository(Sortie::class)->find($id);
        if (($this->getUser() === $sortie->getOrganisateur() && $sortie->getEtat()->getLibelle() === 'Créée') or  $this->isGranted('ROLE_ADMIN')) {
            $em->remove($sortie);
            $this->addFlash('warning', 'la sortie est supprimée !');
            $em->flush();
        }
        return $this->redirectToRoute('accueil');
    }


//Afficher lieu

    /**
     * @Route ("/detailsList", name="list")
     */

    public function detailsList(Request $request, EntityManagerInterface $em) {
        $idList = $request->request->get("lieu");
        $em = $this->getDoctrine()->getManager();
        $repo = $em -> getRepository(Lieu::class);

        $lieu = $repo ->find($idList);

        if(!is_null($lieu)) {

            $ville = $lieu ->getVille();
            $city = $ville ->getNom();
            $codePostal = $ville->getCodePostal();
            $rue = $lieu ->getRue();
            $longitude = $lieu -> getLongitude();
            $latitude = $lieu -> getLatitude();


        $retour = array (
            'ville' => $city,
            'codePostal' => $codePostal,
            'rue' => $rue,
            'longitude' => $longitude,
            'latitude' => $latitude
        );


        }

        return $this -> json($retour);

    }

}