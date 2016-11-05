<?php

namespace Drupal\lcm_monitoring\Logger\Formatter;

use Drupal\Core\Logger\LogMessageParserInterface;
use Monolog\Formatter\GelfMessageFormatter;

/**
 * Formats and strips down a log message.
 */
class LcmGelfFormatter extends GelfMessageFormatter {

  /**
   * Properties to remove.
   *
   * @var array
   *
   * severity_level duplicates $record['level'].
   * timestamp duplicates $record['datetime'].
   * user duplicates $record['extra']['uid'].
   */
  private static $contextIgnore = [
    'severity_level',
    'timestamp',
    'user',
  ];

  private $parser;

  /**
   * Constructor.
   */
  public function __construct($projectName, $systemName, LogMessageParserInterface $parser) {
    $this->projectName = $projectName;
    $this->parser = $parser;
    parent::__construct($systemName);
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $record) {
    $record['extra']['project'] = $this->projectName;

    $placeholders = $this->parser->parseMessagePlaceholders($record['message'], $record['context']);
    // Now strip out the placeholders so they don't take up extra space.
    $record['context'] = array_diff_key($record['context'], $placeholders);
    foreach (static::$contextIgnore as $property) {
      unset($record['context'][$property]);
    }

    return parent::format($record);
  }

}
