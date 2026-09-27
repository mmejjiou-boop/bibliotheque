# Bibliothèque

Application Symfony de gestion d'une bibliothèque.

## Description

Ce projet permet de gérer :
- les livres ;
- les lecteurs ;
- les genres ;
- les emprunts ;
- la base de données associée avec Doctrine ORM.

## Stack technique

- PHP 8.2
- Symfony 7
- Doctrine ORM
- Doctrine Migrations
- Twig
- MySQL / MariaDB
- Bootstrap (si utilisé dans le projet)

## Prérequis

Avant de lancer le projet, vérifie que tu as installé :
- PHP 8.2+
- Composer
- MySQL / MariaDB ou SQLite
- Symfony CLI (recommandé)

## Installation

1. Clone le dépôt :

```bash
git clone https://github.com/mmejjiou-boop/bibliotheque.git
cd bibliotheque
```

2. Installe les dépendances :

```bash
composer install
```

3. Configure la base de données dans le fichier `.env` ou `.env.local` :

Exemple MySQL :

```dotenv
DATABASE_URL="mysql://root:@127.0.0.1:8888/bibliotheque?serverVersion=8.0.32&charset=utf8mb4"
```

4. Crée la base de données et applique les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Lancer le projet

### Avec Symfony CLI

```bash
symfony server:start
```

Puis ouvre :

```text
http://127.0.0.1:8000
```

### Alternative via PHP intégré

```bash
php -S 127.0.0.1:8000 -t public
```

## Démarrage rapide

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

## Structure du projet

```text
bibliotheque/
├── config/
├── migrations/
├── public/
├── src/
├── templates/
├── tests/
├── var/
├── .env
├── composer.json
├── README.md
└── symfony.lock
```

## Liens utiles

- Dépôt GitHub : https://github.com/mmejjiou-boop/bibliotheque
- Documentation Symfony : https://symfony.com/doc/current/index.html
- Doctrine ORM : https://www.doctrine-project.org/projects/orm.html
- API Platform : https://api-platform.com/

## API

Le projet expose également des ressources API via Symfony et API Platform pour faciliter l'intégration avec d'autres outils ou applications.

Points d'accès principaux (selon la configuration du projet) :

- `/api` : point d'entrée principal de l'API
- `/api/docs` : documentation de l'API (si activée)
- Ressources liées aux entités de la bibliothèque : livres, lecteurs, genres, emprunts

Ces endpoints permettent de lire, créer, modifier ou supprimer des données via des requêtes HTTP JSON.

## Auteur

Projet réalisé dans le cadre du développement d'une application de gestion de bibliothèque.
