<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use App\Entity\Lecteur;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private const GENRES = ['Roman', 'Science-Fiction', 'Policier', 'Fantastique', 'Biographie'];

    private const TITRE_NOMS = [
        'ombre', 'mystère', 'voyage', 'silence', 'lumière', 'nuit', 'rêve', 'secret',
        'destin', 'chemin', 'mer', 'ciel', 'mémoire', 'jardin', 'étoile', 'feu',
        'sang', 'temps', 'vent', 'murmure', 'promesse', 'frontière', 'labyrinthe', 'aube',
    ];

    private const TITRE_ADJECTIFS = [
        'dernier', 'perdue', 'oublié', 'éternelle', 'brisé', 'lointain',
        'infini', 'sombre', 'doré', 'secrète', 'invisible', 'fragile',
    ];

    private const TITRE_COMPLEMENTS = [
        'de minuit', 'des origines', 'du silence', 'de l\'aube', 'des étoiles',
        'du passé', 'de l\'oubli', 'des sables', 'de la mémoire', 'du monde',
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $genres = [];
        foreach (self::GENRES as $nom) {
            $genre = new Genre();
            $genre->setNom($nom);
            $manager->persist($genre);
            $genres[] = $genre;
        }

        for ($i = 0; $i < 40; $i++) {
            $livre = new Livre();
            $livre->setTitre($this->genererTitre($faker));
            $livre->setIsbn($faker->isbn13());
            $livre->setDatePublication($faker->dateTimeBetween('-40 years', 'now'));
            $livre->setDisponible($faker->boolean(70));
            $livre->setGenre($faker->randomElement($genres));
            $manager->persist($livre);
        }

        for ($i = 0; $i < 20; $i++) {
            $sexe = $faker->randomElement(['M', 'F']);
            $prenom = $sexe === 'M' ? $faker->firstNameMale() : $faker->firstNameFemale();

            $lecteur = new Lecteur();
            $lecteur->setNom($faker->lastName());
            $lecteur->setPrenom($prenom);
            $lecteur->setEmail($faker->unique()->safeEmail());
            $lecteur->setAge($faker->numberBetween(12, 90));
            $lecteur->setSex($sexe);
            $manager->persist($lecteur);
        }

        $manager->flush();
    }

    private function genererTitre($faker): string
    {
        $determinant = $faker->randomElement(['Le', 'La', 'Les']);
        $nom = $faker->randomElement(self::TITRE_NOMS);

        $variante = $faker->numberBetween(1, 3);

        if ($variante === 1) {
            $titre = sprintf('%s %s', $determinant, $nom);
        } elseif ($variante === 2) {
            $adjectif = $faker->randomElement(self::TITRE_ADJECTIFS);
            $titre = sprintf('%s %s %s', $determinant, $nom, $adjectif);
        } else {
            $complement = $faker->randomElement(self::TITRE_COMPLEMENTS);
            $titre = sprintf('%s %s %s', $determinant, $nom, $complement);
        }

        return ucfirst($titre);
    }
}
