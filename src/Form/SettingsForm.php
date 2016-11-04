<?php

namespace Drupal\lcm_monitoring\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'lcm_monitoring_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['lcm_monitoring.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('lcm_monitoring.settings');

    $form['projectid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Project ID'),
      '#required' => TRUE,
      '#default_value' => $config->get('projectid'),
    ];
    $form['accesskey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Access Key'),
      '#required' => TRUE,
      '#default_value' => $config->get('accesskey'),
    ];
    $form['logger_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable logging'),
      '#default_value' => $config->get('logger.enabled'),
    ];

    $loggerStates = [
      'visible' => [
        ':input[name="logger_enabled"]' => array('checked' => TRUE),
      ],
      'required' => [
        ':input[name="logger_enabled"]' => array('checked' => TRUE),
      ],
    ];

    $form['logger_host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Log Host'),
      '#default_value' => $config->get('logger.host'),
      '#states' => $loggerStates,
    ];

    $form['logger_port'] = [
      '#type' => 'number',
      '#title' => $this->t('Log Port'),
      '#default_value' => $config->get('logger.port'),
      '#states' => $loggerStates,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $host = $values['logger_host'];
    $port = $values['logger_port'];

    if ($values['logger_enabled']) {
      // Check that the port is valid.
      if (empty($port) || $port < 1 || $port > 65535) {
        $form_state->setError($form['logger_port'], $this->t('Invalid port'));
      }
      // Check that the hostname is either an IP for a valid hostname.
      if (empty($host) || (!filter_var($host, FILTER_VALIDATE_IP) && !checkdnsrr($host))) {
        $form_state->setError($form['logger_host'], $this->t('Invalid hostname'));
      }
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('lcm_monitoring.settings')
      ->set('projectid', $values['projectid'])
      ->set('accesskey', $values['accesskey'])
      ->set('logger.enabled', (bool) $values['logger_enabled'])
      ->set('logger.host', $values['logger_host'])
      ->set('logger.port', $values['logger_port'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
