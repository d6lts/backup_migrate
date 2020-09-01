<?php

namespace Drupal\Tests\backup_migrate\Functional;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Tests\BrowserTestBase;

/**
 * Checks if admin functionality works correctly.
 *
 * @group backup_migrate
 */
class AdminFunctionalityTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['backup_migrate'];

  /**
   * {@inheritdoc}
   */
  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->container->get('router.builder')->rebuild();

    // Ensure backup_migrate folder exists, the
    // `admin/config/development/backup_migrate/backups` path will fail without
    // this.
    $path = 'private://backup_migrate/';
    \Drupal::service('file_system')->prepareDirectory($path, FileSystemInterface::CREATE_DIRECTORY);

    // Log in an admin user.
    $account = $this->drupalCreateUser([
      'access backup files',
      'administer backup and migrate',
      'perform backup',
      'restore from backup',
    ]);
    $this->drupalLogin($account);
  }

  /**
   * Tests each of the admin pages loads correctly.
   *
   * This is to be unsed until a 
   *
   * @param string $path
   *   The path to check.
   * @param string $string_on_page
   *   A string to look for on the page above..
   *
   * @dataProvider pagesListProvider
   */
  public function testPages($path, $string_on_page) {
    $this->drupalGet($path);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($string_on_page);
  }

  /**
   * A list of paths to check and a string that should be present on that page.
   *
   * @return array
   *   A list of paths with a string that should be present on that page.
   */
  public function pagesListProvider() {
    return [
      ['admin/config/development/backup_migrate', 'Quick Backup'],
      ['admin/config/development/backup_migrate/advanced', 'Advanced Backup'],
      ['admin/config/development/backup_migrate/restore', 'Restore'],
      ['admin/config/development/backup_migrate/backups', 'Backups'],
      ['admin/config/development/backup_migrate/schedule', 'Schedule'],
      ['admin/config/development/backup_migrate/schedule/add', 'Add schedule'],
      ['admin/config/development/backup_migrate/settings', 'Settings'],
      ['admin/config/development/backup_migrate/settings/add', 'Add settings profile'],
      ['admin/config/development/backup_migrate/settings/source', 'Backup sources'],
      ['admin/config/development/backup_migrate/settings/source/add', 'Add Backup Source'],
    ];
  }

  /**
   * Make sure the 'destinations' system works correctly.
   */
  public function testDestinationsAdmin() {
    // Load the destination page.
    $this->drupalGet('admin/config/development/backup_migrate/settings/destination');
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->pageTextContains('Backup Destination');
    $session->pageTextContains('Private Files Directory');
    $session->pageTextContains('private_files');
    $session->pageTextContains('Server File Directory');
    // @todo Confirm the table only has one record.

    // Load the destination-add form.
    $this->drupalGet('admin/config/development/backup_migrate/settings/destination/add');
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->pageTextContains('Add destination');
    $session->fieldExists('label');
    $session->fieldExists('id');
    $session->fieldExists('type');
    $session->buttonExists('Save and edit');

    // Create a new destination.
    $edit = [
      'label' => 'Test destination',
      'id' => 'test_destination',
      'type' => 'Directory',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save and edit');

    // This should load a new version of the form with the directory filled in.
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->addressEquals('admin/config/development/backup_migrate/settings/destination/edit/test_destination');
    $session->fieldExists('label');
    $session->fieldExists('config[directory]');
    $session->buttonExists('Save');
    $session->linkExists('Delete');

    // Fill in a path.
    $edit = [
      'config[directory]' => 'test_path',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->addressEquals('admin/config/development/backup_migrate/settings/destination');
    $session->pageTextContains('Saved Test destination.');
    $session->pageTextContains('Private Files Directory');
    $session->pageTextContains('private_files');
  }

}
