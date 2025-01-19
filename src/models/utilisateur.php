<?php

class Utilisateur
{

  //Constructeur
  public function __construct(
    private ?int $id,
    private string $email,
    private string $motDePasse,
    private string $pseudo,
    private DateTime $dateCreation,
  ) {
  }

  // Getters
  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
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
  public function create(): void
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare(
        "INSERT INTO utilisateurs (email, mot_de_passe, pseudo, date_creation)
        VALUES (:email, :mot_de_passe, :pseudo, :date_creation)
        RETURNING id"
      );

      $req->execute([
        'email' => $this->email,
        'mot_de_passe' => password_hash($this->motDePasse, PASSWORD_DEFAULT),
        'pseudo' => $this->pseudo,
        'date_creation' => $this->dateCreation->format('Y-m-d H:i:s'),
      ]);

      // On récupère l'id de l'utilisateur créé et on le stocke dans l'objet
      $this->setId($req->fetchColumn());
    } catch (Exception $e) {
      die('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
    }
  }

  // Lire un utilisateur à partir de son email et de son mot de passe
  public static function read(string $email, string $motDePasse): Utilisateur
  {
    try {
      $linkpdo = Database::getPDO();

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("L'email n'est pas valide.");
      }

      $req = $linkpdo->prepare('SELECT * FROM utilisateurs WHERE email = :email');
      $req->execute(['email' => $email]);
      $res = $req->fetch(PDO::FETCH_ASSOC);

      if (!$res) {
        throw new Exception("Email ou mot de passe incorrect.");
      }

      if (!password_verify($motDePasse, $res['mot_de_passe'])) {
        throw new Exception("Email ou mot de passe incorrect.");
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
  public function update(): void
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare(
        "UPDATE utilisateurs
        SET email = :email, mot_de_passe = :mot_de_passe, pseudo = :pseudo 
        WHERE id = :id"
      );

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
