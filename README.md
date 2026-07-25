# New-Tech — Boutique en ligne

Site e-commerce développé en PHP simulant une boutique de matériel informatique et gaming. Les visiteurs créent un compte, parcourent le catalogue, ajoutent des produits au panier et valident une commande avec choix de la livraison et du mode de paiement.

Projet réalisé dans le cadre de ma première année de Bachelor Informatique et Systèmes Digitaux à l'EPSI.

## Fonctionnalités

- Inscription et connexion des utilisateurs (comptes sécurisés)
- Catalogue de produits avec description, prix et stock
- Panier d'achat : ajout, modification des quantités et suppression
- Validation de commande avec informations de livraison
- Choix du mode de paiement et page de confirmation
- Commandes enregistrées en base de données

## Technologies utilisées

- PHP (PDO)
- MySQL — base de données
- HTML / CSS — structure et mise en page
- Sessions PHP — gestion de la connexion

## Structure du projet

**Authentification**
- `register.php` — création de compte
- `login.php` / `logout.php` — connexion et déconnexion
- `auth_check.php` — protection des pages réservées aux membres

**Boutique**
- `index.php` — catalogue des produits
- `ajouter.php` — ajout d'un produit au panier
- `panier.php` — affichage du panier
- `maj_panier.php` — mise à jour des quantités

**Commande**
- `paiement.php` — informations de livraison et de paiement
- `confirmation.php` — récapitulatif de la commande validée

**Éléments communs**
- `connexion.php` — connexion à la base de données
- `header.php` / `footer.php` — en-tête et pied de page
- `style.css` — feuille de style
- `database.sql` — création de la base et produits de démonstration

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

## Auteur

Réalisé par Mana, étudiant en Bachelor Informatique et Systèmes Digitaux à l'EPSI.
