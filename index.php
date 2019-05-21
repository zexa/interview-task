<?php

if ($argc < 2) {
  echo("Missing required argument." . PHP_EOL);
  exit;
}

require __DIR__ . '/vendor/autoload.php';

use ComissionsApp\ComissionCalculator;
use Parsers\JSONParser;
use Parsers\CSVParser;

$jsonParser = new JSONParser();
$csvParser = new CSVParser();

// Config
$configFilePath = __DIR__ . '/config.json';
if (!$jsonParser->setFile($configFilePath)) {
  echo("Configuration file '" . $configFilePath . "' does not exist." . PHP_EOL);
  exit;
}
$config = $jsonParser->parse();

// Rates
$ratesFilePath = __DIR__ . '/' . $config["rates"]["file"];
if (!$jsonParser->setFile($ratesFilePath)) {
  echo("Rates file '" . $ratesFilePath . "' does not exist." . PHP_EOL);
  exit;
}
$rates = $jsonParser->parse();

$inputFilePath = $argv[1];
if (!$csvParser->setFile($inputFilePath)) {
  echo("Input file '" . $inputFilePath . "' does not exist." . PHP_EOL);
  exit;
}

$comissionsCalculator = new ComissionsApp\ComissionCalculator($config, $rates);
$csvParser->format = array(
	"date",
	"userID",
	"userType",
	"operation",
	"value",
	"currency"
);
$csvParser->parseByLine(function($arr) use (&$comissionsCalculator)
{
	$comissionsCalculator->inputEntry($arr);
	$comission = $comissionsCalculator->calculateComission();
	echo($comission . PHP_EOL);
});

?>
