<?php

namespace Drupal\lcm_monitoring\Subscriber;

use Drupal\lcm_monitoring\Event\CollectMetricsEvent;
use Drupal\lcm_monitoring\MetricEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Collects APC metrics.
 */
class ApcMetricsSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      MetricEvents::FETCH_METRICS => 'collectMetrics',
    ];
  }

  /**
   * React on a metrics request.
   */
  public function collectMetrics(CollectMetricsEvent $event) {
    if (!function_exists('apc_add')) {
      return;
    }
    $registry = $event->getRegistry();

    $cache = apc_cache_info();
    $mem = apc_sma_info();
    $mem_size = $mem['num_seg'] * $mem['seg_size'];

    $registry
      ->registerGauge('apc', 'mem_used', 'APC used memory')
      ->set($mem_size - $mem['avail_mem']);

    $registry
      ->registerGauge('apc', 'mem_total', 'APC total memory')
      ->set($mem_size);

    $registry
      ->registerGauge('apc', 'num_hits', 'APC hits')
      ->set($cache['num_hits']);

    $registry
      ->registerGauge('apc', 'num_misses', 'APC misses')
      ->set($cache['num_misses']);
  }

}
