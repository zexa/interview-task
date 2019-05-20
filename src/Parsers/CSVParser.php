<?php

namespace Parsers;

class CSVParser extends Template {

	public $format;

	public function parseByLine(callable $callback) {
		if (($handle = fopen($this->filePath, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				$num = count($data);
				$parsedLine = array();
				for ($c=0; $c < $num; $c++) {
					// gives array key name as per format if format is set.
					if (isset($this->format) && (array_key_exists($c, $this->format))) {
						$parsedLine[$this->format[$c]] = $data[$c];
					} else {
						$parsedLine[] = $data[$c];
					}
				}
				call_user_func($callback, $parsedLine);
			}
			fclose($handle);
		}
	}

	public function parse() {
		return array_map('str_getcsv', file($this->filePath));
	}  

} 

?>
