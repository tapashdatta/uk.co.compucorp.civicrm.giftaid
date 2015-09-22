<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.3                                              |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package   CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */
require_once 'CRM/Report/Form.php';
require_once 'CRM/Civigiftaid/Utils/Contribution.php';

class CRM_Civigiftaid_Report_Form_Contribute_GiftAid extends CRM_Report_Form {
  protected $_addressField = FALSE;
  protected $_customGroupExtends = array('Contribution');

  public function __construct() {
    $this->_columns =
      array(
        'civicrm_entity_batch'   => array(
          'dao'     => 'CRM_Batch_DAO_EntityBatch',
          'filters' =>
            array(
              'batch_id' => array(
                'title'        => 'Batch',
                'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                'options'      => CRM_Civigiftaid_Utils_Contribution::getBatchIdTitle('id desc'),
              ),
            ),
          'fields'  => array(
            'batch_id' => array(
              'name'       => 'batch_id',
              'title'      => 'Batch ID',
              'no_display' => TRUE,
              'required'   => TRUE
            )
          )
        ),
        'civicrm_contribution'   =>
          array(
            'dao'    => 'CRM_Contribute_DAO_Contribution',
            'fields' => array(
              'contribution_id' => array(
                'name'       => 'id',
                'title'      => 'Payment No',
                'no_display' => FALSE,
                'required'   => TRUE,
              ),
              'contact_id'      => array(
                'name'       => 'contact_id',
                'title'      => 'Donor Name',
                'no_display' => FALSE,
                'required'   => TRUE,
              ),
              'receive_date'    => array(
                'name'       => 'receive_date',
                'title'      => 'Contribution Date',
                'no_display' => FALSE,
                'required'   => TRUE,
              ),
            ),
          ),
        'civicrm_financial_type' =>
          array(
            'dao'    => 'CRM_Financial_DAO_FinancialType',
            'fields' => array(
              'financial_type_id' => array(
                'name'       => 'id',
                'title'      => 'Financial Type No',
                'no_display' => TRUE,
                'required'   => TRUE,
              ),
            ),
          ),
        'civicrm_address'        =>
          array(
            'dao'      => 'CRM_Core_DAO_Address',
            'grouping' => 'contact-fields',
            'fields'   =>
              array(
                'street_address'    => NULL,
                'city'              => NULL,
                'state_province_id' => array('title' => ts('State/Province'),),
                'country_id'        => array('title' => ts('Country'),),
                'postal_code'       => NULL,
              ),
          ),
        'civicrm_line_item'      =>
          array(
            'dao'    => 'CRM_Price_DAO_LineItem',
            'fields' => array(
              'id'           => array(
                'name'       => 'id',
                'title'      => 'Line Item No',
                'no_display' => FALSE,
                'required'   => TRUE,
              ),
              'amount'       => array(
                'name'       => 'line_total',
                'title'      => 'Line Total',
                'no_display' => FALSE,
                'required'   => TRUE,
                'type'       => CRM_Utils_Type::T_MONEY
              ),
              'quantity'     => array(
                'name'       => 'qty',
                'title'      => 'Qty',
                'no_display' => FALSE,
                'required'   => TRUE,
                'type'       => CRM_Utils_Type::T_INT
              ),
              'entity_table' => array(
                'name'       => 'entity_table',
                'title'      => 'Item',
                'no_display' => FALSE,
                'required'   => TRUE,
              ),
              'label'        => array(
                'name'       => 'label',
                'title'      => 'Description',
                'no_display' => FALSE,
                'required'   => TRUE,
              ),
            ),
          )
      );

    parent::__construct();

    // set defaults
    if (is_array($this->_columns['civicrm_value_gift_aid_submission'])) {
      foreach (
        $this->_columns['civicrm_value_gift_aid_submission']['fields']
        as $field => $values
      ) {
        if (in_array($this->_columns['civicrm_value_gift_aid_submission']['fields'][$field]['name'],
          array('amount', 'gift_aid_amount'))) {
          unset($this->_columns['civicrm_value_gift_aid_submission']['fields'][$field]);
          continue;
        }
        $this->_columns['civicrm_value_gift_aid_submission']['fields'][$field]['default'] =
          TRUE;
      }
    }

    $this->_settings = CRM_Civigiftaid_Form_Admin::getSettings();
  }

