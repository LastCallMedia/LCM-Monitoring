<?php

namespace Drupal\lcm_monitoring\Settings;

use Drupal\Core\Site\Settings;

class MonitoringSettings {

  private $loggerEnabled = FALSE;
  private $host = NULL;
  private $port = NULL;
  private $project = NULL;
  private $environment = NULL;

  public static function factory(Settings $settings) {
    $monitoringSettings = $settings->get('lcm_monitoring', []);

    return new static(
      isset($monitoringSettings['logger']) ? $monitoringSettings['logger'] : FALSE,
      isset($monitoringSettings['host']) ? $monitoringSettings['host'] : NULL,
      isset($monitoringSettings['port']) ? $monitoringSettings['port'] : NULL,
      isset($monitoringSettings['project']) ? $monitoringSettings['project'] : NULL,
      isset($monitoringSettings['environment']) ? $monitoringSettings['environment'] : NULL
    );
  }

  public function __construct($loggerEnabled = FALSE, $host = NULL, $port = NULL, $project = NULL, $environment = NULL) {
    $this->loggerEnabled = $loggerEnabled;
    $this->host = $host;
    $this->port = $port;
    $this->project = $project;
    $this->environment = $environment;
  }

  public function getLoggerEnabled() {
    return (bool) $this->loggerEnabled;
  }

  public function getHost() {
    return $this->host;
  }

  public function getPort() {
    return $this->port;
  }

  public function getProject() {
    return $this->project;
  }

  public function getEnvironment() {
    return $this->environment;
  }

}
