<?php
/**
 * @var ?Joueur $joueurSelectionne
 * @var array $erreurs
 * @var bool $aJoue
 */

$mode = $joueurSelectionne ? 'modifier' : 'ajouter';
?>

<?php ob_start(); ?>

<!-- Formulaire de création/modification d'un joueur -->
<div class="surface" id="main-panel">

  <?php if ($mode === 'modifier'): ?>
    <div id="main-panel-header">
      <h2> <?= $joueurSelectionne->getPrenom() ?>   <?= $joueurSelectionne->getNom() ?> </h2>
      <p class="subtext">
        Numéro de license : <?= $joueurSelectionne->getNumeroLicense() ?>
      </p>
    </div>
  <?php else: ?>
    <h2>Nouveau joueur</h2>
  <?php endif; ?>

  <?php if ($mode === 'modifier'): ?>
    <form action="/submit.php/?form=modifier-joueur&joueur=<?= $joueurSelectionne->getNumeroLicense() ?>" method="post">
    <?php else: ?>
      <form action="/submit.php?form=ajouter-joueur" method="post">
      <?php endif; ?>

      <div class="ligne">
        <label class="input" for="statut">
          <div class="label">Statut</div>
          <select name="statut" id="statut">
            <?php foreach (StatutJoueur::cases() as $statut): ?>
              <option value="<?= $statut->value ?>" <?= $joueurSelectionne?->getStatut() === $statut ? 'selected' : '' ?>>
                <?= match ($statut) {
                  StatutJoueur::ACTIF => 'Actif/ve',
                  StatutJoueur::BLESSE => 'Blessé(e)',
                  StatutJoueur::SUSPENDU => 'Suspendu(e)',
                  StatutJoueur::ABSENT => 'Absent(e)',
                } ?>
              </option>
            <?php endforeach; ?>
          </select>

          <?php if (isset($erreurs['statut'])): ?>
            <div class="error"><?= $erreurs['statut'] ?></div>
          <?php endif; ?>
        </label>
      </div>


      <div class="ligne">
        <label class="input" for="prenom">
          <div class="label">Prénom</div>
          <input type="text" name="prenom" id="prenom" value="<?= $joueurSelectionne?->getPrenom() ?>" required
            maxlength="50">

          <?php if (isset($erreurs['prenom'])): ?>
            <div class="error"><?= $erreurs['prenom'] ?></div>
          <?php endif; ?>
        </label>

        <label class="input" for="nom">
          <div class="label">Nom</div>
          <input type="text" name="nom" id="nom" value="<?= $joueurSelectionne?->getNom() ?>" required maxlength="50">

          <?php if (isset($erreurs['nom'])): ?>
            <div class="error"><?= $erreurs['nom'] ?></div>
          <?php endif; ?>
        </label>
      </div>
      <div class="ligne">
        <label class="input" for="numero_license">
          <div class="label">Numéro de license</div>
          <input type="text" name="numero_license" id="numero_license"
            value="<?= $joueurSelectionne?->getNumeroLicense() ?? '' ?>" required maxlength="50">

          <?php if (isset($erreurs['numero_license'])): ?>
            <div class="error"><?= $erreurs['numero_license'] ?></div>
          <?php endif; ?>
        </label>
      </div>
      <div class="ligne">
        <label class="input" for="date_naissance">
          <div class="label">Date de naissance</div>
          <input type="date" name="date_naissance" id="date_naissance"
            value="<?= $joueurSelectionne?->getDateNaissance()?->format('Y-m-d') ?>" required>

          <?php if (isset($erreurs['date_naissance'])): ?>
            <div class="error"><?= $erreurs['date_naissance'] ?></div>
          <?php endif; ?>
        </label>

        <label class="input" for="taille">
          <div class="label">Taille (en cm)</div>
          <input type="number" name="taille" id="taille" value="<?= $joueurSelectionne?->getTaille() ?>" min="50"
            max="250" required>

          <?php if (isset($erreurs['taille'])): ?>
            <div class="error"><?= $erreurs['taille'] ?></div>
          <?php endif; ?>
        </label>

        <label class="input" for="poids">
          <div class="label">Poids (en kg)</div>
          <input type="number" name="poids" id="poids" value="<?= $joueurSelectionne?->getPoids() ?>" min="40" max="200"
            required>

          <?php if (isset($erreurs['poids'])): ?>
            <div class="error"><?= $erreurs['poids'] ?></div>
          <?php endif; ?>
        </label>
      </div>
      <div class="ligne">
        <label class="input" for="note">
          <div class="label">Note</div>
          <textarea maxlength="2000" name="note" id="note"><?= $joueurSelectionne?->getNote() ?></textarea>

          <?php if (isset($erreurs['note'])): ?>
            <div class="error"><?= $erreurs['note'] ?></div>
          <?php endif; ?>
        </label>
      </div>

      <?php if ($joueurSelectionne && !$aJoue): ?>

        <dialog id="delete-dialog" class="surface">
          <h2>Supprimer le joueur</h2>
          <p>Êtes-vous sûr de vouloir supprimer ce joueur ?</p>

          <div class="buttons">
            <button id="dialog-close" class="button">Annuler</button>
            <button class="button danger"
              formaction="/submit.php?form=supprimer-joueur&joueur=<?= $joueurSelectionne?->getId() ?>" type="submit">
              Supprimer
            </button>
          </div>
        </dialog>

      <?php endif; ?>

      <div class="buttons">
        <?php if ($joueurSelectionne && !$aJoue): ?>
          <button id="delete-button" class="button danger" type="button">
            Supprimer
          </button>
        <?php endif; ?>

        <button class="button" type="reset">Réinitialiser la saisie</button>
        <button class="button primary" type="submit">Enregistrer</button>
      </div>
    </form>
</div>

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
<style data-file="équipe/joueur">
  #main-panel {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;

    flex: 1;

    padding: 1.5rem;
    overflow-y: auto;
  }

  #main-panel h2 {
    font-size: 1.75rem;
    font-weight: 500;
    margin: 0;
  }

  #main-panel form {
    display: contents;
  }

  #main-panel .ligne {
    display: flex;
    gap: 1rem;
  }

  .buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: auto;
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
<?php $head = isset($head) ? $head . ob_get_clean() : ob_get_clean() ?>