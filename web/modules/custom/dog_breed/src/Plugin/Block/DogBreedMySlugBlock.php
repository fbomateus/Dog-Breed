<?php

namespace Drupal\dog_breed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\dog_breed\Service\DogBreedApiService;

/**
 * Provides a 'DogBreedMySlugBlock' Block.
 *
 * @Block(
 *   id = "dog_breed_my_slug_block",
 *   admin_label = @Translation("Dog Breed My Slug"),
 *   category = @Translation("Dog Breed"),
 * )
 */
class DogBreedMySlugBlock extends BlockBase implements ContainerFactoryPluginInterface {
  
  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Container Interface.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * The dog breed API service.
   *
   * @var \Drupal\dog_breed\Service\DogBreedApiService
   */
  private $dog_breed_api;

  /**
   * My custom slug from Dog Breed API.
   *
   * @var string
   */
  private $slug;

  /**
   * DogBreedMySlugBlock constructor.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\dog_breed\Service\DogBreedApiService $dog_breed_api
   *   The dog breed API service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory,
    DogBreedApiService $dog_breed_api
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory->get('dog_breed.dog_breed_my_slug_settings');
    $this->slug = $this->configFactory->get('field_slug');
    $this->dog_breed_api = $dog_breed_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('dog_breed.dog_breed_api.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $slug = $this->slug[0]['value'];
    $breed_image = $this->dog_breed_api->getBreedsImageByName($slug);
    $results = [
      "value" => $slug,
      "label" => ucwords(str_replace('-', ' ', $slug)),
      "image" => $breed_image["message"] ?? "",
    ];

    return [
      '#theme' => 'dog_breed_my_slug',
      '#data' => $results,
    ];
  }
}
