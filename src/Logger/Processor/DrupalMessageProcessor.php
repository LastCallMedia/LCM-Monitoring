<?php

namespace Drupal\lcm_monitoring\Logger\Processor;

use Drupal\Core\Logger\LogMessageParserInterface;

/**
 * Processes Drupal log messages to make them more monolog-y.
 */
class DrupalMessageProcessor {

  protected static $extraProperties = [
    'request_uri',
    'referer',
    'ip',
    'uid',
  ];

  /**
   * Constructor.
   */
  public function __construct(LogMessageParserInterface $parser) {
    $this->parser = $parser;
  }

  /**
   * Processes a record.
   */
  public function __invoke(array $record) {
    $placeholders = $this->parser->parseMessagePlaceholders($record['message'], $record['context']);
    // Preserve the file and line placeholders if they're present.
    $record['message'] = empty($placeholders) ? $record['message'] : strtr($record['message'], $placeholders);

    // Channel is handled differently between monolog and Drupal. Fix it.
    if (isset($record['context']['channel'])) {
      $record['channel'] = $record['context']['channel'];
      unset($record['context']['channel']);
    }

    // Line and file are often set as placeholders in Drupal logs.  Copy them to
    // extra.
    foreach(['line', 'file'] as $key) {
      if(isset($placeholders["%${key}"]) && !isset($record['extra'][$key])) {
        $record['extra'][$key] = $placeholders["%${key}"];
      }
    }

    // Move context properties to extra.
    foreach (static::$extraProperties as $property) {
      if (isset($record['context'][$property])) {
        $record['extra'][$property] = $record['context'][$property];
        unset($record['context'][$property]);
      }
    }
    return $record;
  }

}
