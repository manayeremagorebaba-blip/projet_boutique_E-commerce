Projet boutique_php

1. Mettre le dossier boutique_php dans htdocs si vous utilisez XAMPP.
2. Demarrer Apache et MySQL.
3. Ouvrir phpMyAdmin.
4. Importer le fichier database.sql.
5. Ouvrir http://localhost/boutique_php/register.php.
6. Creer un compte, se connecter, ajouter des produits, puis valider une commande.

Le dossier images peut contenir vos images de produits.
Le site fonctionne aussi sans images grace aux blocs de remplacement.


ESPACE ADMINISTRATEUR (fonctionnalité ajoutée)
1. L’administrateur se connecte séparément via http://localhost/boutique_php/admin.php. Le parcours de connexion des clients reste inchangé.
2. Identifiants de démonstration :
   Adresse : admin@newtech.local
   Mot de passe : Admin123!
3. Après connexion, l’administrateur arrive dans la boutique. Il peut ajouter un produit via « Ajouter un produit » et modifier un article via « Modifier ce produit ». La page admin.php est le point d’entrée unique : elle affiche le formulaire de connexion si aucune session administrateur n’est active, puis la gestion des produits après authentification. Elle reste protégée côté serveur et permet aussi de retirer les produits. Les clients ne voient aucun de ces boutons et ne peuvent pas ouvrir admin.php. Les changements sont enregistrés dans la table produit de MySQL. Les images sont enregistrées dans images/products/.
4. Avant une mise en ligne, changez les identifiants dans admin_config.php et protégez ce fichier côté serveur. Ne réutilisez pas le mot de passe de démonstration en production.

Remarque : si un produit est déjà lié à une commande, MySQL peut empêcher sa suppression afin de préserver l’historique des achats.
