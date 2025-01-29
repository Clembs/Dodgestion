<?php
require_once __DIR__ . '/../models/session.php';
require_once __DIR__ . '/../models/utilisateur.php';

class ControleurAuthentification
{
  /**
   * Affiche la page de connexion.
   */
  public static function connexion(?string $erreur): void
  {
    // Si l'utilisateur est déjà connecté, redirige vers la page d'accueil
    if (Session::isLoggedIn()) {
      header('Location: /');
      exit;
    }

    // On affiche la page de connexion
    require __DIR__ . '/../views/connexion.php';

    // On définit le titre de la page (pour le layout)
    $title = 'Mes matches';

    // On requiert le layout pour afficher la page
    require __DIR__ . '/../views/layout.php';
  }

  /**
   * Connecte l'utilisateur en créant une session à partir de son email et mot de passe.
   */
  public static function connecter(string $email, string $motDePasse): void
  {

    try {
      // Vérifie si l'utilisateur existe
      $utilisateur = Utilisateur::authenticate($email, $motDePasse);
    } catch (Exception $e) {
      $erreur =
        $e->getMessage() === 'Email ou mot de passe incorrect.'
        ? $e->getMessage()
        : 'Une erreur est survenue. Veuillez réessayer.';
    }

    // S'il y a des erreurs, redirige vers la page de connexion
    if (!empty($erreur)) {
      header("Location: /?page=connexion?erreur=$erreur");
      exit;
    }

    Session::create($utilisateur);
    header('Location: /');
  }

  /**
   * Déconnecte l'utilisateur en détruisant la session active.
   */
  public static function deconnecter(): void
  {
    Session::destroy();

    // Redirige vers la page de connexion
    header('Location: /?page=connexion');
  }
}
