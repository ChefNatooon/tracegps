<?php
// Projet TraceGPS
// fichier : modele/Trace.class.php
// Rôle : la classe Trace représente une trace ou un parcours
// Dernière mise à jour : 9/7/2021 par dPlanchet
include_once ('PointDeTrace.class.php');
class Trace
{
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------- Attributs privés de la classe -------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    private $id; // identifiant de la trace
    private $dateHeureDebut; // date et heure de début
    private $dateHeureFin; // date et heure de fin
    private $terminee; // true si la trace est terminée, false sinon
    private $idUtilisateur; // identifiant de l'utilisateur ayant créé la trace
    private $lesPointsDeTrace; // la collection (array) des objets PointDeTrace formant la trace
    
    // ------------------------------------------------------------------------------------------------------
    // ----------------------------------------- Constructeur -----------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function __construct($unId, $uneDateHeureDebut, $uneDateHeureFin, $terminee, $unIdUtilisateur) {
        // A VOUS DE TROUVER LE CODE MANQUANT
        $this->id = $unId;
        $this->dateHeureDebut = $uneDateHeureDebut;
        $this->dateHeureFin = $uneDateHeureFin;
        $this->terminee = $terminee;
        $this->idUtilisateur = $unIdUtilisateur;
        $this->lesPointsDeTrace = array();
    }
    
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------------- Getters et Setters ------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function getId() {return $this->id;}
    public function setId($unId) {$this->id = $unId;}
    
    public function getDateHeureDebut() {return $this->dateHeureDebut;}
    public function setDateHeureDebut($uneDateHeureDebut) {$this->dateHeureDebut = $uneDateHeureDebut;}
    
    public function getDateHeureFin() {return $this->dateHeureFin;}
    public function setDateHeureFin($uneDateHeureFin) {$this->dateHeureFin= $uneDateHeureFin;}
    
    public function getTerminee() {return $this->terminee;}
    public function setTerminee($terminee) {$this->terminee = $terminee;}
    
    public function getIdUtilisateur() {return $this->idUtilisateur;}
    public function setIdUtilisateur($unIdUtilisateur) {$this->idUtilisateur = $unIdUtilisateur;}
    
    public function getLesPointsDeTrace() {return $this->lesPointsDeTrace;}
    public function setLesPointsDeTrace($lesPointsDeTrace) {$this->lesPointsDeTrace = $lesPointsDeTrace;}
    
    // Fournit une chaine contenant toutes les données de l'objet
    public function toString() {
        $msg = "Id : " . $this->getId() . "<br>";
        $msg .= "Utilisateur : " . $this->getIdUtilisateur() . "<br>";
        if ($this->getDateHeureDebut() != null) {
            $msg .= "Heure de début : " . $this->getDateHeureDebut() . "<br>";
        }
        if ($this->getTerminee()) {
            $msg .= "Terminée : Oui <br>";
        }
        else {
            $msg .= "Terminée : Non <br>";
        }
        $msg .= "Nombre de points : " . $this->getNombrePoints() . "<br>";
        if ($this->getNombrePoints() > 0) {
            if ($this->getDateHeureFin() != null) {
                $msg .= "Heure de fin : " . $this->getDateHeureFin() . "<br>";
            }
            $msg .= "Durée en secondes : " . $this->getDureeEnSecondes() . "<br>";
            $msg .= "Durée totale : " . $this->getDureeTotale() . "<br>";
            $msg .= "Distance totale en Km : " . $this->getDistanceTotale() . "<br>";
            $msg .= "Dénivelé en m : " . $this->getDenivele() . "<br>";
            $msg .= "Dénivelé positif en m : " . $this->getDenivelePositif() . "<br>";
            $msg .= "Dénivelé négatif en m : " . $this->getDeniveleNegatif() . "<br>";
            $msg .= "Vitesse moyenne en Km/h : " . $this->getVitesseMoyenne() . "<br>";
            $msg .= "Centre du parcours : " . "<br>";
            $msg .= " - Latitude : " . $this->getCentre()->getLatitude() . "<br>";
            $msg .= " - Longitude : " . $this->getCentre()->getLongitude() . "<br>";
            $msg .= " - Altitude : " . $this->getCentre()->getAltitude() . "<br>";
        }
        return $msg;
    }
    
    public function getNombrePoints(){
        return sizeof($this->lesPointsDeTrace);
    }
    
    public function getCentre()
    {
        // Si on ne dispose pas de minimum 2 points
        if (sizeof($this->lesPointsDeTrace) <= 1)
        {
            return null;
        }
        $longList = Array();
        $latList = Array();
        foreach ($this->lesPointsDeTrace as $lePoint)
        {
            $longList[]=$lePoint->getLongitude();
            $latList[]=$lePoint->getLatitude();
        }
        Sort($latList);
        Sort($longList);
        
        $lastIndex = sizeof($latList) - 1;
        
        $latMilieu = ($latList[0] + $latList[$lastIndex]) / 2;
        $longMilieu = ($longList[0] + $longList[$lastIndex]) / 2;
        return new Point($latMilieu, $longMilieu, 0);
    }
    
    public function getDenivele()
    {
        // Si on ne dispose pas de minimum 2 points
        if (sizeof($this->lesPointsDeTrace) <= 1)
        {
            return 0;
        }
        
        $denivele = 0;
        $altitude = 0;
        $altMin = 100000000;
        $altMax = 0;
        
        foreach ($this->lesPointsDeTrace as $lePoint)
        {
            $altitude = $lePoint->getAltitude();
            
            if ($altitude > $altMax)
            {
                $altMax = $altitude;
            }
            if ($altitude < $altMin)
            {
                $altMin = $altitude;
            }
        }
        $denivele = $altMax - $altMin;
        return $denivele;
    }
    
    public function getDureeEnSecondes()
    {
        // Si on ne dispose pas de minimum 2 points
        if (sizeof($this->lesPointsDeTrace) <= 1)
        {
            return 0;
        }
        $lePoint = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) - 1];
        return $lePoint->getTempsCumule();
    }
    
    public function getDureeTotale()
    {
        $tempsCumule = $this->getDureeEnSecondes();
        $heures = $tempsCumule / 3600;
        $duree = $tempsCumule%3600;
        $minutes = $duree / 60;
        $secondes = $duree%60;
        return sprintf("%02d", $heures) . ":" . sprintf("%02d", $minutes) . ":" . sprintf("%02d", $secondes);
    }
    
    public function getDistanceTotale()
    {
        // Si on ne dispose pas de minimum 2 points
        if (sizeof($this->lesPointsDeTrace) <= 1)
        {
            return 0;
        }
        $lePoint = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) - 1];
        return $lePoint->getDistanceCumulee();
        
    }
    
    public function getDenivelePositif()
    {
        // Si on ne dispose pas de minimum 2 points
        if (sizeof($this->lesPointsDeTrace) <= 1)
        {
            return 0;
        }
        $denivele = 0;
        for ($i = 0; $i < sizeof($this->lesPointsDeTrace) - 1; $i++)
        {
            $lePoint = $this->lesPointsDeTrace[$i];
            $lePointSuivant = $this->lesPointsDeTrace[$i + 1];
            $denivelePtaPt = $lePointSuivant->getAltitude() - $lePoint->getAltitude();
            if ($lePointSuivant->getAltitude() > $lePoint->getAltitude())
            {
                $denivele += $denivelePtaPt;
            }
        }
        return $denivele;
    }
    
    public function getDeniveleNegatif()
    {
        // Si on ne dispose pas de minimum 2 points
        if (sizeof($this->lesPointsDeTrace) <= 1)
        {
            return 0;
        }
        $denivele = 0;
        for ($i = 0; $i < sizeof($this->lesPointsDeTrace) - 1; $i++)
        {
            $lePoint = $this->lesPointsDeTrace[$i];
            $lePointSuivant = $this->lesPointsDeTrace[$i + 1];
            $denivelePtaPt = $lePointSuivant->getAltitude() - $lePoint->getAltitude();
            if ($lePointSuivant->getAltitude() > $lePoint->getAltitude())
            {
                $denivele += $denivelePtaPt;
            }
        }
        return $denivele;
    }
    
    public function getVitesseMoyenne()
    {
        if ($this->getDistanceTotale() == 0)
        {
            return 0;
        }
        else
        {
            $vitesseMoy = $this->getDistanceTotale() / ($this->getDureeEnSecondes() / 3600);
            
            return $vitesseMoy;
        }
    }
    
    public function ajouterPoint(PointDeTrace $unPoint)
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            $unPoint->setDistanceCumulee(0);
            $unPoint->setVitesse(0);
            $unPoint->setDistanceCumulee(0);
        }
        else
        {
            // On récupère le dernier point de la liste
            $dernierPoint = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) - 1];
            
            //Calcul de la durée entre le dernier point et le nouveau point
            $duree = strtotime($unPoint->getDateHeure()) - strtotime($dernierPoint->getDateHeure());
            $unPoint->setTempsCumule($dernierPoint->getTempsCumule() + $duree);
            
            //Calcul de la distance entre le dernier point et le nouveau point
            $distance = Point::getDistance($dernierPoint, $unPoint);
            $unPoint->setDistanceCumulee($dernierPoint->getDistanceCumulee() + $distance);
            
            if($duree = 0) {
                $vitesse = $distance / $duree * 3600;
            }
            else {
                $vitesse = 0;
            }
            
            $unPoint->setVitesse($vitesse);
            
        }
        $this->lesPointsDeTrace[] = $unPoint;
    }
    
    public function viderListePoints()
    {
        $this->lesPointsDeTrace->Clear();
    }
    
} // fin de la classe Trace
// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!
