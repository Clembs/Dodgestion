CREATE TABLE utilisateurs {
  id_utilisateur SERIAL PRIMARY KEY,
  email VARCHAR(50) NOT NULL,
  mot_de_passe VARCHAR(50) NOT NULL,
  pseudo VARCHAR(50) NOT NULL,
};

-- CREATE TYPE statut AS ENUM('ACTIF', 'BLESSE', 'SUSPENDU', 'ABSENT');

-- CREATE TABLE joueurs {
--   id_joueur SERIAL PRIMARY KEY,
--   prenom VARCHAR(50) NOT NULL,
--   nom VARCHAR(50) NOT NULL,
--   note TEXT,
--   statut STATUT NOT NULL DEFAULT 'ACTIF',
-- };

CREATE TABLE sessions {
  id_session SERIAL PRIMARY KEY,
  expires TIMESTAMP NOT NULL,
  id_utilisateur INT NOT NULL,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur),
};