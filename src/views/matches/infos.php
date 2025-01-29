<?php
/**
 * @var ?Rencontre $rencontreSelectionnee
 * @var array $erreurs
 */

$estPassee = $rencontreSelectionnee && ($rencontreSelectionnee->getDate() <= new DateTime() || $rencontreSelectionnee->getResultat() !== null);
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

    <?php if (!$estPassee): ?>

      <dialog id="delete-dialog" class="surface">
        <h2>Supprimer le match</h2>
        <p>Êtes-vous sûr de vouloir supprimer ce match ?</p>

        <div class="buttons">
          <button id="dialog-close" class="button">Annuler</button>
          <button class="button danger"
            formaction="/submit.php?form=supprimer-match&match=<?= $rencontreSelectionnee->getId() ?>" type="submit">
            Supprimer
          </button>
        </div>
      </dialog>

    <?php endif; ?>

    <div class="buttons">
      <?php if (!$estPassee): ?>
        <button id="delete-button" class="button danger" type="button">
          Supprimer
        </button>
      <?php endif; ?>

      <button class="button" type="reset">Réinitialiser la saisie</button>
      <button class="button primary" type="submit">Enregistrer</button>
    </div>
  </form>

  <!-- Un chouïa de JavaScript pour pouvoir gérer l'ouverture/la fermeture des boîtes de dialogue -->
  <script lang="javascript">
    /** @type {HTMLButtonElement} */
    const deleteButton = document.querySelector('#delete-button');
    /** @type {HTMLDialogElement} */
    const deleteDialog = document.querySelector('#delete-dialog');
    /** @type {HTMLButtonElement} */
    const closeDialogButton = document.querySelector('#dialog-close');

    deleteButton.addEventListener('click', (e) => {
      deleteDialog.showModal();
    });

    closeDialogButton.addEventListener('click', (e) => {
      deleteDialog.open && deleteDialog.close();
    });
  </script>

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

    dialog {
      background: var(--color-background) !important;
      color: var(--color-text);
    }

    dialog[open] {
      display: flex;
      flex-direction: column;
    }

    dialog[open]::backdrop {
      background: rgba(0, 0, 0, 0.5);
    }
  </style>

  <?php $head .= ob_get_clean(); ?>