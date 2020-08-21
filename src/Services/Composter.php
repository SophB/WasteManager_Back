<?php

namespace App\Services;

use App\Business\Report;
use App\Entity\Waste;

class Composter implements CentersInterface
{
    public string $name;
    public float $capacity;
    public array $wasteTypeTreated;
    public float $CO2Rejected;

    public function __construct(string $name, float $capacity)
    {
        $this->name = $name;
        $this->capacity = $capacity;
        $this->wasteTypeTreated = ['organic'];
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
        $this->CO2Rejected = floatval($waste->weight);
        $report->setReport($waste, $this, $this->CO2Rejected);
        $waste->isTreated = true;
    }
}
