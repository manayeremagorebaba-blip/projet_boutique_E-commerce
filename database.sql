DROP DATABASE IF EXISTS boutique_php;
CREATE DATABASE boutique_php CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE boutique_php;

CREATE TABLE utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE produit (
    id_produit INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT '',
    stock INT NOT NULL DEFAULT 0
);

CREATE TABLE commande (
    id_commande INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    date_commande DATETIME NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    nom_livraison VARCHAR(100) NOT NULL,
    adresse TEXT NOT NULL,
    telephone VARCHAR(30) NOT NULL,
    mode_paiement VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE ligne_commande (
    id_ligne INT AUTO_INCREMENT PRIMARY KEY,
    id_commande INT NOT NULL,
    id_produit INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_commande) REFERENCES commande(id_commande),
    FOREIGN KEY (id_produit) REFERENCES produit(id_produit)
);

INSERT INTO produit (nom, description, prix, image, stock) VALUES
('PC Gamer Orion', 'Ordinateur puissant pour jouer et travailler.', 1299.99, 'images/pc-gamer.jpg', 8),
('Clavier RGB Nova', 'Clavier mecanique confortable avec eclairage RGB.', 79.90, 'images/clavier.jpg', 25),
('Souris Gaming Pulse', 'Souris rapide et precise pour les jeux video.', 39.90, 'images/souris.jpg', 40),
('Ecran 24 pouces Full HD', 'Ecran fluide avec une bonne qualite image.', 189.99, 'images/ecran.jpg', 15),
('Casque Audio Pro', 'Casque confortable avec micro integre.', 59.90, 'images/casque.jpg', 18),
('Webcam HD', 'Webcam ideale pour les cours et reunions en ligne.', 49.90, 'images/webcam.jpg', 12);
