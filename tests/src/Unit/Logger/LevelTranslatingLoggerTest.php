<?php

namespace Drupal\Tests\lcm_metrics\Logger;

use Drupal\lcm_monitoring\Logger\LevelTranslatingLogger;
use Monolog\Handler\HandlerInterface;
use Prophecy\Argument;

/**
 * Test the level translating logger.
 *
 * @group lcm_monitoring
 */
class LevelTranslatingLoggerTest extends \PHPUnit_Framework_TestCase {

  public function getLevelTests() {
    return [
      [0, 600],
      [1, 550],
      [2, 500],
      [3, 400],
      [4, 300],
      [5, 250],
      [6, 200],
      [7, 100],
    ];
  }

  /**
   * @dataProvider getLevelTests
   */
  public function testTranslatesLevel($rfcLevel, $expectedMonologLevel) {
    $handler = $this->prophesize(HandlerInterface::class);
    $handler->isHandling(['level' => $expectedMonologLevel])
      ->shouldBeCalled()
      ->willReturn(TRUE);

    $handler
      ->handle(Argument::type('array'))
      ->shouldBeCalled();

    $logger = new LevelTranslatingLogger('test', [$handler->reveal()]);
    $logger->addRecord($rfcLevel, '', []);
  }

}
