<?php
$title = 'Mon équipe';

$joueurs = [
  '123456' => [
    'nom' => 'Pawilowski',
    'prenom' => 'Kylian',
    'status' => 'ACTIF',
    'licence' => '123456',
    'date_naissance' => '1990-01-01',
    'taille' => 180,
    'poids' => 75,
    'note' => 'Cool et sympa',
    'photo' => 'https://www.thispersondoesnotexist.com/',
  ],
  '123457' => [
    'nom' => 'Voisin',
    'prenom' => 'Clément',
    'status' => 'BLESSE',
    'licence' => '123457',
    'date_naissance' => '1991-01-01',
    'taille' => 175,
    'poids' => 70,
    'note' => 'Bonne condition physique',
    'photo' => 'https://www.thispersondoesnotexist.com/',
  ],
];

$joueurSélectionné = $joueurs[$_GET['joueur'] ?? '123456'];
?>

<?php ob_start(); ?>

<main>
  <!-- Barre latérale listant les joueurs -->
  <aside>
    <h1> Mon équipe </h1>

    <div id="recherche-joueur">
      <input type="text" placeholder="Rechercher un joueur" />
    </div>

    <ul id="liste-joueurs" class="surface">
      <?php foreach ($joueurs as $id => $joueur): ?>
        <li>
          <a class="joueur" href="?page=équipe&joueur=<?= $id ?>"
            aria-current="<?= $joueurSélectionné === $joueur ? 'page' : 'false' ?>">
            <img width="48" height="48" src="<?= $joueur['photo'] ?>"
              alt="Photo de  <?= $joueur['prenom'] ?> <?= $joueur['nom'] ?>" />

            <div class="text">
              <div class="player-name">
                <?= $joueur['prenom'] ?>   <?= $joueur['nom'] ?>
              </div>

              <div class="subtext player-status <?= strtolower($joueur['status']) ?>">
                <?= match ($joueur['status']) {
                  'ACTIF' => 'Actif',
                  'BLESSE' => 'Blessé',
                  'SUSPENDU' => 'Suspendu',
                  'ABSENT' => 'Absent',
                } ?>
              </div>

            </div>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <!-- Informations sur le joueur sélectionné -->
  <div class="surface" id="infos-joueur">
    <div id="infos-joueur-header">
      <h2> <?= $joueurSélectionné['prenom'] ?> <?= $joueurSélectionné['nom'] ?> </h2>
      <p class="subtext">
        Numéro de licence : <?= $joueurSélectionné['licence'] ?>
      </p>
    </div>

    <form action="/?form=équipe" method="post">
      <div class="ligne">
        <label class="input" for="prenom">
          <div class="label">Prénom</div>
          <input type="text" name="prenom" id="prenom" value="<?= $joueurSélectionné['prenom'] ?>">
        </label>

        <label class="input" for="nom">
          <div class="label">Nom</div>
          <input type="text" name="nom" id="nom" value="<?= $joueurSélectionné['nom'] ?>">
        </label>
      </div>
      <div class="ligne">
        <label class="input" for="licence">
          <div class="label">Licence</div>
          <input type="text" name="licence" id="licence" value="<?= $joueurSélectionné['licence'] ?>">
        </label>
      </div>
      <div class="ligne">
        <label class="input" for="date_naissance">
          <div class="label">Date de naissance</div>
          <input type="date" name="date_naissance" id="date_naissance"
            value="<?= $joueurSélectionné['date_naissance'] ?>">
        </label>

        <label class="input" for="taille">
          <div class="label">Taille (en cm)</div>
          <input type="number" name="taille" id="taille" value="<?= $joueurSélectionné['taille'] ?>">
        </label>

        <label class="input" for="poids">
          <div class="label">Poids (en kg)</div>
          <input type="number" name="poids" id="poids" value="<?= $joueurSélectionné['poids'] ?>">
        </label>
      </div>
      <div class="ligne">
        <label class="input" for="note">
          <div class="label">Note</div>
          <textarea name="note" id="note"><?= $joueurSélectionné['note'] ?></textarea>
        </label>
      </div>

      <div class="buttons">
        <button class="button" type="reset">Annuler</button>
        <button class="button" type="submit">Enregistrer</button>
      </div>
    </form>
  </div>
</main>

<?php $content = ob_get_clean(); ?>


<?php ob_start(); ?>
<!-- Ajout des balises meta -->
<meta name="description" content="Tableau de bord de l'application de gestion sportive" />

<!-- CSS -->
<style data-file="tableau-de-bord">
  main {
    display: flex;
    gap: 2rem;
    padding: 2rem;
    padding-top: 0;
    flex: 1;
  }

  /* Partie barre latérale */
  aside {
    width: 320px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    flex-shrink: 0;
  }

  aside h1 {
    font-size: 3rem;
    font-weight: 700;
    margin: 0;
  }

  aside input {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 1rem;
  }

  #liste-joueurs {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;

    list-style: none;

    padding: 0.75rem;
    margin: 0;
    height: 100%;

    overflow-y: auto;
  }

  #liste-joueurs .joueur {
    display: flex;
    gap: 1rem;

    padding: 0.75rem;
    border-radius: 1rem;

    text-decoration: none;
  }

  #liste-joueurs .joueur img {
    border-radius: 50%;
  }

  #liste-joueurs .joueur[aria-current="page"] {
    background-color: var(--color-primary);
    color: var(--color-background);
  }

  #liste-joueurs .joueur[aria-current="page"] .player-status {
    color: var(--color-background);
  }

  #liste-joueurs .joueur[aria-current="false"]:hover {
    background-color: var(--color-surface-variant);
  }

  /* Partie informations joueur */
  #infos-joueur {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;

    flex: 1;

    padding: 1.5rem;
    overflow-y: auto;
  }

  #infos-joueur h2 {
    font-size: 1.75rem;
    font-weight: 500;
    margin: 0;
  }

  #infos-joueur form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    height: 100%;
  }

  #infos-joueur .ligne {
    display: flex;
    gap: 1rem;
  }

  .input {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    width: 100%;
  }

  .input .label {
    color: var(--color-subtext);
    margin-left: 1rem;
  }

  .input input,
  .input textarea {
    border: none;
    background-color: var(--color-surface-variant);
    border-radius: 1.75rem;
    padding: 1rem 1.5rem;
    color: var(--color-text);
    width: 100%;
  }

  .input:has(input) {
    max-width: 320px;
  }

  .input textarea {
    resize: vertical;
    min-height: 100px;
  }

  .buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: auto;
  }

  .button {
    padding: 0.75rem 1.5rem;
    border-radius: 1.75rem;
    background-color: var(--color-primary);
    color: var(--color-background);
    border: none;
    cursor: pointer;
  }

  .button[type="reset"] {
    background-color: var(--color-surface-variant);
    color: var(--color-text);
  }
</style>
<?php $head = isset($head) ? $head . ob_get_clean() : ob_get_clean(); ?>

<?php require 'layout.php'; ?>