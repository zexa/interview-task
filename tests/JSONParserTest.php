<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Parsers\JSONParser;

final class JSONParserTest extends TestCase
{
    public function testEmptyStringToSetFileReturnsFalse()
    {
        $jsonParser = new JSONParser();
        $emptyFpSet = $jsonParser->setFile('');
        $this->assertFalse($emptyFpSet);
    }
}
