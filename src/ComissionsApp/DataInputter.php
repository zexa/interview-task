<?php

namespace ComissionsApp;

use Parsers\CSVParser;
use ComissionsApp\ComissionCalculator;

class DataInputter
{
    private $results;

    public function getResults(
        string $input_path,
        ?string $config_path = null,
        ?string $rates_path = null
    ): array {
        $csvParser = new CSVParser();

        if (!$csvParser->setFile($input_path)) {
            throw new \Exception(
                "Input file '" . $input_path . "' does not exist."
            );
        }

        $comissionsCalculator = new ComissionCalculator();
        $csvParser->format = array(
            "date",
            "userID",
            "userType",
            "operation",
            "value",
            "currency"
        );

        $results = [];
        $csvParser->parseByLine(function (array $arr) use (
            &$comissionsCalculator, &$results
        ) {
            $comissionsCalculator->inputEntry($arr);
            $comission = $comissionsCalculator->calculateComission();
            $results[] = (string)$comission;
        });
        $this->results = $results;
        return $results;
    }

    public function outputResults()
    {
        foreach ($this->results as $result) {
            echo $result . PHP_EOL;
        }
    }
}
