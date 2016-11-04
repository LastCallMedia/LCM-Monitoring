<?php

namespace Drupal\Tests\lcm_monitoring\Unit\EventListener;

use Drupal\Core\State\StateInterface;
use Drupal\lcm_monitoring\Event\CollectMetricsEvent;
use Drupal\lcm_monitoring\Subscriber\DrupalMetricsSubscriber;
use Prometheus\CollectorRegistry;
use Prometheus\Gauge;

/**
 * Test the Drupal metrics listener.
 *
 * @group lcm_monitoring
 */
class DrupalMetricsListenerTest extends \PHPUnit_Framework_TestCase {

  public function testCronMetric() {
    $state = $this->prophesize(StateInterface::class);
    $state->get('system.cron_last')
      ->willReturn(1);

    $registry = $this->prophesize(CollectorRegistry::class);
    $gauge = $this->prophesize(Gauge::class);
    $registry
      ->registerGauge('drupal', 'cron_last', 'Last time cron ran')
      ->willReturn($gauge);

    $gauge
      ->set(1)
      ->shouldBeCalled();

    $listener = new DrupalMetricsSubscriber($state->reveal());
    $listener->fetchMetrics(new CollectMetricsEvent($registry->reveal()));
  }

}
