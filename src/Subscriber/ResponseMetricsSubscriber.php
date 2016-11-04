<?php

namespace Drupal\lcm_monitoring\Subscriber;

use Prometheus\CollectorRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Captures metrics on response/exception events.
 */
class ResponseMetricsSubscriber implements EventSubscriberInterface {

  private $registry;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::EXCEPTION => 'onException',
      KernelEvents::RESPONSE => 'onResponse',
    ];
  }

  /**
   * Constructor.
   */
  public function __construct(CollectorRegistry $registry) {
    $this->registry = $registry;
  }

  /**
   * Capture response counts.
   */
  public function onResponse(FilterResponseEvent $event) {
    $counter = $this->registry->registerCounter('drupal', 'response', 'Response codes', ['code']);
    $counter->inc([$event->getResponse()->getStatusCode()]);
  }

  /**
   * Capture exception count.
   */
  public function onException() {
    $counter = $this->registry->registerCounter('drupal', 'exception', 'Exception count');
    $counter->inc();
  }

}
