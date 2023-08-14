<?php

namespace Drupal\dog_breed\Service;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;

/**
 * Class Dog Breed Api Service.
 *
 * @package Drupal\dog_breed\Service
 */
class DogBreedApiService {

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannel
   */
  protected $logger;

  /**
   * The baseURL from promo admin.
   *
   * @var string
   */
  private $baseUrl;

  /**
   * Construct to campaign API service.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Logger\LoggerChannelFactory $logger
   *   A logger instance.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ClientInterface $http_client, LoggerChannelFactory $logger) {
    $this->configFactory = $config_factory->get('dog_breed.dog_breed_api_settings');
    $this->httpClient = $http_client;
    $this->logger = $logger->get('dog_breed_api');
    $this->baseUrl = $this->configFactory->get('base_url');
  }

  /**
   * Request the server endpoint using Guzzle.
   *
   * @param string $uri
   *   The API endpoint to be called.
   * @param array $data
   *   An array for form fields to be sent.
   * @param array $headers
   *   The custom headers to server.
   * @param string $method
   *   The request method.
   * @param array $query
   *   An array for query to be sent.
   *
   * @return array
   *   The endpoint response or error.
   *
   * @throws \Exception
   */
  public function request(
    string $uri,
    string $method = 'GET',
  ): array {
    try {
      $response = $this->httpClient->request($method, $this->baseUrl . $uri,
        [
          'headers' => [
            'Content-Type' => 'application/json',
          ]
        ],
      );

      $contents = $response->getBody()->getContents();

      $this->logger->info('@class_method || Service url: @uri | Method: @method | Status code: @status_code | Contents: @contents', [
        '@class_method' => __METHOD__,
        '@uri' => $this->baseUrl . $uri,
        '@method' => $method,
        '@status_code' => $response->getStatusCode(),
        '@contents' => $contents,
      ]);

      return Json::decode($contents);
    }
    // Thrown for 400 level errors.
    catch (ClientException $exception) {

      $this->logger->error('@class_method || Service url: @uri | Method: @method | Message: @message', [
        '@class_method' => __METHOD__,
        '@uri' => $this->baseUrl . $uri,
        '@method' => $method,
        '@message' => $exception->getMessage(),
      ]);

      switch (!$exception->hasResponse()) {
        case TRUE:
          throw new \Exception('Internal Server Error', 500);
        break;

        default:
          $response = $exception->getResponse();
          return Json::decode($response->getBody());
        break;
      }
    }
    catch (GuzzleException $exception) {

      $this->logger->error('@class_method || Service url: @uri | Method: @method | Message: @message', [
        '@class_method' => __METHOD__,
        '@uri' => $this->baseUrl . $uri,
        '@method' => $method,
        '@message' => $exception->getMessage(),
      ]);

      throw new \Exception('Internal Server Error', 500);
    }
  }
  
  /**
   * List all breeds by name.
   *
   * @param string $name
   *   The name of the breed.
   * 
   * @return array
   *   The query result.
   *
   * @throws \Exception
   */
  public function getListAllBreedsByName($name): array {
    if (!empty($name)) {
      $breeds = $this->request('/breeds/list/all');
      $results = [];
  
      foreach ($breeds['message'] as $key => $breed) {
        if (!empty($breed)) {
          foreach ($breed as $subBreed) {
            $results[] = [
              "value" => $key . "-" . $subBreed,
              "label" => ucwords($subBreed . " " . $key),
            ];
          }
        } else {
          $results[] = [
            "value" => $key,
            "label" => ucwords($key),
          ];
        }
      }
  
      $response = [];
      foreach ($results as $result) {
        if (str_contains($result["value"], $name) || str_contains($result["label"], $name)) {
          $response[] = $result;
        }
      }
    }

    return $response ?? [];
  }
}
