<?php
// Routeur des envois de formulaire

// Les formulaires se font vers /?form=nom-du-formulaire
require_once '../src/controllers/authentification.php';
require_once '../src/controllers/équipe.php';
require_once '../src/controllers/matches.php';

// Comme dans index.php, on require la page correspondante avec la syntaxe match
switch ($_GET['form']) {
  case 'connexion':
    ControleurAuthentification::connecter($_POST['email'], $_POST['mot_de_passe']);
    break;
  case 'modifier-joueur':
    ControleurÉquipe::updatePlayerInfo($_GET['joueur'], $_POST);
    break;
  case 'ajouter-participant':
    ControleurMatches::ajouterParticipation($_GET['match'], $_POST);
    break;
  default:
    require '../src/views/404.php';
}