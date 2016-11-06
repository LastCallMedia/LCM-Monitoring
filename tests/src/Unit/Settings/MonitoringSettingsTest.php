<?php


namespace Drupal\Tests\lcm_monitoring\Unit\Settings;


use Drupal\Core\Site\Settings;
use Drupal\lcm_monitoring\Settings\MonitoringSettings;

class MonitoringSettingsTest extends \PHPUnit_Framework_TestCase {

  public function testBareDefaults() {
    $settings = MonitoringSettings::factory(new Settings([]));
    $this->assertInstanceOf(MonitoringSettings::class, $settings);
    $this->assertFalse($settings->getLoggerEnabled());
    $this->assertNull($settings->getHost());
    $this->assertNull($settings->getPort());
    $this->assertNull($settings->getProject());
    $this->assertNull($settings->getEnvironment());
  }

  public function testOverriddenSettings() {
    $drupalSettings = [
      'lcm_monitoring' => [
        'logger' => TRUE,
        'host' => 'testhost',
        'port' => 5,
        'project' => 'testproject',
        'environment' => 'testenvironment',
      ]
    ];
    $settings = MonitoringSettings::factory(new Settings($drupalSettings));
    $this->assertInstanceOf(MonitoringSettings::class, $settings);
    $this->assertTrue($settings->getLoggerEnabled());
    $this->assertEquals('testhost', $settings->getHost());
    $this->assertEquals(5, $settings->getPort());
    $this->assertEquals('testproject', $settings->getProject());
    $this->assertEquals('testenvironment', $settings->getEnvironment());
  }
}
