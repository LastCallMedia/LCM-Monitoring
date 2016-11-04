<?php

namespace Drupal\lcm_monitoring\Logger;

use Drupal\lcm_monitoring\Logger\Formatter\LcmGelfFormatter;
use Drupal\lcm_monitoring\Logger\Processor\DrupalMessageProcessor;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LogMessageParserInterface;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use Monolog\Handler\GelfHandler;
use Psr\Log\NullLogger;

/**
 * Instantiates a logger based on configuration values.
 */
class LoggerFactory {

  private $configFactory;
  private $parser;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactoryInterface $configFactory, LogMessageParserInterface $parser) {
    $this->configFactory = $configFactory;
    $this->parser = $parser;
  }

  /**
   * Returns the logger instance.
   *
   * @return \Psr\Log\LoggerInterface
   *   The logger instance.
   */
  public function getLogger() {
    $config = $this->configFactory->get('lcm_monitoring.settings');
    $loggerConfig = $config->get('logger');
    $projectId = $config->get('projectid');
    if (TRUE !== $loggerConfig['enabled']) {
      // We aren't active, but we've promised to return a logger.
      return new NullLogger();
    }

    $transport = new UdpTransport($loggerConfig['host'], $loggerConfig['port']);
    $publisher = new Publisher($transport);
    $handler = new GelfHandler($publisher);
    $handler->setFormatter(new LcmGelfFormatter($projectId, gethostname(), $this->parser));
    $processor = new DrupalMessageProcessor($this->parser);
    return new LevelTranslatingLogger('default', [$handler], [$processor]);
  }

}
