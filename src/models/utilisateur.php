<?php

class Utilisateur
{

  //Constructeur
  public function __construct(
    private int $Id,
    private string $Email,
    private string $MotDePasse,
    private string $Pseudo,
    private DateTime $DateCreation,
  ) {}

  // Getters
  public function getId(): int
  {
    return $this->Id;
  }

  public function getEmail(): string
  {
    return $this->Email;
  }

  public function getMotDePasse(): string
  {
    return $this->MotDePasse;
  }

  public function getPseudo(): string
  {
    return $this->Pseudo;
  }

  public function getDateCreation(): DateTime
  {
    return $this->DateCreation;
  }

  // Méthodes pour insérer, lire, mettre à jour et supprimer un utilisateur

  // Insérer un utilisateur
  public function create(): void
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare('INSERT INTO utilisateurs (email, mot_de_passe, pseudo, date_creation)  VALUES (:email, :mot_de_passe, :pseudo, :date_creation)');

      $req->execute([
        'email' => $this->Email,
        'mot_de_passe' => $this->MotDePasse,
        'pseudo' => $this->Pseudo,
        'date_creation' => $this->DateCreation->format('Y-m-d H:i:s'),
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
    }
  }

  // Lire un utilisateur
  public static function getUtilisateur(int $Id): Utilisateur
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare('SELECT * FROM utilisateurs WHERE id = :id');
      $req->execute(['id' => $Id]);
      $res = $req->fetch(PDO::FETCH_ASSOC);

      if (!$res) {
        throw new Exception("Utilisateur non trouvé.");
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
      $req = $linkpdo->prepare('UPDATE utilisateurs SET email = :email, mot_de_passe = :mot_de_passe, pseudo = :pseudo WHERE id = :id');

      $req->execute([
        'id' => $this->Id,
        'email' => $this->Email,
        'mot_de_passe' => $this->MotDePasse,
        'pseudo' => $this->Pseudo,
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
      $req->execute(['id' => $this->Id]);
    } catch (Exception $e) {
      die('Erreur lors de la suppression de l\'utilisateur : ' . $e->getMessage());
    }
  }
}
