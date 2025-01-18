<!--
Fichier de mise en page avec le boilerplate HTML nécessaire pour toutes les pages de l'application.
Ce fichier est inclus dans toutes les pages de l'application.
-->

<?php
require 'navbar.php';

$completeTitle = "Gestion sportive | $title";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Chargement de la police d'écriture -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400..600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/styles/globals.css" />

  <!-- Ajout des tags méta, de l'OpenGraph, du titre, etc (le SEO) -->
  <title><?= $completeTitle ?></title>
  <meta name="title" content="<?= $completeTitle ?>" />
  <meta name="og:title" content="<?= $completeTitle ?>" />
  <meta name="og:type" content="website" />
  <meta name="description" content="Application de gestion sportive" />
  <meta property="theme-color" content="#e1ff00" />
  <meta name="copyright" content=<?= date('Y') ?> />
  <meta name="robots" content="index, follow" />
  <link rel="canonical" href=<?= $_SERVER['REQUEST_URI'] ?> />
  <!-- TODO: ajouter un favicon -->

  <!-- Ajout de l'HTML contenu dans la variable globale $head -->
  <?= $head ?>
</head>

<body>
  <?= $navbar ?>

  <?= $content ?>
</body>

</html>