<?php

/**
 * @file Extended environmnent for the drupal version.
 *
 * Part of the Drupal twig extension distribution
 * http://renebakx.nl/twig-for-drupal.
 */

/**
 *
 */
class TFD_Environment extends Twig_Environment {

  protected $templateClassPrefix = '__TFDTemplate_';
  protected $fileExtension = 'tpl.twig';
  protected $autoRender = FALSE;

  /**
   *
   */
  public function __construct(Twig_LoaderInterface $loader = NULL, $options = array()) {
    $this->fileExtension = twig_extension();
    $options = array_merge(array(
      'autorender' => TRUE,
    ), $options);
    // Auto render means, overrule default class.
    if ($options['autorender']) {
      $this->autoRender = TRUE;
    }
    parent::__construct($loader, $options);
  }

  /**
   *
   */
  private function generateCacheKeyByName($name) {
    return $name = preg_replace('/\.' . $this->fileExtension . '$/', '', $this->loader->getCacheKey($name));
  }

  /**
   *
   */
  public function isAutoRender() {
    return $this->autoRender;
  }

  /**
   * Returns the name of the class to be created
   * which is also the name of the cached instance.
   *
   * @param string $name
   *   of template.
   *
   * @return string
   */
  public function getTemplateClass($name, $index = NULL) {
    return str_replace(array('-', '.', '/'), "_", $this->generateCacheKeyByName($name)) . (NULL === $index ? '' : '_' . $index);
  }

  /**
   *
   */
  public function loadTemplate($name, $index = NULL) {

    if (substr_count($name, '::') == 1) {
      // $paths = twig_get_discovered_templates(); // Very expensive call.
      /** @var TFD_Loader_Filesystem $loader */
      $loader = $this->getLoader();
      $name = $loader->findTemplate($name);
      // $name = $paths[$name];.
    }

    return parent::loadTemplate($name, $index);
  }

}
