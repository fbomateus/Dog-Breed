<?php

namespace Drupal\dog_breed\Form;

/**
 * @file
 * Contains \Drupal\dog_breed\Form\DogBreedApiForm.
 */

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Dog breed api form settings.
 */
class DogBreedApiForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dog_breed.dog_breed_api_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dog_breed.dog_breed_api_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dog_breed.dog_breed_api_settings');
    $form['base_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base url'),
      '#required' => TRUE,
      '#default_value' => $config->get('base_url'),
    ];
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('dog_breed.dog_breed_api_settings');
    $config->set('base_url', rtrim($form_state->getValue('base_url'), '/'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
