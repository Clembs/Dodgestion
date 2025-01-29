<?php
require_once __DIR__ . '/../models/session.php';
require_once __DIR__ . '/../models/joueur.php';
require_once __DIR__ . '/../helpers/validation.php';

class ControleurÉquipe
{
  public static function playerInfo(
    ?string $numeroLicense,
    ?string $recherche = '',
    array $erreurs
  ): void {
    if (!Session::isLoggedIn()) {
      header('Location: /?page=connexion');
      return;
    }

    // On récupère les joueurs
    $joueurs = Joueur::getJoueurs();

    // Ne donner que les des joueurs filtrés selon $recherche (recherche par nom, prénom ou numéro de licence)
    $joueurs = array_filter(
      $joueurs,
      fn($joueur) =>
      str_contains(strtolower($joueur->getNom()), strtolower($recherche)) ||
      str_contains(strtolower($joueur->getPrenom()), strtolower($recherche)) ||
      str_contains(strtolower($joueur->getNumeroLicense()), strtolower($recherche))
    );

    // On trie les joueurs par leur clef (numéro de licence)
    ksort($joueurs);

    if ($numeroLicense === 'nouveau') {
      // Si le numéro de licence est 'nouveau', on met le $joueurSelectionne à null pour modifier l'URL du formulaire sur la page
      $joueurSelectionne = null;
    } else {
      // On récupère le joueur sélectionné (ou le premier joueur si aucun numéro de licence n'est fourni)
      $joueurSelectionne = $joueurs[$numeroLicense ?? array_key_first($joueurs)];

      // Si le joueur sélectionné n'existe pas, on affiche une page 404
      if ($joueurSelectionne === null) {
        require __DIR__ . '/../views/404.php';
        return;
      }
    }

    // On requiert la page de formulaire de création/édition
    require __DIR__ . '/../views/équipe/joueur.php';

    // On requiert le layout d'équipe (càd la barre latérale et les trucs fixes)
    require __DIR__ . '/../views/équipe/layout.php';

    // On définit le titre de la page (pour le layout)
    $title = 'Mon équipe';

    // On requiert le layout pour afficher la page
    require __DIR__ . '/../views/layout.php';
  }

  public static function addPlayer(array $data): void
  {
    $erreurs = [];

    $joueurExistant = Joueur::findByNumeroLicense($data['numero_license']);

    if ($joueurExistant !== null) {
      $erreurs['numero_license'] = 'Un joueur avec ce numéro de license existe déjà';
    }
    if (
      !in_array($data['statut'], array_map(
        fn($c) => $c->value,
        StatutJoueur::cases()
      ), true)
    ) {
      $erreurs['statut'] = 'Statut invalide';
    }
    if (!Validation::validateStringLength($data['prenom'], 1, 50)) {
      $erreurs['prenom'] = 'Prénom doit être compris entre 1 et 50 caractères';
    }
    if (!Validation::validateStringLength($data['nom'], 1, 50)) {
      $erreurs['nom'] = 'Nom doit être compris entre 1 et 50 caractères';
    }
    if (!Validation::validateNumber($data['taille'], 50, 250)) {
      $erreurs['taille'] = 'Taille doit être comprise entre 50 et 250 cm';
    }
    if (!Validation::validateNumber($data['poids'], 40, 200)) {
      $erreurs['poids'] = 'Poids doit être compris entre 40 et 200 kg';
    }

    // S'il y a des erreurs, on les affiche avant de créer le joueur
    if (!empty($erreurs)) {
      header("Location: /?page=équipe&joueur=nouveau&erreurs=" . json_encode($erreurs));
      return;
    }

    // On crée le joueur
    Joueur::create(
      prenom: $data['prenom'],
      nom: $data['nom'],
      numeroLicense: $data['numero_license'],
      dateNaissance: new DateTime($data['date_naissance']),
      taille: $data['taille'],
      poids: $data['poids'],
      note: $data['note'],
      statut: StatutJoueur::from($data['statut'])
    );

    // On rafrachît la page vers le joueur en question
    header("Location: /?page=équipe&joueur={$data['numero_license']}");
  }

  public static function updatePlayerInfo(string $numeroLicense, array $data): void
  {
    if (!isset($numeroLicense)) {
      require __DIR__ . '/../views/404.php';
      return;
    }

    $erreurs = [];

    // On récupère le joueur
    $joueur = Joueur::findByNumeroLicense($numeroLicense);

    if ($joueur === null) {
      // Si le joueur n'existe pas, on affiche une page 404
      $erreurs['numero_license'] = 'Joueur introuvable';
    }

    $joueurExistant = Joueur::findByNumeroLicense($data['numero_license']);

    if ($joueurExistant !== null && $joueurExistant->getId() !== $joueur->getId()) {
      $erreurs['numero_license'] = 'Un autre joueur avec ce numéro de license existe déjà';
    }

    if (
      !in_array($data['statut'], array_map(
        fn($c) => $c->value,
        StatutJoueur::cases()
      ), true)
    ) {
      $erreurs['statut'] = 'Statut invalide';
    }
    if (!Validation::validateStringLength($data['prenom'], 1, 50)) {
      $erreurs['prenom'] = 'Prénom doit être compris entre 1 et 50 caractères';
    }
    if (!Validation::validateStringLength($data['nom'], 1, 50)) {
      $erreurs['nom'] = 'Nom doit être compris entre 1 et 50 caractères';
    }
    if (!Validation::validateNumber($data['taille'], 50, 250)) {
      $erreurs['taille'] = 'Taille doit être comprise entre 50 et 250 cm';
    }
    if (!Validation::validateNumber($data['poids'], 40, 200)) {
      $erreurs['poids'] = 'Poids doit être compris entre 40 et 200 kg';
    }

    // S'il y a des erreurs, on les affiche avant de mettre à jour le joueur
    if (!empty($erreurs)) {
      header("Location: /?page=équipe&joueur={$joueur->getNumeroLicense()}&erreurs=" . json_encode($erreurs));
      return;
    }

    // On met à jour les informations du joueur
    $joueur->update(
      prenom: $data['prenom'],
      nom: $data['nom'],
      numeroLicense: $data['numero_license'],
      dateNaissance: new DateTime($data['date_naissance']),
      taille: $data['taille'],
      poids: $data['poids'],
      note: $data['note'],
      statut: StatutJoueur::from($data['statut'])
    );

    // On rafrachît la page
    header("Location: /?page=équipe&joueur={$joueur->getNumeroLicense()}");
  }
}