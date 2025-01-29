<?php

class Utilisateur
{
  //Constructeur
  public function __construct(
    private int $id,
    private string $email,
    private string $motDePasse,
    private string $pseudo,
    private DateTime $dateCreation,
  ) {
  }

  // Getters
  public function getId(): int
  {
    return $this->id;
  }
  public function getEmail(): string
  {
    return $this->email;
  }

  public function getMotDePasse(): string
  {
    return $this->motDePasse;
  }

  public function getPseudo(): string
  {
    return $this->pseudo;
  }

  public function getDateCreation(): DateTime
  {
    return $this->dateCreation;
  }

  // Méthodes pour insérer, lire, mettre à jour et supprimer un utilisateur

  // Insérer un utilisateur
  public static function create(
    string $email,
    string $motDePasse,
    string $pseudo,
    DateTime $dateCreation
  ): Utilisateur {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare(
        "INSERT INTO utilisateurs (email, mot_de_passe, pseudo, date_creation)
        VALUES (:email, :mot_de_passe, :pseudo, :date_creation)
        RETURNING id"
      );

      $req->execute([
        'email' => $email,
        'mot_de_passe' => password_hash($motDePasse, PASSWORD_DEFAULT),
        'pseudo' => $pseudo,
        'date_creation' => $dateCreation->format('Y-m-d H:i:s'),
      ]);

      return new Utilisateur(
        $linkpdo->lastInsertId(),
        $email,
        $motDePasse,
        $pseudo,
        $dateCreation
      );
    } catch (Exception $e) {
      die('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
    }
  }

  // Lire un utilisateur à partir de son email et de son mot de passe
  public static function authenticate(string $email, string $motDePasse): Utilisateur
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new Exception("L'email n'est pas valide.");
    }
    $linkpdo = Database::getPDO();

    try {
      $req = $linkpdo->prepare('SELECT * FROM utilisateurs WHERE email = :email');
      $req->execute(['email' => $email]);
      $res = $req->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      throw new Exception('Erreur lors de la lecture de l\'utilisateur : ' . $e->getMessage());
    }

    if (!$res) {
      throw new Exception("Email ou mot de passe incorrect.");
    }

    if (!password_verify($motDePasse, $res['mot_de_passe'])) {
      throw new Exception("Email ou mot de passe incorrect.");
    }

    return new Utilisateur(
      $res['id_utilisateur'],
      $res['email'],
      $res['mot_de_passe'],
      $res['pseudo'],
      new DateTime($res['date_creation'])
    );
  }

  public static function getByEmail(int $email): Utilisateur
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('SELECT * FROM utilisateurs WHERE email = :email');
      $req->execute(['email' => $email]);
      $res = $req->fetch(PDO::FETCH_ASSOC);

      if (!$res) {
        throw new Exception("L'utilisateur n'existe pas.");
      }

      return new Utilisateur(
        $res['id'],
        $res['email'],
        $res['mot_de_passe'],
        $res['pseudo'],
        new DateTime($res['date_creation'])
      );
    } catch (Exception $e) {
      die('Erreur lors de la lecture de l\'utilisateur : ' . $e->getMessage());
    }
  }

  // Mettre à jour un utilisateur
  public function update(
    ?string $email,
    ?string $motDePasse,
    ?string $pseudo
  ): void {
    try {
      if (isset($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("L'email n'est pas valide.");
      }

      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare(
        "UPDATE utilisateurs
        SET email = :email, mot_de_passe = :mot_de_passe, pseudo = :pseudo 
        WHERE id = :id"
      );

      $this->email = $email ?? $this->email;
      $this->motDePasse = $motDePasse ? password_hash($motDePasse, PASSWORD_DEFAULT) : $this->motDePasse;
      $this->pseudo = $pseudo ?? $this->pseudo;

      $req->execute([
        'id' => $this->id,
        'email' => $this->email,
        'mot_de_passe' => $this->motDePasse,
        'pseudo' => $this->pseudo,
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage());
    }
  }

  // Supprimer un utilisateur
  public function delete(): void
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('DELETE FROM utilisateurs WHERE id = :id');
      $req->execute(['id' => $this->id]);
    } catch (Exception $e) {
      die('Erreur lors de la suppression de l\'utilisateur : ' . $e->getMessage());
    }
  }
}
