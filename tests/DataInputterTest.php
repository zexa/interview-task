<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use ComissionsApp\DataInputter;
use TestHelpers\DataInputterTestHelper;

final class DataInputterTest extends TestCase
{
    public function testGetResultsThrowsExceptionOnNonExistantInputFile()
    {
        $dataInputter = new DataInputter();
        $this->expectException(\Exception::class);
        $dataInputter->getResults('');
    }

    public function testGetResultsReturnsArray()
    {
        $dataInputter = new DataInputter();

        $fp = __DIR__ . '/inputs/input.csv';
        $this->assertFileExists($fp);

        $result = $dataInputter->getResults($fp);
        $resultIsArray = is_array($result);
        $this->assertTrue($resultIsArray);
    }

    /* Test passes. So why is there a new line after executing index.php
     * on the very first line?
     */
    public function testGetResultsDoesntReturnFirstElementAsEmpty()
    {
        $dataInputter = new DataInputter();

        $fp = __DIR__ . '/inputs/input.csv';
        $this->assertFileExists($fp);

        $results = $dataInputter->getResults($fp);
        $firstElemIsEmpty = $results[0] == null;
        $this->assertFalse($firstElemIsEmpty);
    }

    public function testGetResultsReturnsArrayOfString()
    {
        $dataInputter = new DataInputter();

        $fp = __DIR__ . '/inputs/input.csv';
        $this->assertFileExists($fp);

        $results = $dataInputter->getResults($fp);

        foreach ($results as $result) {
            $this->assertTrue(
                is_string($result),
                "Received " . gettype($result) . " instead of string."
            );
        }
    }

    public function InputCheck($filename)
    {
        $dataInputterTestHelper = new DataInputterTestHelper();
        $dataInputter = new DataInputter();

        //$filename = 'input-jpy-0-no-decimal.csv';

        $realfp = __DIR__ . '/inputs/' . $filename;
        $this->assertFileExists($realfp);

        $expectedfp = __DIR__ . '/results/' . $filename;
        $this->assertFileExists($expectedfp);

        $realResults = $dataInputter->getResults($realfp);
        $expectedResults = $dataInputterTestHelper->getExpectedResults(
            $expectedfp
        );

        $realResultsCount = count($realResults);
        $expectedResultsCount = count($expectedResults);

        $this->assertEquals($realResultsCount, $expectedResultsCount);

        $this->assertEquals($realResults, $expectedResults);

        $this->assertTrue(
            $dataInputterTestHelper->compareRealAndExpectedResults(
                $realfp,
                $expectedfp
            )
        );
    }

    public function testDefaultInputReturnsCorrectResult()
    {
        $this->InputCheck('input.csv');
    }

    public function testJPYReturnsIntegerOn0()
    {
        $this->InputCheck('input-jpy-0-no-decimal.csv');
    }

    public function testUnknownCurrencyThrowsException()
    {
        $dataInputter = new DataInputter();

        $filename = 'input-unknown-currency.csv';
        $realfp = __DIR__ . '/inputs/' . $filename;
        $this->assertFileExists($realfp);

        $this->expectException(\Exception::class);
        $dataInputter->getResults($realfp);
    }
}
