<?php
enum Lieu: string
{
  case DOMICILE = "Domicile";
  case EXTERIEUR = "Exterieur";
}

enum Resultat: string
{
  case VICTOIRE = "Victoire";
  case DEFAITE = "Defaite";
  case NUL = "Nul";
}
require_once 'joueur.php';
class Rencontres
{
  // Constructeur
  public function __construct(
    private int $IdRencontre,
    private DateTime $DateRencontre,
    private Resultat $Resultat,
    private Lieu $Lieu,
    private DateTime $DateCreation,
    private string $NomAdversaire,


  ) {}


  // Getters
  public function getIdRencontre(): int
  {
    return $this->IdRencontre;
  }

  public function getDateRencontre(): DateTime
  {
    return $this->DateRencontre;
  }

  public function getResultat(): Resultat
  {
    return $this->Resultat;
  }

  public function getLieu(): Lieu
  {
    return $this->Lieu;
  }

  public function getDateCreation(): DateTime
  {
    return $this->DateCreation;
  }

  public function getNomAdversaire(): string
  {
    return $this->NomAdversaire;
  }

  public function create(): void
  {
    try {
      $linkpdo = Database::getPDO();

      $req  = $linkpdo->prepare('INSERT INTO rencontres(id_rencontre,date_rencontre, lieu, nom_adversaire, resultat, date_creation) VALUES(:id_rencontre, :date_rencontre, :lieu, :nom_adversaire, :resultat, :date_creation)');

      $req->execute([
        'id_rencontre' => $this->getIdRencontre(),
        'date_rencontre' => $this->getDateRencontre(),
        'lieu' => $this->getLieu(),
        'nom_adversaire' => $this->getNomAdversaire(),
        'resultat' => $this->getResultat(),
        'date_creation' => $this->getDateCreation()
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la creation du match: ' .  $e->getMessage());
    }
  }

  public static function  read(
    string $idRencontre
  ): Rencontres {
    try {
      $linkpdo = Database::getPDO();
    } catch (Exception $e) {
      die('Erreur: ' . $e->getMessage());
    }
    $req = $linkpdo->prepare('SELECT* FROM joueur WHERE id_rencontre = :id_rencontre');

    $req->execute([
      'id_rencontre' => $idRencontre
    ]);
    $res = $req->fetch(PDO::FETCH_ASSOC);

    return new Rencontres(
      $res['id_rencontre'],
      new DateTime($res['date_rencontre']),
      Resultat::from($res['resultat']),
      Lieu::from($res['']),
      new DateTime($res['date_creation']),
      $res['nom_adversaire']
    );
  }

  public function update(): void
  {
    try {
      $linkpdo = Database::getPDO();

      $req = $linkpdo->prepare('UPDATE rencontres SET date_rencontre = :date_rencontre, lieu = :lieu, nom_adversaire = :nom_adversaire, resultat = :resultat, date_creation = :date_creation WHERE id_rencontre = :id_rencontre');

      $req->execute([
        'id_rencontre' => $this->getIdRencontre(),
        'date_rencontre' => $this->getDateRencontre(),
        'lieu' => $this->getLieu(),
        'nom_adversaire' => $this->getNomAdversaire(),
        'resultat' => $this->getResultat(),
        'date_creation' => $this->getDateCreation(),
      ]);
    } catch (Exception $e) {
      die('Erreur lors de la mise à jour de la rencontre : ' . $e->getMessage());
    }
  }
}
