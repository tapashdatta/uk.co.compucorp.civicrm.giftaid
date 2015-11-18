<?php

/**
 * Collection of upgrade steps
 */
class CRM_Civigiftaid_Upgrader extends CRM_Civigiftaid_Upgrader_Base {

  const REPORT_CLASS = 'CRM_Civigiftaid_Report_Form_Contribute_GiftAid';
  const REPORT_URL = 'civicrm/contribute/report/uk-giftaid';

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed
   *
   */
  public function install() {
    //only for vesion 1.0 to 2.1
    //To remove the future
    self::removeLegacyRegisteredReport();
    self::migrateOneToTwo($this);
    //end step for upgrading version 1.0 t0 2.1

    $ogId = self::getReportTemplateGroupId();
    if($ogId){
      $className = self::REPORT_CLASS;
      $reportUrl = new CRM_Core_DAO_OptionValue();
      $reportUrl->option_group_id = $ogId;
      $reportUrl->value = self::REPORT_URL;
      $dupeURL = $dupeClass = FALSE;
      if ($reportUrl->find(TRUE)) {
        //if url exist
        $dupeURL = TRUE;
        if ($reportUrl->name == $className) {
          $dupeClass = TRUE;
        }
      }
      if (!$dupeClass) {
        $reportClass = new CRM_Core_DAO_OptionValue();
        $reportClass->option_group_id = $ogId;
        $reportClass->name = $className;
        if ($reportClass->find(TRUE)) {
          $dupeClass = TRUE;
        }
      }

      if (!$dupeClass && !$dupeURL) {
        $params = array(
          'version' => 3,
          'option_group_id' => $ogId,
          'label' => 'Gift Aid Report',
          'name' => self::REPORT_CLASS,
          'value' => 'civicrm/contribute/uk-giftaid',
          'description' => 'For submitting Gift Aid reports to HMRC treasury.',
          'component_id' => CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Component',
            'CRM_Contribute',
            'id',
            'namespace'
          ),
          'is_active' => 1
        );
        $result = civicrm_api('OptionValue', 'create', $params);
      }
    }
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled
   *
   */
  public function uninstall() {
    CRM_Core_DAO::executeQuery("DELETE FROM civicrm_option_group WHERE name = 'giftaid_batch_name'");
    CRM_Core_DAO::executeQuery("DELETE FROM civicrm_option_group WHERE name = 'giftaid_basic_rate_tax'");
    CRM_Core_DAO::executeQuery("DELETE FROM civicrm_option_group WHERE name = 'reason_ended'");

    civicrm_api('CustomGroup', 'delete', array(
      'version' => 3,
      'id' => CRM_Utils_Array::value('id',civicrm_api('CustomGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'Gift_Aid')
      )),
    ));

    civicrm_api('CustomGroup', 'delete', array(
      'version' => 3,
      'id' =>  CRM_Utils_Array::value('id',civicrm_api('CustomGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'Gift_Aid_Declaration')
      )),
    ));

    civicrm_api('UFGroup', 'delete', array(
      'version' => 3,
      'id' =>  CRM_Utils_Array::value('id',civicrm_api('UFGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'Gift_Aid_Declaration')
      )),
    ));

    $reportUrl = new CRM_Core_DAO_OptionValue();
    $reportUrl->option_group_id = self::getReportTemplateGroupId();;
    $reportUrl->value = self::REPORT_URL;
    if ($reportUrl->find(TRUE)) {
      if ($reportUrl->name == $className) {
        $reportUrl->delete();
      }
    }

    $reportClass = new CRM_Core_DAO_OptionValue();
    $reportClass->option_group_id = self::getReportTemplateGroupId();;
    $reportClass->name = self::REPORT_CLASS;
    if ($reportClass->find(TRUE)) {
      $reportClass->delete();
    }

  }

