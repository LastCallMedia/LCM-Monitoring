<?php

namespace Drupal\Tests\lcm_monitoring\Unit\Metrics {

  use Drupal\lcm_monitoring\Metrics\MetricsStorageFactory;
  use Prometheus\Storage\APC;
  use Prometheus\Storage\InMemory;

  /**
   * Test the metrics storage factory.
   *
   * @group lcm_monitoring
   */
  class MetricsStorageFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testWithoutApc() {
      $factory = new MetricsStorageFactory();
      \Drupal\lcm_monitoring\Metrics\function_exists('apc_add', FALSE);
      $this->assertInstanceOf(InMemory::class, $factory->getStorage());
    }

    public function testWithApc() {
      $factory = new MetricsStorageFactory();
      \Drupal\lcm_monitoring\Metrics\function_exists('apc_add', TRUE);
      $this->assertInstanceOf(APC::class, $factory->getStorage());
    }

  }

}

/**
 * Mock of function_exists() for testing APC detection.
 */
namespace Drupal\lcm_monitoring\Metrics {

  /**
   * Mock of function_exists().
   */
  function function_exists($name, $exists = NULL) {
    static $_exists;
    if (isset($exists)) {
      $_exists[$name] = $exists;
    }
    return $_exists[$name];
  }

}
