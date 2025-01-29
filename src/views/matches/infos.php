<?php
/**
 * @var ?Rencontre $rencontreSelectionnee
 * @var array $erreurs
 */

$mode = $rencontreSelectionnee ? 'modifier' : 'ajouter';
?>

<?php ob_start(); ?>

<?php if ($mode === 'modifier'): ?>
  <form action="/submit.php?form=modifier-match&match=<?= $rencontreSelectionnee?->getId() ?>" method="post">
  <?php else: ?>
    <form action="/submit.php?form=ajouter-match" method="post">
    <?php endif; ?>
    <div class="ligne">
      <label class="input" for="nom-adversaire">
        <div class="label">Nom de l'équipe adversaire</div>
        <input type="text" name="nom-adversaire" id="nom-adversaire" minlength="2" maxlength="50"
          value="<?= $rencontreSelectionnee?->getNomAdversaire() ?>">
        <?php if (isset($erreurs['nom-adversaire'])): ?>
          <div class="error"><?= $erreurs['nom-adversaire'] ?></div>
        <?php endif; ?>
      </label>
    </div>

    <div class="ligne">
      <label class="input" for="lieu">
        <div class="label">Lieu</div>
        <input type="text" name="lieu" id="lieu" value="<?= $rencontreSelectionnee?->getLieu() ?>">
        <?php if (isset($erreurs['lieu'])): ?>
          <div class="error"><?= $erreurs['lieu'] ?></div>
        <?php endif; ?>
      </label>


      <label class="input" for="date">
        <div class="label">Date</div>
        <input type="datetime-local" name="date" id="date"
          value="<?= $rencontreSelectionnee?->getDate()?->format('Y-m-d\TH:i') ?>">
        <?php if (isset($erreurs['date'])): ?>
          <div class="error"><?= $erreurs['date'] ?></div>
        <?php endif; ?>
      </label>
    </div>

    <div class="ligne">
      <label for="resultat" class="input">
        <div class="label">Résultat</div>
        <select id="resultat" name="resultat">
          <?php if (!$rencontreSelectionnee || $rencontreSelectionnee?->getDate() > new DateTime()): ?>
            <option value="NON-DEFINI">Non-défini</option>
          <?php endif; ?>
          <?php foreach (ResultatRencontre::cases() as $resultat): ?>
            <option value="<?= $resultat->value ?>" <?= $rencontreSelectionnee?->getResultat() === $resultat ? 'selected' : '' ?>>
              <?= match ($resultat) {
                ResultatRencontre::VICTOIRE => "Victoire de l'équipe",
                ResultatRencontre::DEFAITE => 'Défaite',
              } ?>
            </option>
          <?php endforeach; ?>
          <?php if (isset($erreurs['resultat'])): ?>
            <div class="error"><?= $erreurs['resultat'] ?></div>
          <?php endif; ?>
        </select>
      </label>
    </div>

    <div class="buttons">
      <button class="button" type="reset">Effacer la saisie</button>
      <button class="button primary" type="submit">Enregistrer</button>
    </div>
  </form>
  <?php $slot = ob_get_clean(); ?>

  <?php ob_start(); ?>
  <style data-file="matches/infos">
    #infos-match form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      flex: 1;

    }

    #infos-match .ligne {
      display: flex;
      gap: 1rem;
    }

    .buttons {
      display: flex;
      gap: 1rem;
      margin-top: auto;
      justify-content: flex-end;
    }
  </style>

  <?php $head .= ob_get_clean(); ?>