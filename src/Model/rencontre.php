<?php
enum Lieu: string {
  case DOMICILE = "Domicile";
  case EXTERIEUR ="Exterieur";
}
require_once 'joueur.php' ;
class rencontre{
  private string $Date; 
  private string $Heure;
  private int $NosPoints;
  private int $LeursPoints; 
  private Lieu $Lieu;
  private string $EquipeAdverse; 
  private array $JoueursTitulaires; 
  private array $JoueursRemplacants; 

  // Constructeur
  public function __construct(string $Date, string $Heure, int $NosPoints, int $LeursPoints, Lieu $Lieu,  string $EquipeAdverse, array $JoueursTitulaires, array $JoueursRemplacants) {
      $this->Date = $Date;
      $this->Heure = $Heure;
      $this->NosPoints= $NosPoints;
      $this->LeursPoints =$LeursPoints;
      $this->Lieu =$Lieu;  
      $this->EquipeAdverse = $EquipeAdverse;
      $this->setJoueursTitulaires($JoueursTitulaires);
      $this->setJoueursRemplacants($JoueursRemplacants);
  }

  // Getters
  public function getDate(): string {
      return $this->Date;
  }

  public function getEquipeAdverse(): string {
      return $this->EquipeAdverse;
  }

  public function getJoueursTitulaires(): array {
      return $this->JoueursTitulaires;
  }

  public function getJoueursRemplacants(): array {
      return $this->JoueursRemplacants;
  }

  public function getNosPoints(): int {
    return $this->NosPoints;
  }
  public function getLeursPoints(): int {
    return $this->LeursPoints;
  }

  public function getLieu(): Lieu {
    return $this->Lieu; 
  }

  // Setters
  public function setDate(string $Date): void {
      $this->Date = $Date;
  }

  public function setEquipeAdverse(string $EquipeAdverse): void {
      $this->EquipeAdverse = $EquipeAdverse;
  }

  public function setNosPoints(int $NosPoints): void {
    $this->NosPoints = $NosPoints;
  }
  public function setLeursPoints(int $LeursPoints): void {
    $this->LeursPoints = $LeursPoints;
  }

  public function setLieu(Lieu $Lieu): void{
    $this->Lieu = $Lieu; 
  } 

  public function setJoueursTitulaires(array $JoueursTitulaires): void {
      foreach ($JoueursTitulaires as $joueur) {
          if (!$joueur instanceof joueur) {
              throw new InvalidArgumentException("Tous les joueurs titulaires doivent être des instances de la classe Joueur.");
          }
      }
      $this->JoueursTitulaires = $JoueursTitulaires;
  }

  public function setJoueursRemplacants(array $JoueursRemplacants): void {
      foreach ($JoueursRemplacants as $joueur) {
          if (!$joueur instanceof joueur) {
              throw new InvalidArgumentException("Tous les joueurs remplaçants doivent être des instances de la classe Joueur.");
          }
      }
      $this->JoueursRemplacants = $JoueursRemplacants;
  }
  // Ajout de donnée dans un tableau
  public function addJoueurTitulaire(joueur $joueur): void{
    if (!in_array($joueur,$this->JoueursTitulaires )){
      array_push($this->JoueursTitulaires, $joueur);
    }else{
      echo "Joueur deja titulaire" ; 
    }
    
  }
  public function addJoueurRempacant(joueur $joueur): void{
    if (!in_array($joueur,$this->JoueursRemplacants )){
      array_push($this->JoueursRemplacants, $joueur);
    }else{
      echo "Joueur deja Remplacant" ; 
    }
    
  }
  //Supression de donnée dans un tableau 
  public function deleteJoueurTitulaire(joueur $joueur){
    if (in_array($joueur,$this->JoueursTitulaires )){
      unset($this->JoueursTitulaires[$joueur]);
    }else{
      echo "Joueur deja non titulaire" ; 
    }
  }

  public function deleteJoueurRemplacant(joueur $joueur){
    if (in_array($joueur,$this->JoueursRemplacants)){
      unset($this->JoueursRemplacants[$joueur]);
    }else{
      echo "Joueur deja non titulaire" ; 
    }
  }

}

?>