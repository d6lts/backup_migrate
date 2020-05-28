<?php

namespace Drupal\backup_migrate\Core\Exception;

/**
 * Class IgnorableException.
 *
 * This exception can be avoided by setting the 'ignore errors' setting.
 *
 * @package Drupal\backup_migrate\Core\Exception
 */
class IgnorableException extends BackupMigrateException {}
