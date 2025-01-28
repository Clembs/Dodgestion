<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/joueur.php';
require_once __DIR__ . '/rencontre.php';

enum PositionJoueur: string
{
  case AVANT = "AVANT";
  case ARRIERE = "ARRIERE";
}

enum RoleJoueur: string
{
  case TITULAIRE = "TITULAIRE";
  case REMPLACANT = "REMPLACANT";
}

class Participation
{
  public function __construct(
    private int $id,
    private Joueur $joueur,
    private Rencontre $rencontre,
    private ?int $note,
    private ?string $commentaire,
    private PositionJoueur $position,
    private RoleJoueur $roleJoueur
  ) {
  }

  // Getters
  public function getId(): int
  {
    return $this->id;
  }

  public function getJoueur(): Joueur
  {
    return $this->joueur;
  }

  public function getRencontre(): Rencontre
  {
    return $this->rencontre;
  }

  public function getNote(): ?int
  {
    return $this->note;
  }

  public function getCommentaire(): ?string
  {
    return $this->commentaire;
  }

  public function getPosition(): PositionJoueur
  {
    return $this->position;
  }

  public function getRoleJoueur(): RoleJoueur
  {
    return $this->roleJoueur;
  }

  public static function create(
    Joueur $joueur,
    Rencontre $rencontre,
    ?int $note,
    ?string $commentaire,
    PositionJoueur $position,
    RoleJoueur $roleJoueur
  ): Participation {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare(
        "INSERT INTO participations(id_joueur, id_rencontre, note, commentaire, position, role_joueur)
                VALUES(:id_joueur, :id_rencontre, :note, :commentaire, :position, :role_joueur)"
      );
      $req->execute([
        'id_joueur' => $joueur->getId(),
        'id_rencontre' => $rencontre->getId(),
        'note' => $note,
        'commentaire' => $commentaire,
        'position' => $position->value,
        'role_joueur' => $roleJoueur->value
      ]);

      return new Participation(
        $linkpdo->lastInsertId(),
        $joueur,
        $rencontre,
        $note,
        $commentaire,
        $position,
        $roleJoueur
      );
    } catch (Exception $e) {
      die('Erreur lors de la création de la participation : ' . $e->getMessage());
    }
  }

  /**
   * @return Participation[]
   */
  public static function getAllByRencontre(int $idRencontre)
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('SELECT * FROM participations WHERE id_rencontre = :id_rencontre');
      $req->execute(['id_rencontre' => $idRencontre]);
      $res = $req->fetchAll(PDO::FETCH_ASSOC);

      return array_map(
        fn($participation) =>
        new Participation(
          $participation['id_participation'],
          Joueur::read($participation['id_joueur']),
          Rencontre::read($idRencontre),
          $participation['note'],
          $participation['commentaire'],
          PositionJoueur::from($participation['position']),
          RoleJoueur::from($participation['role_joueur'])
        ),
        $res
      );
    } catch (Exception $e) {
      die('Erreur lors de la lecture des participations : ' . $e->getMessage());
    }
  }

  public static function findByJoueurAndRencontre(int $idJoueur, int $idRencontre): ?Participation
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('SELECT * FROM participations WHERE id_joueur = :id_joueur AND id_rencontre = :id_rencontre');
      $req->execute(['id_joueur' => $idJoueur, 'id_rencontre' => $idRencontre]);
      $res = $req->fetch(PDO::FETCH_ASSOC);

      if ($res == null) {
        return null;
      }

      return new Participation(
        $res['id_participation'],
        Joueur::read($res['id_joueur']),
        Rencontre::read($res['id_rencontre']),
        $res['note'],
        $res['commentaire'],
        PositionJoueur::from($res['position']),
        RoleJoueur::from($res['role_joueur'])
      );
    } catch (Exception $e) {
      die('Erreur lors de la lecture de la participation : ' . $e->getMessage());
    }
  }

  public static function read(int $id): Participation
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('SELECT * FROM participations WHERE id_participation = :id');
      $req->execute(['id' => $id]);
      $res = $req->fetch(PDO::FETCH_ASSOC);
      return new Participation(
        $res['id_participation'],
        Joueur::findByNumeroLicense($res['id_joueur']),
        Rencontre::read($res['id_rencontre']),
        $res['note'],
        $res['commentaire'],
        PositionJoueur::from($res['position']),
        RoleJoueur::from($res['role_joueur'])
      );
    } catch (Exception $e) {
      die('Erreur lors de la lecture de la participation : ' . $e->getMessage());
    }
  }
}