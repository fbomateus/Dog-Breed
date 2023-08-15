<?php

namespace Drupal\dog_breed\Service;

use Drupal\dog_breed\Service\DogBreedApiService;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Dog Breed View Results Service.
 *
 * @package Drupal\dog_breed\Service
 */
class DogBreedViewResultsService {

  /**
   * The dog breed API service.
   *
   * @var \Drupal\dog_breed\Service\DogBreedApiService
   */
  private $dog_breed_api;

  /**
   * The storage handler class for nodes.
   *
   * @var \Drupal\node\NodeStorage
   */
  private $nodeStorage;

  /**
   * Container Interface.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * {@inheritDoc}
   */
  public function __construct(DogBreedApiService $dog_breed_api, EntityTypeManagerInterface $entityManager) {
    $this->dog_breed_api = $dog_breed_api;
    $this->nodeStorage = $entityManager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dog_breed.dog_breed_api.service'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * list result of view 'Dog Breeds List page'.
   */
  public function getDogBreedListPage() {
    if ($this->nodeStorage) {
      $query = $this->nodeStorage->getQuery()
        ->condition('status', 1)
        ->condition('type', 'dog_breeds');
      $items_ids = $query->execute();

      $results = [];
      foreach ($items_ids as $item) {
        $node = $this->nodeStorage->load($item);

        $name = $node->field_common_name->value;
        $slug = $node->field_slug->value;
        $breed_image = $this->dog_breed_api->getBreedsImageByName($slug);

        $results[] = [
          "value" => $slug,
          "label" => $name,
          "image" => $breed_image["message"] ?? "",
        ];
      }

      return $results;
    }
  }
}
