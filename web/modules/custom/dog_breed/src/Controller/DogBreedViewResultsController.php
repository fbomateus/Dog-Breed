<?php

namespace Drupal\dog_breed\Controller;

use Drupal\dog_breed\Service\DogBreedViewResultsService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The dog breed view results.
 */
class DogBreedViewResultsController extends ControllerBase {

  /**
   * The dog breed view results service.
   *
   * @var \Drupal\dog_breed\Service\DogBreedViewResultsService
   */
  private $dog_breed_view_results;

  /**
   * DogBreedViewResultsController constructor.
   *
   * @param \Drupal\dog_breed\Service\DogBreedViewResultsService $dog_breed_view_results
   *   The dog breed view results service.
   */
  public function __construct(DogBreedViewResultsService $dog_breed_view_results) {
    $this->dog_breed_view_results = $dog_breed_view_results;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dog_breed.dog_breed_view_result.service')
    );
  }

  public function dogBreedListPage() {
    $results = $this->dog_breed_view_results->getDogBreedListPage();

    return [
      '#theme' => 'dog_breed_list',
      '#data' => $results,
      '#attached' => [
        'library' => [
          'dog_breed/dog_breed_list',
        ],
      ],
    ];
  }
}
