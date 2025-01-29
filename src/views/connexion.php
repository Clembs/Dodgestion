<?php
/**
 * @var string $email
 * @var string $erreur
 */
?>

<?php ob_start(); ?>

<main>
  <div class="surface">
    <h1>Connexion à Dodgestion</h1>

    <!-- Formulaire de connexion -->
    <form method="POST" action="/submit.php?form=connexion">
      <label class="input full" for="email">
        <div class="label">Email</div>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required maxlength="100">
      </label>

      <label class="input full" for="mot_de_passe">
        <div class="label">Mot de passe</div>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required maxlength="100">
      </label>

      <?php if (!empty($erreur)): ?>
        <div class="error">
          <?= $erreur ?>
        </div>
      <?php endif; ?>

      <button class="button primary" type="submit">Se connecter</button>
      <button id="creer-compte" class="button inline" disabled>Créer un compte</button>
    </form>
  </div>
</main>

<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>

<style data-file="connexion">
  main {
    display: flex;
    justify-content: center;
    align-items: center;
    flex: 1;
  }

  h1 {
    margin: 0;
  }

  .surface {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    padding: 1.5rem;
  }

  form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .button {
    margin-top: 1rem;
  }

  #creer-compte {
    align-self: flex-end;
  }
</style>

<?php $head .= ob_get_clean(); ?>