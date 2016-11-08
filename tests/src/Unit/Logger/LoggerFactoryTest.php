<?php

namespace Drupal\Tests\lcm_metrics\Unit\Logger;

use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\lcm_monitoring\Logger\LevelTranslatingLogger;
use Drupal\lcm_monitoring\Logger\LoggerFactory;
use Drupal\lcm_monitoring\Settings\MonitoringSettings;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Psr\Log\NullLogger;

/**
 * Test the logger factory.
 *
 * @group lcm_monitoring
 */
class LoggerFactoryTest extends UnitTestCase {

  public function testDisabledLogger() {
    $settings = new MonitoringSettings(FALSE);
    $parser = $this->prophesize(LogMessageParserInterface::class);
    $factory = new LoggerFactory($settings, $parser->reveal());
    $this->assertInstanceOf(NullLogger::class, $factory->getLogger());
  }

  public function testEnabledLogger() {
    $settings = new MonitoringSettings(TRUE);
    $parser = $this->prophesize(LogMessageParserInterface::class);
    $factory = new LoggerFactory($settings, $parser->reveal());
    $this->assertInstanceOf(LevelTranslatingLogger::class, $factory->getLogger());
  }

  public function testLoggerCannotThrowException() {
    $settings = new MonitoringSettings(TRUE, 'badhostname');
    $parser = $this->prophesize(LogMessageParserInterface::class);
    $parser->parseMessagePlaceholders(Argument::type('string'), Argument::type('array'))->willReturn([]);
    $factory = new LoggerFactory($settings, $parser->reveal());
    $logger = $factory->getLogger();
    $logger->alert('test');
  }

}
