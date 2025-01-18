<?php
// Definition de l'enum pour l'etat du joueur
enum EtatJoueur: string {
    case ACTIF = "Actif";
    case BLESSE = "Blessé";
    case SUSPENDU = "Suspendu";
    case RETRAITE = "Absent";
}
class Joueur {

    const DESC = "Cette classe permet de définir un joueur";

    private string $Nom; 
    private string $Prenom; 
    private string $NumeroLicence; 
    private string $DateNaissance; 
    private float $Taille; 
    private float $Poids; 
    private EtatJoueur $Etat;
    
    // Constructeur
    public function __construct(string $Nom, string $Prenom, string $NumeroLicence, string $DateNaissance, float $Taille, float $Poids, EtatJoueur $Etat) {
        $this->Nom = $Nom;
        $this->Prenom = $Prenom;
        $this->NumeroLicence = $NumeroLicence;
        $this->DateNaissance = $DateNaissance;
        $this->Taille = $Taille;
        $this->Poids = $Poids;
        $this->Etat = $Etat;
    }

    // Destructeur
    public function __destruct() {
        echo "Le joueur $this->Nom $this->Prenom n'existe plus !<br/>";
    }

    // Getters
    public function getNom(): string {
        return $this->Nom;
    }

    public function getPrenom(): string {
        return $this->Prenom;
    }

    public function getNumeroLicence(): string {
        return $this->NumeroLicence;
    }

    public function getDateNaissance(): string {
        return $this->DateNaissance;
    }

    public function getTaille(): float {
        return $this->Taille;
    }

    public function getPoids(): float {
        return $this->Poids;
    }

    public function getEtat(){
        return $this->Etat;
    }
}
?>
