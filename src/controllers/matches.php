<?php

class ControleurMatches
{
  public static function matchInfo(?string $matchId, string $currentTab)
  {
    require __DIR__ . '/../models/rencontre.php';

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
      'resultats' => [
        'label' => 'Résultats',
        'file' => '/../views/matches/resultats.php'
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
}