<?php

namespace Drupal\Tests\lcm_monitoring\Unit\Controller;

use Drupal\lcm_monitoring\Controller\MetricsController;
use Drupal\lcm_monitoring\Event\CollectMetricsEvent;
use Drupal\Tests\UnitTestCase;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group lcm_monitoring
 */
class MetricsControllerTest extends UnitTestCase {

  public function testUsesRegistry() {
    $request = new Request();
    $request->headers->set('Bearer', 'foo');
    $configFactory = $this->getConfigFactoryStub([
      'lcm_monitoring.settings' => [
        'accesskey' => 'foo',
      ],
    ]);

    $registry = $this->prophesize(CollectorRegistry::class);
    $registry
      ->getMetricFamilySamples()
      ->shouldBeCalled()
      ->willReturn([]);
    $dispatcher = $this->prophesize(EventDispatcherInterface::class);
    $dispatcher
      ->dispatch('lcm_monitoring.fetch_metrics', Argument::type(CollectMetricsEvent::class))
      ->shouldBeCalled();

    $controller = new MetricsController($registry->reveal(), $dispatcher->reveal(), $configFactory);
    $response = $controller->getMetricsAction($request);
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals(RenderTextFormat::MIME_TYPE, $response->headers->get('Content-Type'));
  }

  public function testDeniesAccess() {
    $request = new Request();
    $configFactory = $this->getConfigFactoryStub([
      'lcm_monitoring.settings' => [
        'accesskey' => 'foo',
      ],
    ]);

    $registry = $this->prophesize(CollectorRegistry::class);
    $dispatcher = $this->prophesize(EventDispatcherInterface::class);

    $controller = new MetricsController($registry->reveal(), $dispatcher->reveal(), $configFactory);
    $response = $controller->getMetricsAction($request);
    $this->assertEquals(403, $response->getStatusCode());
  }

}
