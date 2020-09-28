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
      ['admin/config/development/backup_migrate/settings/destination', 'Backup Destination'],
      ['admin/config/development/backup_migrate/settings/destination/add', 'Add destination'],
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

  /**
   * Make sure the 'schedules' system works correctly.
   */
  public function testSchedulesAdmin() {
    // Load the schedule page.
    $this->drupalGet('admin/config/development/backup_migrate/schedule');
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->pageTextContains('Schedule Name');
    $session->pageTextContains('Enabled');
    $session->pageTextContains('Frequency');
    $session->pageTextContains('Last Run');
    $session->pageTextContains('Next Run');
    $session->pageTextContains('Keep');
    $session->pageTextContains('Daily Schedule');
    $session->pageTextContains('No');
    $session->pageTextContains('Every 0 weeks');
    $session->pageTextContains('Never');
    $session->pageTextContains('Disabled');
    $session->pageTextContains('Last backups');
    // @todo Confirm the table only has one record.

    // Load the schedule-add form.
    $this->drupalGet('admin/config/development/backup_migrate/schedule/add');
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->pageTextContains('Add schedule');
    $session->fieldExists('label');
    $session->fieldExists('id');
    $session->fieldExists('enabled');
    $session->fieldExists('source_id');
    $session->fieldExists('destination_id');
    $session->fieldExists('period_number');
    $session->fieldExists('period_type');
    $session->fieldExists('keep');
    $session->buttonExists('Save');

    // Create a new schedule.
    $edit = [
      'label' => 'Test schedule',
      'id' => 'test_schedule',
      'enabled' => TRUE,
      'source_id' => 'default_db',
      'destination_id' => 'private_files',
      'period_number' => 14400,
      'period_type' => 'minutes',
      'keep' => 1000,
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');

    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->addressEquals('admin/config/development/backup_migrate/schedule');
    $session->pageTextContains('Created the Test schedule Schedule.');
    $session->pageTextContains('Every 10 days');
    $session->pageTextContains('Last 1000 backups');
    $session->pageTextContains('Yes');
  }

  /**
   * Make sure the 'profiles' system works correctly.
   */
  public function testProfilesAdmin() {
    // Load the profiles page.
    $this->drupalGet('admin/config/development/backup_migrate/settings');
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->pageTextContains('Settings');
    $session->pageTextContains('Profile Name');
    // @todo Confirm the table has no records.

    // Load the profile-add form.
    $this->drupalGet('admin/config/development/backup_migrate/settings/add');
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->pageTextContains('Add settings profile');
    $session->fieldExists('label');
    $session->fieldExists('id');
    $session->fieldExists('config[namer][filename]');
    $session->fieldExists('config[namer][timestamp]');
    $session->fieldExists('config[namer][timestamp_format]');
    $session->fieldExists('config[compressor][compression]');
    $session->fieldExists('config[utils][site_offline]');
    $session->fieldExists('config[metadata][description]');
    $session->fieldExists('config[db_exclude][exclude_tables][]');
    $session->fieldExists('config[db_exclude][nodata_tables][]');
    $session->fieldExists('config[private_files_exclude][exclude_filepaths]');
    $session->fieldExists('config[public_files_exclude][exclude_filepaths]');
    $session->buttonExists('Save');

    // Create a new profile.
    $edit = [
      'label' => 'Test profile',
      'id' => 'test_profile',
      'config[namer][filename]' => 'test_backup',
      'config[namer][timestamp]' => 'Y-m-d\TH-i-s',
      'config[compressor][compression]' => 'none',
      'config[utils][site_offline]' => 1,
      'config[metadata][description]' => 'Test description text.',
      'config[db_exclude][exclude_tables][]' => [],
      'config[db_exclude][nodata_tables][]' => [],
      'config[private_files_exclude][exclude_filepaths]' => 'test_private_exclude',
      'config[public_files_exclude][exclude_filepaths]' => 'test_public_exclude',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');

    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->addressEquals('admin/config/development/backup_migrate/settings');
    $session->pageTextContains('Created the Test profile Settings Profile.');
    $session->pageTextContains('Profile Name');
    $session->pageTextContains('Machine name');
    $session->pageTextContains('Compression');
    $session->pageTextContains('Take site offline');
    $session->pageTextContains('Description');
    $session->pageTextContains('Test profile');
    $session->pageTextContains('test_profile');
    $session->pageTextContains('none');
    $session->pageTextContains('1');
    $session->pageTextContains('Test description text.');
  }

}
