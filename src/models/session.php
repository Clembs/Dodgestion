<?php
class Session
{
  private const int SESSION_TEMPS_VIE = 7 * 24 * 60 * 60; // Durée de vie de la session en secondes (7 jours)
  private const string SESSION_NOM_COOKIE = 'session_token'; // l'identifiant de session et le nom du cookie

  // Start the session if needed
  private static function initSession(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  // Create a new session
  public static function create(Utilisateur $user): void
  {
    try {
      self::initSession();
      $sessionId = session_id();

      $linkpdo = Database::getPDO();

      // On supprime les sessions expirées
      $req = $linkpdo->prepare('DELETE FROM sessions WHERE id_utilisateur = :id OR expires <= CURRENT_TIMESTAMP');
      $req->execute(['id' => $user->getId()]);

      // On crée une nouvelle session qui expire dans 1 heure
      $expires = new DateTime('@' . (time() + self::SESSION_TEMPS_VIE));
      $req = $linkpdo->prepare(
        "INSERT INTO sessions (id_session, expires, id_utilisateur) 
                 VALUES (:session_id, :expires, :user_id)"
      );

      $req->execute([
        'session_id' => $sessionId,
        'expires' => $expires->format('Y-m-d H:i:s'),
        'user_id' => $user->getId()
      ]);

      // On ajoute les informations de session à la session PHP
      $_SESSION['user_id'] = $user->getId();
      $_SESSION['expires'] = $expires->format('Y-m-d H:i:s');

      // On crée un cookie de session
      setcookie(
        self::SESSION_NOM_COOKIE,
        $sessionId,
        [
          'expires' => time() + self::SESSION_TEMPS_VIE,
          'path' => '/',
          'secure' => true,
          'httponly' => true,
          'samesite' => 'Strict'
        ]
      );

    } catch (Exception $e) {
      die('Erreur lors de la création de la session : ' . $e->getMessage());
    }
  }

  // Get current user if logged in
  public static function getCurrentUser(): ?Utilisateur
  {
    try {
      self::initSession();

      if (!isset($_SESSION['user_id']) || !isset($_SESSION['expires'])) {
        return null;
      }

      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare(
        'SELECT u.* FROM utilisateurs AS u 
        JOIN sessions AS s ON u.id_utilisateur = s.id_utilisateur 
        WHERE s.id_session = :session_id 
        AND s.expires > CURRENT_TIMESTAMP'
      );

      $req->execute(['session_id' => session_id()]);
      $res = $req->fetch(PDO::FETCH_ASSOC);

      if (!$res) {
        self::destroy();
        return null;
      }

      // Extend session
      self::extend();

      return new Utilisateur(
        $res['id_utilisateur'],
        $res['email'],
        $res['mot_de_passe'],
        $res['pseudo'],
        new DateTime($res['date_creation'])
      );

    } catch (Exception $e) {
      die('Erreur lors de la lecture de la session : ' . $e->getMessage());
    }
  }

  // Check if user is logged in
  public static function isLoggedIn(): bool
  {
    return self::getCurrentUser() !== null;
  }

  // Extend session duration
  private static function extend(): void
  {
    try {
      $newExpires = new DateTime('@' . (time() + self::SESSION_TEMPS_VIE));

      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare(
        'UPDATE sessions 
          SET expires = :expires 
          WHERE id_session = :session_id'
      );

      $req->execute([
        'expires' => $newExpires->format('Y-m-d H:i:s'),
        'session_id' => session_id()
      ]);

      $_SESSION['expires'] = $newExpires->format('Y-m-d H:i:s');

      setcookie(
        self::SESSION_NOM_COOKIE,
        session_id(),
        [
          'expires' => time() + self::SESSION_TEMPS_VIE,
          'path' => '/',
          'secure' => true,
          'httponly' => true,
          'samesite' => 'Strict'
        ]
      );

    } catch (Exception $e) {
      die('Erreur lors de l\'extension de la session : ' . $e->getMessage());
    }
  }

  // Destroy current session
  public static function destroy(): void
  {
    try {
      self::initSession();

      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('DELETE FROM sessions WHERE id_session = :session_id');
      $req->execute(['session_id' => session_id()]);

      session_unset();
      session_destroy();

      setcookie(self::SESSION_NOM_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
      ]);

    } catch (Exception $e) {
      die('Erreur lors de la destruction de la session : ' . $e->getMessage());
    }
  }
}