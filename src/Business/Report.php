<?php

namespace App\Business;

use App\Entity\Waste;

class Report
{
    public function setReport(Waste $waste, $center, $CO2) {
        echo $waste->type . ' has been treated in ' . $center->name . ', weight treated = ' . $waste->weight . ', CO2 rejected : ' . $CO2 . "\n";
    }
}