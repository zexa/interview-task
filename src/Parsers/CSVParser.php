<?php

namespace Parsers;

class CSVParser extends Template
{
    public $format;

    public function parseByLine(callable $callback)
    {
        if (($handle = fopen($this->filePath, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($data === null) {
                    throw new \Exception("CSVParser parsebyLine returns null.");
                }
                $num = count($data);
                $parsedLine = array();
                $c = 0;
                foreach ($data as $key) {
                    if (
                        isset($this->format) &&
                        array_key_exists($c, $this->format)
                    ) {
                        $parsedLine[$this->format[$c]] = $key;
                    } else {
                        $parsedLine[] = $key;
                    }
                    $c++;
                }
                call_user_func($callback, $parsedLine);
            }
            fclose($handle);
        }
    }
}
