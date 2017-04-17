<?php

namespace Drupal\my_api;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityInterface;

class MyApiFetcher {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new UserRegistrationResource instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get Program.
   */
  public function getProgram($id = NULL) {
    if (empty($id)) {
      return;
    }
    return reset($this->getPrograms($id));
  }

  /**
   * Get Programs.
   */
  public function getPrograms($id = NULL) {
    $properties = [
      'type' => 'program',
      'status' => TRUE,
    ];
    if (!empty($id) && is_numeric($id)) {
      $properties['program_number'] = $id;
    }
    $programs = $this
      ->entityTypeManager
      ->getStorage('node')
      ->loadByProperties($properties);
    return $programs;
  }

  /**
   * Get Field Value.
   */
  public function getFieldValue(EntityInterface $entity, $field_name) {
    if (empty($field_name)) {
      return '';
    }
    $data = '';
    if (!empty($entity->{$field_name})) {
      $field_value = $entity->get($field_name)->getValue();
      if (!empty($field_value)) {
        $data = $field_value[0]['value'];
      }
    }
    return $data;
  }

}
