<?php

/**
 * @file
 * API documentation for the twig engine, part of tfd7.
 */

/**
 * Customize the twig environment after initialisation.
 *
 * @param \Twig_Environment $environment
 *   The initialized twig environment.
 */
function hook_twig_init_alter(Twig_Environment $environment) {
  $loader = $environment->getLoader();
  $loaders = array($loader);
  $loaders[] = (new Twig_Loader_Filesystem())
    ->setPaths(array(DRUPAL_ROOT . '/../custom_path'), 'namespace');
  $chain = new Twig_Loader_Chain($loaders);
  $environment->setLoader($chain);
}

/**
 * Add or change the filters in the Twig Extension.
 *
 * @param array $filters
 *   the current stack of filters.
 * @param \TFD_Extension $TFD_Extension
 *
 * @return array $filters
 */
function hook_twig_filter($filters, TFD_Extension $TFD_Extension) {
  $filters['filter_name'] = new Twig_SimpleFilter('filter_name', 'filter_callable');
  return $filters;
}

/**
 * Add or change the functions in the Twig Extension.
 *
 * @param array $functions
 *   the current stack of functions.
 * @param \TFD_Extension $TFD_Extension
 *
 * @return array $functions
 */
function hook_twig_function($functions, TFD_Extension $TFD_Extension) {
  $functions['function_name'] = new Twig_SimpleFunction('function_name', 'function_callable');
  return $functions;
}

/**
 * Add or change the tests in the Twig Extension.
 *
 * @param array $tests
 *   the current stack of functions.
 * @param \TFD_Extension $TFD_Extension
 *
 * @return array $tests
 */
function hook_twig_test($tests, TFD_Extension $TFD_Extension) {
  $functions['test_name'] = new Twig_SimpleTest('test_name', 'test_callable');
  return $functions;
}
