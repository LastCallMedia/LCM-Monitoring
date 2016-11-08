<?php

namespace Drupal\lcm_monitoring\Logger;

use Drupal\lcm_monitoring\Logger\Formatter\LcmGelfFormatter;
use Drupal\lcm_monitoring\Logger\Processor\DrupalMessageProcessor;
use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\lcm_monitoring\Logger\Processor\ProjectEnvironmentProcessor;
use Drupal\lcm_monitoring\Settings\MonitoringSettings;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use Monolog\Handler\GelfHandler;
use Monolog\Handler\WhatFailureGroupHandler;
use Psr\Log\NullLogger;

/**
 * Instantiates a logger based on configuration values.
 */
class LoggerFactory {

  /**
   * @var \Drupal\Core\Site\Settings
   */
  private $settings;
  private $parser;

  /**
   * Constructor.
   */
  public function __construct(MonitoringSettings $settings, LogMessageParserInterface $parser) {
    $this->settings = $settings;
    $this->parser = $parser;
  }

  /**
   * Returns the logger instance.
   *
   * @return \Psr\Log\LoggerInterface
   *   The logger instance.
   */
  public function getLogger() {
    if (TRUE !== $this->settings->getLoggerEnabled()) {
      // We aren't active, but we've promised to return a logger.
      return new NullLogger();
    }

    $transport = new UdpTransport(
      $this->settings->getHost(),
      $this->settings->getPort()
    );
    $publisher = new Publisher($transport);
    $handler = new GelfHandler($publisher);
    $handler->setFormatter(new LcmGelfFormatter(gethostname(), $this->parser));

    $processors[] = new DrupalMessageProcessor($this->parser);
    $processors[] = new ProjectEnvironmentProcessor(
      $this->settings->getProject(),
      $this->settings->getEnvironment()
    );
    // Don't ever allow the logger to crash the site.  Wrap our handler with one
    // that swallows exceptions.
    $wrappingHandler = new WhatFailureGroupHandler([$handler]);
    return new LevelTranslatingLogger('default', [$wrappingHandler], $processors);
  }

}
