<?php
/**
 * @var Joueur $joueurSelectionne
 * @var Joueur[] $joueurs
 * @var string $recherche
 */
?>

<?php ob_start(); ?>

<main>
  <!-- Barre latérale listant les joueurs -->
  <aside>
    <h1> Mon équipe </h1>

    <form method="GET" class="input" id="recherche-joueur">
      <input type="hidden" name="page" value="équipe" />
      <input name="query" type="text" placeholder="Rechercher un joueur" value="<?= $recherche ?>">

      <button class="button icon" type="submit" aria-label="Rechercher">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          title="Rechercher" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-search">
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.3-4.3" />
        </svg>
      </button>
    </form>

    <ul id="liste-joueurs" class="surface">
      <?php foreach ($joueurs as $id => $joueur): ?>
        <li>
          <a class="joueur" href="?page=équipe&joueur=<?= $id ?>"
            aria-current="<?= $joueurSelectionne->getId() === $joueur->getId() ? 'page' : 'false' ?>">
            <!-- TODO: ajouter la photo -->

            <div class="text">
              <div class="player-name">
                <?= $joueur->getPrenom() ?>   <?= $joueur->getNom() ?>
              </div>

              <div class="subtext player-status <?= strtolower($joueur->getStatut()->value) ?>">
                <?= match ($joueur->getStatut()) {
                  StatutJoueur::ACTIF => 'Actif',
                  StatutJoueur::BLESSE => 'Blessé',
                  StatutJoueur::SUSPENDU => 'Suspendu',
                  StatutJoueur::ABSENT => 'Absent',
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
      <h2> <?= $joueurSelectionne->getPrenom() ?> <?= $joueurSelectionne->getNom() ?> </h2>
      <p class="subtext">
        Numéro de licence : <?= $joueurSelectionne->getNumeroLicense() ?>
      </p>
    </div>

    <form action="/?form=équipe" method="post">
      <div class="ligne">
        <label class="input" for="prenom">
          <div class="label">Prénom</div>
          <input type="text" name="prenom" id="prenom" value="<?= $joueurSelectionne->getPrenom() ?>">
        </label>

        <label class="input" for="nom">
          <div class="label">Nom</div>
          <input type="text" name="nom" id="nom" value="<?= $joueurSelectionne->getNom() ?>">
        </label>
      </div>
      <div class="ligne">
        <label class="input" for="licence">
          <div class="label">Licence</div>
          <input type="text" name="licence" id="licence" value="<?= $joueurSelectionne->getNumeroLicense() ?>">
        </label>
      </div>
      <div class="ligne">
        <label class="input" for="date_naissance">
          <div class="label">Date de naissance</div>
          <input type="date" name="date_naissance" id="date_naissance"
            value="<?= $joueurSelectionne->getDateNaissance()->format('Y-m-d') ?>">
        </label>

        <label class="input" for="taille">
          <div class="label">Taille (en cm)</div>
          <input type="number" name="taille" id="taille" value="<?= $joueurSelectionne->getTaille() ?>">
        </label>

        <label class="input" for="poids">
          <div class="label">Poids (en kg)</div>
          <input type="number" name="poids" id="poids" value="<?= $joueurSelectionne->getPoids() ?>">
        </label>
      </div>
      <div class="ligne">
        <label class="input" for="note">
          <div class="label">Note</div>
          <textarea name="note" id="note"><?= $joueurSelectionne->getNote() ?></textarea>
        </label>
      </div>

      <div class="buttons">
        <button class="button" type="reset">Annuler</button>
        <button class="button primary" type="submit">Enregistrer</button>
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
  body {
    height: 100vh;
  }

  main {
    display: flex;
    gap: 2rem;
    padding: 2rem;
    padding-top: 0;
    flex: 1;
    overflow: hidden;
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

  aside .input {
    flex-direction: row;
    gap: 0.5rem;
    align-items: center;
  }

  #liste-joueurs {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;

    list-style: none;

    height: 100%;
    padding: 0.75rem;
    margin: 0;

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
    display: contents;
  }

  #infos-joueur .ligne {
    display: flex;
    gap: 1rem;
  }

  .buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: auto;
  }
</style>
<?php $head = isset($head) ? $head . ob_get_clean() : ob_get_clean(); ?>