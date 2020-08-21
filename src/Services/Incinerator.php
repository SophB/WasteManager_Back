<?php

namespace App\Services;

use App\Business\Report;
use App\Entity\Waste;

class Incinerator implements CentersInterface
{
    public string $name;
    public float $capacity;
    public float $CO2Rejected;

    public function __construct(string $name, float $capacity)
    {
        $this->name = $name;
        $this->capacity = $capacity;
    }

    public function canTreatWaste(string $type): bool
    {
        return ($this->capacity > 0);
    }

    public function updateCenterCapacity(float $wasteWeight): int
    {
        $this->capacity -= $wasteWeight;
        return $this->capacity;
    }

    public function treatWaste(Waste $waste)
    {
        $waste->isTreated = true;
        $report = new Report();

        switch ($waste->type) {
            case 'various':
                $this->CO2Rejected = floatval($waste->weight) * 30;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'paper':
                $this->CO2Rejected = floatval($waste->weight) * 25;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'organic':
                $this->CO2Rejected = floatval($waste->weight) * 28;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'metal':
            case 'glass':
                $this->CO2Rejected = floatval($waste->weight) * 50;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'PET':
                $this->CO2Rejected = floatval($waste->weight) * 40;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'PVC':
                $this->CO2Rejected = floatval($waste->weight) * 38;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'PEHD':
                $this->CO2Rejected = floatval($waste->weight) * 35;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
            case 'PC':
                $this->CO2Rejected = floatval($waste->weight) * 42;
                $report->setReport($waste, $this, $this->CO2Rejected);
                break;
        }
    }
}
