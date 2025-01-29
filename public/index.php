<?php
// Routeur basique

// Les liens se font vers /?page=nom-de-la-page
$_GET['page'] ??= 'équipe';

require_once '../src/controllers/authentification.php';
require_once '../src/controllers/équipe.php';
require_once '../src/controllers/matches.php';

switch ($_GET['page']) {
  case 'connexion':
    ControleurAuthentification::connexion($_GET['erreur']);
    break;
  case 'équipe':
    ControleurÉquipe::joueurInfo(
      $_GET['joueur'],
      $_GET['query'],
      // on récupère les erreurs de validation
      isset($_GET['erreurs']) ? json_decode($_GET['erreurs'], true) : []
    );
    break;
  case 'matches':
    ControleurMatches::matchInfo(
      $_GET['match'],
      $_GET['tab'] ?? 'infos',
      $_GET['erreurs'] ? json_decode($_GET['erreurs'], true) : []
    );
    break;
  default:
    require_once '../src/views/404.php';
}