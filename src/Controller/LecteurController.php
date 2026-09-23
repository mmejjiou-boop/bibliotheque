<?php

namespace App\Controller;

use App\Entity\Lecteur;
use App\Form\LecteurType;
use App\Repository\LecteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lecteur')]
final class LecteurController extends AbstractController
{
    #[Route(name: 'app_lecteur_index', methods: ['GET'])]
    public function index(LecteurRepository $lecteurRepository): Response
    {
        return $this->render('lecteur/index.html.twig', [
            'lecteurs' => $lecteurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_lecteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lecteur = new Lecteur();
        $form = $this->createForm(LecteurType::class, $lecteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lecteur);
            $entityManager->flush();

            return $this->redirectToRoute('app_lecteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lecteur/new.html.twig', [
            'lecteur' => $lecteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lecteur_show', methods: ['GET'])]
    public function show(Lecteur $lecteur): Response
    {
        return $this->render('lecteur/show.html.twig', [
            'lecteur' => $lecteur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lecteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lecteur $lecteur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LecteurType::class, $lecteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_lecteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lecteur/edit.html.twig', [
            'lecteur' => $lecteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lecteur_delete', methods: ['POST'])]
    public function delete(Request $request, Lecteur $lecteur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lecteur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($lecteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lecteur_index', [], Response::HTTP_SEE_OTHER);
    }
}
