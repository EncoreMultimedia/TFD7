<?php

/**
 * @file
 * Class TFD_Cache_Filesystem
 * Part of the Drupal twig extension distribution.
 *
 * @see http://tfd7.rocks for more information
 */

/**
 *
 */
class TFD_Loader_Filesystem extends Twig_Loader_Filesystem {
  protected $resolverCache;

  /**
   *
   */
  public function __construct() {
    parent::__construct(array());
    $this->resolverCache = array();
  }

  /**
   *
   */
  public function getSource($filename) {
    return file_get_contents($this->getCacheKey($filename));
  }

  /**
   *
   */
  public function findTemplate($name) {
    $this->validateName($name);

    try {
      return parent::findTemplate($name);
    }
    catch (Twig_Error_Loader $e) {
      $previous = $e;

      // For BC.
      if (!isset($this->resolverCache[$name])) {
        if (is_readable($name)) {
          $this->resolverCache[$name] = $name;
        }
        else {
          throw new Twig_Error_Loader(sprintf('Could not find a cache key for template "%s"', $name), -1, NULL, $previous);
        }
      }
    }
    return $this->resolverCache[$name];
  }

  /**
   * @param $name
   * @param $completeName
   * @return bool
   */
  private function isTemplateReadable($name, $completeName) {
    $found = FALSE;
    if (is_readable($completeName)) {
      $this->resolverCache[$name] = $completeName;
      $found = TRUE;
      return $found;
    }
    return $found;
  }

}
