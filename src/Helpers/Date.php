<?php

namespace Helpers;

class Date {

	/**
	 * Returns true if both dates are in the same week,
	 * false otherwise, null on error.
	 */
	public function sameWeek($d1, $d2) {
		$dayDifference = (array)date_diff($d1, $d2);
		$dayDifference = $dayDifference["d"];
		if ($dayDifference > 7) {
			return false;
		} elseif ($dayDifference === 0) {
			return true;
		}
		if (date_timestamp_get($d1) > date_timestamp_get($d2)) {
			$dateNewer = $d1;
			$dateOlder = $d2;
		} else {
			$dateNewer = $d2;
			$dateOlder = $d1;
		}

			
	}

}

?>
