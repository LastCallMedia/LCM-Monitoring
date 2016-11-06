<?php

namespace Drupal\Tests\lcm_monitoring\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\lcm_monitoring\Logger\LevelTranslatingLogger;
use Drupal\lcm_monitoring\MetricEvents;
use Prometheus\CollectorRegistry;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Test that the container has expected services.
 *
 * @group lcm_monitoring
 */
class ContainerTest extends KernelTestBase {

  public static $modules = [
    'lcm_monitoring',
  ];

  public function testHasLogger() {
    $this->config('lcm_monitoring.settings')
      ->set('logger', [
        'enabled' => TRUE,
        'host' => 'example.com',
        'port' => 5555,
      ])
      ->save();
    $this->assertInstanceOf(
      LevelTranslatingLogger::class,
      $this->container->get('lcm_monitoring.logger')
    );
  }
}
