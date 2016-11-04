<?php

namespace Drupal\lcm_monitoring\Event;

use Prometheus\CollectorRegistry;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event fired when metrics are being collected.
 */
class CollectMetricsEvent extends Event {

  private $registry;

  /**
   * Constructor.
   *
   * @param \Prometheus\CollectorRegistry $registry
   *   The registry to collect metrics to.
   */
  public function __construct(CollectorRegistry $registry) {
    $this->registry = $registry;
  }

  /**
   * Returns the CollectorRegistry.
   *
   * @return \Prometheus\CollectorRegistry
   *   The registry to collect metrics to.
   */
  public function getRegistry() {
    return $this->registry;
  }

}
