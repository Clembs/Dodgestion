<?php
/**
 * @var Rencontre $rencontreSelectionnee
 * @var Participation[] $participants
 * @var Joueur[] $joueurs
 */

$estPassee = $rencontreSelectionnee->getDate() <= new DateTime() || $rencontreSelectionnee->getResultat()
  ?>

<?php ob_start(); ?>

<h3>Modifier les participants (<?= count($participants) ?> participants sur 10) </h3>

<table>
  <thead>
    <tr>
      <th>Joueur</th>
      <th width="10%">Position</th>
      <th width="10%">Rôle</th>
      <?php if ($estPassee): ?>
        <th width="10%">Note (sur 5)</th>
        <th width="30%">Commentaire</th>
      <?php endif; ?>
      <th width="200px">Actions</th>
    </tr>
  </thead>
  <tbody>

    <?php foreach ($participants as $participant): ?>
      <?php $formId = "form-{$participant->getId()}"; ?>

      <form action="/submit.php/?form=modifier-participant&participant=<?= $participant->getId() ?>" method="post"
        id="<?= $formId ?>">
      </form>
      <tr>
        <td><?= $participant->getJoueur()->getPrenom() ?>   <?= $participant->getJoueur()->getNom() ?></td>
        <td>
          <?php if ($estPassee): ?>
            <?= match ($participant->getPosition()) {
              PositionJoueur::AVANT => 'Avant',
              PositionJoueur::ARRIERE => 'Arrière',
            } ?>
          <?php else: ?>
            <select name="position" id="position" form="<?= $formId ?>">
              <?php foreach (PositionJoueur::cases() as $position): ?>
                <option value="<?= $position->value ?>" <?= $participant->getPosition() === $position ? 'selected' : '' ?>>
                  <?= match ($position) {
                    PositionJoueur::AVANT => 'Avant',
                    PositionJoueur::ARRIERE => 'Arrière',
                  } ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($estPassee): ?>
            <?= match ($participant->getRoleJoueur()) {
              RoleJoueur::TITULAIRE => 'Titulaire',
              RoleJoueur::REMPLACANT => 'Remplaçant',
            } ?>
          <?php else: ?>
            <select name="role" id="role" form="<?= $formId ?>">
              <?php foreach (RoleJoueur::cases() as $role): ?>
                <option value="<?= $role->value ?>" <?= $participant->getRoleJoueur() === $role ? 'selected' : '' ?>>
                  <?= match ($role) {
                    RoleJoueur::TITULAIRE => 'Titulaire',
                    RoleJoueur::REMPLACANT => 'Remplaçant',
                  } ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
        </td>

        <?php if ($estPassee): ?>
          <td>
            <label class="input">
              <input type="number" name="note" id="note" min="0" max="5" value="<?= $participant->getNote() ?>">
            </label>
          </td>
          <td>
            <label class="input">
              <textarea name="commentaire" id="commentaire" minlength="200"><?= $participant->getCommentaire() ?></textarea>
            </label>
          </td>
        <?php endif; ?>

        <td>
          <div class="buttons">
            <button class="button primary inline" type="submit" form="<?= $formId ?>">Enregistrer</button>

            <form action="/submit.php/?form=supprimer-participant&participant=<?= $participant->getId() ?>" method="POST">
              <button type="submit" class="button danger inline icon" aria-label="Supprimer le participant">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-trash-2">
                  <path d="M3 6h18" />
                  <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                  <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                  <line x1="10" x2="10" y1="11" y2="17" />
                  <line x1="14" x2="14" y1="11" y2="17" />
                </svg>
              </button>
            </form>
          </div>
        </td>

      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php if (!$estPassee || count($participants) >= 10): ?>
  <form action="/submit.php/?form=ajouter-participant&match=<?= $rencontreSelectionnee->getId() ?>" method="post">

    <div class="ajouter">
      <h3>Ajouter un participant</h3>
    </div>

    <label class="input" for="joueur">
      <div class="label">Joueurs disponibles</div>

      <select name="joueur-numero" id="joueur">
        <?php foreach ($joueurs as $joueur): ?>
          <option value="<?= $joueur->getNumeroLicense() ?>">
            <?= $joueur->getPrenom() ?>     <?= $joueur->getNom() ?>
          </option>
        <?php endforeach; ?>
      </select>

      <?php if (isset($erreurs['joueur-numero'])): ?>
        <div class="error"><?= $erreurs['joueur-numero'] ?></div>
      <?php endif; ?>
    </label>

    <div class="ligne">
      <label class="input" for="position">
        <div class="label">Position</div>
        <select name="position" id="position">
          <option value="AVANT">Avant</option>
          <option value="ARRIERE">Arrière</option>
        </select>

        <?php if (isset($erreurs['position'])): ?>
          <div class="error"><?= $erreurs['position'] ?></div>
        <?php endif; ?>
      </label>

      <label class="input" for="role">
        <div class="label">Rôle</div>
        <select name="role" id="role">
          <option value="TITULAIRE">Titulaire</option>
          <option value="REMPLACANT">Remplaçant</option>
        </select>

        <?php if (isset($erreurs['role'])): ?>
          <div class="error"><?= $erreurs['role'] ?></div>
        <?php endif; ?>
      </label>
    </div>

    <div class="buttons">
      <button class="button primary" type="submit">Ajouter</button>
    </div>

  </form>
<?php endif; ?>

<?php $slot = ob_get_clean(); ?>

<?php ob_start(); ?>

<style data-file="matches/joueurs">
  form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    flex: 1;
  }

  .participants {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 1rem;
    overflow: hidden;
  }

  th,
  td {
    border: 1px solid var(--color-border);
    padding: 0.5rem;
  }

  th {
    background-color: var(--color-surface);
  }

  td {
    background-color: var(--color-background);
  }

  td .buttons {
    display: flex;
    gap: 0.25rem;
  }

  .ligne {
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