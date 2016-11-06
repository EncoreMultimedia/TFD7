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
  public function isAutoRender() {
    return $this->autoRender;
  }

}
