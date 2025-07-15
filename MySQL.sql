/* creation base de données*/
CREATE DATABASE IF NOT EXISTS globaljobs;
USE globaljobs;

/*creation table utilisateur*/
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(20) NOT NULL,
    prenom VARCHAR(30) NOT NULL,
    email VARCHAR(60) UNIQUE NOT NULL,
    adresse VARCHAR(20) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    password VARCHAR(200) NOT NULL,
    role ENUM('Candidat', 'Recruteur') NOT NULL,
    statut ENUM('activer', 'bloquer') DEFAULT 'activer',
    reset_token VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

/*creation table offres*/
CREATE TABLE IF NOT EXISTS offres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recruteur_id INT NOT NULL,
    titre VARCHAR(20) NOT NULL,
    description TEXT NOT NULL,
    lieu VARCHAR(40) NOT NULL,
    type_contrat VARCHAR(20) NOT NULL,
    domain VARCHAR(40) NOT NULL,
    date_postee TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_expire TIMESTAMP NOT NULL,
    statut ENUM('En attente', 'Publier', 'Rejété') DEFAULT 'En attente',
    FOREIGN KEY (recruteur_id) REFERENCES recruteurs(id) ON DELETE CASCADE
);


/*creation de la table candidature*/
CREATE TABLE IF NOT EXISTS Candidature (
    id INT PRIMARY KEY AUTO_INCREMENT,
    candidat_id INT,
    offre_id INT,
    statut ENUM('En attente', 'Accepté', 'Refusé') DEFAULT 'En attente',
    date_postulation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidat_id) REFERENCES candidat(id) ON DELETE CASCADE,
    FOREIGN KEY (offre_id) REFERENCES offres(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS candidat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    metier VARCHAR(50),
    cv TEXT, -- Stocker le lien vers le CV
    competences TEXT,
    niveau_etudes VARCHAR(40) NOT NULL,
    domain VARCHAR(30) NOT NULL,
    experience TEXT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS recruteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    entreprise VARCHAR(50) NOT NULL,
    secteur VARCHAR(20) NOT NULL,
    adresse_entreprise VARCHAR(30) NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

);

CREATE TABLE IF NOT EXISTS admin (
    idAdmin INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(30) NOT NULL,
    email VARCHAR(40) UNIQUE NOT NULL,
    password VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);