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
  case 'ajouter-joueur':
    ControleurÉquipe::ajouterJoueur($_POST);
    break;
  case 'modifier-joueur':
    ControleurÉquipe::modifierJoueur($_GET['joueur'], $_POST);
    break;
  case 'supprimer-joueur':
    ControleurÉquipe::supprimerJoueur($_GET['joueur']);
    break;
  case 'ajouter-participant':
    ControleurMatches::ajouterParticipation($_GET['match'], $_POST);
    break;
  case 'supprimer-participant':
    ControleurMatches::supprimerParticipation($_GET['participant']);
    break;
  case 'ajouter-match':
    ControleurMatches::ajouterRencontre($_POST);
    break;
  case 'modifier-match':
    ControleurMatches::modifierRencontre($_GET['match'], $_POST);
    break;
  case 'supprimer-match':
    ControleurMatches::supprimerRencontre($_GET['match']);
    break;
  default:
    require '../src/views/404.php';
}