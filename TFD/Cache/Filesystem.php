<?php

/**
 * Class TFD_Cache_Filesystem.
 */
class TFD_Cache_Filesystem extends Twig_Cache_Filesystem implements Twig_CacheInterface {

  /**
   * The scheme wrapper.
   *
   * @var DrupalLocalStreamWrapper
   */
  private $scheme_wrapper;

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
    parent::__construct($this->scheme_wrapper->getDirectoryPath());
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

}
