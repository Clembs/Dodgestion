<?php

class ControleurÉquipe
{
  public static function playerInfo(?string $numeroLicense): void
  {
    require __DIR__ . '/../models/joueur.php';

    // On récupère les joueurs
    /**
     * @var Joueur[] $joueurs
     */
    $joueurs = Joueur::getJoueurs();

    // On récupère le joueur sélectionné (ou le premier joueur si aucun numéro de licence n'est fourni)
    $joueurSelectionne = $joueurs[$numeroLicense ?? array_key_first($joueurs)];

    // Si le joueur sélectionné n'existe pas, on affiche une page 404
    if ($joueurSelectionne === null) {
      require __DIR__ . '/../views/404.php';
    }

    // On affiche la page de l'équipe
    require __DIR__ . '/../views/équipe.php';

    // On définit le titre de la page (pour le layout)
    $title = 'Mon équipe';

    // On requiert le layout pour afficher la page
    require __DIR__ . '/../views/layout.php';
  }
}