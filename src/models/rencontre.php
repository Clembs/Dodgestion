<?php
require __DIR__ . '/database.php';

class Rencontre
{
  // Constructeur
  public function __construct(
    private ?int $id,
    private DateTime $date,
    private string $lieu,
    private string $nomAdversaire,
    private ?int $pointsEquipe,
    private ?int $pointsAdversaire,
  ) {
  }

  // Getters/Setters
  public function getId(): ?int
  {
    return $this->id;
  }
  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function getDate(): DateTime
  {
    return $this->date;
  }

  public function getLieu(): string
  {
    return $this->lieu;
  }

  public function getNomAdversaire(): string
  {
    return $this->nomAdversaire;
  }

  public function getPointsEquipe(): ?int
  {
    return $this->pointsEquipe;
  }

  public function getPointsAdversaire(): ?int
  {
    return $this->pointsAdversaire;
  }

  public function create(): void
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare(
        "INSERT INTO rencontres(date_rencontre, lieu, nom_adversaire, points_equipe, points_adversaire)
        VALUES(:id_rencontre, :date_rencontre, :lieu, :nom_adversaire, :points_equipe, :points_adversaire)"
      );

      $req->execute([
        'date_rencontre' => $this->getDate(),
        'lieu' => $this->getLieu(),
        'nom_adversaire' => $this->getNomAdversaire(),
        'points_equipe' => $this->getPointsEquipe(),
        'points_adversaire' => $this->getPointsAdversaire(),
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la creation de la rencontre : ' . $e->getMessage());
    }
  }

  /**
   * @return Rencontre[]
   */
  public static function getRencontres(): array
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->query('SELECT * FROM rencontres');
      $res = $req->fetchAll(PDO::FETCH_ASSOC);

      // Retourne un tableau de Recontre avec pour clef l'id de la rencontre
      return array_reduce(
        $res,
        function ($acc, $rencontre) {
          $acc[$rencontre['id_rencontre']] = new Rencontre(
            $rencontre['id_rencontre'],
            new DateTime($rencontre['date_rencontre']),
            $rencontre['lieu'],
            $rencontre['nom_adversaire'],
            $rencontre['points_equipe'],
            $rencontre['points_adversaire']
          );
          return $acc;
        },
        []
      );
    } catch (Exception $e) {
      die('Erreur lors de la récupération des rencontres : ' . $e->getMessage());
    }
  }

  public static function read(
    string $idRencontre
  ): Rencontre {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('SELECT * FROM rencontres WHERE id_rencontre = :id_rencontre');

      $req->execute([
        'id_rencontre' => $idRencontre
      ]);
      $res = $req->fetch(PDO::FETCH_ASSOC);

      return new Rencontre(
        $res['id_rencontre'],
        new DateTime($res['date_rencontre']),
        $res['lieu'],
        $res['nom_adversaire'],
        $res['points_equipe'],
        $res['points_adversaire']
      );

    } catch (Exception $e) {
      die('Erreur lors de la lecture de la rencontre : ' . $e->getMessage());
    }
  }

  public function update(): void
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare(
        "UPDATE rencontres
        SET date_rencontre = :date_rencontre, lieu = :lieu, nom_adversaire = :nom_adversaire, points_equipe = :points_equipe, points_adversaire = :points_adversaire
        WHERE id_rencontre = :id_rencontre"
      );

      $req->execute([
        'id_rencontre' => $this->getId(),
        'date_rencontre' => $this->getDate(),
        'lieu' => $this->getLieu(),
        'nom_adversaire' => $this->getNomAdversaire(),
        'points_equipe' => $this->getPointsEquipe(),
        'points_adversaire' => $this->getPointsAdversaire(),
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la mise à jour de la rencontre : ' . $e->getMessage());
    }
  }
}
