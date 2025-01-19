<?php
require __DIR__ . '/database.php';

enum ResultatRencontre: string
{
  case VICTOIRE = 'VICTOIRE';
  case DEFAITE = 'DEFAITE';
  case NUL = 'NUL';
}

class Rencontre
{
  // Constructeur
  public function __construct(
    private ?int $id,
    private DateTime $date,
    private string $lieu,
    private string $nomAdversaire,
    private ResultatRencontre $resultat,
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

  public function getResultat(): ResultatRencontre
  {
    return $this->resultat;
  }

  public function getLieu(): string
  {
    return $this->lieu;
  }

  public function getNomAdversaire(): string
  {
    return $this->nomAdversaire;
  }

  public function create(): void
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare(
        "INSERT INTO rencontres(date_rencontre, lieu, nom_adversaire, resultat)
        VALUES(:id_rencontre, :date_rencontre, :lieu, :nom_adversaire, :resultat)"
      );

      $req->execute([
        'date_rencontre' => $this->getDate(),
        'lieu' => $this->getLieu(),
        'nom_adversaire' => $this->getNomAdversaire(),
        'resultat' => $this->getResultat(),
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la creation de la rencontre : ' . $e->getMessage());
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
        ResultatRencontre::from($res['resultat'])
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
        SET date_rencontre = :date_rencontre, lieu = :lieu, nom_adversaire = :nom_adversaire, resultat = :resultat
        WHERE id_rencontre = :id_rencontre"
      );

      $req->execute([
        'id_rencontre' => $this->getId(),
        'date_rencontre' => $this->getDate(),
        'lieu' => $this->getLieu(),
        'nom_adversaire' => $this->getNomAdversaire(),
        'resultat' => $this->getResultat(),
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la mise à jour de la rencontre : ' . $e->getMessage());
    }
  }
}
