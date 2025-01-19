CREATE TABLE utilisateurs (
  id_utilisateur SERIAL PRIMARY KEY,
  email VARCHAR(50) NOT NULL,
  mot_de_passe VARCHAR(50) NOT NULL,
  pseudo VARCHAR(50) NOT NULL,
  date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TYPE STATUT_JOUEUR AS ENUM ('ACTIF', 'BLESSE', 'SUSPENDU', 'ABSENT');

CREATE TABLE joueurs (
  id_joueur SERIAL PRIMARY KEY,
  prenom VARCHAR(50) NOT NULL,
  nom VARCHAR(50) NOT NULL,
  numero_license VARCHAR(50) UNIQUE NOT NULL,
  date_naissance DATE NOT NULL,
  taille INT NOT NULL,
  poids INT NOT NULL,
  note TEXT,
  statut STATUT_JOUEUR NOT NULL DEFAULT 'ACTIF'
);

CREATE TABLE sessions (
  id_session SERIAL PRIMARY KEY,
  expires TIMESTAMP NOT NULL,
  id_utilisateur INT NOT NULL,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
);

CREATE TYPE RESULTAT_MATCH AS ENUM ('VICTOIRE', 'DEFAITE', 'NUL');

CREATE TABLE matches (
  id_match SERIAL PRIMARY KEY,
  date_match TIMESTAMP NOT NULL,
  lieu VARCHAR(50) NOT NULL,
  nom_adversaire VARCHAR(50) NOT NULL,
  resultat RESULTAT_MATCH NOT NULL,
  date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TYPE POSITION_JOUEUR AS ENUM ('AVANT', 'ARRIERE');

CREATE TABLE participations (
  id_participation SERIAL PRIMARY KEY,
  id_joueur INT NOT NULL,
  id_match INT NOT NULL,
  note INT CHECK (note BETWEEN 0 AND 5), -- note sur 5 sur la performance du joueur (après match)
  commentaire TEXT, -- commentaire sur la performance du joueur (après match)
  position POSITION_JOUEUR NOT NULL,
  FOREIGN KEY (id_joueur) REFERENCES joueurs(id_joueur),
  FOREIGN KEY (id_match) REFERENCES matches(id_match)
);