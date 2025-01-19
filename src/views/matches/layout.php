<?php
/**
 * @var Rencontre[] $rencontresFutures
 * @var Rencontre[] $rencontresPassees
 * @var Rencontre $rencontreSelectionnee
 * @var array $tabs
 * @var string $currentTab
 */
?>

<?php ob_start(); ?>

<main>
  <!-- Barre latérale listant les rencontres -->
  <aside>
    <h1> Mes matches </h1>

    <ul id="liste-matches" class="surface">
      <li>
        <div class="subtext">
          Matches à venir
        </div>

        <ul>
          <?php foreach ($rencontresFutures as $rencontre): ?>
            <li>
              <a class="match" href="?page=matches&match=<?= $rencontre->getId() ?>"
                aria-current="<?= $rencontreSelectionnee !== null && $rencontreSelectionnee->getId() === $rencontre->getId() ? 'page' : 'false' ?>">

                <div class="nom-adversaire">
                  vs. <?= $rencontre->getNomAdversaire() ?>
                </div>
                <div class="date subtext">
                  <?= $rencontre->getDate()->format('d/m/Y') ?>
                </div>
                <div class="lieu subtext">
                  <?= $rencontre->getLieu() ?>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </li>

      <hr />

      <li>
        <div class="subtext">
          Matches passés
        </div>

        <ul>
          <?php foreach ($rencontresPassees as $rencontre): ?>
            <li>
              <a class="match" href="?page=matches&match=<?= $rencontre->getId() ?>"
                aria-current="<?= $rencontreSelectionnee !== null && $rencontreSelectionnee->getId() === $rencontre->getId() ? 'page' : 'false' ?>">

                <div class="nom-adversaire">
                  vs. <?= $rencontre->getNomAdversaire() ?> (<?= $rencontre->getPointsEquipe() ?> -
                  <?= $rencontre->getPointsAdversaire() ?>)
                </div>
                <div class="date subtext">
                  <?= $rencontre->getDate()->format('d/m/Y') ?>
                </div>
                <div class="lieu subtext">
                  <?= $rencontre->getLieu() ?>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
      </li>

    </ul>
  </aside>

  <!-- Informations sur la rencontre sélectionnée -->
  <div id="infos-match" class="surface">
    <div id="header">
      <div id="score">
        <?php if ($rencontreSelectionnee->getPointsEquipe()): ?>
          <div class="score-number"
            aria-label=" <?= $rencontreSelectionnee->getPointsEquipe() ?> points pour votre équipe">
            <?= $rencontreSelectionnee->getPointsEquipe() ?>
          </div>
        <?php endif; ?>


        <h2>Votre équipe vs. <?= $rencontreSelectionnee->getNomAdversaire() ?></h2>

        <?php if ($rencontreSelectionnee->getPointsAdversaire()): ?>
          <div class="score-number"
            aria-label=" <?= $rencontreSelectionnee->getPointsAdversaire() ?> points pour l'adversaire">
            <?= $rencontreSelectionnee->getPointsAdversaire() ?>
          </div>
        <?php endif; ?>
      </div>

      <div id="infos">
        <div class="subtext">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            title="Lieu du match" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-map-pin">
            <path
              d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
            <circle cx="12" cy="10" r="3" />
          </svg>
          <p><?= $rencontreSelectionnee->getLieu() ?></p>
        </div>

        <div class="subtext">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            title="Date du match" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-calendar">
            <path d="M8 2v4" />
            <path d="M16 2v4" />
            <rect width="18" height="18" x="3" y="4" rx="2" />
            <path d="M3 10h18" />
          </svg>
          <p><?= $rencontreSelectionnee->getDate()->format('d/m/Y à H:i') ?></p>
        </div>

      </div>
    </div>

    <nav id="nav-tabs">
      <ul>
        <?php foreach ($tabs as $tabKey => $tab): ?>
          <a href="/?page=matches&match=<?= $rencontreSelectionnee->getId() ?>&tab=<?= $tabKey ?>" class="tab"
            aria-current="<?= $tabKey === $currentTab ? 'page' : 'false' ?>">
            <?= $tab['label'] ?>
          </a>
        <?php endforeach; ?>
      </ul>
    </nav>

    <?= $slot ?>
  </div>
  </div>
</main>

<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
<style data-file="matches">
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

  #liste-matches {
    display: flex;
    flex-direction: column;

    list-style: none;

    height: 100%;
    padding: 0.75rem;
    margin: 0;

    overflow-y: auto;
  }

  #liste-matches hr {
    margin: 2rem 0;
    border: none;
    border-top: 1px solid var(--color-subtext);
  }

  #liste-matches li>.subtext {
    margin-left: 0.75rem;
  }

  #liste-matches li ul {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin: 0;
    padding: 0;
    list-style: none;
    margin-top: 0.5rem;
  }

  #liste-matches .match {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;

    padding: 0.75rem;
    border-radius: 1rem;

    text-decoration: none;
  }

  #liste-matches .match .nom-adversaire {
    font-size: 1.125rem;
    font-weight: 500;
  }

  #liste-matches .match[aria-current="page"] {
    background-color: var(--color-primary);
    color: var(--color-background);
  }

  #liste-matches .match[aria-current="page"] .subtext {
    color: var(--color-background);
  }

  /* Partie informations match */
  #infos-match {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;

    flex: 1;

    padding: 1.5rem;
    overflow-y: auto;
  }

  #header {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  #header #infos {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
  }

  #header #infos .subtext {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }

  #header #score {
    display: flex;
    gap: 1rem;
    align-items: center;
    text-align: center;
  }

  #header #score .score-number {
    font-size: 2.5rem;
    font-weight: 700;
  }

  #nav-tabs ul {
    display: flex;
    gap: 1rem;
    list-style: none;
    padding: 0;
    overflow-x: auto;
    padding: 0 1.5rem;
    margin: 0 -1.5rem;
  }

  #nav-tabs a {
    white-space: nowrap;

  }
</style>

<?php $head = isset($head) ? $head . ob_get_clean() : ob_get_clean(); ?>