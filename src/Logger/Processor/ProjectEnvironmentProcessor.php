<?php


namespace Drupal\lcm_monitoring\Logger\Processor;


class ProjectEnvironmentProcessor {

  private $project;
  private $environment;

  public function __construct($project, $environment) {
    $this->project = $project;
    $this->environment = $environment;
  }

  public function __invoke(array $record) {
    $record['extra']['project'] = $this->project;
    $record['extra']['environment'] = $this->environment;

    return $record;
  }

}
