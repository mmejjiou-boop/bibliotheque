<?php

namespace App\Repository;

use App\Entity\Genre;
use App\Entity\Lecteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lecteur>
 */
class LecteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lecteur::class);
    }

    /**
     * Lecteurs dont le genre préféré (le genre qu'ils ont le plus emprunté) est $genre.
     * En cas d'égalité entre plusieurs genres, le lecteur est retenu pour chacun d'eux.
     *
     * @return Lecteur[]
     */
    public function findByGenrePrefere(Genre $genre): array
    {
        $rows = $this->createQueryBuilder('r')
            ->select('r.id AS lecteurId', 'IDENTITY(l.genre) AS genreId', 'COUNT(e.id) AS nb')
            ->innerJoin('r.emprunts', 'e')
            ->innerJoin('e.livre', 'l')
            ->groupBy('r.id', 'l.genre')
            ->getQuery()
            ->getArrayResult();

        $parLecteur = [];
        foreach ($rows as $row) {
            $parLecteur[$row['lecteurId']][(int) $row['genreId']] = (int) $row['nb'];
        }

        $ids = [];
        foreach ($parLecteur as $id => $genres) {
            if (($genres[$genre->getId()] ?? 0) === max($genres)) {
                $ids[] = $id;
            }
        }

        if (!$ids) {
            return [];
        }

        return $this->createQueryBuilder('r')
            ->andWhere('r.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('r.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
