<?php

namespace Drupal\dog_breed\Form;

/**
 * @file
 * Contains \Drupal\dog_breed\Form\DogBreedMySlugForm.
 */

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Dog breed my slug form settings.
 */
class DogBreedMySlugForm extends ConfigFormBase {

  /**
   * The storage handler class for nodes.
   *
   * @var \Drupal\node\NodeStorage
   */
  private $entityManager;

  /**
   * Container Interface.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dog_breed.dog_breed_my_slug_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dog_breed.dog_breed_my_slug_settings'];
  }

    /**
   * {@inheritDoc}
   */
  public function __construct(EntityTypeManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dog_breed.dog_breed_my_slug_settings');
    $form['#parents'] = [];

    // Create an empty representative entity.
    $node = $this->entityManager->getStorage('node')->create([
      'type' => 'dog_breeds',
    ]);

    // Get the EntityFormDisplay (i.e. the default Form Display) of this content type.
    $entity_form_display = $this->entityManager->getStorage('entity_form_display')->load('node.dog_breeds.default');

    // Get the body field widget and add it to the form.
    // Returns the widget class.
    $widget = $entity_form_display->getRenderer('field_slug') ?? FALSE;
    if ($widget) {
      // Returns the FieldItemsList interface.
      $items = $node->get('field_slug');
      $items->filterEmptyItems();
      
      // Builds the widget form and attach it to form.
      $form['field_slug'] = $widget->form($items, $form, $form_state);
      $form['field_slug']['widget'][0]['value']['#default_value'] = $config->get('field_slug')[0]['value'];
      $form['field_slug']['#access'] = $items->access('edit');
    }
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('dog_breed.dog_breed_my_slug_settings');
    $config->set('field_slug', $form_state->getValue('field_slug'));
    $config->save();
    parent::submitForm($form, $form_state);
  }
}
