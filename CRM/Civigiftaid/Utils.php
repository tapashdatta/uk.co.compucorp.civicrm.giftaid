<?php
/**
 * https://civicrm.org/licensing
 */

/**
 * Class CRM_Civigiftaid_Utils
 */
class CRM_Civigiftaid_Utils {
  /**********************
   * MJW_Utils: 20190822
   *********************/

  /**
   * Return the field ID for $fieldName custom field
   *
   * @param $fieldName
   * @param $fieldGroup
   * @param bool $fullString
   *
   * @return mixed
   * @throws \CiviCRM_API3_Exception
   */
  public static function getCustomByName($fieldName, $fieldGroup, $fullString = TRUE) {
    if (!isset(Civi::$statics[__CLASS__][$fieldGroup][$fieldName])) {
      $field = civicrm_api3('CustomField', 'get', array(
        'custom_group_id' => $fieldGroup,
        'name' => $fieldName,
      ));

      if (!empty($field['id'])) {
        Civi::$statics[__CLASS__][$fieldGroup][$fieldName]['id'] = $field['id'];
        Civi::$statics[__CLASS__][$fieldGroup][$fieldName]['string'] = 'custom_' . $field['id'];
      }
    }

    if ($fullString) {
      return Civi::$statics[__CLASS__][$fieldGroup][$fieldName]['string'];
    }
    return Civi::$statics[__CLASS__][$fieldGroup][$fieldName]['id'];
  }

}
