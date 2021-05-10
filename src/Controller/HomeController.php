<?php

namespace App\Controller;

use App\Entity\Home;
use App\Form\HomeType;
use App\Repository\CarouselRepository;
use App\Repository\HomeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home-page")
     */
    // La méthode showHome() prend en paramètre deux injections de dépendance (Repository) qui vont nous éviter de devoir récupérer les repositries dans le controller avce $this->getDoctrine()...
    public function showHome(HomeRepository $homeRepository, CarouselRepository $carouselRepository) : Response
    {
        // Récupération des données
        $home = $homeRepository->findOneBy(["active"=>1]);
        $slides = $carouselRepository->findBy(["active"=>1], ["id"=>"DESC"]);
        // Rendu
        return $this->render('home/home.html.twig', ["home" => $home, "slides" => $slides]);
    }

    /**
     * @Route("/admin/home", name="home_index", methods={"GET"})
     */
    public function index(HomeRepository $homeRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'homes' => $homeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/home/new", name="home_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $home = new Home();
        $form = $this->createForm(HomeType::class, $home);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($home);
            $entityManager->flush();

            return $this->redirectToRoute('home_index');
        }

        return $this->render('home/new.html.twig', [
            'home' => $home,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/home/{id}", name="home_show", methods={"GET"})
     */
    public function show(Home $home): Response
    {
        return $this->render('home/show.html.twig', [
            'home' => $home,
        ]);
    }

    /**
     * @Route("/admin/home/{id}/edit", name="home_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Home $home): Response
    {
        $form = $this->createForm(HomeType::class, $home);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home_index');
        }

        return $this->render('home/edit.html.twig', [
            'home' => $home,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/home/{id}", name="home_delete", methods={"POST"})
     */
    public function delete(Request $request, Home $home): Response
    {
        if ($this->isCsrfTokenValid('delete'.$home->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($home);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home_index');
    }
}
