<?php

namespace App\DataFixtures;

use App\Entity\Emprunt;
use App\Entity\Lecteur;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Génère des emprunts à partir des livres et lecteurs existants.
 * Ajout sans purge : php bin/console doctrine:fixtures:load --group=emprunts --append
 */
class EmpruntFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    private const NB_EN_COURS = 8;
    private const NB_TERMINES = 30;
    private const DUREE_PRET = 14;

    /** @var array<int, array<array{\DateTime, \DateTime}>> périodes occupées par livre */
    private array $periodes = [];

    public static function getGroups(): array
    {
        return ['emprunts'];
    }

    public function getOrder(): int
    {
        return 2;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $livres = $manager->getRepository(Livre::class)->findAll();
        $lecteurs = $manager->getRepository(Lecteur::class)->findAll();

        // Périodes des emprunts déjà présents en base
        foreach ($manager->getRepository(Emprunt::class)->findAll() as $existant) {
            $this->reserver($existant->getLivre(), $existant->getDateEmprunt(), $existant->getDateRetourEffective() ?? new \DateTime('today'));
        }

        // Emprunts en cours : livres distincts, non déjà empruntés
        $libres = array_values(array_filter($livres, fn (Livre $l) => !$this->estEmprunteAujourdhui($l)));
        foreach ($faker->randomElements($libres, self::NB_EN_COURS) as $livre) {
            // Début il y a 1 à 20 jours : certains à rendre bientôt, d'autres en retard
            $debut = new \DateTime(sprintf('-%d days', $faker->numberBetween(1, 20)));
            $debut->setTime(0, 0);

            $manager->persist($this->creer($livre, $faker->randomElement($lecteurs), $debut, null));
            $this->reserver($livre, $debut, new \DateTime('today'));
            $livre->setDisponible(false);
        }

        // Emprunts terminés : sur les 12 derniers mois, sans chevauchement pour un même livre
        $crees = 0;
        while ($crees < self::NB_TERMINES) {
            $livre = $faker->randomElement($livres);
            $debut = $faker->dateTimeBetween('-12 months', '-25 days');
            $debut->setTime(0, 0);
            $retour = (clone $debut)->modify(sprintf('+%d days', $faker->numberBetween(2, 21)));

            if ($this->chevauche($livre, $debut, $retour)) {
                continue;
            }

            $manager->persist($this->creer($livre, $faker->randomElement($lecteurs), $debut, $retour));
            $this->reserver($livre, $debut, $retour);
            $crees++;
        }

        $manager->flush();
    }

    private function creer(Livre $livre, Lecteur $lecteur, \DateTime $debut, ?\DateTime $retour): Emprunt
    {
        return (new Emprunt())
            ->setLivre($livre)
            ->setLecteur($lecteur)
            ->setDateEmprunt($debut)
            ->setDateRetourPrevue((clone $debut)->modify(sprintf('+%d days', self::DUREE_PRET)))
            ->setDateRetourEffective($retour);
    }

    private function reserver(Livre $livre, \DateTime $debut, \DateTime $fin): void
    {
        $this->periodes[$livre->getId()][] = [$debut, $fin];
    }

    private function chevauche(Livre $livre, \DateTime $debut, \DateTime $fin): bool
    {
        foreach ($this->periodes[$livre->getId()] ?? [] as [$d, $f]) {
            if ($debut <= $f && $fin >= $d) {
                return true;
            }
        }

        return false;
    }

    private function estEmprunteAujourdhui(Livre $livre): bool
    {
        $aujourdhui = new \DateTime('today');

        return $this->chevauche($livre, $aujourdhui, $aujourdhui);
    }
}
