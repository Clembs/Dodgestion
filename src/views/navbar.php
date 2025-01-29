<?php
$pages = [
  'équipe' => 'Mon équipe',
  'matches' => 'Mes matches',
];
?>

<?php ob_start(); ?>
<header>
  <nav>
    <div id="navbar-title">
      Dodgestion
    </div>

    <ul>
      <?php foreach ($pages as $url => $pageTitle): ?>
        <li>
          <a href="/?page=<?= $url ?>" aria-current="<?= $url === $_GET['page'] ? 'page' : 'false' ?>">
            <?= $pageTitle ?>
          </a>
        </li>
      <?php endforeach; ?>
      <li>
        <a href="/?page=deconnexion" aria-label="Se déconnecter">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-door-open">
            <path d="M13 4h3a2 2 0 0 1 2 2v14" />
            <path d="M2 20h3" />
            <path d="M13 20h9" />
            <path d="M10 12v.01" />
            <path d="M13 4.562v16.157a1 1 0 0 1-1.242.97L5 20V5.562a2 2 0 0 1 1.515-1.94l4-1A2 2 0 0 1 13 4.561Z" />
          </svg>
        </a>
      </li>
    </ul>
  </nav>
</header>

<?php $navbar = ob_get_clean(); ?>

<?php ob_start(); ?>
<style data-file="navbar">
  header nav {
    padding: 1.5rem;
    padding-left: 2rem;
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

  #navbar-title {
    font-size: 1.5rem;
    font-weight: 700;
  }

  nav ul a {
    display: block;
    padding: 1rem 1.5rem;
    border-radius: 99rem;
    text-decoration: none;
  }

  nav ul a:has(svg) {
    padding: 1rem;
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