<?php

namespace Drupal\Tests\lcm_monitoring\Unit\EventListener;

use Drupal\lcm_monitoring\Subscriber\ResponseMetricsSubscriber;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Test the response metrics event listener.
 *
 * @group lcm_monitoring
 */
class ResponseMetricsEventListenerTest extends \PHPUnit_Framework_TestCase {

  public function testExceptionTracking() {
    $registry = $this->prophesize(CollectorRegistry::class);
    $counter = $this->prophesize(Counter::class);
    $registry
      ->registerCounter('drupal', 'exception', 'Exception count')
      ->willReturn($counter)
      ->shouldBeCalled();

    $counter
      ->inc()
      ->shouldBeCalled();

    $listener = new ResponseMetricsSubscriber($registry->reveal());
    $listener->onException();
  }

  public function testResponseTracking() {
    $registry = $this->prophesize(CollectorRegistry::class);
    $counter = $this->prophesize(Counter::class);
    $registry
      ->registerCounter('drupal', 'response', 'Response codes', ['code'])
      ->willReturn($counter)
      ->shouldBeCalled();

    $counter
      ->inc([200])
      ->shouldBeCalled();

    $event = $this->prophesize(FilterResponseEvent::class);
    $event->getResponse()
      ->willReturn(new Response('', 200));
    $listener = new ResponseMetricsSubscriber($registry->reveal());
    $listener->onResponse($event->reveal());
  }

}
