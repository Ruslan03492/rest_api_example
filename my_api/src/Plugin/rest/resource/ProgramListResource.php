<?php

namespace Drupal\my_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Drupal\my_api\myApiFetcher;

/**
 * Provides a Program List Resource
 *
 * @RestResource(
 *   id = "program_list_resource",
 *   label = @Translation("Program List Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/program/list"
 *   }
 * )
 */
class ProgramListResource extends ResourceBase {

  /**
   * The my API fetcher.
   *
   * @var \Drupal\my_api\myApiFetcher
   */
  protected $myApiFetcher;

  /**
   * Constructs a new ProgramListResource instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\my_api\myApiFetcher $my_api_fetcher
   *   The my API fetcher.
   */
  public function __construct(array $configuration, $plugin_id,
                              $plugin_definition, array $serializer_formats,
                              LoggerInterface $logger,
                              myApiFetcher $my_api_fetcher) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->myApiFetcher = $my_api_fetcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('my_api.fetcher')
    );
  }

  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $data = [];
    $programs = $this
      ->myApiFetcher
      ->getPrograms();
    if (!empty($programs)) {
      foreach ($programs as $program) {
        /** @var \Drupal\node\Entity\Node $program */
        $data[] = [
          'program_id' => $this->myApiFetcher->getFieldValue($program, 'program_number'),
          'program_title' => $program->getTitle(),
        ];
      }
    }
    $response = new ResourceResponse($data);
    $response->addCacheableDependency($data);
    return $response;
  }

}
