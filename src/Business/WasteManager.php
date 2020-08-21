<?php

namespace App\Business;

use App\Entity\Waste;
use App\Services\Incinerator;
use App\Services\RecyclingCenter;
use App\Services\SortingCenter;

class WasteManager
{
    /**
     * Décodage du fichier d'entrée en format Json pour le convertir en tableau PHP
     */
    private array $jsonInput;
    /**
     * Répartition des quartiers
     */
    public array $districts;
    /**
     * Répartition des centres de traitement
     */
    public array $centers;
    /**
     * Déchets à traiter
     * @var Waste[]
     */
    public array $wastes = array();
    /**
     * Centres de Recyclage
     * @var RecyclingCenter[]
     */
    public array $recyclingCenters = array();
    /**
     * Incinérateurs
     * @var Incinerator
     */
    public array $incinerators = array();
    /**
     * Centre de tri
     * @var SortingCenter
     */
    public SortingCenter $sortingCenter;


    public function __construct(string $path)
    {
        $jsondata = file_get_contents($path);
        $this->jsonInput = json_decode($jsondata, true);
        $this->districts = $this->jsonInput['quartiers'];
        $this->centers = $this->jsonInput['services'];
    }

    /**
     * Méthode qui crée les instances de déchets à traiter
     */
    public function createWastes()
    {
        foreach ($this->districts as $district) {
            // Plastiques :
            array_push($this->wastes, new Waste('PET', $district['plastiques']['PET']));
            array_push($this->wastes, new Waste('PC', $district['plastiques']['PC']));
            array_push($this->wastes, new Waste('PEHD', $district['plastiques']['PEHD']));
            array_push($this->wastes, new Waste('PVC', $district['plastiques']['PVC']));
            // non plastiques :
            array_push($this->wastes, new Waste('paper', $district['papier']));
            array_push($this->wastes, new Waste('organic', $district['organique']));
            array_push($this->wastes, new Waste('glass', $district['verre']));
            array_push($this->wastes, new Waste('metal', $district['metaux']));
            array_push($this->wastes, new Waste('various', $district['autre']));
        }
    }

    /**
     * Méthode qui crée les instances des centres de traitement
     */
    public function createCenters()
    {
        foreach ($this->centers as $center) {
            switch ($center['type']) {
                case "incinerateur":
                    $capacity = intval($center['capaciteLigne'] * $center['ligneFour']);
                    array_push($this->incinerators, new Incinerator($center['type'], $capacity));
                    break;
                case "composteur":
                    array_push($this->recyclingCenters, new RecyclingCenter($center['type'], $center['capacite'], ['organic']));
                    break;
                case "centreTri":
                    $this->sortingCenter = new SortingCenter($center['capacite']);
                    break;
                case "recyclagePlastique":
                    array_push($this->recyclingCenters, new RecyclingCenter($center['type'], $center['capacite'], $center['plastiques']));
                    break;
                case "recyclagePapier":
                    array_push($this->recyclingCenters, new RecyclingCenter($center['type'], $center['capacite'], ['paper']));
                    break;
                case "recyclageVerre":
                    array_push($this->recyclingCenters, new RecyclingCenter($center['type'], $center['capacite'], ['glass']));
                    break;
                case "recyclageMetaux":
                    array_push($this->recyclingCenters, new RecyclingCenter($center['type'], $center['capacite'], ['metal']));
                    break;
            }
        }
    }

    /**
     * Méthode de tri des déchets
     */
    public function dispatchWastes()
    {
        foreach ($this->wastes as $waste) {
            switch ($waste->type) {
                case 'paper':
                case 'glass':
                case 'metal':
                case 'PET':
                case 'PVC':
                case 'PC':
                case 'PEHD':
                case 'organic':
                    if ($this->sortingCenter->capacity > 0) {
                        // Si le centre de tri n'est pas plein et que le poids de déchets à traiter est inférieur à la capacité du centre:
                        if ($waste->weight < $this->sortingCenter->capacity) {
                            // on parcourt les centres de recyclage pour pouvoir traiter le déchet
                            foreach ($this->recyclingCenters as $center) {
                                if (!$waste->isTreated) {
                                    $this->sortingCenter->sendToTreatmentCenter($waste, $center);
                                }
                            }
                            // et on met à jour la capacité du centre de tri
                            $this->sortingCenter->updateCenterCapacity($waste->weight);
                        }
                        // Si le centre de tri n'est pas plein et que le poids de déchets à traiter est supérieur à la capacité du centre:
                        else {
                            // on calcule le poids qui restera à traiter après être passé au centre de tri
                            $wasteWeightLeftToTreat = $waste->weight - $this->sortingCenter->capacity;

                        
                            // et on traite la qté de déchets équivalente à la capacité restante du centre de tri (qu'on passe à 0)
                            $wasteWeightToRecycle = $this->sortingCenter->capacity;
                            $this->sortingCenter->capacity = 0;
                            $waste->weight = $wasteWeightToRecycle;

                            foreach ($this->recyclingCenters as $center) {
                                $this->sortingCenter->sendToTreatmentCenter($waste, $center);
                            }

                            // on passe le déchet en non traité pour pouvoir passer à l'incinérateur ce qui n'est pas passé dans le centre de tri
                            $waste->isTreated = false;
                            $waste->weight = $wasteWeightLeftToTreat;
                            for ($i = 0; $i < count($this->incinerators); $i++) {
                                if ($this->incinerators[$i]->capacity > 0 && !$waste->isTreated) {
                                    $this->incinerators[$i]->treatWaste($waste);
                                }
                            }
                        }
                        break;
                    }
                default:
                    // par défaut, on passe les déchets qui ne sont pas traités à l'incinérateur
                    for ($i = 0; $i < count($this->incinerators); $i++) {
                            if ($this->incinerators[$i]->capacity > 0 && !$waste->isTreated) {
                                $this->incinerators[$i]->treatWaste($waste);
                            }
                        }
                    break;
            }
        }
    }

    public function setReport()
    {
        
        // $petWeight = 0;
        // $pvcWeight = 0;
        // $pehdWeight = 0;
        // $pcWeight = 0;
        // $paperWeight = 0;
        // $metalWeight = 0;
        // $compostWeight = 0;
        // $glassWeight = 0;
        // $variousWeight = 0;
        // foreach ($this->wastes as $wastes) {
        //     if ($waste->isTreated) {
        //         switch ($wastes->type) {
        //             case 'PET':
        //                 $petWeight += $wastes->weight;
        //                 break;
        //             case 'PVC':
        //                 $pvcWeight += $wastes->weight;
        //                 break;
        //             case 'PEHD':
        //                 $pehdWeight += $wastes->weight;
        //                 break;
        //             case 'PC':
        //                 $pcWeight += $wastes->weight;
        //                 break;
        //             case 'paper':
        //                 $paperWeight += $wastes->weight;
        //                 break;
        //             case 'metal':
        //                 $metalWeight += $wastes->weight;
        //                 break;
        //             case 'glass':
        //                 $glassWeight += $wastes->weight;
        //                 break;
        //             case 'compost':
        //                 $compostWeight += $wastes->weight;
        //                 break;
        //             case 'various':
        //                 $variousWeight += $wastes->weight;
        //                 break;
        //         }
        //     }
        // }
    }
}
