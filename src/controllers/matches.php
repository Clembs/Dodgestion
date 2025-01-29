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

    // On sépare les rencontres en deux tableaux : celles passées et celles à venir
    $rencontresFutures = array_filter(
      $rencontres,
      fn(Rencontre $rF) => $rF->getDate() > new DateTime()
    );

    $rencontresPassees = array_filter(
      $rencontres,
      fn(Rencontre $rP) => $rP->getDate() <= new DateTime()
    );

    // On récupère la rencontre sélectionnée (ou la rencontre la plus récente si aucun match n'est fourni)
    $rencontreSelectionnee = $matchId !== null
      ? ($rencontres[(int) $matchId] ?? null)
      : current($rencontresFutures);

    // On les trie par date (ordre décroissant)
    usort(
      $rencontres,
      fn($a, $b) =>
      $b->getDate() <=> $a->getDate()
    );

    // Si la rencontre sélectionnée n'existe pas, on affiche une page 404
    if ($rencontreSelectionnee === null) {
      require __DIR__ . '/../views/404.php';
      return;
    }

    $tabs = [
      'infos' => [
        'label' => 'Informations générales',
        'file' => '/../views/matches/infos.php'
      ],
      'joueurs' => [
        'label' => 'Joueurs',
        'file' => '/../views/matches/joueurs.php'
      ],
      'stats' => [
        'label' => 'Statistiques',
        'file' => '/../views/matches/stats.php'
      ]
    ];

    if ($currentTab === 'joueurs') {
      $participants = Participation::getAllByRencontre($rencontreSelectionnee->getId());


      $joueurs = array_filter(
        Joueur::getJoueurs(),
        fn(Joueur $j) => $j->getStatut() === StatutJoueur::ACTIF && !in_array($j->getId(), array_map(fn($p) => $p->getJoueur()->getId(), $participants))
      );

    }

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
}