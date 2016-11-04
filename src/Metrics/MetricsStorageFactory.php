<?php

namespace Drupal\lcm_monitoring\Metrics;

use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;

/**
 * Factory service to create the best possible metric storage adapter.
 */
class MetricsStorageFactory {

  /**
   * Create a new storage adapter instance.
   *
   * @return \Prometheus\Storage\Adapter
   *   The storage adapter.
   */
  public function getStorage() {
    if (function_exists('apc_add')) {
      return new APC();
    }
    return new InMemory();
  }

}
