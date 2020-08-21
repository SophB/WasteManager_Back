<?php

namespace App\Entity;


class Waste
{
    public string $type;
    public float $weight;
    public bool $isTreated;

    public function __construct(string $type, float $weight)
    {
        $this->type = $type;
        $this->weight = $weight;
        $this->isTreated = false;
    }
}
