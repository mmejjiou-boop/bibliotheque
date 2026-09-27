<?php

namespace App\Repository;

use App\Entity\Genre;
use App\Entity\Lecteur;
use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }


    /**
     * Livres empruntés par un lecteur donné.
     *
     * @return Livre[]
     */
    public function findEmpruntesParLecteur(Lecteur $lecteur): array
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.emprunts', 'e')
            ->andWhere('e.lecteur = :lecteur')
            ->setParameter('lecteur', $lecteur)
            ->distinct()
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Livre[]
     */
    public function findByGenre(Genre $genre): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.genre = :genre')
            ->setParameter('genre', $genre)
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Livres classés par nombre d'emprunts.
     *
     * @return array<array{livre: Livre, nbEmprunts: int}>
     */
    public function findClassementEmprunts(string $ordre = 'DESC'): array
    {
        $ordre = strtoupper($ordre) === 'ASC' ? 'ASC' : 'DESC';

        $rows = $this->createQueryBuilder('l')
            ->select('l AS livre', 'COUNT(e.id) AS nbEmprunts')
            ->leftJoin('l.emprunts', 'e')
            ->groupBy('l.id')
            ->orderBy('nbEmprunts', $ordre)
            ->addOrderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn (array $r) => ['livre' => $r['livre'], 'nbEmprunts' => (int) $r['nbEmprunts']], $rows);
    }

    /**
     * @return Livre[]
     */
    public function findJamaisEmpruntes(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.emprunts', 'e')
            ->andWhere('e.id IS NULL')
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
