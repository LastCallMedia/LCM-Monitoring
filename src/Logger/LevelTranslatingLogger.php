<?php

namespace Drupal\lcm_monitoring\Logger;

use Monolog\Logger;
use Drupal\Core\Logger\RfcLogLevel;

/**
 * PSR-3 logger to translate RFC log levels into Monolog log levels.
 *
 * Graciously borrowed from Drupal's Monolog module.
 */
class LevelTranslatingLogger extends Logger {

  /**
   * Map of RFC 5424 log constants to Monolog log constants.
   *
   * @var array
   */
  protected $levelTranslation = array(
    RfcLogLevel::EMERGENCY => self::EMERGENCY,
    RfcLogLevel::ALERT => self::ALERT,
    RfcLogLevel::CRITICAL => self::CRITICAL,
    RfcLogLevel::ERROR => self::ERROR,
    RfcLogLevel::WARNING => self::WARNING,
    RfcLogLevel::NOTICE => self::NOTICE,
    RfcLogLevel::INFO => self::INFO,
    RfcLogLevel::DEBUG => self::DEBUG,
  );

  /**
   * {@inheritdoc}
   */
  public function addRecord($level, $message, array $context = array()) {
    if (array_key_exists($level, $this->levelTranslation)) {
      $level = $this->levelTranslation[$level];
    }
    parent::addRecord($level, $message, $context);
  }

}
