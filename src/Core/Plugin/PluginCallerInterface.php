<?php

namespace Drupal\backup_migrate\Core\Plugin;

use Drupal\backup_migrate\Core\Plugin\PluginManagerInterface;

/**
 * Interface PluginCallerPluginInterface.
 *
 * @package Drupal\backup_migrate\Core\Plugin
 *
 * An interface for plugins which need to access other plugins and therefore
 * must have access to a plugin manager.
 */
interface PluginCallerInterface {

  /**
   * Inject the plugin manager.
   *
   * @param \Drupal\backup_migrate\Core\Plugin\PluginManagerInterface $plugins
   */
  public function setPluginManager(PluginManagerInterface $plugins);

  /**
   * Get the plugin manager.
   *    * @return \Drupal\backup_migrate\Core\Plugin\PluginManagerInterface.
   */
  public function plugins();

}
