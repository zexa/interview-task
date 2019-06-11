<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use ComissionsApp\ConfigProvider;

final class ConfigProviderTest extends TestCase
{
    public function testDefaultConfigurationExists()
    {
        $config_path = strval(realpath(__DIR__ . '/../config.json'));
        $config_exists = file_exists($config_path);
        $this->assertTrue($config_exists);
    }

    public function testReturnsArrayOnDefaultConfiguration()
    {
        $config_provider = new ConfigProvider();
        $config = $config_provider->getConfig();
        $this->assertIsArray($config);
    }

    public function testGetConfigThrowsExceptionOnNonExistantConfigFile()
    {
        $config_provider = new ConfigProvider();
        $this->expectException(\Exception::class);
        $config_provider->getConfig('');
    }

    public function testGetRatesThrowsExceptionOnNonExistantConfigFile()
    {
        $config_provider = new ConfigProvider();
        $this->expectException(\Exception::class);
        $config_provider->getRates('');
    }
}
