<?php

namespace Drupal\lcm_monitoring\Subscriber;

use Drupal\Core\State\StateInterface;
use Drupal\lcm_monitoring\Event\CollectMetricsEvent;
use Drupal\lcm_monitoring\MetricEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Collects Drupal metrics data.
 */
class DrupalMetricsSubscriber implements EventSubscriberInterface {

  private $state;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      MetricEvents::FETCH_METRICS => 'fetchMetrics',
    ];
  }

  /**
   * Constructor.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   Drupal's state store.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * Perform the metric collection.
   *
   * @param CollectMetricsEvent $event
   *   The event containing the collector registry.
   */
  public function fetchMetrics(CollectMetricsEvent $event) {
    $registry = $event->getRegistry();

    $registry
      ->registerGauge('drupal', 'cron_last', 'Last time cron ran')
      ->set($this->state->get('system.cron_last'));
  }

}
