<?php

namespace ComissionsApp;

class ComissionCalculator {
	private $entryStack;
	private $lastEntry;
	private $currencyConverter;
	private $config;

	public function __construct($config, $rates) {
		$this->entryStack = array();
		$this->config = $config;
		$this->currencyConverter = new CurrencyConverter($rates);
	}

	public function inputEntry($arrEntry) {
		$this->entryStack[] = $arrEntry;
		$this->lastEntry = $arrEntry;
	} 

	private function sameWeek(int $entryInStack) : bool {
		//$d1 = new \DateTime($this->lastEntry["date"]);
		//$d2 = new \DateTime($this->entryStack[$entryInStack]["date"]);	
		//if ($d1->format("W") === $d2->format("W")) {
		//	return true;
		//}
		//return false;

		$lastEntryDate = $this->lastEntry["date"];
		$sameWeekDates[] = 
			date("Y-m-d", strtotime('monday this week', strtotime($lastEntryDate)));
		$sameWeekDates[] = 
			date("Y-m-d", strtotime('tuesday this week', strtotime($lastEntryDate)));
		$sameWeekDates[] = 
			date("Y-m-d", strtotime('wednesday this week', strtotime($lastEntryDate)));
		$sameWeekDates[] = 
			date("Y-m-d", strtotime('thursday this week', strtotime($lastEntryDate)));
		$sameWeekDates[] = 
			date("Y-m-d", strtotime('friday this week', strtotime($lastEntryDate)));
		$sameWeekDates[] = 
			date("Y-m-d", strtotime('saturday this week', strtotime($lastEntryDate)));
		$sameWeekDates[] = 
			date("Y-m-d", strtotime('sunday this week', strtotime($lastEntryDate)));
		$sw = in_array($this->entryStack[$entryInStack]["date"], $sameWeekDates);
		return $sw;
	}

	private function sameUserID($entryInStack) : bool {
		$uid = $this->entryStack[$entryInStack]["userID"];
		if ($uid === $this->lastEntry["userID"]) {
			return true;
		}
		return false;
	}

	private function getComission($value) {
		$userType = $this->lastEntry["userType"];
		$operation = $this->lastEntry["operation"];
		$multiplier = $this->config["comissions"][$userType][$operation]["multiplier"];
		return $value * $multiplier;
	}

	private function naturalCashOut() {
		$discountLimit = 
			-1 * $this->config["comissions"]["natural"]["cash_out"]["discountLimit"];
		$discountOperationsLimit = 
			$this->config["comissions"]["natural"]["cash_out"]["discountOperationsLimit"];
		$discountOperations = 0;
		for ($i = count($this->entryStack) - 1; $i >= 0; $i--) {
			if (!$this->sameUserId($i)) continue;
			if (!$this->sameWeek($i)) break;
			$converted = $this->currencyConverter->convert(
				$this->entryStack[$i]["value"],
				$this->entryStack[$i]["currency"],
				"EUR"
			);
			$discountLimit += $converted;
			$discountOperations++;
			if ($discountOperations > $discountOperationsLimit) {
				return $this->getComission($this->lastEntry["value"]);
			}
		}
		if ($discountLimit <= 0) {
			return 0;
		}
		$converted = $this->currencyConverter->convert(
			$this->lastEntry["value"],
			$this->lastEntry["currency"],
			"EUR"
		);
		if ($discountLimit > $converted) {
			return $this->getComission($this->lastEntry["value"]);
		}
		$discountLimitConverted = $this->currencyConverter->convert(
			$discountLimit,
			"EUR",
			$this->lastEntry["currency"]
		);
		return $this->getComission($discountLimitConverted);
	}

	private function naturalCashIn() {
		$comission = $this->getComission($this->lastEntry["value"]);
		$converted = $this->currencyConverter->convert(
			$this->lastEntry["value"],
			$this->lastEntry["currency"],
			"EUR"
		);
		$max = $this->config["comissions"]["natural"]["cash_in"]["max"];
		if (($max !== null) && ($comission > $max)) {
			$maxConverted = $this->currencyConverter->convert(
				$max,
				"EUR",
				$this->lastEntry["currency"]
			);
			return $maxConverted;
		}
		return $comission;
	}

	private function natural() {
		switch ($this->lastEntry["operation"]) {
			case "cash_out":
				return $this->naturalCashOut();
			case "cash_in":
				return $this->naturalCashIn();
		}
		return 0;
	}

	private function legalCashOut() {
		$comission = $this->getComission($this->lastEntry["value"]);
		$converted = $this->currencyConverter->convert(
			$this->lastEntry["value"],
			$this->lastEntry["currency"],
			"EUR"
		);
		$min = $this->config["comissions"]["legal"]["cash_out"]["min"];
		if (($min !== null) && ($converted < $min)) {
			return $this->currencyConverter->convert(
				$min,
				"EUR",
				$this->lastEntry["currency"]
			);
		}
		return $comission;
	}

	private function legalCashIn() {
		$comission = $this->getComission($this->lastEntry["value"]);
		$converted = $this->currencyConverter->convert(
			$this->lastEntry["value"],
			$this->lastEntry["currency"],
			"EUR"
		);
		$max = $this->config["comissions"]["legal"]["cash_in"]["max"];
		if (($max !== null) && ($comission > $max)) {
			$maxConverted = $this->currencyConverter->convert(
				$max,
				"EUR",
				$this->lastEntry["currency"]
			);
			return $maxConverted;
		}
		return $comission;
	}

	private function legal() {
		$comission = 0;
		switch ($this->lastEntry["operation"]) {
			case "cash_out":
				$comission = $this->legalCashOut();
				break;
			case "cash_in":
				$comission = $this->legalCashIn();
				break;
		}
		return $comission;
	}

	public function calculateComission() {
		$comission = 0;
		switch ($this->lastEntry["userType"]) {
			case "natural":
				$comission = $this->natural();
				break;
			case "legal":
				$comission = $this->legal();
				break;
		}
		$comission = number_format($comission, 2);
		return $comission;
	}
}


?>
