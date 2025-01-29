<?php
/**
 * @var Joueur[] $joueurs
 * @var Joueur $joueurSelectionne
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
            aria-current="<?= $joueurSelectionne?->getId() === $joueur->getId() ? 'page' : 'false' ?>">
            <!-- TODO: ajouter la photo -->

            <div class="text">
              <div class="player-name">
                <?= $joueur->getPrenom() ?>   <?= $joueur->getNom() ?>
              </div>

              <div class="subtext player-status <?= strtolower($joueur->getStatut()->value) ?>">
                <?= match ($joueur->getStatut()) {
                  StatutJoueur::ACTIF => 'Actif/ve',
                  StatutJoueur::BLESSE => 'Blessé(e)',
                  StatutJoueur::SUSPENDU => 'Suspendu(e)',
                  StatutJoueur::ABSENT => 'Absent(e)',
                } ?>
              </div>

            </div>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <a href="?page=équipe&joueur=nouveau" class="button primary inline" id="nouveau-joueur">
      Nouveau joueur
    </a>
  </aside>

  <?= $slot ?>
</main>

<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
<!-- CSS -->
<style data-file="équipe/layout">
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

  #liste-joueurs-wrapper {
    display: flex;
    flex-direction: column;
    max-height: 100%;
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

  #nouveau-joueur {
    align-self: center;
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
</style>

<?php $head = isset($head) ? $head . ob_get_clean() : ob_get_clean() ?>