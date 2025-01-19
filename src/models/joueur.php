<?php
require 'database.php';

enum StatutJoueur: string
{
  case ACTIF = "Actif";
  case BLESSE = "Blessé";
  case SUSPENDU = "Suspendu";
  case ABSENT = "Absent";
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

      $req = $linkpdo->prepare('INSERT INTO joueur(prenom, nom, numero_license, date_naissance, taille, poids, note, statut) VALUES(:prenom, :nom, :numero_license, :date_naissance, :taille, :poids, :note, :statut)');

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

  public static function read(
    string $numeroLicense
  ): Joueur {
    try {
      $linkpdo = Database::getPDO();
    } catch (Exception $e) {
      die('Erreur: ' . $e->getMessage());
    }
    $req = $linkpdo->prepare('SELECT * FROM  joueur WHERE numero_license = :numero_license ');

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
  }
}
