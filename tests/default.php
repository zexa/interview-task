<?php

use PHPUnit\Framework\TestCase;
use ComissionsApp\ComissionCalculator;
use Parsers\JSONParser;
use Parsers\CSVParser;

class OutputTest extends TestCase
{
	public $JSONParser;
	public $CSVParser;
	public $ComissionCalculator;
	public $config;
	public $rates;
	public $thisTest;

	public function testLoadsFilesWithJSONParser()
	{
		$this->JSONParser = new JSONParser();

		$configFilePath = __DIR__.'/../config.json';	
		$configNotLoaded = $this->JSONParser->setFile($configFilePath);
		$this->assertFalse(!$configNotLoaded);
		$this->config = $this->JSONParser->parse();
		$this->assertFalse($this->config === null);

		$ratesFilePath = __DIR__ . '/../rates.json';
		$ratesNotLoaded = !$this->JSONParser->setFile($ratesFilePath);
		$this->assertFalse($ratesNotLoaded);
		$this->rates = $this->JSONParser->parse();
		$this->assertFalse($this->rates === null);
	}

	/*
	 * @depends testLoadFilesWithJSONParser
	 */
	public function testComissionsCalculator()
	{
		$this->JSONParser = new JSONParser();

		$configFilePath = __DIR__.'/../config.json';	
		$configNotLoaded = $this->JSONParser->setFile($configFilePath);
		$this->config = $this->JSONParser->parse();

		$ratesFilePath = __DIR__ . '/../rates.json';
		$ratesNotLoaded = !$this->JSONParser->setFile($ratesFilePath);
		$this->rates = $this->JSONParser->parse();

		$csvParser = new CSVParser;
		$csvParser->format = array(
			"date",
			"userID",
			"userType",
			"operation",
			"value",
			"currency"
		);
		// Loads tuples of csvFilePaths and Result Arrays
		$csvTuples = [
			[
				"filePath" => __DIR__.'/../input.csv',
				"results" => [
					0.60,
					3.00,
					0.00,
					0.06,
					0.90,
					0.00,
					0.70,
					0.30,
					0.30,
					5.00,
					0.00,
					0.00,
					8612
				]
			]
		];

		foreach ($csvTuples as $csvTuple) {
			$inputFilePath = $csvTuple["filePath"];
			$csvResults = $csvTuple["results"];
			$i = 0;
			$inputNotLoaded = !$csvParser->setFile($inputFilePath);
			$this->assertFalse($inputNotLoaded);
			$comissionsCalculator = new ComissionCalculator($this->config, $this->rates);
			$csvParser->parseByLine(function($arr) use (&$comissionsCalculator, &$i, $csvResults)
			{
				$comissionsCalculator->inputEntry($arr);
				$comission = $comissionsCalculator->calculateComission();
				$this->assertEquals($comission, $csvResults[$i]);
				$i++;
			});
		}
	}




}

?>
