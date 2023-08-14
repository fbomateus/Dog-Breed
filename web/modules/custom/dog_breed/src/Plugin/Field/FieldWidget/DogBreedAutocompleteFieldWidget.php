<?php

namespace Drupal\dog_breed\Plugin\Field\FieldWidget;

use Drupal\dog_breed\Service\DogBreedApiService;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Plugin implementation of the 'Dog Breed Selector' widget.
 *
 * @FieldWidget(
 *     id="dog_breed_autocomplete_field_widget",
 *     module="dog_breed",
 *     label=@Translation("Autocomplete: Dog Breed Selector"),
 *     field_types={
 *         "string"
 *     }
 * )
 */
class DogBreedAutocompleteFieldWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The campaign API service.
   *
   * @var \Drupal\dog_breed\Service\DogBreedApiService
   */
  private $dog_breed_api;

  /**
   * DogBreedAutocompleteFieldWidget constructor.
   *
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   * @param array $settings
   *   Configuration settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\dog_breed\Service\DogBreedApiService $dog_breed_api
   *   The campaign API service.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    DogBreedApiService $dog_breed_api
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $third_party_settings,
    );
    $this->dog_breed_api = $dog_breed_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('dog_breed.dog_breed_api.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(
      FieldItemListInterface $items,
      $delta,
      array $element,
      array &$form,
      FormStateInterface $form_state
    ) {
    $default_value = NULL;

    $breeds = $this->dog_breed_api->getListAllBreedsByName($items[$delta]->value);
    if (is_array($breeds) && !empty($breeds)) {
      $default_value = array_column($breeds, "value");
    }
    
    $element += [
      '#title' => $this->t('Slug'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'dog_breed.autocomplete',
      '#autocomplete_route_parameters' => [],
      '#default_value' => $default_value,
      '#element_validate' => [[static::class, 'validateEntityAutocomplete']],
    ];
    return ['value' => $element];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(
    array $values,
    array $form,
    FormStateInterface $form_state
  ) {
    $values = parent::massageFormValues($values, $form, $form_state);

    return $values;
  }

  /**
   * Form element validation handler for dog_breed_autocomplete_field_widget elements.
   */
  public static function validateEntityAutocomplete($element, FormStateInterface $form_state) {
    $breed_id = $element['#value'];

    if (!empty($breed_id)) {
      $breeds = \Drupal::service('dog_breed.dog_breed_api.service')->getListAllBreedsByName($breed_id);

      if (!is_array($breeds) || empty($breeds)) {
        $form_state->setErrorByName('field_slug', t('The slug entered is incorrect or does not exist.'));
      }

    }
  }
}
