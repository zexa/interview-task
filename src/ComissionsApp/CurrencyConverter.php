<?php

namespace ComissionsApp;

use ComissionsApp\ConfigProvider;

class CurrencyConverter
{
    private $rates;

    public function __construct($rates=null)
    {
        if ($rates != null) {
            $this->rates = $rates;
            return;
        }
        $ConfigProvider = new ConfigProvider();
        $this->rates = $ConfigProvider->getRates();
    }

    public function convert(float $value, string $currencyFrom, string $currencyTo): float
    {
        if (!array_key_exists($currencyFrom, $this->rates)) {
            throw new \Exception($currencyFrom . ' does not exist in rates file');
        }
        if (!array_key_exists($currencyTo, $this->rates[$currencyFrom])) {
            throw new \Exception($currencyTo . ' does not exist in ' . $currencyFrom . ' rates file');
        }

        $multiplier = $this->rates[$currencyFrom][$currencyTo];
        $converted = $value * $multiplier;
        return $converted;
    }
}
