<?php
require_once __DIR__ . '/../models/session.php';
require_once __DIR__ . '/../models/joueur.php';
require_once __DIR__ . '/../helpers/validation.php';

class ControleurÉquipe
{
  public static function playerInfo(
    ?string $numeroLicense,
    ?string $recherche = '',
    $erreurs
  ): void {
    if (!Session::isLoggedIn()) {
      header('Location: /?page=connexion');
      return;
    }

    // On récupère les joueurs
    $joueurs = Joueur::getJoueurs();

    // On récupère le joueur sélectionné (ou le premier joueur si aucun numéro de licence n'est fourni)
    $joueurSelectionne = $joueurs[$numeroLicense ?? array_key_first($joueurs)];

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

    // Si le joueur sélectionné n'existe pas, on affiche une page 404
    if ($joueurSelectionne === null) {
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On affiche la page de l'équipe
    require __DIR__ . '/../views/équipe.php';

    // On définit le titre de la page (pour le layout)
    $title = 'Mon équipe';

    // On requiert le layout pour afficher la page
    require __DIR__ . '/../views/layout.php';
  }


  public static function updatePlayerInfo(string $numeroLicense, array $data): void
  {
    if (!isset($numeroLicense)) {
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On récupère le joueur
    $joueur = Joueur::findByNumeroLicense($numeroLicense);

    if ($joueur === null) {
      // Si le joueur n'existe pas, on affiche une page 404
      require __DIR__ . '/../views/404.php';
      return;
    }

    $erreurs = [];

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
    if (count($erreurs) > 0) {
      header("Location: /?page=équipe&joueur={$joueur->getNumeroLicense()}&erreurs=" . json_encode($erreurs));
      return;
    }

    // On met à jour les informations du joueur
    $joueur->update($data);

    // On rafrachît la page
    header("Location: /?page=équipe&joueur={$joueur->getNumeroLicense()}");
  }
}