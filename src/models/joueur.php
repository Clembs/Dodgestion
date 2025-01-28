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
    private ?int $id,
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
  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
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

  public function create(): void
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare(
        "INSERT INTO joueurs(prenom, nom, numero_license, date_naissance, taille, poids, note, statut)
        VALUES(:prenom, :nom, :numero_license, :date_naissance, :taille, :poids, :note, :statut)"
      );

      $req->execute([
        'prenom' => $this->getPrenom(),
        'nom' => $this->getNom(),
        'numero_license' => $this->getNumeroLicense(),
        'date_naissance' => $this->getDateNaissance(),
        'taille' => $this->getTaille(),
        'poids' => $this->getPoids(),
        'note' => $this->getNote(),
        'statut' => $this->getStatut()
      ]);
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
  ): Joueur {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare('SELECT * FROM joueurs WHERE numero_license = :numero_license');

      $req->execute([
        'numero_license' => $numeroLicense
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

  public function update(array $data): void
  {
    try {
      $linkpdo = Database::getPDO();
      $req = $linkpdo->prepare(
        "UPDATE joueurs
        SET prenom = :prenom, nom = :nom, numero_license = :numero_license, date_naissance = :date_naissance,
        taille = :taille, poids = :poids, note = :note, statut = :statut
        WHERE id_joueur = :id_joueur"
      );

      $req->execute([
        'id_joueur' => $this->getId(),
        'prenom' => $data['prenom'],
        'nom' => $data['nom'],
        'numero_license' => $data['numero_license'],
        'date_naissance' => $data['date_naissance'],
        'taille' => $data['taille'],
        'poids' => $data['poids'],
        'note' => $data['note'],
        'statut' => $data['statut']
      ]);

      // On met à jour les données du joueur
      $this->prenom = $data['prenom'];
      $this->nom = $data['nom'];
      $this->numeroLicense = $data['numero_license'];
      $this->dateNaissance = new DateTime($data['date_naissance']);
      $this->taille = $data['taille'];
      $this->poids = $data['poids'];
      $this->note = $data['note'];
      $this->statut = StatutJoueur::from($data['statut']);
    } catch (Exception $e) {
      die('Erreur lors de la mise à jour du joueur : ' . $e->getMessage());
    }
  }
}
