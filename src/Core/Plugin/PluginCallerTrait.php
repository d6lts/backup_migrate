<?php

namespace Drupal\backup_migrate\Core\Plugin;

use Drupal\backup_migrate\Core\Plugin\PluginManagerInterface;

/**
 * Class PluginCallerTrait.
 *
 * @package Drupal\backup_migrate\Core\Plugin
 *
 * Implements the injection code for a PluginCallerInterface object.
 */
trait PluginCallerTrait {

  /**
   * @var \Drupal\backup_migrate\Core\Plugin\PluginManagerInterface;
   */
  protected $plugins;

  /**
   * Inject the plugin manager.
   *
   * @param \Drupal\backup_migrate\Core\Plugin\PluginManagerInterface $plugins
   */
  public function setPluginManager(PluginManagerInterface $plugins) {
    $this->plugins = $plugins;
  }

  /**
   * Get the plugin manager.
   *
   * @return \Drupal\backup_migrate\Core\Plugin\PluginManagerInterface
   */
  public function plugins() {
    // Return the list of plugins or a blank placeholder.
    return $this->plugins ? $this->plugins : new PluginManager();
  }

}
