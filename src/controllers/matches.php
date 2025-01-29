<?php
require_once __DIR__ . '/../models/rencontre.php';
require_once __DIR__ . '/../models/joueur.php';
require_once __DIR__ . '/../models/participation.php';
require_once __DIR__ . '/../helpers/validation.php';
require_once __DIR__ . '/../models/session.php';

class ControleurMatches
{
  public static function matchInfo(?string $matchId, string $currentTab, array $erreurs)
  {
    if (!Session::isLoggedIn()) {
      header('Location: /?page=connexion');
      return;
    }

    // On récupère les rencontres
    $rencontres = Rencontre::getRencontres();

    // On sépare les rencontres en deux tableaux : celles passées ou avec un résultat et celles à venir
    $rencontresFutures = array_filter(
      $rencontres,
      fn(Rencontre $rF) => $rF->getDate() > new DateTime() && !$rF->getResultat()
    );

    $rencontresPassees = array_filter(
      $rencontres,
      fn(Rencontre $rP) => $rP->getDate() <= new DateTime() || !!$rP->getResultat()
    );

    $tabs = [
      'infos' => [
        'label' => 'Informations générales',
        'file' => '/../views/matches/infos.php'
      ],
      'joueurs' => [
        'label' => 'Joueurs',
        'file' => '/../views/matches/joueurs.php'
      ],
    ];

    if ($matchId === 'nouveau') {
      // Si l'ID du match est 'nouveau', on met la rencontre sélectionnée à null pour modifier l'URL du formulaire sur la page
      $rencontreSelectionnee = null;

      // Le seul onglet sélectionnable pour une création de match est les infos
      if ($currentTab !== 'infos') {
        require __DIR__ . '/../views/404.php';
        return;
      }

    } else {
      // On récupère la rencontre sélectionnée (ou la rencontre la plus récente si aucun match n'est fourni)
      $rencontreSelectionnee = $matchId !== null
        ? ($rencontres[(int) $matchId] ?? null)
        : current($rencontresFutures);

      // Si la rencontre sélectionnée n'existe pas, on affiche une page 404
      if ($rencontreSelectionnee === null) {
        require __DIR__ . '/../views/404.php';
        return;
      }

      if ($currentTab === 'joueurs' || $currentTab === 'stats') {
        $participants = Participation::getAllByRencontre($rencontreSelectionnee->getId());

        $joueurs = array_filter(
          Joueur::getJoueurs(),
          fn(Joueur $j) => $j->getStatut() === StatutJoueur::ACTIF && !in_array($j->getId(), array_map(fn($p) => $p->getJoueur()->getId(), $participants))
        );
      }
    }

    // On les trie par date (ordre décroissant)
    usort(
      $rencontres,
      fn($a, $b) =>
      $b->getDate() <=> $a->getDate()
    );

    try {
      // On affiche le contenu de l'onglet sélectionné
      require __DIR__ . $tabs[$currentTab]['file'];
    } catch (Exception $e) {
      // Si l'onglet n'existe pas, on affiche une page 404
      require __DIR__ . '/../views/404.php';
      return;
    }


    // On affiche le layout de page de la rencontre
    require __DIR__ . '/../views/matches/layout.php';

    // On définit le titre de la page (pour le layout)
    $title = 'Mes matches';

    // On requiert le layout pour afficher la page
    require __DIR__ . '/../views/layout.php';
  }

  public static function ajouterRencontre(array $data): void
  {
    $erreurs = [];

    foreach (['nom-adversaire', 'lieu', 'date'] as $key) {
      if (!isset($data[$key])) {
        $erreurs[$key] = 'Ce champ est requis.';
      }
    }

    if (!Validation::validateStringLength($data['nom-adversaire'], 2, 50)) {
      $erreurs['nom-adversaire'] = "Le nom de l'adversaire doit être compris entre 2 et 50 caractères.";
    }

    if (!Validation::validateStringLength($data['lieu'], 2, 50)) {
      $erreurs['lieu'] = "Le nom du lieu doit être compris entre 2 et 50 caractères.";
    }

    if (isset($data['resultat']) && $data['resultat'] !== 'NON-DEFINI' && !ResultatRencontre::tryFrom($data['resultat'])) {
      $erreurs['resultat'] = 'Résultat invalide.';
    }

    if (!empty($erreurs)) {
      header("Location: /?page=matches&match=nouveau&erreurs=" . json_encode($erreurs));
      return;
    }

    // On crée la rencontre
    $nouvelleRencontre = Rencontre::create(
      nomAdversaire: $data['nom-adversaire'],
      lieu: $data['lieu'],
      date: new DateTime($data['date']),
      resultat: null
    );

    header("Location: /?page=matches&match={$nouvelleRencontre->getId()}");
  }

