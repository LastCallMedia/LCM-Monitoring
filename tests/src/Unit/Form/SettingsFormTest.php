<?php


namespace Drupal\Tests\lcm_monitoring\Form;


use Drupal\Core\Form\FormState;
use Drupal\lcm_monitoring\Form\SettingsForm;
use Drupal\Tests\UnitTestCase;

class SettingsFormTest extends UnitTestCase  {

  public function testSettingsFormHasElements() {
    $configFactory = $this->getConfigFactoryStub([
      'lcm_monitoring.settings' => []
    ]);
    $form = new SettingsForm($configFactory);
    $form->setStringTranslation($this->getStringTranslationStub());
    $formState = new FormState();

    $build = $form->buildForm([], $formState);
    $this->assertTrue(is_array($build['projectid']));
    $this->assertTrue(is_array($build['accesskey']));
    $this->assertTrue(is_array($build['logger_enabled']));
    $this->assertTrue(is_array($build['logger_host']));
    $this->assertTrue(is_array($build['logger_port']));
  }
}
