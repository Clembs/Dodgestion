<?php


class ConnexionController
{

  public static function seConnecter(string $email, string $motDePasse): void
  {
    require __DIR__ . '/../models/utilisateur.php';

    $utilisateur = Utilisateur::read($email, $motDePasse);

    if ($utilisateur === null) {
      require __DIR__ . '/../views/404.php';
    } else {
      session_start();
      $_SESSION['utilisateur'] = [
        'id' => $utilisateur->getId(),
        'email' => $utilisateur->getEmail(),
        'pseudo' => $utilisateur->getPseudo(),
      ];

      // Gestion des cookies (durée de 7 jours par défaut)
      setcookie('utilisateur_id', $utilisateur->getId(), time() + 7 * 24 * 60 * 60, "/", "", false, true);
      setcookie('utilisateur_email', $utilisateur->getEmail(), time() + 7 * 24 * 60 * 60, "/", "", false, true);
      setcookie('utilisateur_pseudo', $utilisateur->getPseudo(), time() + 7 * 24 * 60 * 60, "/", "", false, true);

      require __DIR__ . '/../views/tableau-de-bord.php';

      $title = 'Tableau de bord';

      require __DIR__ . '/../views/layout.php';
    }
  }

  /**
   * Déconnecte l'utilisateur en détruisant la session active.
   */
  public static function seDeconnecter(): void
  {
    session_start();
    session_destroy(); // Détruit la session
    require __DIR__ . '/../views/connexion.php'; // Redirige vers la page de connexion
    exit;
  }

  /**
   * Vérifie si un utilisateur est connecté (via session ou cookies).
   *
   * @return bool Retourne true si un utilisateur est connecté, false sinon.
   */
  public static function estConnecte(): bool
  {
    session_start();

    // Vérifie si l'utilisateur est dans la session ou dans les cookies
    return isset($_SESSION['utilisateur']) || isset($_COOKIE['utilisateur_id']);
  }
  /**
   * Retourne l'utilisateur connecté, soit via session, soit via cookies.
   *
   * @return array|null Retourne les informations de l'utilisateur connecté ou null.
   */
  public static function getUtilisateurConnecte(): ?array
  {
    session_start();

    if (isset($_SESSION['utilisateur'])) {
      return $_SESSION['utilisateur'];
    }

    // Si l'utilisateur n'est pas dans la session mais présent dans les cookies
    if (isset($_COOKIE['utilisateur_id']) && isset($_COOKIE['utilisateur_email']) && isset($_COOKIE['utilisateur_pseudo'])) {
      return [
        'id' => $_COOKIE['utilisateur_id'],
        'email' => $_COOKIE['utilisateur_email'],
        'pseudo' => $_COOKIE['utilisateur_pseudo'],
      ];
    }
    return null;
  }
}
