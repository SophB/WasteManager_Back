<?php

namespace App\Services;

use App\Business\Report;
use App\Entity\Waste;

class SortingCenter
{
    public int $capacity;
    public array $wasteTypeTreated;

    public function __construct(int $capacity)
    {
        $this->capacity = $capacity;
        $this->wasteTypeTreated = ['PET', 'PVC', 'PEHD', 'PC', 'paper', 'glass', 'metal'];
    }

    public function updateCenterCapacity(float $wasteWeight): float
    {
        $this->capacity -= $wasteWeight;
        return $this->capacity;
    }

    public function sendToTreatmentCenter(Waste $waste, RecyclingCenter $treatmentCenter)
    {
        if ($treatmentCenter->canTreatWaste($waste->type)) {
            $treatmentCenter->treatWaste($waste);
        }
    }
}
