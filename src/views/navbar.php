<?php
$page = $_GET['page'] ?? 'tableau-de-bord';

$pages = [
  'tableau-de-bord' => 'Tableau de bord',
  'équipe' => 'Mon équipe',
  'matches' => 'Mes matches',
  'statistiques' => 'Statistiques',
];
?>

<?php ob_start(); ?>
<header>
  <nav>
    (logo ici)

    <ul>
      <?php foreach ($pages as $url => $pageTitle): ?>
        <li>
          <a href="/?page=<?= $url ?>" aria-current="<?= $url === $page ? 'page' : 'false' ?>">
            <?= $pageTitle ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
</header>

<?php $navbar = ob_get_clean(); ?>

<?php ob_start(); ?>
<style data-file="navbar">
  nav {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  nav ul {
    display: flex;
    list-style: none;
    padding: 0;
    gap: 0.5rem;
  }

  nav ul a {
    padding: 1rem 1.5rem;
    border-radius: 99rem;
    text-decoration: none;
  }

  nav ul a:hover {
    background-color: var(--color-surface);
  }

  nav ul a[aria-current="page"] {
    background-color: var(--color-primary);
    color: var(--color-background);
  }
</style>
<?php $head = isset($head) ? $head . ob_get_clean() : ob_get_clean(); ?>