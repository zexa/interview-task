<?php

namespace ComissionsApp;

use Parsers\JSONParser;

class ConfigProvider
{
    public static function getConfig(string $path=null): array
    {
        $jsonParser = new JSONParser();

        if ($path === null) {
            $path = realpath(__DIR__ . '/../../config.json');
        }
        if (!$jsonParser->setFile($path)) {
            throw new \Exception("File " . $path . " does not exist.");
        }
        $config = $jsonParser->parse();

        return $config;
    }

    public static function getRates(string $path=null): array
    {
        $jsonParser = new JSONParser();

        if ($path === null) {
            $path = realpath(__DIR__ . '/../../rates.json');
        }
        if (!$jsonParser->setFile($path)) {
            throw new \Exception("File " . $path . " does not exist.");
        }
        $rates = $jsonParser->parse();

        return $rates;
    }
}