  public function select() {
    $select = array();

    $this->_columnHeaders = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (CRM_Utils_Array::value('required', $field)
            || CRM_Utils_Array::value($fieldName, $this->_params['fields'])
          ) {
            if ($tableName == 'civicrm_address') {
              $this->_addressField = TRUE;
            }
            else {
              if ($tableName == 'civicrm_email') {
                $this->_emailField = TRUE;
              }
            }

            // only include statistics columns if set
            if (CRM_Utils_Array::value('statistics', $field)) {
              foreach ($field['statistics'] as $stat => $label) {
                switch (strtolower($stat)) {
                  case 'sum':
                    $select[] =
                      "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] =
                      $label;
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] =
                      $field['type'];
                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                    break;

                  case 'count':
                    $select[] =
                      "COUNT({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] =
                      $label;
                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                    break;

                  case 'avg':
                    $select[] =
                      "ROUND(AVG({$field['dbAlias']}),2) as {$tableName}_{$fieldName}_{$stat}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] =
                      $field['type'];
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] =
                      $label;
                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                    break;
                }
              }
            }
            else {
              $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
              $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] =
                $field['title'];
              $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] =
                CRM_Utils_Array::value('type', $field);
            }
          }
        }
      }
    }

    $this->_columnHeaders['civicrm_line_item_gift_aid_amount'] = array(
      'title' => 'Gift Aid Amount',
      'type'  => CRM_Utils_Type::T_MONEY
    );

    $this->reorderColumns();

    $this->_select = "SELECT " . implode(', ', $select) . " ";
  }

  public function from() {
    $this->_from = "
      FROM civicrm_entity_batch {$this->_aliases['civicrm_entity_batch']}
      INNER JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']}
      ON {$this->_aliases['civicrm_entity_batch']}.entity_table = 'civicrm_contribution'
        AND {$this->_aliases['civicrm_entity_batch']}.entity_id = {$this->_aliases['civicrm_contribution']}.id
      INNER JOIN civicrm_line_item {$this->_aliases['civicrm_line_item']}
      ON {$this->_aliases['civicrm_contribution']}.id = {$this->_aliases['civicrm_line_item']}.contribution_id
      INNER JOIN civicrm_financial_type {$this->_aliases['civicrm_financial_type']}
      ON {$this->_aliases['civicrm_line_item']}.financial_type_id = {$this->_aliases['civicrm_financial_type']}.id
      LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']}
      ON ({$this->_aliases['civicrm_contribution']}.contact_id = {$this->_aliases['civicrm_address']}.contact_id
        AND {$this->_aliases['civicrm_address']}.is_primary = 1 )";
  }

  public function where() {
    parent::where();

    if (empty($this->_where)) {
      $this->_where =
        "WHERE value_gift_aid_submission_civireport.amount IS NOT NULL";
    }
    else {
      $this->_where .= " AND value_gift_aid_submission_civireport.amount IS NOT NULL";
    }
  }

  public function statistics(&$rows) {
    $statistics = parent::statistics($rows);

    $totalAmount = 0;
    $totalGiftAidAmount = 0;

    foreach ($rows as $row) {
      $totalAmount += $row['civicrm_line_item_amount'];
      $totalGiftAidAmount += $row['civicrm_line_item_gift_aid_amount'];
    }

    $statistics['counts']['amount'] = array(
      'value' => $totalAmount,
      'title' => 'Total Amount',
      'type'  => CRM_Utils_Type::T_MONEY
    );
    $statistics['counts']['giftaid'] = array(
      'value' => $totalGiftAidAmount,
      'title' => 'Total Gift Aid Amount',
      'type'  => CRM_Utils_Type::T_MONEY
    );

    return $statistics;
  }

  public function postProcess() {
    parent::postProcess();
  }

  /**
   * Alter the rows for display
   *
   * @param array $rows
   */
  public function alterDisplay(&$rows) {
    $entryFound = FALSE;
    require_once 'CRM/Contact/DAO/Contact.php';
    foreach ($rows as $rowNum => $row) {
      // i.e. remove row from report if it has financial type ineligible for Gift Aid
      if (FALSE === $this->hasEligibleFinancialType($row)) {
        unset($rows[$rowNum]);
        continue;
      }

      // handle contribution status id
      if (array_key_exists('civicrm_contribution_contact_id', $row)) {
        if ($value = $row['civicrm_contribution_contact_id']) {
          $contact = new CRM_Contact_DAO_Contact();
          $contact->id = $value;
          $contact->find(TRUE);
          $rows[$rowNum]['civicrm_contribution_contact_id'] =
            $contact->display_name;
          $url = CRM_Utils_System::url("civicrm/contact/view",
            'reset=1&cid=' . $value,
            $this->_absoluteUrl);
          $rows[$rowNum]['civicrm_contribution_contact_id_link'] = $url;
          $rows[$rowNum]['civicrm_contribution_contact_id_hover'] =
            ts("View Contact Summary for this Contact.");
        }
        if (isset($row['civicrm_line_item_amount'])) {
          $rows[$rowNum]['civicrm_line_item_gift_aid_amount'] =
            CRM_Civigiftaid_Utils_Contribution::calculateGiftAidAmt(
              $row['civicrm_line_item_amount']
            );
        }
        if (!empty($row['civicrm_line_item_entity_table'])) {
          $rows[$rowNum]['civicrm_line_item_entity_table'] =
            CRM_Civigiftaid_Utils_Contribution::getLineItemName(
              $row['civicrm_line_item_entity_table']
            );
        }

        $entryFound = TRUE;
      }

      // handle State/Province Codes
      if (array_key_exists('civicrm_address_state_province_id', $row)) {
        if ($value = $row['civicrm_address_state_province_id']) {
          $rows[$rowNum]['civicrm_address_state_province_id'] = CRM_Core_PseudoConstant::stateProvince($value, FALSE);
        }
        $entryFound = TRUE;
      }

      // handle Country Codes
      if (array_key_exists('civicrm_address_country_id', $row)) {
        if ($value = $row['civicrm_address_country_id']) {
          $rows[$rowNum]['civicrm_address_country_id'] = CRM_Core_PseudoConstant::country($value, FALSE);
        }
        $entryFound = TRUE;
      }

      // skip looking further in rows, if first row itself doesn't
      // have the column we need
      if (!$entryFound) {
        break;
      }
    }
  }

  /**
   * Return whether a row has financial type eligible for Gift Aid (i.e. has financial type which was enabled as
   * eligible for Gift Aid, at the time the contribution was added to the batch).
   *
   * @param $row
   *
   * @return bool
   */
  private function hasEligibleFinancialType($row) {
    // Lazy cache for batches
    static $batches = array();

    $batchId = $row['civicrm_entity_batch_batch_id'];
    if (!isset($batches[$batchId])) {
      if (($batch = CRM_Civigiftaid_BAO_BatchSettings::findByBatchId($batchId)) instanceof CRM_Core_DAO) {
        $batchArr = $batch->toArray();
        $batchArr['financial_types_enabled'] = unserialize($batchArr['financial_types_enabled']);

        $batches[$batchId] = $batchArr;
      }
      else {
        $batches[$batchId] = NULL;
      }
    }

    if ($batches[$batchId] && !$batches[$batchId]['globally_enabled']) {
      if (!in_array($row['civicrm_financial_type_financial_type_id'], $batches[$batchId]['financial_types_enabled'])) {
        return FALSE;
      }
    }

    return TRUE;
  }

  private function reorderColumns() {
    $columnTitleOrder = array(
      'payment no',
      'line item no',
      'donor name',
      'item',
      'description',
      'contribution date',
      'street address',
      'city',
      'county',
      'country',
      'postal code',
      'eligible for gift aid?',
      'qty',
      'line total',
      'gift aid amount',
      'batch name'
    );

    $compare = function ($a, $b) use (&$columnTitleOrder) {
      $titleA = strtolower($a['title']);
      $titleB = strtolower($b['title']);

      $posA = array_search($titleA, $columnTitleOrder);
      $posB = array_search($titleB, $columnTitleOrder);

      if ($posA === FALSE) {
        $columnTitleOrder[] = $titleA;
      }
      if ($posB === FALSE) {
        $columnTitleOrder[] = $titleB;
      }

      if ($posA > $posB || $posA === FALSE) {
        return 1;
      }
      if ($posA < $posB || $posB === FALSE) {
        return -1;
      }

      return 0;
    };

    $orderedColumnHeaders = $this->_columnHeaders;
    uasort($orderedColumnHeaders, $compare);

    $this->_columnHeaders = $orderedColumnHeaders;
  }
}

