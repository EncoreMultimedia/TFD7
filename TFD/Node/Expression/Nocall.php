<?php

/**
 * @file
 * This file is part of Twig For Drupal 7.
 * *.
 * @see http://tfd7.rocks for more information
 *
 * @author René Bakx
 */

/**
 *
 */
class TFD_Node_Expression_Nocall extends Twig_Node_Expression_Call {

  /**
   *
   */
  protected function compileCallable(Twig_Compiler $compiler) {
    $callable = $this->getAttribute('callable');

    $closingParenthesis = FALSE;
    if ($callable) {
      if (is_array($callable) && $callable[0] instanceof Twig_ExtensionInterface) {
        $compiler->raw(sprintf('$this->env->getExtension(\'%s\')->%s', $callable[0]->getName(), $callable[1]));
      }
      else {
        $type = ucfirst($this->getAttribute('type'));
        $compiler->raw(sprintf('call_user_func_array($this->env->get%s(\'%s\')->getCallable(), array', $type, $this->getAttribute('name')));
        $closingParenthesis = TRUE;
      }
    }
    else {
      $compiler->raw($this->getAttribute('thing')->compile());
    }

    $this->compileArguments($compiler);

    if ($closingParenthesis) {
      $compiler->raw(')');
    }
  }

}
