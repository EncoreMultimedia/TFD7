<?php

/**
 * Class TFD_Cache_Filesystem.
 */
class TFD_Cache_Filesystem implements Twig_CacheInterface {

  /**
   * The scheme wrapper.
   *
   * @var DrupalLocalStreamWrapper
   */
  private $scheme_wrapper;

  /**
   * Hold the original scheme wrapper string.
   *
   * @var string
   */
  private $wrapper;

  /**
   * Class constructor.
   *
   * @param string $cache_scheme
   *   The cache scheme.
   */
  public function __construct($cache_scheme) {
    if ($cache_scheme instanceof DrupalStreamWrapperInterface) {
      $this->scheme_wrapper = $cache_scheme;
    }
    else {
      $this->scheme_wrapper = file_stream_wrapper_get_instance_by_scheme(file_uri_scheme($cache_scheme));
    }

    $this->wrapper = $this->scheme_wrapper->getUri();
  }

  /**
   * Generates a cache key for the given template class name.
   * Cleans the $name, everything before the name is removed.
   *
   * @param string $name
   *   The template name.
   * @param string $className
   *   The template class name.
   *
   * @return string
   */
  public function generateKey($name, $className) {
    $name = preg_replace("/(.*\\/themes\\/)/", "", $name);
    $hash = hash('sha256', $className);
    return dirname($name) . '/' . basename($name) . '_' . $hash . '.php';
  }

  /**
   * Support for fopen(), file_get_contents(), file_put_contents() etc.
   *
   * @param $uri
   *   A string containing the URI to the file to open.
   * @param $mode
   *   The file mode.
   * @param $options
   *   A bit mask of STREAM_USE_PATH and STREAM_REPORT_ERRORS.
   *
   * @return bool
   *   Returns TRUE if file was opened successfully.
   */
  public function open($uri, $mode, $options) {
    $opened_path = NULL;
    $this->scheme_wrapper->stream_open($uri, $mode, $options, $opened_path);
  }

  /**
   * Writes the compiled template to cache.
   *
   * @param string $key
   *   The cache key.
   * @param string $content
   *   The template representation as a PHP class.
   *
   * @return bool
   *   True on success, otherwise False.
   */
  public function write($key, $content) {
    $this->createDirectory($key);
    $this->open($this->wrapper . $key, 'w', array());
    return $this->scheme_wrapper->stream_write($content);
  }

  /**
   * Loads a template from the cache.
   *
   * @param string $key
   *   The cache key.
   *
   * @return bool
   *   True on success, otherwise False.
   */
  public function load($key) {
    $key = $this->wrapper . $key;

    if (file_exists($key)) {
      @include_once $key;
    }
  }

  /**
   * Returns the modification timestamp of a key.
   *
   * @param string $key
   *   The cache key.
   *
   * @return int
   */
  public function getTimestamp($key) {
    $key = $this->wrapper . $key;

    if (!file_exists($key)) {
      return 0;
    }

    return (int) @filemtime($key);
  }

  /**
   * Removes all files in this bin.
   */
  public function deleteAll() {
    return $this->unlink($this->scheme_wrapper->getDirectoryPath());
  }

  /**
   * Deletes files and/or directories in the specified path.
   *
   * If the specified path is a directory the method will
   * call itself recursively to process the contents. Once the contents have
   * been removed the directory will also be removed.
   *
   * @param string $path
   *   A string containing either a file or directory path.
   *
   * @return bool
   *   TRUE for success or if path does not exist, FALSE in the event of an
   *   error.
   */
  protected function unlink($path) {
    if (file_exists($path)) {
      if (is_dir($path)) {
        // Ensure the folder is writable.
        @chmod($path, 0777);
        foreach (new \DirectoryIterator($path) as $fileinfo) {
          if (!$fileinfo->isDot()) {
            $this->unlink($fileinfo->getPathName());
          }
        }
        return @rmdir($path);
      }
      // Windows needs the file to be writable.
      @chmod($path, 0700);
      return @unlink($path);
    }
    // If there's nothing to delete return TRUE anyway.
    return TRUE;
  }

  /**
   * Ensures the requested directory exists and has the right permissions.
   *
   * For compatibility with open_basedir, the requested directory is created
   * using a recursion logic that is based on the relative directory path/tree:
   * It works from the end of the path recursively back towards the root
   * directory, until an existing parent directory is found. From there, the
   * subdirectories are created.
   *
   * @param string $key
   *   The cache key.
   * @param int $mode
   *   The mode, permissions, the directory should have.
   *
   * @return bool
   *   TRUE if the directory exists or has been created, FALSE otherwise.
   */
  protected function createDirectory($key, $mode = 0777) {
    $options = array(STREAM_MKDIR_RECURSIVE);
    $this->scheme_wrapper->mkdir($this->wrapper . dirname($key), $mode, $options);
  }

}
