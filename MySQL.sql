/* creation base de données*/
CREATE DATABASE IF NOT EXISTS globaljobs;
USE globaljobs;

/*creation table utilisateur*/
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(200) UNIQUE NOT NULL,
    telephone VARCHAR(100) NOT NULL,
    password VARCHAR(200) NOT NULL,
    statut ENUM('Candidat', 'Recruteur') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

/*creation table offres*/
CREATE TABLE IF NOT EXISTS offres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recruteur_id INT NOT NULL,
    titre VARCHAR(60) NOT NULL,
    description TEXT NOT NULL,
    lieu VARCHAR(40) NOT NULL,
    secteur VARCHAR(40) NOT NULL,
    date_postee TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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


/*creation de la table message pour gerer le discution entre le recruteur et le client*/
 DROP TABLE IF EXISTS messages;

CREATE TABLE IF NOT EXISTS messages (
  id_message INT NOT NULL AUTO_INCREMENT,
  id_expediteur INT NOT NULL,
  id_destinataire INT NOT NULL,
  contenu TEXT COLLATE utf8mb4_general_ci NOT NULL,
  date_envoi DATETIME NOT NULL,
  PRIMARY KEY (id_message),
  KEY id_expediteur (id_expediteur),
  KEY id_destinataire (id_destinataire)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS candidat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    metier VARCHAR(50),
    cv TEXT, -- Stocker le lien vers le CV
    competences TEXT,
    niveau_etudes VARCHAR(100),
    experience TEXT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS recruteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    entreprise VARCHAR(50) NOT NULL,
    email_entreprise VARCHAR(50) UNIQUE NOT NULL,
    secteur VARCHAR(20) NOT NULL,
    adresse VARCHAR(20) NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

);

CREATE TABLE IF NOT EXISTS admin (
    idAdmin INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);