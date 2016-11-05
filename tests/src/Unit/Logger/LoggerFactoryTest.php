<?php

namespace Drupal\Tests\lcm_metrics\Unit\Logger;

use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\lcm_monitoring\Logger\LevelTranslatingLogger;
use Drupal\lcm_monitoring\Logger\LoggerFactory;
use Drupal\Tests\UnitTestCase;
use Psr\Log\NullLogger;

/**
 * Test the logger factory.
 *
 * @group lcm_monitoring
 */
class LoggerFactoryTest extends UnitTestCase {

  public function testDisabledLogger() {
    $configFactory = $this->getConfigFactoryStub([
      'lcm_monitoring.settings' => [
        'logger' => ['enabled' => FALSE],
      ],
    ]);
    $parser = $this->prophesize(LogMessageParserInterface::class);
    $factory = new LoggerFactory($configFactory, $parser->reveal());
    $this->assertInstanceOf(NullLogger::class, $factory->getLogger());
  }

  public function testEnabledLogger() {
    $configFactory = $this->getConfigFactoryStub([
      'lcm_monitoring.settings' => [
        'logger' => [
          'enabled' => TRUE,
          'host' => 'foo.com',
          'port' => 5555,
        ],
      ],
    ]);
    $parser = $this->prophesize(LogMessageParserInterface::class);
    $factory = new LoggerFactory($configFactory, $parser->reveal());
    $this->assertInstanceOf(LevelTranslatingLogger::class, $factory->getLogger());
  }

}
