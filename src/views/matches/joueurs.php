<?php
/**
 * @var Rencontre $rencontreSelectionnee
 * @var Participation[] $participants
 * @var Joueur[] $joueurs
 */

$estPassee = $rencontreSelectionnee->getDate() <= new DateTime();
?>

<?php ob_start(); ?>

<h3>Modifier les participants</h3>

<table>
  <!-- show mock data with the rows: player name/surname, position (avant/arrière) & titulaire/remplaçant -->
  <tr>
    <th>Joueur</th>
    <th>Position</th>
    <th>Rôle</th>
    <?php if ($estPassee): ?>
      <th>Note (sur 5)</th>
      <th>Commentaire</th>
    <?php endif; ?>
  </tr>
  <?php foreach ($participants as $participant): ?>
    <form action="/submit.php/?form=modifier-participant&match=TODO" method="post" id="form-<?= $participant->getId() ?>">
    </form>
    <tr>
      <td><?= $participant->getJoueur()->getPrenom() ?>   <?= $participant->getJoueur()->getNom() ?></td>
      <td>
        <select name="position" id="position" form="form-<?= $participant->getJoueur()->getId() ?>">
          <?php foreach (PositionJoueur::cases() as $position): ?>
            <option value="<?= $position->value ?>" <?= $participant->getPosition() === $position ? 'selected' : '' ?>>
              <?= match ($position) {
                PositionJoueur::AVANT => 'Avant',
                PositionJoueur::ARRIERE => 'Arrière',
              } ?>
            </option>
          <?php endforeach; ?>
        </select>
      </td>
      <td>
        <select name="role" id="role">
          <?php foreach (RoleJoueur::cases() as $role): ?>
            <option value="<?= $role->value ?>" <?= $participant->getRoleJoueur() === $role ? 'selected' : '' ?>>
              <?= match ($role) {
                RoleJoueur::TITULAIRE => 'Titulaire',
                RoleJoueur::REMPLACANT => 'Remplaçant',
              } ?>
            </option>
          <?php endforeach; ?>
        </select>
      </td>

      <?php if ($estPassee): ?>
        <td>
          <input type="number" name="note" id="note" min="0" max="5">
        </td>
        <td>
          <input type="text" name="commentaire" id="commentaire">
        </td>
      <?php endif; ?>

    </tr>
  <?php endforeach; ?>
</table>

<?php if (!$estPassee): ?>
  <form action="/submit.php/?form=ajouter-participant&match=<?= $rencontreSelectionnee->getId() ?>" method="post">


    <div class="ajouter">
      <h3>Ajouter un participant</h3>
    </div>


    <label class="input" for="joueur">
      <div class="label">Joueurs disponibles</div>

      <select name="joueur-numero" id="joueur" multiple>
        <!-- TODO: vérifier qu'il est pas inclus dans les participants -->
        <?php foreach (array_filter($joueurs, fn(Joueur $j) => $j->getId() !== $joueurs) as $joueur): ?>
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