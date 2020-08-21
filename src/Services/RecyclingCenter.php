<?php

namespace App\Services;

use App\Business\Report;
use App\Entity\Waste;

class RecyclingCenter implements CentersInterface
{
    public string $name;
    public float $capacity;
    public array $wasteTypeTreated;
    public float $CO2Rejected;

    public function __construct(string $name, float $capacity, array $wasteTypeTreated)
    {
        $this->name = $name;
        $this->capacity = $capacity;
        $this->wasteTypeTreated = $wasteTypeTreated;
        $this->CO2Rejected = 0;
    }

    public function canTreatWaste(string $type): bool
    {
        return in_array($type, $this->wasteTypeTreated);
    }

    public function updateCenterCapacity(float $wasteWeight): int
    {
        $this->capacity -= $wasteWeight;
        return $this->capacity;
    }

    public function treatWaste(Waste $waste)
    {
        $report = new Report();

        // Calcul du CO2 rejeté lors du recyclage :
        switch ($waste->type) {
            case 'paper':
                $this->CO2Rejected = floatval($waste->weight) * 5;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'metal':
                $this->CO2Rejected = floatval($waste->weight) * 7;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'glass':
                $this->CO2Rejected = floatval($waste->weight) * 6;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'PET':
                $this->CO2Rejected = floatval($waste->weight) * 8;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'PVC':
                $this->CO2Rejected = floatval($waste->weight) * 12;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'PEHD':
                $this->CO2Rejected = floatval($waste->weight) * 11;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'PC':
                $this->CO2Rejected = floatval($waste->weight) * 10;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'organic':
                $this->CO2Rejected = floatval($waste->weight);
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
        }
        $waste->isTreated = true;
    }

}
