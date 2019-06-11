<?php

if ($argc < 2) {
    echo "Missing required argument." . PHP_EOL;
    exit();
}

require __DIR__ . '/vendor/autoload.php';

use ComissionsApp\DataInputter;

$data_inputter = new ComissionsApp\DataInputter();
$data_inputter->getResults($argv[1]);
$data_inputter->outputResults();
