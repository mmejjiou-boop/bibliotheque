<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    /**
     * Emprunts terminés dont le livre a été rendu moins de $jours jours après l'emprunt.
     *
     * @return Emprunt[]
     */
    public function findRendusEnMoinsDe(int $jours = 7): array
    {
        return $this->createQueryBuilder('e')
            ->addSelect('l', 'r')
            ->innerJoin('e.livre', 'l')
            ->innerJoin('e.lecteur', 'r')
            ->andWhere('e.dateRetourEffective IS NOT NULL')
            ->andWhere('DATE_DIFF(e.dateRetourEffective, e.dateEmprunt) < :jours')
            ->setParameter('jours', $jours)
            ->orderBy('e.dateRetourEffective', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
