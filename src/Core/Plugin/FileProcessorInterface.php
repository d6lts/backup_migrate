<?php

namespace Drupal\backup_migrate\Core\Plugin;

use Drupal\backup_migrate\Core\File\TempFileManagerInterface;

/**
 * Interface FileProcessorPluginInterface.
 *
 * @package Drupal\backup_migrate\Core\Plugin
 *
 * An interface for plugins which process files and therefore must have access
 * to a temp file factory.
 */
interface FileProcessorInterface {

  /**
   * Inject the temp file manager.
   *
   * @param \Drupal\backup_migrate\Core\File\TempFileManagerInterface $tempfilemanager
   *
   * @return mixed
   */
  public function setTempFileManager(TempFileManagerInterface $tempfilemanager);

  /**
   * Get the temp file manager.
   *    * @return \Drupal\backup_migrate\Core\File\TempFileManagerInterface.
   */
  public function getTempFileManager();

}