  /**
   * Example: Run a simple query when a module is enabled
   *
  */
  public function enable() {

    CRM_Core_DAO::executeQuery("UPDATE civicrm_option_group SET is_active = 1  WHERE name = 'giftaid_batch_name'");
    CRM_Core_DAO::executeQuery("UPDATE civicrm_option_group SET is_active = 1  WHERE name = 'giftaid_basic_rate_tax'");
    CRM_Core_DAO::executeQuery("UPDATE civicrm_option_group SET is_active = 1  WHERE name = 'reason_ended'");

    civicrm_api('CustomGroup', 'update', array(
      'version' => 3,
      'is_active' => 1,
      'id' => CRM_Utils_Array::value('id',civicrm_api('CustomGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'Gift_Aid')
      )),
    ));

    civicrm_api('CustomGroup', 'update', array(
      'version' => 3,
      'is_active' => 1,
      'id' =>  CRM_Utils_Array::value('id',civicrm_api('CustomGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'Gift_Aid_Declaration')
      )),
    ));

    civicrm_api('UFGroup', 'update', array(
      'version' => 3,
      'is_active' => 1,
      'id' =>  CRM_Utils_Array::value('id',civicrm_api('UFGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'Gift_Aid_Declaration')
      )),
    ));
    $gid = self::getReportTemplateGroupId();
    $className = self::REPORT_CLASS;
    CRM_Core_DAO::executeQuery("UPDATE civicrm_option_value SET is_active = 1 WHERE option_group_id = $gid AND name = '$className'");
  }

  /**
   * Example: Run a simple query when a module is disabled
   *
   */
  public function disable() {

    CRM_Core_DAO::executeQuery("UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'giftaid_batch_name'");
    CRM_Core_DAO::executeQuery("UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'giftaid_basic_rate_tax'");
    CRM_Core_DAO::executeQuery("UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'reason_ended'");

     civicrm_api('CustomGroup', 'update', array(
      'version' => 3,
      'is_active' => 0,
      'id' => CRM_Utils_Array::value('id',civicrm_api('CustomGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'Gift_Aid')
      )),
    ));

    civicrm_api('CustomGroup', 'update', array(
      'version' => 3,
      'is_active' => 0,
      'id' =>  CRM_Utils_Array::value('id',civicrm_api('CustomGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'Gift_Aid_Declaration')
      )),
    ));

    civicrm_api('UFGroup', 'update', array(
      'version' => 3,
      'is_active' => 0,
      'id' =>  CRM_Utils_Array::value('id',civicrm_api('UFGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'Gift_Aid_Declaration')
      )),
    ));
    $gid = self::getReportTemplateGroupId();
    $className = self::REPORT_CLASS;
    CRM_Core_DAO::executeQuery("UPDATE civicrm_option_value SET is_active = 0 WHERE option_group_id = $gid AND name = '$className'");

    /*
    civicrm_api('OptionValue', 'update', array(
      'version' => 3,
      'is_active' => 0,
      'name' => self::REPORT_CLASS,
      'option_group_id' => self::getReportTemplateGroupId()
    ));*/

  }

  /**
   * Run a update schema
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_2100() {
    $this->ctx->log->info('Applying update 2100');
    self::removeLegacyRegisteredReport();
    return TRUE;
  }

  /**
   * @return bool
   */
  public function upgrade_3000() {
    $this->ctx->log->info('Applying update 3000');

    // Add admin settings for the extension, and set globally enabled to TRUE by default
    $settings = new stdClass();
    $settings->globally_enabled = 1;
    $settings->financial_types_enabled = array();
    CRM_Core_BAO_Setting::setItem(
      $settings,
      'Extension',
      'uk.co.compucorp.civicrm.giftaid:settings'
    );

    return TRUE;
  }

  public function upgrade_3100() {
    $this->ctx->log->info('Applying update 3100');
    $this->executeSqlFile('sql/upgrade_3100.sql');
    static::importBatches();

    return TRUE;
  }

