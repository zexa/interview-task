<?php

require __DIR__ . '/vendor/autoload.php';

use function CLI\_echo;
use ComissionsApp\ComissionCalculator;
use Parsers\JSONParser;
use Parsers\CSVParser;

$jsonParser = new Parsers\JSONParser();
$csvParser = new Parsers\CSVParser();

// Config
$configFilePath = __DIR__ . '/config.json';
if (!$jsonParser->setFile($configFilePath)) {
  _echo("Configuration file '" . $configFilePath . "' does not exist.");
  exit;
}
$config = $jsonParser->parse();

// Rates
$ratesFilePath = __DIR__ . '/' . $config["rates"]["file"];
if (!$jsonParser->setFile($ratesFilePath)) {
  _echo("Rates file '" . $ratesFilePath . "' does not exist.");
  exit;
}
$rates = $jsonParser->parse();

// Input
if (!array_key_exists(1, $argv)) {
  _echo("Missing required argument.");
  exit;
}
$inputFilePath = $argv[1];
if (!$csvParser->setFile($inputFilePath)) {
  _echo("Input file '" . $inputFilePath . "' does not exist.");
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
$csvParser->parseByLine(function($arr) use (&$comissionsCalculator) {
	$comissionsCalculator->inputEntry($arr);
	$comission = $comissionsCalculator->calculateComission();
	_echo($comission);
});

?>
