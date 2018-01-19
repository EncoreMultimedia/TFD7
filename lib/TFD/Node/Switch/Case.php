<?php

/**
 * @file
 * This file is part of Twig For Drupal 7.
 * *.
 * @see http://tfd7.rocks for more information
 *
 * @author René Bakx
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

/**
 *
 */
class TFD_Node_Switch_Case extends Twig_Node {

  /**
   *
   */
  public function __construct($body, $expression, $fallthrough, $lineno = 0, $tag = NULL) {
    parent::__construct(
      array(
        'body' => $body,
      ),
      array(
        'expression' => $expression,
        'fallthrough' => $fallthrough,
      ),
      $lineno,
      $tag
    );
  }

}
