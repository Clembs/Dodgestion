<?php
// Routeur basique

// Les liens se font vers /?page=nom-de-la-page
$page = $_GET['page'] ?? 'équipe';

require_once '../src/controllers/équipe.php';
require_once '../src/controllers/matches.php';

// on require la page correspondante avec la syntaxe match
require_once match ($page) {
  // il suffit d'ajouter comme clef le nom de la page, et comme valeur le contrôleur
  'équipe' => ControleurÉquipe::playerInfo(
    $_GET['joueur'],
    $_GET['query'],
    // on récupère les erreurs de validation
    $_GET['erreurs'] ? json_decode($_GET['erreurs'], true) : []
  ),
  'matches' => ControleurMatches::matchInfo(
    $_GET['match'],
    $_GET['tab'] ?? 'infos'
  ),
  // fallback sur une page 404
  default => '../src/views/404.php',
};
