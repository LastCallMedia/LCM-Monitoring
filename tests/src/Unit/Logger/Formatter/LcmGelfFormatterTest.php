<?php


namespace Drupal\Tests\lcm_monitoring\Unit\Logger\Formatter;


use Drupal\Core\Logger\LogMessageParser;
use Drupal\lcm_monitoring\Logger\Formatter\LcmGelfFormatter;
use Drupal\Tests\lcm_monitoring\Unit\Logger\LoggerTestTrait;

class LcmGelfFormatterTest extends \PHPUnit_Framework_TestCase {
  use LoggerTestTrait;

  public function testSetsProject() {
    $record = $this->createRecord();
    $formatter = new LcmGelfFormatter('foo', 'bar', new LogMessageParser());
    $message = $formatter->format($record);
    $this->assertEquals($message->getAdditional('project'), 'foo');
  }

  public function testStripsPlaceholders() {
    $record = $this->createRecord(['context' => ['%foo' => 'barbaz']]);
    $formatter = new LcmGelfFormatter('foo', 'bar', new LogMessageParser());
    $message = $formatter->format($record);
    $this->assertFalse(in_array('barbaz', $message->getAllAdditionals()));
  }

  public function testRemovesExtraContext() {
    $record = $this->createRecord([
      'context' => [
        'severity_level' => 250,
        'timestamp' => 5,
        'user' => 'john',
      ]
    ]);
    $formatter = new LcmGelfFormatter('foo', 'bar', new LogMessageParser());
    $message = $formatter->format($record);
    $this->assertEquals($message->getAllAdditionals(), ['project' => 'foo']);
  }
}
