<?php
// Routeur des envois de formulaire

// Les formulaires se font vers /?form=nom-du-formulaire
$form = $_GET['form'];

require_once '../src/controllers/authentification.php';
require_once '../src/controllers/équipe.php';
require_once '../src/controllers/matches.php';

// Comme dans index.php, on require la page correspondante avec la syntaxe match
require_once match ($form) {
  'connexion' => ControleurAuthentification::connecter($_POST['email'], $_POST['mot_de_passe']),
  'équipe' => ControleurÉquipe::updatePlayerInfo($_GET['joueur'], $_POST),
  'ajouter-participant' => ControleurMatches::ajouterParticipation($_GET['match'], $_POST),
  default => '../src/views/404.php',
};