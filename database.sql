-- =====================================================================
-- Base de données : bibliotheque_en_ligne
-- Projet : Création d'un Site Web pour une Bibliothèque en Ligne
-- =====================================================================

CREATE DATABASE IF NOT EXISTS bibliotheque_en_ligne
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE bibliotheque_en_ligne;

-- ---------------------------------------------------------------------
-- Table : livres
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS liste_lecture;
DROP TABLE IF EXISTS livres;
DROP TABLE IF EXISTS lecteurs;

CREATE TABLE livres (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    titre              VARCHAR(100) NOT NULL,
    auteur             VARCHAR(100) NOT NULL,
    description        TEXT,
    maison_edition     VARCHAR(100),
    nombre_exemplaire  INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : lecteurs
-- ---------------------------------------------------------------------
CREATE TABLE lecteurs (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    nom     VARCHAR(100) NOT NULL,
    prenom  VARCHAR(100) NOT NULL,
    email   VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : liste_lecture (table de liaison livres <-> lecteurs)
-- ---------------------------------------------------------------------
CREATE TABLE liste_lecture (
    id_livre     INT NOT NULL,
    id_lecteur   INT NOT NULL,
    date_emprunt DATE NOT NULL,
    date_retour  DATE NULL,
    PRIMARY KEY (id_livre, id_lecteur),
    CONSTRAINT fk_liste_livre
        FOREIGN KEY (id_livre) REFERENCES livres(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_liste_lecteur
        FOREIGN KEY (id_lecteur) REFERENCES lecteurs(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Données de démonstration
-- ---------------------------------------------------------------------
INSERT INTO livres (titre, auteur, description, maison_edition, nombre_exemplaire) VALUES
('Le Petit Prince', 'Antoine de Saint-Exupéry', 'Un conte poétique et philosophique sous l''apparence d''un livre pour enfants.', 'Gallimard', 5),
('Les Misérables', 'Victor Hugo', 'Fresque sociale et historique de la France du XIXe siècle.', 'A. Lacroix, Verboeckhoven & Cie', 3),
('L''Étranger', 'Albert Camus', 'Roman emblématique de la philosophie de l''absurde.', 'Gallimard', 4),
('Une si longue lettre', 'Mariama Bâ', 'Roman épistolaire sénégalais sur la condition de la femme.', 'Nouvelles Éditions Africaines', 2),
('Vingt mille lieues sous les mers', 'Jules Verne', 'Roman d''aventures et de science-fiction.', 'Pierre-Jules Hetzel', 6),
('Le Rouge et le Noir', 'Stendhal', 'Roman d''apprentissage et critique sociale de la Restauration.', 'Levasseur', 3);

INSERT INTO lecteurs (nom, prenom, email) VALUES
('Diop', 'Awa', 'awa.diop@example.com'),
('Ndiaye', 'Moussa', 'moussa.ndiaye@example.com');

INSERT INTO liste_lecture (id_livre, id_lecteur, date_emprunt, date_retour) VALUES
(1, 1, '2026-09-01', NULL),
(3, 1, '2026-08-15', '2026-09-01');
