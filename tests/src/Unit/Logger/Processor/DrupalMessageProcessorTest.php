<?php


namespace Drupal\Tests\lcm_monitoring\Unit\Logger\Processor;


use Drupal\Core\Logger\LogMessageParser;
use Drupal\lcm_monitoring\Logger\Processor\DrupalMessageProcessor;
use Drupal\Tests\lcm_monitoring\Unit\Logger\LoggerTestTrait;

class DrupalMessageProcessorTest extends \PHPUnit_Framework_TestCase {
  use LoggerTestTrait;

  public function testReplacesPlaceholders() {
    $processor = new DrupalMessageProcessor(new LogMessageParser());
    $record = $this->createRecord([
      'message' => 'Foo %bar',
      'context' => ['%bar' => 'baz', 'Foo' => 'fiz'],
    ]);
    $record = $processor->__invoke($record);
    $this->assertEquals('Foo baz', $record['message']);
  }

  public function testMovesChannel() {
    $processor = new DrupalMessageProcessor(new LogMessageParser());
    $record = $this->createRecord(['context' => ['channel' => 'foo']]);
    $record = $processor($record);
    $this->assertEquals('foo', $record['channel']);
  }

  public function testCopiesFile() {
    $processor = new DrupalMessageProcessor(new LogMessageParser());
    $record = $this->createRecord(['context' => ['%file' => 'foo']]);
    $record = $processor($record);
    $this->assertEquals(['file' => 'foo'], $record['extra']);
  }

  public function testCopiesLine() {
    $processor = new DrupalMessageProcessor(new LogMessageParser());
    $record = $this->createRecord(['context' => ['%line' => 5]]);
    $record = $processor($record);
    $this->assertEquals(['line' => 5], $record['extra']);
  }

  public function testMovesExtraProperties() {
    $processor = new DrupalMessageProcessor(new LogMessageParser());
    $context = [
      'request_uri' => 'foo',
      'referer' => 'bar',
      'ip' => 'fiz',
      'uid' => 1,
    ];
    $record = $this->createRecord(['context' => $context]);
    $record = $processor($record);
    $this->assertEmpty($record['context']);
    $this->assertEquals($context, $record['extra']);
  }


}
