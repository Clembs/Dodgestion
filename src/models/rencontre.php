<?php
require_once __DIR__ . '/database.php';

enum ResultatRencontre: string
{
  case VICTOIRE = "VICTOIRE";
  case DEFAITE = "DEFAITE";
}

class Rencontre
{
  public function __construct(
    private int $id,
    private DateTime $date,
    private string $lieu,
    private string $nomAdversaire,
    private ?ResultatRencontre $resultat,
  ) {
  }

  // Getters
  public function getId(): int
  {
    return $this->id;
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

  public function getResultat(): ?ResultatRencontre
  {
    return $this->resultat;
  }


  public function create(): void
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare(
        "INSERT INTO rencontres(date_rencontre, lieu, nom_adversaire, resultat)
        VALUES(:date_rencontre, :lieu, :nom_adversaire, :resultat)"
      );

      $req->execute([
        'date_rencontre' => $this->getDate()->format('Y-m-d H:i'),
        'lieu' => $this->getLieu(),
        'nom_adversaire' => $this->getNomAdversaire(),
        'resultat' => $this->getResultat()->value,
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la création de la rencontre : ' . $e->getMessage());
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
            id: $rencontre['id_rencontre'],
            date: new DateTime($rencontre['date_rencontre']),
            lieu: $rencontre['lieu'],
            nomAdversaire: $rencontre['nom_adversaire'],
            resultat: $rencontre['resultat'] ? ResultatRencontre::from($rencontre['resultat']) : null
          );
          return $acc;
        },
        []
      );
    } catch (Exception $e) {
      die('Erreur lors de la récupération des rencontres : ' . $e->getMessage());
    }
  }

  public static function read(string $idRencontre): Rencontre
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('SELECT * FROM rencontres WHERE id_rencontre = :id_rencontre');

      $req->execute(['id_rencontre' => $idRencontre]);
      $res = $req->fetch(PDO::FETCH_ASSOC);

      return new Rencontre(
        id: $res['id_rencontre'],
        date: new DateTime($res['date_rencontre']),
        lieu: $res['lieu'],
        nomAdversaire: $res['nom_adversaire'],
        resultat: $res['resultat'] ? ResultatRencontre::from($res['resultat']) : null
      );
    } catch (Exception $e) {
      die('Erreur lors de la lecture de la rencontre : ' . $e->getMessage());
    }
  }

  public function update(
    ?DateTime $date,
    ?string $lieu,
    ?string $nomAdversaire,
    ResultatRencontre|null $resultat
  ): void {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare(
        "UPDATE rencontres
        SET date_rencontre = :date_rencontre, lieu = :lieu, nom_adversaire = :nom_adversaire, resultat = :resultat
        WHERE id_rencontre = :id_rencontre"
      );

      $this->date = $date ?? $this->date;
      $this->lieu = $lieu ?? $this->lieu;
      $this->nomAdversaire = $nomAdversaire ?? $this->nomAdversaire;
      $this->resultat = $resultat === null ? null : $resultat ?? $this->resultat;

      $req->execute([
        'id_rencontre' => $this->id,
        'date_rencontre' => $this->date->format('Y-m-d H:i'),
        'lieu' => $this->lieu,
        'nom_adversaire' => $this->nomAdversaire,
        'resultat' => $this->resultat?->value,
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la mise à jour de la rencontre : ' . $e->getMessage());
    }
  }
}