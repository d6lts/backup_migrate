<?php

namespace Drupal\backup_migrate\Core\Exception;

/**
 * Class DestinationNotWritableException.
 *
 * @package Drupal\backup_migrate\Core\Exception
 *
 * Thrown if a destination cannot be written to for any reason. May be
 * recoverable if the backup operation specifies alternative destinations or
 * fatal if not.
 */
class DestinationNotWritableException extends BackupMigrateException {}
