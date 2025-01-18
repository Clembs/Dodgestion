<?php
$title = 'Mon équipe';

$joueurs = [
  '123456' => [
    'nom' => 'Pawilowski',
    'prenom' => 'Kylian',
    'status' => 'ACTIF',
    'licence' => '123456',
    'date_naissance' => '01/01/1990',
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
    'date_naissance' => '01/01/1991',
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
              alt="Photo de <?= $joueur['nom'] ?> <?= $joueur['prenom'] ?>" />

            <div class="text">
              <div class="player-name">
                <?= $joueur['nom'] ?>   <?= $joueur['prenom'] ?>
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

  <div class="surface" id="infos-joueur">
    <h2> <?= $joueurSélectionné['nom'] ?> <?= $joueurSélectionné['prenom'] ?> </h2>

    <p> Licence : <?= $joueurSélectionné['licence'] ?> </p>
    <p> Date de naissance : <?= $joueurSélectionné['date_naissance'] ?> </p>
    <p> Taille : <?= $joueurSélectionné['taille'] ?> cm </p>
    <p> Poids : <?= $joueurSélectionné['poids'] ?> kg </p>
    <p> Note : <?= $joueurSélectionné['note'] ?> </p>
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

  h1 {
    font-size: 3rem;
    margin: 0;
  }

  aside {
    width: 320px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
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

  #infos-joueur {
    flex: 1;
    padding: 1.5rem;
    overflow-y: auto;
  }
</style>
<?php $head = isset($head) ? $head . ob_get_clean() : ob_get_clean(); ?>

<?php require 'layout.php'; ?>