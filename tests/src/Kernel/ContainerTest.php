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

  public function testHasCollectorRegistry() {
    $this->assertInstanceOf(
      CollectorRegistry::class,
      $this->container->get('lcm_monitoring.metrics_registry')
    );
  }

  public function testDispatcherHasCollectMetricsListeners() {
    $dispatcher = $this->container->get('event_dispatcher');

    $metricsListeners = $dispatcher->getListeners(MetricEvents::FETCH_METRICS);
    $this->assertListenersContainService('lcm_monitoring.apc_metrics_subscriber', $metricsListeners);
    $this->assertListenersContainService('lcm_monitoring.drupal_metrics_subscriber', $metricsListeners);

    $responseListeners = $dispatcher->getListeners(KernelEvents::RESPONSE);
    $this->assertListenersContainService('lcm_monitoring.response_metrics_subscriber', $responseListeners);

    $exceptionListeners = $dispatcher->getListeners(KernelEvents::EXCEPTION);
    $this->assertListenersContainService('lcm_monitoring.response_metrics_subscriber', $exceptionListeners);
  }

  protected function assertListenersContainService($serviceId, array $listeners) {
    $serviceIds = array_map(function ($listener) {
      if (is_array($listener) && isset($listener[0]->_serviceId)) {
        return $listener[0]->_serviceId;
      }
    }, $listeners);
    $this->assertTrue(in_array($serviceId, $serviceIds));
  }

}
