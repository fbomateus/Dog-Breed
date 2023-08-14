<?php

namespace Drupal\dog_breed\Controller;

use Drupal\dog_breed\Service\DogBreedApiService;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;

/**
 * Defines a route controller for watches autocomplete form elements.
 */
class DogBreedAutoCompleteController extends ControllerBase {

  use StringTranslationTrait;

  /**
   * The dog breed API service.
   *
   * @var \Drupal\dog_breed\Service\DogBreedApiService
   */
  private $dog_breed_api;

  /**
   * DogBreedAutoCompleteController constructor.
   *
   * @param \Drupal\dog_breed\Service\DogBreedApiService $dog_breed_api
   *   The dog breed API service.
   */
  public function __construct(DogBreedApiService $dog_breed_api) {
    $this->dog_breed_api = $dog_breed_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      $container->get('dog_breed.dog_breed_api.service')
    );
  }

  /**
   * Handler for autocomplete request.
   */
  public function handleAutocomplete(Request $request) {
    $results = [];
    $input = $request->query->get('q');

    // Get the typed string from the URL, if it exists.
    if (!$input) {
      return new JsonResponse($results);
    }

    $input = Xss::filter($input);
    $breeds = $this->dog_breed_api->getListAllBreedsByName($input);

    return new JsonResponse($breeds);
  }
}
