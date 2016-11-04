<?php

namespace Drupal\lcm_monitoring\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\lcm_monitoring\Event\CollectMetricsEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the metrics page.
 */
class MetricsController extends ControllerBase {

  private $registry;

  private $dispatcher;

  private $access;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('lcm_monitoring.metrics_registry'),
      $container->get('event_dispatcher'),
      $container->get('config.factory')
    );
  }

  /**
   * MetricsController constructor.
   */
  public function __construct(CollectorRegistry $registry, EventDispatcherInterface $dispatcher, ConfigFactoryInterface $configFactory) {
    $this->registry = $registry;
    $this->dispatcher = $dispatcher;
    $this->configFactory = $configFactory;
  }

  /**
   * Check access for the getMetrics endpoint.
   */
  public function accessMetricsAction(Request $request) {
    $config = $this->configFactory->get('lcm_monitoring.settings');

    $requestBearer = $request->headers->get('Bearer');
    $expectedBearer = $config->get('accesskey');
    return AccessResult::allowedIf($expectedBearer && $expectedBearer === $requestBearer);
  }

  /**
   * Show the metrics page.
   */
  public function getMetricsAction(Request $request) {
    // Step around normal controller access control because it isn't compatible
    // with caching yet (the headers context doesn't work).
    if ($this->accessMetricsAction($request)->isAllowed()) {
      $event = new CollectMetricsEvent($this->registry);
      $this->dispatcher->dispatch('lcm_monitoring.fetch_metrics', $event);
      $renderer = new RenderTextFormat();
      $out = $renderer->render($this->registry->getMetricFamilySamples());

      return new Response($out, 200, [
        'Content-Type' => $renderer::MIME_TYPE,
        'Cache-Control' => 'no-cache',
      ]);
    }

    return new Response('Access denied', 403, [
      'Cache-Control' => 'no-cache',
    ]);
  }

}
