<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use ComissionsApp\ComissionCalculator;

final class ComissionCalculatorTest extends TestCase
{
    public function testSameWeekReturnsTrueWhenSameWeekAfterNewYears()
    {
        $comissionsCalculator = new ComissionCalculator();
        $sw = $comissionsCalculator->sameWeek('2014-12-31', '2015-01-01');
        $this->assertTrue($sw);
    }

    public function testSameWeekReturnsFalseOnDifferentWeeks()
    {
        $comissionsCalculator = new ComissionCalculator();
        $sw = $comissionsCalculator->sameWeek('2014-12-31', '2015-02-01');
        $this->assertFalse($sw);
    }

    public function testSameWeekReturnsTrueOnSameWeek()
    {
        $comissionsCalculator = new ComissionCalculator();
        $sw = $comissionsCalculator->sameWeek('2019-06-11', '2019-06-10');
        $this->assertTrue($sw);
    }
}

?>

