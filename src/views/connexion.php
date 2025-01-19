<?php

/**
 * @var string $email
 * @var array $erreurs
 */

// Initialisation des variables pour les champs de saisie et erreurs
$email = $_POST['email'] ?? '';
$motDePasse = $_POST['mot_de_passe'] ?? '';
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Validation des champs
    if (empty($email)) {
      $erreurs[] = "L'adresse email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $erreurs[] = "Le format de l'adresse email est invalide.";
    }

    if (empty($motDePasse)) {
      $erreurs[] = "Le mot de passe est obligatoire.";
    }

    // Si aucune erreur, tentative de connexion
    if (empty($erreurs)) {
      ConnexionController::seConnecter($email, $motDePasse);
      exit; // Redirection effectuée par le contrôleur
    }
  } catch (Exception $e) {
    $erreurs[] = $e->getMessage(); // Capture des erreurs du contrôleur ou du modèle
  }
}
?>

<?php ob_start(); ?>

<main>
  <div class="surface" id="form-connexion">
    <h1>Connexion</h1>

    <!-- Affichage des erreurs -->
    <?php if (!empty($erreurs)): ?>
      <div class="error-list">
        <ul>
          <?php foreach ($erreurs as $erreur): ?>
            <li><?= htmlspecialchars($erreur) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form method="POST" action="?page=connexion">
      <div class="ligne">
        <label class="input" for="email">
          <div class="label">Email</div>
          <input
            type="email"
            name="email"
            id="email"
            value="<?= htmlspecialchars($email) ?>"
            required
            maxlength="100">
        </label>
      </div>

      <div class="ligne">
        <label class="input" for="mot_de_passe">
          <div class="label">Mot de passe</div>
          <input
            type="password"
            name="mot_de_passe"
            id="mot_de_passe"
            required
            maxlength="100">
        </label>
      </div>

      <div class="buttons">
        <button class="button" type="reset">Annuler</button>
        <button class="button primary" type="submit">Se connecter</button>
      </div>
    </form>
  </div>
</main>

<?php $content = ob_get_clean(); ?>