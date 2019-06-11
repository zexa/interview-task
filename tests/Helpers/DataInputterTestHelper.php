<?php

namespace TestHelpers;

use ComissionsApp\DataInputter;

class DataInputterTestHelper
{
    private $realResults;
    private $expectedResults;

    public function getResults(string $filepath)
    {
        $dataInputter = new DataInputter();
        $results = $dataInputter->getResults($filepath);
        $this->realResults = $results;
        return $results;
    }

    public function getExpectedResults(string $filepath): array
    {
        $file = fopen($filepath, "r");
        if ($file === false) {
            throw new \Exception("ExpectedResults file could not be opened");
        }
        $expectedResults = [];
        while (!feof($file)) {
            $res = strval(fgets($file));
            if ($res != null) {
                $expectedResults[] = trim($res);
            }
        }
        fclose($file);
        $this->expectedResults = $expectedResults;
        return $expectedResults;
    }

    public function compareRealAndExpectedResults(
        string $inputFP,
        string $expectedFP
    ): bool {
        $realResults = $this->getResults($inputFP);
        $expectedResults = $this->getExpectedResults($expectedFP);
        $areEquals = $realResults == $expectedResults;
        if (!$areEquals) {
            for ($i = 0; $i < count($expectedResults); $i++) {
                if ((string) $realResults[$i] !== $expectedResults[$i]) {
                    echo "real: " .
                        (string) $realResults[$i] .
                        " type: " .
                        gettype($realResults[$i]) .
                        PHP_EOL;
                    echo "expected: " .
                        $expectedResults[$i] .
                        " type: " .
                        gettype($expectedResults[$i]) .
                        PHP_EOL;
                }
            }
        }
        return $areEquals;
    }
}
