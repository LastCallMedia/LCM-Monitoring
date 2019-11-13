<?php

namespace Drupal\lcm_monitoring\Log\Handler;

use Monolog\Handler\SocketHandler;

/**
 * Extends the default Socket handler to send logging timing data to New Relic.
 */
class InstrumentedSocketHandler extends SocketHandler {

  protected function write(array $record) {
    $this->executeInstrumented(function() use ($record) {
      parent::write($record);
    });
  }

  private function executeInstrumented(callable $callback) {
    if(function_exists('newrelic_record_datastore_segment')) {
      return newrelic_record_datastore_segment($callback, [
        'product' => 'Logger',
        'collection' => 'drupal.watchdog',
        'operation' => 'put',
      ]);
    }
    return $callback();
  }

}
