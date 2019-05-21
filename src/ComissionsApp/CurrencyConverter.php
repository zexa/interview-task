<?php

namespace ComissionsApp;

class CurrencyConverter
{

  private $rates;

  public function __construct($rates)
  {
    $this->rates = $rates;
  }

  public function convert(float $value, $currencyFrom, $currencyTo)
  {
    if ($currencyFrom === $currencyTo) {
      return $value;
    }

    $multiplier = $this->rates[$currencyFrom][$currencyTo];
    $converted = $value * $multiplier;
    return $converted;
  }

}

?>
