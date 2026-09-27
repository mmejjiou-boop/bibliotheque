<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\Lecteur;
use App\Repository\EmpruntRepository;
use App\Repository\GenreRepository;
use App\Repository\LecteurRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/recherche')]
final class RechercheController extends AbstractController
{
    #[Route(name: 'app_recherche_index', methods: ['GET'])]
    public function index(GenreRepository $genreRepository, LecteurRepository $lecteurRepository): Response
    {
        return $this->render('recherche/index.html.twig', [
            'genres' => $genreRepository->findBy([], ['nom' => 'ASC']),
            'lecteurs' => $lecteurRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/livres-par-lecteur', name: 'app_recherche_livres_lecteur', methods: ['GET'])]
    public function livresParLecteur(Request $request, LecteurRepository $lecteurRepository, LivreRepository $livreRepository): Response
    {
        $lecteur = $lecteurRepository->find($request->query->getInt('lecteur'));
        if (!$lecteur instanceof Lecteur) {
            throw $this->createNotFoundException('Lecteur introuvable');
        }

        return $this->render('recherche/livres.html.twig', [
            'titre' => sprintf('Livres empruntés par %s %s', $lecteur->getPrenom(), $lecteur->getNom()),
            'livres' => $livreRepository->findEmpruntesParLecteur($lecteur),
        ]);
    }

    #[Route('/retours-rapides', name: 'app_recherche_retours_rapides', methods: ['GET'])]
    public function retoursRapides(EmpruntRepository $empruntRepository): Response
    {
        return $this->render('recherche/retours_rapides.html.twig', [
            'emprunts' => $empruntRepository->findRendusEnMoinsDe(7),
        ]);
    }

    #[Route('/livres-par-genre', name: 'app_recherche_livres_genre', methods: ['GET'])]
    public function livresParGenre(Request $request, GenreRepository $genreRepository, LivreRepository $livreRepository): Response
    {
        $genre = $genreRepository->find($request->query->getInt('genre'));
        if (!$genre instanceof Genre) {
            throw $this->createNotFoundException('Genre introuvable');
        }

        return $this->render('recherche/livres.html.twig', [
            'titre' => sprintf('Livres du genre « %s »', $genre->getNom()),
            'livres' => $livreRepository->findByGenre($genre),
        ]);
    }

    #[Route('/lecteurs-genre-prefere', name: 'app_recherche_lecteurs_genre', methods: ['GET'])]
    public function lecteursParGenrePrefere(Request $request, GenreRepository $genreRepository, LecteurRepository $lecteurRepository): Response
    {
        $genre = $genreRepository->find($request->query->getInt('genre'));
        if (!$genre instanceof Genre) {
            throw $this->createNotFoundException('Genre introuvable');
        }

        return $this->render('recherche/lecteurs.html.twig', [
            'genre' => $genre,
            'lecteurs' => $lecteurRepository->findByGenrePrefere($genre),
        ]);
    }

    #[Route('/classement/{ordre}', name: 'app_recherche_classement', requirements: ['ordre' => 'asc|desc'], defaults: ['ordre' => 'desc'], methods: ['GET'])]
    public function classement(string $ordre, LivreRepository $livreRepository): Response
    {
        return $this->render('recherche/classement.html.twig', [
            'ordre' => $ordre,
            'classement' => $livreRepository->findClassementEmprunts($ordre),
        ]);
    }

    #[Route('/jamais-empruntes', name: 'app_recherche_jamais_empruntes', methods: ['GET'])]
    public function jamaisEmpruntes(LivreRepository $livreRepository): Response
    {
        return $this->render('recherche/livres.html.twig', [
            'titre' => 'Livres jamais empruntés',
            'livres' => $livreRepository->findJamaisEmpruntes(),
        ]);
    }
}
