<?php
// Routeur des envois de formulaire

// Les formulaires se font vers /?form=nom-du-formulaire
$form = $_GET['form'];

require_once '../src/controllers/équipe.php';

// Comme dans index.php, on require la page correspondante avec la syntaxe match
require_once match ($form) {
  'équipe' => ControleurÉquipe::updatePlayerInfo($_GET['joueur'], $_POST),
  default => '../src/views/404.php',
};