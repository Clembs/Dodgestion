<?php
/**
 * @var Rencontre $rencontreSelectionnee
 */
?>

<?php ob_start(); ?>

<form action="/submit.php/?form=modifier-match&match=<?= $rencontreSelectionnee->getId() ?>" method="post">
  <div class="ligne">
    <label class="input" for="lieu">
      <div class="label">Lieu</div>
      <input type="text" name="lieu" id="lieu" value="<?= $rencontreSelectionnee->getLieu() ?>">
    </label>
    <label class="input" for="date">
      <div class="label">Date</div>
      <input type="datetime-local" name="date" id="date"
        value="<?= $rencontreSelectionnee->getDate()->format('Y-m-d\TH:i') ?>">
    </label>
  </div>

  <div class="buttons">
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