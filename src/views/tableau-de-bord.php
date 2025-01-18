<?php $title = 'Tableau de bord'; ?>

<?php ob_start(); ?>
<main>
  <div class="surface">
    <h1>Tableau de bord</h1>

    <p>
      Matches à venir
    </p>
  </div>
  <!-- TODO: tout -->
</main>
<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
<!-- Ajout des balises meta -->
<meta name="description" content="Tableau de bord de l'application de gestion sportive" />

<!-- CSS -->
<style data-file="tableau-de-bord">
  .surface {
    padding: 1rem;
  }
</style>
<?php $head = isset($head) ? $head . ob_get_clean() : $head; ?>

<?php require 'layout.php'; ?>