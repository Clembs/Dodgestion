<?php
require_once __DIR__ . '/database.php';

enum StatutJoueur: string
{
  case ACTIF = "ACTIF";
  case BLESSE = "BLESSE";
  case SUSPENDU = "SUSPENDU";
  case ABSENT = "ABSENT";
}

class Joueur
{
  // Constructeur
  public function __construct(
    private int $id,
    private string $prenom,
    private string $nom,
    private string $numeroLicense,
    private DateTime $dateNaissance,
    private int $taille,
    private int $poids,
    private ?string $note,
    private StatutJoueur $statut = StatutJoueur::ACTIF
  ) {
  }

  // Getters/Setters
  public function getId(): int
  {
    return $this->id;
  }

  public function getNom(): string
  {
    return $this->nom;
  }

  public function getPrenom(): string
  {
    return $this->prenom;
  }

  public function getNumeroLicense(): string
  {
    return $this->numeroLicense;
  }

  public function getDateNaissance(): DateTime
  {
    return $this->dateNaissance;
  }

  public function getTaille(): int
  {
    return $this->taille;
  }

  public function getPoids(): int
  {
    return $this->poids;
  }

  public function getNote(): ?string
  {
    return $this->note;
  }

  public function getStatut(): StatutJoueur
  {
    return $this->statut;
  }

  public static function create(
    string $prenom,
    string $nom,
    string $numeroLicense,
    DateTime $dateNaissance,
    int $taille,
    int $poids,
    ?string $note,
    StatutJoueur $statut
  ): Joueur {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare(
        "INSERT INTO joueurs(prenom, nom, numero_license, date_naissance, taille, poids, note, statut)
        VALUES(:prenom, :nom, :numero_license, :date_naissance, :taille, :poids, :note, :statut)"
      );

      $req->execute([
        'prenom' => $prenom,
        'nom' => $nom,
        'numero_license' => $numeroLicense,
        'date_naissance' => $dateNaissance->format('Y-m-d'),
        'taille' => $taille,
        'poids' => $poids,
        'note' => $note,
        'statut' => $statut->value
      ]);

      return new Joueur(
        $linkpdo->lastInsertId(),
        $prenom,
        $nom,
        $numeroLicense,
        $dateNaissance,
        $taille,
        $poids,
        $note,
        $statut,
      );
    } catch (Exception $e) {
      die('Erreur lors de la création du joueur : ' . $e->getMessage());
    }
  }

  /**
   * @return Joueur[]
   */
  public static function getJoueurs(): array
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare('SELECT * FROM joueurs');
      $req->execute();
      $res = $req->fetchAll(PDO::FETCH_ASSOC);

      // Retourne un tableau de Joueur avec pour clef le numéro de licence
      return array_reduce(
        $res,
        function ($acc, $joueur) {
          $acc[$joueur['numero_license']] = new Joueur(
            $joueur['id_joueur'],
            $joueur['prenom'],
            $joueur['nom'],
            $joueur['numero_license'],
            new DateTime($joueur['date_naissance']),
            $joueur['taille'],
            $joueur['poids'],
            $joueur['note'],
            StatutJoueur::from($joueur['statut'])
          );
          return $acc;
        },
        []
      );
    } catch (Exception $e) {
      die('Erreur lors de la lecture des joueurs : ' . $e->getMessage());
    }
  }

  public static function findByNumeroLicense(
    string $numeroLicense
  ): ?Joueur {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('SELECT * FROM joueurs WHERE numero_license = :numero_license');

      $req->execute([
        'numero_license' => $numeroLicense
      ]);

      $res = $req->fetch(PDO::FETCH_ASSOC);

      if (!$res) {
        return null;
      }

      return new Joueur(
        $res['id_joueur'],
        $res['prenom'],
        $res['nom'],
        $res['numero_license'],
        new DateTime($res['date_naissance']),
        $res['taille'],
        $res['poids'],
        $res['note'],
        StatutJoueur::from($res['statut'])
      );
    } catch (Exception $e) {
      die('Erreur lors de la lecture du joueur : ' . $e->getMessage());
    }
  }

  public static function read(
    string $idJoueur
  ): Joueur {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('SELECT * FROM joueurs WHERE id_joueur = :id_joueur');

      $req->execute([
        'id_joueur' => $idJoueur
      ]);

      $res = $req->fetch(PDO::FETCH_ASSOC);

      return new Joueur(
        $res['id_joueur'],
        $res['prenom'],
        $res['nom'],
        $res['numero_license'],
        new DateTime($res['date_naissance']),
        $res['taille'],
        $res['poids'],
        $res['note'],
        StatutJoueur::from($res['statut'])
      );
    } catch (Exception $e) {
      die('Erreur lors de la lecture du joueur : ' . $e->getMessage());
    }
  }

  public function update(
    ?string $prenom,
    ?string $nom,
    ?string $numeroLicense,
    ?DateTime $dateNaissance,
    ?int $taille,
    ?int $poids,
    ?string $note,
    ?StatutJoueur $statut
  ): void {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare(
        "UPDATE joueurs
        SET prenom = :prenom, nom = :nom, numero_license = :numero_license, date_naissance = :date_naissance,
        taille = :taille, poids = :poids, note = :note, statut = :statut
        WHERE id_joueur = :id_joueur"
      );

      // On met à jour les données du joueur
      $this->prenom = $prenom ?? $this->prenom;
      $this->nom = $nom ?? $this->nom;
      $this->numeroLicense = $numeroLicense ?? $this->numeroLicense;
      $this->dateNaissance = $dateNaissance ?? $this->dateNaissance;
      $this->taille = $taille ?? $this->taille;
      $this->poids = $poids ?? $this->poids;
      $this->note = $note ?? $this->note;
      $this->statut = $statut ?? $this->statut;

      $req->execute([
        'id_joueur' => $this->id,
        'prenom' => $this->prenom,
        'nom' => $this->nom,
        'numero_license' => $this->numeroLicense,
        'date_naissance' => $this->dateNaissance->format('Y-m-d'),
        'taille' => $this->taille,
        'poids' => $this->poids,
        'note' => $this->note,
        'statut' => $this->statut->value
      ]);

    } catch (Exception $e) {
      die('Erreur lors de la mise à jour du joueur : ' . $e->getMessage());
    }
  }

  public function delete(): void
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('DELETE FROM joueurs WHERE id_joueur = :id_joueur');
      $req->execute(['id_joueur' => $this->id]);
    } catch (Exception $e) {
      die('Erreur lors de la suppression du joueur : ' . $e->getMessage());
    }
  }
}
