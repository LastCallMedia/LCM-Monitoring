<?php


namespace Drupal\Tests\lcm_monitoring\Unit\Logger;


trait LoggerTestTrait {

  protected function createRecord(array $override = []) {
    return $override + [
      'datetime' => new \DateTime(),
      'message' => '',
      'channel' => 'anychannel',
      'level' => 250,
      'extra' => [],
      'context' => [],
    ];
  }

}
