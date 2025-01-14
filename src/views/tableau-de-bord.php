<?php $title = 'Accueil'; ?>

<?php ob_start(); ?>
<meta name="description" content="Tableau de bord de l'application de gestion sportive" />
<?php $head = ob_get_clean(); ?>

<?php ob_start(); ?>
<h1>Tableau de bord</h1>

<p>
  Matches à venir
</p>

<!-- TODO: tout -->

<?php $content = ob_get_clean(); ?>

<?php require 'layout.php'; ?>