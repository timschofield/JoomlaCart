<?php
/**
 * @package CARTwebERP
 * @Joomla version 1.6
 * @copyright Copyright (C) 2011 Mo Kelly. All rights reserved.

 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.*
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.*

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class cartweberpModelusercross extends JModelList {

	var $_data = null;

	var $_total = null;

	var $_pagination = null;

	var $_table_prefix = null;

	function __construct($config = array()) {
		// global $mainframe, $context;
		// $mainframe = JFactory::getApplication();
		// $limit		= $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $mainframe->getCfg('list_limit'), 0);
		// $limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0 );

		// $this->setState('limit', $limit);
		// $this->setState('limitstart', $limitstart);
		parent::__construct();
	}

	function getData() {
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	function getTotal() {
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	protected function populateState($ordering = null, $direction = null) {
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// List state information.
		parent::populateState('a.id', 'asc');
	}
	function _buildQuery() {
		$this->_table_prefix = '#__cart_';
		$query = ' SELECT * FROM ' . $this->_table_prefix . 'usercustomer' . ' ORDER BY user';
		return $query;
	}
	function getCustomerList() {
		$this->_table_prefix = '#__cart_';
		$query = 'SELECT customer FROM ' . $this->_table_prefix . 'usercustomer';
		$errorMessage = 'Error Code-UM' . __LINE__ . ' Unable to get customer list.';
		$CustomerList = modCARTwebERPHelper::getCollumnArray($query, 'Joomla', $errorMessage);
		return $CustomerList;
	}
	function getCustomerName() {
		$weberp = modCARTwebERPHelper::getwebERP();
		$CustomerList = $this->getCustomerList();
		$customers = "'";
		Foreach ($CustomerList as $key => $customer) {
			$customers = $customers . $customer . "', '";
		}
		$customers = substr($customers, 0, strrpos($customers, ","));
		// echo $customers  . '=customers   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
		$query = "SELECT 	name,debtorno
							FROM " . $weberp['database'] . ".debtorsmaster
				        WHERE 	debtorno IN(" . $customers . ")";
		$errorMessage = 'Error Code-UM' . __LINE__ . ' Unable to get customer names.';
		$CustomerName = modCARTwebERPHelper::getRowList($query, 'webERP', $errorMessage, $index = 'debtorno', $publicMessage = 1);
		return $CustomerName;
	}
	function getUserName() {
		$this->_table_prefix = '#__';
		$query = "SELECT 	id,name
							FROM " . $this->_table_prefix . "users";
		$errorMessage = 'Error Code-UM' . __LINE__ . ' Unable to get user names.';
		$UserName = modCARTwebERPHelper::getRowList($query, 'Joomla', $errorMessage, 'id', 1);
		return $UserName;
	}
}
?>