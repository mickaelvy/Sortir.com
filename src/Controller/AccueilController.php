<?php

namespace App\Controller;

use App\Entity\Search;
use App\Form\SearchType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */

    public function home(Request $request,ParticipantRepository $participantRepository,EtatRepository $etatRepository,
                         SortieRepository $sortieRepository, SiteRepository $siteRepository)
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

/*RECUPERATION DE L'UTILISATEUR CONNECTE  */
        $currentUserID  = $this->getUser()->getSalt();
        $currentUser = $participantRepository->find($currentUserID) ;

/*RECUPERATION DES SORTIES*/
        $sorties = $sortieRepository->findAll();

 /*RECUPERATION DES SITES POUR LA LISTE DEROULANTE DE LA PAGE D'ACCUEIL*/
        $sites = $siteRepository->findAll();

        $currentDateTime = new \DateTime('now');


/*MISE A JOUR DE L'ETAT DE CHAQUE SORTIE*/
        foreach($sorties as $sortie)
        {
            if($sortie->getDateLimiteInscription() < $currentDateTime and $sortie->getDateHeureDebut() > $currentDateTime)
            {
                $etatOuverte = $etatRepository->findOneBy(['libelle'=>"Clôturée"]);
                $sortie->setEtat($etatOuverte);
                $em->persist($sortie);
            }
            if($sortie->getDateHeureDebut() < $currentDateTime and $sortie->getEtat() != "Créée")
            {
                $etatPassee = $etatRepository->findOneBy(['libelle'=>"Passée"]);
                $sortie->setEtat($etatPassee);
                $em->persist($sortie);
            }
            if($sortie->getDateHeureDebut() == $currentDateTime)
            {
                $etatEnCours = $etatRepository->findOneBy(['libelle'=>"Activité en cours"]);
                $sortie->setEtat($etatEnCours);
                $em->persist($sortie);
            }
            $em->flush();
        }




/*RECUPERATION DUN TABLEAU DE SORTIE SELON FILTRES FAITS*/
        if ( $request->get('selected_site')
                or $request->get('search_area')
                or $request->get('dateA')
                or $request->get('dateB')
                or $request->get('myOwnSortie')
                or $request->get('registeredSortie')
                or $request->get('unRegisteredSortie')
                or $request->get('passedSorties'))
        {

            $selectedSite = $siteRepository->findOneBy(['nom'=>$request->get('selected_site')]);
            $motSaisie = $request->get('search_area');
            $dateA = $request->get("dateA");
            $dateB = $request->get("dateB");
            $boolMyOwnSortie = $request->get('myOwnSortie');
            $boolRegisteredSortie = $request->get('registeredSortie');
            $boolUnRegisteredSortie = $request->get('unRegisteredSortie');
            $boolPassedSortie = $request->get('passedSorties');

            $sorties = $sortieRepository->rechercher($currentDateTime,$currentUserID,$selectedSite,$motSaisie,$dateA,$dateB,$boolMyOwnSortie,$boolRegisteredSortie,$boolPassedSortie);



        }
         else
        {
             //$sorties = $sortieRepository->findAll();
            $sorties = $sortieRepository->afficherSortieMoinsDunMois();
        }










////*COMMENCONS LE FILTRE SELON LE SITE QUI A ETE SELECTIONNE  SI SELECTION Y'A*/
//        if ($request->get('selected_site') && $request->get('search_area'))
//        {
//            $motSaisie = $request->get('search_area');
//            $selectedSite = $siteRepository->findOneBy(['nom'=>$request->get('selected_site')]);
//            $sorties = $sortieRepository->filtrerSelonSite($selectedSite,$motSaisie);
//        }


//
///*COMMENCONS LE FILTRE SELON LE NOM DE SORTIE TAPE DANS LA ZONE DE RECHERCHE*/
///*LE NOM DUNE SORTIE DOIT CONTENIR OU EGAL AU MOT TAPE*/
//        if ($request->get('search_area'))
//        {
//            $sorties = $sortieRepository->afficherSortiesDontLeNomContient($request->get('search_area'));
//        }
//
///*COMMENCONS LE FILTRE POUR LES SORTIES ENTRE DEUX DATES */
//        if($request->get("dateA") && $request->get("dateB"))
//        {
//            $dateA = new \DateTime($request->get("dateA"));
//            $dateB = new \DateTime($request->get("dateB"));
//            $sorties = $sortieRepository->afficherSortiesEntreDeuxDates($dateA, $dateB);
//        }
//
//
///*COMMENCONS LE FILTRE POUR LES SORTIES DONT L'ORGANISATEUR */
//        if($request->get("myOwnSortie"))
//        {
//            $sorties = $sortieRepository->findBy(['organisateur'=>$currentUser]);
//        }
//
//
//
///*COMMENCONS LE FILTRE POUR LES SORTIES AUXQUELLES JE SUIS INSCRIT*/
//        if($request->get("registeredSortie"))
//        {
//            $sorties = $currentUser->getSorties();
//        }
//
//
///*COMMENCONS LE FILTRE POUR LES SORTIES AUXQUELLES JE NE SUIS PAS INSCRIT*/
//        if($request->get("unRegisteredSortie"))
//        {
//            $runRegisteredSorties = [];
//            foreach ($sorties as $sortie){
//                if (!$sortie->getParticipants()->contains($currentUser))
//                {
//                    $runRegisteredSorties[] = $sortie;
//                }
//            }
//            $sorties = $runRegisteredSorties;
//        }
//
//
///*COMMENCONS LE FILTRE POUR LES SORTIES PASSEES*/
//        if($request->get("passedSorties"))
//        {
//            $etatPassee = $etatRepository->findOneBy(['libelle'=>"Passée"]);
//            $sorties = $sortieRepository->afficherSortiesPassees($etatPassee);
//        }



        return $this->render('accueil/accueil.html.twig',[
            'searchForm' => $form ->createView(),
            'sorties' => $sorties,
            'currentUser'=>$currentUser,
            'sites'=>$sites
        ]);
    }
}