  public static function modifierRencontre(string $matchId, array $data): void
  {
    if (!isset($matchId)) {
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On récupère la rencontre
    $rencontre = Rencontre::read($matchId);

    if ($rencontre === null) {
      // Si la rencontre n'existe pas, on affiche une page 404
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On valide les données
    $erreurs = [];

    foreach (['nom-adversaire', 'lieu', 'date'] as $key) {
      if (!isset($data[$key])) {
        $erreurs[$key] = 'Ce champ est requis.';
      }
    }

    if (!Validation::validateStringLength($data['nom-adversaire'], 2, 50)) {
      $erreurs['nom-adversaire'] = "Le nom de l'adversaire doit être compris entre 2 et 50 caractères.";
    }

    if (!Validation::validateStringLength($data['lieu'], 2, 50)) {
      $erreurs['lieu'] = "Le nom du lieu doit être compris entre 2 et 50 caractères.";
    }

    if (isset($data['resultat']) && $data['resultat'] === 'NON-DEFINI' && $rencontre->getDate() <= new DateTime()) {
      $erreurs['resultat'] = 'Le résultat ne peut être non-défini car il est terminé.';
    }

    if (isset($data['resultat']) && $data['resultat'] !== 'NON-DEFINI' && !ResultatRencontre::tryFrom($data['resultat'])) {
      $erreurs['resultat'] = 'Résultat invalide.';
    }

    // S'il y a des erreurs, on les affiche avant de modifier le match
    if (!empty($erreurs)) {
      header("Location: /?page=matches&match=$matchId&erreurs=" . json_encode($erreurs));
      exit;
    }

    // On modifie le match
    $rencontre->update(
      nomAdversaire: $data['nom-adversaire'],
      lieu: $data['lieu'],
      date: new DateTime($data['date']),
      resultat: (!isset($data['resultat']) || $data['resultat'] === 'NON-DEFINI') ? null : ResultatRencontre::from($data['resultat'])
    );

    // On rafraîchit la page vers le match
    header("Location: /?page=matches&match=$matchId");
  }

  public static function supprimerRencontre(string $matchId): void
  {
    if (!isset($matchId)) {
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On récupère la rencontre
    $rencontre = Rencontre::read($matchId);

    if ($rencontre === null) {
      // Si la rencontre n'existe pas, on affiche une page 404
      require __DIR__ . '/../views/404.php';
      return;
    }

    // Si le match est passé ou a un résultat, on ne peut pas le supprimer
    if ($rencontre->getDate() <= new DateTime() || $rencontre->getResultat() !== null) {
      header("Location: /?page=matches&match=$matchId&erreurs=" . json_encode(['match' => 'Impossible de supprimer un match terminé ou avec un résultat.']));
      return;
    }

    // On supprime les participations liées
    Participation::deleteAllByRencontre($rencontre->getId());

    // On supprime la rencontre
    $rencontre->delete();

    // On redirige vers la page des matches
    header('Location: /?page=matches');
  }

  public static function ajouterParticipation(string $matchId, array $data): void
  {
    if (!isset($matchId)) {
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On récupère la rencontre
    $rencontre = Rencontre::read($matchId);

    if ($rencontre === null) {
      // Si la rencontre n'existe pas, on affiche une page 404
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On valide les données
    $erreurs = [];

    foreach (['joueur-numero', 'position', 'role'] as $key) {
      if (!isset($data[$key])) {
        $erreurs[$key] = 'Ce champ est requis.';
      }
    }

    // beaucoup de ces cas sont juste impossibles dans le front-end sans modification de l'interface,
    // donc c'est très overkill mais j'aime beaucoup faire une validation de fond en comble
    // parce que je suis moi-même un petit malin qui aime bien tester les limites des systèmes
    // et ça serait un peu embêtant que les utilisateurs puissent faire pareil

    if (!in_array($data['position'], ['AVANT', 'ARRIERE'])) {
      $erreurs['position'] = 'La position est invalide.';
    }

    if (!in_array($data['role'], ['TITULAIRE', 'REMPLACANT'])) {
      $erreurs['role'] = 'Le rôle est invalide.';
    }

    $joueur = Joueur::findByNumeroLicense($data['joueur-numero']);

    if ($joueur === null) {
      $erreurs['joueur-numero'] = 'Le joueur n\'existe pas.';
    }

    if ($joueur->getStatut() !== StatutJoueur::ACTIF) {
      $erreurs['joueur-numero'] = "Impossible d'ajouter une participation pour un joueur inactif.";
    }

    $participations = Participation::getAllByRencontre($rencontre->getId());

    $participation = array_filter(
      $participations,
      fn(Participation $p) => $p->getJoueur()->getId() === $joueur->getId()
    )[0];

    if ($participation !== null) {
      $erreurs['joueur-numero'] = 'Le joueur participe déjà à ce match.';
    }

    if (count($participations) >= 10) {
      $erreurs['joueur-numero'] = "Impossible d'ajouter plus de 10 participations à une rencontre.";
    }

    if (!empty($erreurs)) {
      header("Location: /?page=matches&match=$matchId&tab=joueurs&erreurs=" . json_encode($erreurs));
      return;
    }

    // On crée la participation    
    $participation = Participation::create(
      $joueur,
      $rencontre,
      null,
      null,
      PositionJoueur::from($data['position']),
      RoleJoueur::from($data['role'])
    );

    header("Location: /?page=matches&match=$matchId&tab=joueurs");
  }

  public static function modifierParticipation(string $idParticipant, array $data): void
  {
    if (!isset($idParticipant)) {
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On récupère la participation
    $participation = Participation::read($idParticipant);

    if ($participation === null) {
      // Si la participation n'existe pas, on affiche une page 404
      require __DIR__ . '/../views/404.php';
      return;
    }

    $rencontre = $participation->getRencontre();
    $estPassee = $rencontre->getDate() <= new DateTime() || $rencontre->getResultat();

    // On valide les données
    $erreurs = [];

    // On valide la note & le commentaire si le match est passé, ou le rôle et la position si le match est à venir
    if ($estPassee) {
      if (isset($data['note']) && !Validation::validateNumber($data['note'], 0, 5)) {
        $erreurs['note'] = 'La note doit être comprise entre 0 et 5.';
      }

      if (isset($data['commentaire']) && !Validation::validateStringLength($data['commentaire'], 0, 255)) {
        $erreurs['commentaire'] = 'Le commentaire doit être inférieur à 255 caractères.';
      }

      if (!empty($erreurs)) {
        header("Location: /?page=matches&match=" . $participation->getRencontre()->getId() . "&tab=joueurs&participant=$idParticipant&erreurs=" . json_encode($erreurs));
        return;
      }

      // On modifie la participation
      $participation->update(
        note: $data['note'] ?? null,
        commentaire: $data['commentaire'] ?? null,
        position: null,
        roleJoueur: null
      );
    } else {
      if (isset($data['position']) && !in_array($data['position'], ['AVANT', 'ARRIERE'])) {
        $erreurs['position'] = 'La position est invalide.';
      }

      if (isset($data['role']) && !in_array($data['role'], ['TITULAIRE', 'REMPLACANT'])) {
        $erreurs['role'] = 'Le rôle est invalide.';
      }

      if (!empty($erreurs)) {
        header("Location: /?page=matches&match=" . $participation->getRencontre()->getId() . "&tab=joueurs&participant=$idParticipant&erreurs=" . json_encode($erreurs));
        return;
      }

      // On modifie la participation
      $participation->update(
        note: null,
        commentaire: null,
        position: PositionJoueur::from($data['position']),
        roleJoueur: RoleJoueur::from($data['role'])
      );
    }

    // On rafraîchit la page vers la participation
    header("Location: /?page=matches&match=" . $participation->getRencontre()->getId() . "&tab=joueurs");
  }

  public static function supprimerParticipation(string $idParticipant): void
  {
    if (!isset($idParticipant)) {
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On récupère la participation
    $participation = Participation::read($idParticipant);

    if ($participation === null) {
      // Si la participation n'existe pas, on affiche une page 404
      require __DIR__ . '/../views/404.php';
      return;
    }

    // On supprime la participation
    $participation->delete();

    // On redirige vers la page des matches
    header('Location: /?page=matches&match=' . $participation->getRencontre()->getId() . '&tab=joueurs');
  }
}