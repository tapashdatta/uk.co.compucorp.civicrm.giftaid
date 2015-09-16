<?php

class CRM_Civigiftaid_BAO_BatchSettings extends CRM_Civigiftaid_DAO_BatchSettings {

  /**
   * Create a new BatchSettings based on array-data
   *
   * @param array $params key-value pairs
   *
   * @return CRM_Civigiftaid_DAO_BatchSettings|NULL
   */
  public static function create($params) {
    static::addDefaults($params);
    static::preProcessParams($params);

    $entityName = 'BatchSettings';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);

    $instance = new static;
    $instance->copyValues($params);
    $instance->save();

    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  public static function findByBatchId($id) {
    $dao = static::executeQuery("SELECT * FROM " . static::getTableName() . " WHERE batch_id = {$id}");

    if ($dao->fetch()) {
      return $dao;
    }

    return FALSE;
  }

  /**
   * Add default value of certain params, if not provided.
   *
   * @param $params
   */
  private static function addDefaults(&$params) {
    if (!isset($params['financial_types_enabled'])) {
      $params['financial_types_enabled'] = CRM_Civigiftaid_Form_Admin::getFinancialTypesEnabled();
    }
    if (!isset($params['globally_enabled'])) {
      $params['globally_enabled'] = CRM_Civigiftaid_Form_Admin::isGloballyEnabled();
    }
  }

  private static function preProcessParams(&$params) {
    if (is_array($params['financial_types_enabled'])) {
      $params['financial_types_enabled'] = serialize($params['financial_types_enabled']);
    }
  }

}
