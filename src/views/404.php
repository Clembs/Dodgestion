<?php $title = '404'; ?>

<?php ob_start(); ?>

<h1>404</h1>

<p>Cette page n'a pas pu être trouvée.</p>

<a href="/index.php">
  Retour à l'accueil
</a>

<?php $content = ob_get_clean(); ?>

<?php require 'layout.php'; ?>