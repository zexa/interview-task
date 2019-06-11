<?php

namespace ComissionsApp;

use ComissionsApp\ConfigProvider;

class ComissionCalculator
{
    private $entryStack;
    private $lastEntry;
    private $currencyConverter;
    private $config;

    public function __construct($config = null, $rates = null)
    {
        $this->entryStack = array();

        $ConfigProvider = new ConfigProvider();
        if ($config == null) {
            $config = $ConfigProvider->getConfig();
        }
        $this->config = $config;
        $this->currencyConverter = new CurrencyConverter($rates);
    }

    public function inputEntry(array $arrEntry): void
    {
        $this->entryStack[] = $arrEntry;
        $this->validateEntry($arrEntry);
        $this->lastEntry = $arrEntry;
    }

    private function validateEntry(array $entry)
    {
        $this->validateDate($entry["date"]);
        $this->validateValue($entry["value"], $entry["currency"]);
    }

    private function validateDate(string $dateInput): void
    {
        $date = \DateTime::createFromFormat('Y-m-d', $dateInput);
        $date_errors = \DateTime::getLastErrors();
        if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {
            throw new \Exception(
                var_export($dateInput, true) .
                    PHP_EOL .
                    " is in an invalid date format."
            );
        }
    }

    private function validateValue(string $value, string $currency): void
    {
        $this->currencyConverter->convert($value, $currency, $currency);
    }

    public function calculateComission()
    {
        $comission = 0;
        switch ($this->lastEntry["userType"]) {
            case "natural":
                $comission = $this->natural();
                break;
            case "legal":
                $comission = $this->legal();
                break;
            default:
                throw new \Exception(
                    var_export($this->lastEntry, true) .
                        PHP_EOL .
                        "Unknown client type"
                );
        }
        $comission = $this->ceiling($comission);

        // JPY MUST NOT have decimal places
        // in the case that it does, we round the value up
        if ($this->lastEntry["currency"] === "JPY") {
            return $this->removeDecimals($comission);
        }

        return $comission;
    }

    private function natural()
    {
        switch ($this->lastEntry["operation"]) {
            case "cash_out":
                return $this->naturalCashOut();
            case "cash_in":
                return $this->naturalCashIn();
            default:
                throw new \Exception(
                    var_export($this->lastEntry, true) .
                        PHP_EOL .
                        "Operation '" .
                        $this->lastEntry["operation"] .
                        "' does not exist for client type '" .
                        $this->lastEntry["userType"] .
                        "'."
                );
        }
        return 0;
    }

    private function naturalCashOut()
    {
        // Starts counting from negatives values so that we could check
        // if value is > 0 to decide if we are still in bounds
        $discountLimit =
            -1 *
            $this->config["comissions"]["natural"]["cash_out"]["discountLimit"];
        // Shows how many free operations are allowed per week
        $discountOperationsLimit =
            $this->config["comissions"]["natural"]["cash_out"][
                "discountOperationsLimit"
            ];
        // Couts how many free operations have been counted so far
        $discountOperations = 0;
        // Iterates entries from the last till the first
        for ($i = count($this->entryStack) - 1; $i >= 0; $i--) {
            // Skips entries that do not match user id or operation type
            if (!$this->sameUserId($i)) {
                continue;
            }
            if (!$this->sameOperation($i)) {
                continue;
            }
            // Stops iterations if we have went out of our time bounds
            if (!$this->sameWeekWrapper($i)) {
                break;
            }
            // Converts the entry because all operations are done with euros
            $converted = $this->currencyConverter->convert(
                $this->entryStack[$i]["value"],
                $this->entryStack[$i]["currency"],
                "EUR"
            );
            $discountLimit += $converted;
            $discountOperations++;
            // Stops execution if we have reached our free operation limit
            // and counts comission rate without a discount
            if ($discountOperations > $discountOperationsLimit) {
                return $this->getComission($this->lastEntry["value"]);
            }
        }
        // If iteration has stoped and we have still not broken
        // discount bounds, apply discount
        if ($discountLimit <= 0) {
            return 0;
        }
        $lastEntryConverted = $this->currencyConverter->convert(
            $this->lastEntry["value"],
            $this->lastEntry["currency"],
            "EUR"
        );
        // Checks if the converted lastEntry ammount goes over the limit
        // In the case that it does, it means that we have already exhausted
        // out weekly discount
        if ($discountLimit > $lastEntryConverted) {
            return $this->getComission($this->lastEntry["value"]);
        }
        $discountLimitConverted = $this->currencyConverter->convert(
            $discountLimit,
            "EUR",
            $this->lastEntry["currency"]
        );
        // Otherwise, if we are over the discount bounds,
        // but not over the value of lastEntry, it means that we have a
        // surplus on the discount limit, which is the ammount that we have
        // to calculate the comission for
        return $this->getComission($discountLimitConverted);
    }

    private function sameUserID(int $entryInStack): bool
    {
        $uid = $this->entryStack[$entryInStack]["userID"];
        if ($uid === $this->lastEntry["userID"]) {
            return true;
        }
        return false;
    }

