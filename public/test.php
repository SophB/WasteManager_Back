<?php

use App\Business\WasteManager;

require __DIR__ . '/../vendor/autoload.php';

// Decoding the jsonFile
$wasteManager = new WasteManager('data.json');

// creating instances of wastes and centers
$wasteManager->createWastes();
$wasteManager->createCenters();

// SORTING :
$wasteManager->dispatchWastes();

// REPORT :
$wasteManager->setReport();