  /**
   * Create default settings for existing batches, for which settings don't already exist.
   */
  private static function importBatches() {
    $sql = "
      SELECT id
      FROM civicrm_batch
      WHERE name LIKE 'GiftAid%'
    ";

    $dao = CRM_Core_DAO::executeQuery($sql);

    while ($dao->fetch()) {
      // Only add settings for batches for which settings don't exist already
      if (CRM_Civigiftaid_BAO_BatchSettings::findByBatchId($dao->id) === FALSE) {
        // Set globally enabled to TRUE by default, for existing batches
        CRM_Civigiftaid_BAO_BatchSettings::create(array(
          'batch_id' => (int) $dao->id,
          'financial_types_enabled' => array(),
          'globally_enabled' => TRUE
        ));
      }
    }
  }

  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4201() {
    $this->ctx->log->info('Applying update 4201');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_4201.sql');
    return TRUE;
  } // */


  /**
   * Example: Run a slow upgrade process by breaking it up into smaller chunk
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4202() {
    $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

    $this->addTask(ts('Process first step'), 'processPart1', $arg1, $arg2);
    $this->addTask(ts('Process second step'), 'processPart2', $arg3, $arg4);
    $this->addTask(ts('Process second step'), 'processPart3', $arg5);
    return TRUE;
  }
  public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
  public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
  public function processPart3($arg5) { sleep(10); return TRUE; }
  // */


  /**
   * Example: Run an upgrade with a query that touches many (potentially
   * millions) of records by breaking it up into smaller chunks.
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4203() {
    $this->ctx->log->info('Planning update 4203'); // PEAR Log interface

    $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
    $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
    for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
      $endId = $startId + self::BATCH_SIZE - 1;
      $title = ts('Upgrade Batch (%1 => %2)', array(
        1 => $startId,
        2 => $endId,
      ));
      $sql = '
        UPDATE civicrm_contribution SET foobar = whiz(wonky()+wanker)
        WHERE id BETWEEN %1 and %2
      ';
      $params = array(
        1 => array($startId, 'Integer'),
        2 => array($endId, 'Integer'),
      );
      $this->addTask($title, 'executeSql', $sql, $params);
    }
    return TRUE;
  } //
  */

  /**  Remove all the report that registerd on GiftAid 1.0beta and 2.0beta
  **/
  static function removeLegacyRegisteredReport(){
    $reportClass = new CRM_Core_DAO_OptionValue();
    $reportClass->option_group_id = self::getReportTemplateGroupId();
    $reportClass->name = 'GiftAid_Report_Form_Contribute_GiftAid';
    if ($reportClass->find(TRUE)) {
      $reportClass->delete();
    }
  }

  static function getReportTemplateGroupId(){
    $params = array(
      'version' => 3,
      'name' => 'report_template',
    );
    $og = civicrm_api('OptionGroup', 'getsingle', $params);
    $ogId = CRM_Utils_Array::value('id', $og);
    return $ogId;
  }

  static function migrateOneToTwo($ctx){
    $ctx->executeSqlFile('sql/upgrade_20.sql');
    $query = "SELECT DISTINCT batch_name
              FROM civicrm_value_gift_aid_submission
             ";
    $batchNames = array();
    $dao = CRM_Core_DAO::executeQuery($query);
    while ($dao->fetch()) {
      array_push($batchNames, $dao->batch_name);
    }
    $gId = CRM_Utils_Array::value('id',civicrm_api('OptionGroup', 'getsingle', array(
        'version' => 3,
        'name' => 'giftaid_batch_name')
    ));
    if($gId){
      foreach ($batchNames as $name) {
        $params = array(
          'version' => 3,
          'option_group_id' => $gId,
          'label' => $name,
          'name' => $name,
          'value' => $name,
          'is_active' => 1
        );
        $result = civicrm_api('OptionValue', 'create', $params);
      }
    }
  }

  public static function migrateToThree($ctx) {
    $ctx->executeSqlFile('sql/upgrade_3100');
  }

}