    private function sameOperation(int $entryInStack): bool
    {
        $operation = $this->entryStack[$entryInStack]["operation"];
        if ($operation === $this->lastEntry["operation"]) {
            return true;
        }
        return false;
    }

    // Creates array of lastEntry's week dates
    // Checks if current entry in stack is within that array.
    private function sameWeekWrapper(int $entryInStack): bool
    {
        return $this->sameWeek(
            $this->lastEntry["date"],
            $this->entryStack[$entryInStack]["date"]
        );
    }

    public function sameWeek(string $date1, string $date2): bool
    {
        $sameWeekDates[] = date(
            "Y-m-d",
            strtotime('monday this week', strtotime($date1))
        );
        $sameWeekDates[] = date(
            "Y-m-d",
            strtotime('tuesday this week', strtotime($date1))
        );
        $sameWeekDates[] = date(
            "Y-m-d",
            strtotime('wednesday this week', strtotime($date1))
        );
        $sameWeekDates[] = date(
            "Y-m-d",
            strtotime('thursday this week', strtotime($date1))
        );
        $sameWeekDates[] = date(
            "Y-m-d",
            strtotime('friday this week', strtotime($date1))
        );
        $sameWeekDates[] = date(
            "Y-m-d",
            strtotime('saturday this week', strtotime($date1))
        );
        $sameWeekDates[] = date(
            "Y-m-d",
            strtotime('sunday this week', strtotime($date1))
        );
        $sw = in_array($date2, $sameWeekDates);
        return $sw;
    }

    // Returns $value * $multiplier, where $multiplier is the
    // multiplier for the current userType/operationType from config
    private function getComission($value)
    {
        $userType = $this->lastEntry["userType"];
        $operation = $this->lastEntry["operation"];
        $multiplier =
            $this->config["comissions"][$userType][$operation]["multiplier"];
        return $value * $multiplier;
    }

    private function naturalCashIn()
    {
        // Gets the default comission
        $comission = $this->getComission($this->lastEntry["value"]);
        // Converts default commission to EUR because all operations are done in EUR
        $converted = $this->currencyConverter->convert(
            $this->lastEntry["value"],
            $this->lastEntry["currency"],
            "EUR"
        );
        $max = $this->config["comissions"]["natural"]["cash_in"]["max"];
        // Returns the maximal ammount if converted default commission goes over max
        if ($max !== null && $comission > $max) {
            // converts max to current entries currency
            $maxConverted = $this->currencyConverter->convert(
                $max,
                "EUR",
                $this->lastEntry["currency"]
            );
            return $maxConverted;
        }
        // returns default comission otherwise
        return $comission;
    }

    private function legal()
    {
        $comission = 0;
        switch ($this->lastEntry["operation"]) {
            case "cash_out":
                $comission = $this->legalCashOut();
                break;
            case "cash_in":
                $comission = $this->legalCashIn();
                break;
            default:
                throw new \Exception(
                    var_export($this->lastEntry, true) .
                        PHP_EOL .
                        "Operation '" .
                        $this->lastEntry["operation"] .
                        "' does not exist for client type '" .
                        $this->lastEntry["userType"] .
                        "'."
                );
        }
        return $comission;
    }

    // Basically same as naturalCashIn, but checks for min instead
    private function legalCashOut()
    {
        $comission = $this->getComission($this->lastEntry["value"]);
        $converted = $this->currencyConverter->convert(
            $this->lastEntry["value"],
            $this->lastEntry["currency"],
            "EUR"
        );
        $min = $this->config["comissions"]["legal"]["cash_out"]["min"];
        if ($min !== null && $converted < $min) {
            return $this->currencyConverter->convert(
                $min,
                "EUR",
                $this->lastEntry["currency"]
            );
        }
        return $comission;
    }

    // Basically same as naturalCashIn
    private function legalCashIn()
    {
        $comission = $this->getComission($this->lastEntry["value"]);
        $converted = $this->currencyConverter->convert(
            $this->lastEntry["value"],
            $this->lastEntry["currency"],
            "EUR"
        );
        $max = $this->config["comissions"]["legal"]["cash_in"]["max"];
        if ($max !== null && $comission > $max) {
            $maxConverted = $this->currencyConverter->convert(
                $max,
                "EUR",
                $this->lastEntry["currency"]
            );
            return $maxConverted;
        }
        return $comission;
    }

    private function ceiling(string $fl)
    {
        // Checks for the ammount of numbers in decimal places
        // if numbers after comma > 2, +0.01.
        if (strlen(substr(strval(strrchr($fl, '.')), 1)) > 2) {
            $fl = (float) $fl + 0.01;
        } else {
            $fl = (float) $fl;
        }
        return number_format($fl, 2);
    }

    private function removeDecimals($fl)
    {
        if (strpos((string) $fl, '.')) {
            return intval(ceil($fl));
        }
    }
}
