<?php

namespace App\Services;

use App\Entity\Waste;

interface CentersInterface
{
    public function canTreatWaste(string $type): bool;
    public function updateCenterCapacity(float $weight): int;
    public function treatWaste(Waste $waste); //returns void
}