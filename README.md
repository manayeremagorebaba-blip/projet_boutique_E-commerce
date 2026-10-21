# New-Tech — Boutique en ligne

Site e-commerce développé en PHP proposant du matériel informatique et gaming. Les utilisateurs peuvent créer un compte, parcourir le catalogue, gérer leur panier et passer commande.

Une interface d’administration permet également de gérer la boutique.
## Fonctionnalités

- Inscription et connexion des utilisateurs
- Catalogue de produits avec description, prix et stock
- Gestion du panier et validation des commandes
- Choix de la livraison et du mode de paiement
- Espace administrateur sécurisé
- Gestion des produits et suivi des commandes

## Technologies utilisées

- PHP (PDO)
- MySQL — base de données
- HTML / CSS — structure et mise en page
- Sessions PHP — gestion de la connexion

## Structure du projet

**Espace client**
- `register.php` — inscription
- `login.php` / `logout.php` — connexion et déconnexion
- `index.php` — catalogue de produits
- `ajouter.php` / `panier.php` / `maj_panier.php` — gestion du panier
- `paiement.php` / `confirmation.php` — commande et confirmation

**Administration**
- `admin.php` — interface d’administration
- `admin_login.php` / `admin_logout.php` — connexion et déconnexion administrateur
- `admin_auth_check.php` — protection des pages administrateur
- `admin_config.php` — configuration de l’administration

**Fichiers communs**
- `connexion.php` — connexion à la base de données
- `header.php` / `footer.php` — éléments communs du site
- `style.css` — mise en page
- `database.sql` — structure et données de la base


## Installation

Ce projet nécessite un serveur local avec PHP et MySQL (XAMPP ou WAMP).

1. Installer XAMPP : https://www.apachefriends.org
2. Placer le dossier `boutique_php` dans le répertoire `htdocs`
3. Démarrer **Apache** et **MySQL** depuis le panneau XAMPP
4. Ouvrir `http://localhost/phpmyadmin` et importer le fichier `database.sql`
5. Ouvrir le site : `http://localhost/boutique_php/register.php`
6. Créer un compte, se connecter, ajouter des produits, puis valider une commande

Le dossier `images` peut contenir les visuels des produits. Le site fonctionne aussi sans images grâce à des blocs de remplacement.

## Base de données

La base `boutique_php` contient quatre tables :
- `utilisateur` — comptes clients
- `produit` — articles de la boutique
- `commande` — commandes passées
- `ligne_commande` — détail des produits de chaque commande

## Sécurité

- Mots de passe chiffrés avant enregistrement (`password_hash`)
- Requêtes préparées (PDO) pour se protéger des injections SQL
- Accès administrateur protégé

## Auteur

Projet réalisé dans le cadre de ma première année de Bachelor Informatique et Systèmes Digitaux à l’EPSI.
