<?php
require __DIR__ . '/database.php';
require __DIR__ . '/models/joueur.php';
require __DIR__ . '/models/rencontre.php';

enum PositionJoueur: string
{
  case AVANT = "AVANT";
  case ARRIERE = "ARRIERE";
}

class Participations
{

  public function __construct(
    private ?int $id,
    private Rencontre $rencontre,
    private  Joueur $joueur,
    private string $commentaire,
    private PositionJoueur $position
  ) {}

  // Getters
  public function getId(): ?int
  {
    return $this->id;
  }

  public function getRencontre(): Rencontre
  {
    return $this->rencontre;
  }

  public function getJoueur(): Joueur
  {
    return $this->joueur;
  }

  public function getCommentaire(): string
  {
    return $this->commentaire;
  }

  public function getPosition(): PositionJoueur
  {
    return $this->position;
  }


  /**
   * Crée une nouvelle participation dans la base de données.
   */
  public function create(): void
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare(
        "INSERT INTO participations (id_rencontre, id_joueur, commentaire, position) 
          VALUES (:id_rencontre, :id_joueur, :commentaire, :position) 
          RETURNING id"
      );
      $req->execute([
        'id_rencontre' => $this->rencontre->getId(),
        'id_joueur' => $this->joueur->getId(),
        'commentaire' => $this->commentaire,
        'position' => $this->position->value,
      ]);

      // Récupération de l'id auto-généré
      $this->id = $req->fetchColumn();
    } catch (Exception $e) {
      die("Erreur lors de la création de la participation : " . $e->getMessage());
    }
  }

  /**
   * Lit une participation à partir de son ID.
   */
  public static function read(int $id): ?Participations
  {
    try {
      $pdo = Database::getPDO();
      $stmt = $pdo->prepare("SELECT * FROM participations WHERE id = :id");
      $stmt->execute(['id' => $id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($result) {
        return new Participations(
          $result['id'],
          Rencontre::read($result['id_rencontre']),
          Joueur::getJoueurs($result['id_joueur']),
          $result['commentaire'],
          PositionJoueur::from($result['position'])
        );
      }
      return null;
    } catch (Exception $e) {
      die("Erreur lors de la lecture de la participation : " . $e->getMessage());
    }
  }

  /**
   * Met à jour une participation existante.
   */
  public function update(): void
  {
    try {
      if ($this->id === null) {
        throw new Exception("Impossible de mettre à jour une participation sans ID.");
      }

      $pdo = Database::getPDO();
      $stmt = $pdo->prepare(
        "UPDATE participations 
          SET id_rencontre = :id_rencontre, 
              id_joueur = :id_joueur, 
              commentaire = :commentaire, 
              position = :position 
          WHERE id = :id"
      );
      $stmt->execute([
        'id_rencontre' => $this->rencontre->getId(),
        'id_joueur' => $this->joueur->getId(),
        'commentaire' => $this->commentaire,
        'position' => $this->position->value,
        'id' => $this->id,
      ]);
    } catch (Exception $e) {
      die("Erreur lors de la mise à jour de la participation : " . $e->getMessage());
    }
  }

  /**
   * Supprime une participation de la base de données.
   */
  public function delete(): void
  {
    try {
      if ($this->id === null) {
        throw new Exception("Impossible de supprimer une participation sans ID.");
      }

      $pdo = Database::getPDO();
      $stmt = $pdo->prepare("DELETE FROM participations WHERE id = :id");
      $stmt->execute(['id' => $this->id]);
    } catch (Exception $e) {
      die("Erreur lors de la suppression de la participation : " . $e->getMessage());
    }
  }

  /**
   * Récupère toutes les participations d'une rencontre.
   */
  public static function getParticipationsByRencontre(int $rencontreId): array
  {
    try {
      $pdo = Database::getPDO();
      $stmt = $pdo->prepare("SELECT * FROM participations WHERE id_rencontre = :id_rencontre");
      $stmt->execute(['id_rencontre' => $rencontreId]);
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $participations = [];
      foreach ($results as $result) {
        $participations[] = new Participations(
          $result['id'],
          Rencontre::read($result['id_rencontre']),
          Joueur::getJoueurs($result['id_joueur']),
          $result['commentaire'],
          PositionJoueur::from($result['position'])
        );
      }
      return $participations;
    } catch (Exception $e) {
      die("Erreur lors de la récupération des participations : " . $e->getMessage());
    }
  }
}
