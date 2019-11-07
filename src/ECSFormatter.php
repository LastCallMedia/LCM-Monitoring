<?php


namespace Drupal\lcm_monitoring;

use Monolog\Formatter\NormalizerFormatter;

/**
 * This formatter was originally based on elastic/ecs-logging, but has evolved
 * to send additional useful data specific to the Drupal context.
 */
class ECSFormatter extends NormalizerFormatter {

  public function __construct() {
    parent::__construct('Y-m-d\TH:i:s.uZ');
  }

  public function format(array $record) {
    $record = $this->normalize($record);
    $extra = $record['extra'] ?? [];
    $message = [
      '@timestamp' => $record['datetime'],
      'log' => [
        'level'  => $record['level_name'],
        'logger' => $record['channel'],
      ],
      'host' => [
        'name' => gethostname(),
      ],
      'event' => [
        'dataset' => 'drupal.watchdog',
      ],
    ];

    if (isset($extra['request_uri'])) {
      $parsed = parse_url($extra['request_uri']);
      $message['url'] = [
        'full' => $extra['request_uri'],
        'hostname' => $parsed['host'],
        'path' => $parsed['path'],
      ];
    }
    if (isset($extra['ip'])) {
      $message['client']['address'] = $extra['ip'];
      $message['client']['ip'] = $extra['ip'];
    }
    if (isset($extra['uid'])) {
      $message['user']['id'] = $extra['uid'];
    }
    if ($cloudId = $this->determineCloudId()) {
      $message['cloud']['id'] = $cloudId;
    }
    if ($cloudProvider = $this->determineCloudProvider()) {
      $message['cloud']['provider'] = $cloudProvider;
    }
    if ($cloudEnvironment = $this->determineCloudEnvironment()) {
      $message['cloud']['instance']['name'] = $cloudEnvironment;
    }

    // Add Log Message
    if (isset($record['message']) === true) {
      $message['message'] = $record['message'];
    }

    return $this->toJson($message) . "\n";
  }

  private function determineCloudId() {
    if (isset($_SERVER['PANTHEON_SITE_NAME'])) {
      return $_SERVER['PANTHEON_SITE_NAME'];
    }
    if (isset($_SERVER['AH_SITE_GROUP'])) {
      return $_SERVER['AH_SITE_GROUP'];
    }
  }

  private function determineCloudProvider() {
    if (isset($_SERVER['PANTHEON_SITE_NAME'])) {
      return 'pantheon';
    }
    if (isset($_SERVER['AH_SITE_GROUP'])) {
      return 'acquia';
    }
  }

  private function determineCloudEnvironment() {
    if (isset($_SERVER['PANTHEON_ENVIRONMENT'])) {
      return $_SERVER['PANTHEON_ENVIRONMENT'];
    }
    if (isset($_SERVER['AH_SITE_ENVIRONMENT'])) {
      return $_SERVER['AH_SITE_ENVIRONMENT'];
    }
  }
}
