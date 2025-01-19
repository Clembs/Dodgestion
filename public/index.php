<?php
// Routeur basique

// Les liens se font vers /?page=nom-de-la-page
$page = $_GET['page'] ?? 'tableau-de-bord';

require_once '../src/controllers/équipe.php';
require_once '../src/controllers/matches.php';

// on require la page correspondante avec la syntaxe match
require_once match ($page) {
  // il suffit d'ajout comme cas un nom de page et le chemin vers le fichier
  'tableau-de-bord' => '../src/views/tableau-de-bord.php',
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
