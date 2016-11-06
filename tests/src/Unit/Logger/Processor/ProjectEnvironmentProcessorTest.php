<?php

namespace Drupal\Tests\lcm_monitoring\Unit\Logger\Processor;

use Drupal\lcm_monitoring\Logger\Processor\ProjectEnvironmentProcessor;

class ProjectEnvironmentProcessorTest extends \PHPUnit_Framework_TestCase {

  public function testProjectIsAdded() {
    $processor = new ProjectEnvironmentProcessor('foo', 'bar');
    $record = $processor(['extra' => []]);
    $this->assertEquals('foo', $record['extra']['project']);
  }

  public function testEnvironmentIsAdded() {
    $processor = new ProjectEnvironmentProcessor('foo', 'bar');
    $record = $processor(['extra' => []]);
    $this->assertEquals('bar', $record['extra']['environment']);
  }

}
