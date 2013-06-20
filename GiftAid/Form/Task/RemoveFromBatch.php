<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.3                                                |
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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/Task.php';

/**
 * This class provides the functionality to delete a group of
 * contacts. This class provides functionality for the actual
 * addition of contacts to groups.
 */

require_once 'CRM/Utils/String.php';
 
class GiftAid_Form_Task_RemoveFromBatch extends CRM_Contribute_Form_Task {
	
	protected $_id     = null;
	
    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
	function preProcess(){

		parent::preProcess( );
		
        require_once 'GiftAid/Utils/Contribution.php';
		list( $total, $toRemove, $notInBatch, $alreadySubmited) = GiftAid_Utils_Contribution::_validationRemoveContributionFromBatch( $this->_contributionIds );
		
        $this->assign('selectedContributions', $total); 
		$this->assign('totalToRemoveContributions', count($toRemove));
        $this->assign('notInBatchContributions', count($notInBatch));
		$this->assign('alreadySubmitedContributions', count($alreadySubmited));

        dprint_r($contributionsAlreadySubmitedRows);

        $contributionsToRemoveRows = GiftAid_Utils_Contribution::getContributionDetails ( $toRemove );
        $this->assign('contributionsToRemoveRows', $contributionsToRemoveRows );
         

        $contributionsAlreadySubmitedRows = GiftAid_Utils_Contribution::getContributionDetails ( $alreadySubmited );

        $this->assign( 'contributionsAlreadySubmitedRows', $contributionsAlreadySubmitedRows );

        $contributionsNotInBatchRows = GiftAid_Utils_Contribution::getContributionDetails ( $notInBatch );
        $this->assign( 'contributionsNotInBatchRows', $contributionsNotInBatchRows );


        //$contributionNotInAnyBatch
    

        
	}
	
	
	
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
		$attributes	= CRM_Core_DAO::getAttribute( 'CRM_Batch_DAO_Batch' );
        $this->addDefaultButtons( ts('Rmove from batch') );

    }
   
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

		//$params = $this->controller->exportValues( );
		require_once 'GiftAid/Utils/Contribution.php';
        list( $total, $removed, $notRemoved ) = GiftAid_Utils_Contribution::removeContributionFromBatch( $this->_contributionIds );
        if ( $removed <= 0 ) {
            // rollback since there were no contributions added, and we might not want to keep an empty batch
            $transaction->rollback( );
            $status = ts('Could not removed contribution from batchess, as there were no valid contribution(s) to be removed.');
        } else {
            $transaction->commit( );
            $status = ts('Total Selected Contribution(s): %1', array(1 => $total));
            CRM_Core_Session::setStatus( $status );
            if ( $removed ) {
                $status = ts('Total Contribution(s) removed from batches: %1', array(1 => $removed));
            }
            if ( $notRemoved ) {
                $status = ts('Total Contribution(s) not removed from batches: %1', array(1 => $notRemoved));
            }
            CRM_Core_Session::setStatus( $status );

        }
	}//end of function
}
