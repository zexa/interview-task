<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use ComissionsApp\CurrencyConverter;

final class CurrencyConverterTest extends TestCase
{
    public function testUnknownCurrencyThrowsException()
    {
        $CurrencyConverter = new CurrencyConverter();
        $this->expectException(\Exception::class);
        $CurrencyConverter->convert(0.0, "EUR", "ABC");
    }
}
