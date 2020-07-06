<?php

namespace App\Controller;


use App\Entity\Search;
use App\Entity\Sortie;
use App\Form\SearchType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class SearchController extends AbstractController
{

    /**
     * @Route("/searchResults", name="search_results")
     */
    public function search(Search $search, SortieRepository $sortieRepository,Request $request)
    {
        $search = new Search();
        $searchForm = $this->createForm(SearchType::class, $search);
        $searchForm->handleRequest($request);
        $sorties = null;
        $nom = null;

        if($searchForm->isSubmitted() && $searchForm->isValid())
        {
            $nom = $request->get('nom');
            $sorties = $sortieRepository->findBy(['Nom'=>"dolor"]);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($sorties);
            //$manager->flush();
            //$nom = $request->get('nom');
            var_dump($sorties);
            return $this->redirectToRoute('accueil');
        }

        return $this->render('accueil/accueil.html.twig',
            [
            'searchForm' => $searchForm ->createView(),
            'sorties' => $sorties
        ]);
    }

}
