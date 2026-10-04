CREATE DATABASE IF NOT EXISTS generateurCV;
USE generateurCV;

CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS template (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    image_apercu VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(255) NOT NULL UNIQUE,
    avec_photo BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS cv (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_template INT NOT NULL,
    titre VARCHAR(150),
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150),
    telephone BIGINT UNSIGNED NULL,
    adresse VARCHAR(200),
    photo VARCHAR(255),
    poste_recherche VARCHAR(150),
    experiences TEXT,
    formations TEXT,
    competences TEXT,
    langues TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (id_template) REFERENCES template(id)
);

INSERT IGNORE INTO template (nom, image_apercu, chemin_fichier, avec_photo) VALUES
('Classique', 'assets/img/apercu-classique.svg', 'modele-1', 0),
('Moderne', 'assets/img/apercu-moderne.svg', 'modele-2', 1),
('Elegant', 'assets/img/apercu-elegant.svg', 'modele-3', 1),
('Minimal', 'assets/img/apercu-minimal.svg', 'modele-4', 0),
('Creatif', 'assets/img/apercu-creatif.svg', 'modele-5', 0);


ALTER TABLE cv ADD COLUMN sommaire TEXT AFTER poste_recherche;
ALTER TABLE cv ADD COLUMN centres_interet TEXT AFTER langues;
ALTER TABLE cv ADD COLUMN references_cv VARCHAR(200) AFTER centres_interet;
