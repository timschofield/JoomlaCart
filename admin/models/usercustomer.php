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
jimport('joomla.application.component.modelitem');
class cartweberpModelusercustomer extends JModelItem {
	var $_data = null;

	var $_total = null;

	var $_pagination = null;

	var $_table_prefix = null;


	function __construct() {
		parent::__construct();

		global $mainframe, $context;
		$this->_table_prefix = '#__cart_';

	}

	function getData() {
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = (array) $this->_getList($query);
		}

		return $this->_data;
	}


	function _buildQuery() {
		$post = JRequest::get('request');
		Foreach ($post as $key => $value) {
			If (array_key_exists('make', $post) AND (!isset($Make))) {
				$Make = $post['make'];
			}
			If (strpos($key, "_x") > 0 AND (!isset($Make))) {
				$Make = substr($key, 0, strpos($key, "_x"));
			}
		}
		$prefix = "#__";
		$query = " SELECT * FROM " . $prefix . "users order by username";

		return $query;
	}


	function createUserCustomer() {
		$post = JRequest::get('post');
		// echo '<pre>';var_dump($post , '<br><br> <b style="color:brown">post in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		$data['customer'] = $post['Customer'];
		$data['user'] = $post['User'];
		// echo '<pre>';var_dump($data , '<br><br> <b style="color:brown">data in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		$this->deleteUserCustomer($data['user']);
		$row = $this->getTable();
		if (!$row->bind($data)) {
			echo $this->setError($this->_db->getErrorMsg());
			return false;
		}
		if (!$row->store()) {
			echo $this->setError($this->_db->getErrorMsg());
			return false;
		}
		return TRUE;
	}
	function deleteUserCustomer($User) {
		$prefix = "#__cart_";
		$query = 'DELETE FROM ' . $prefix . 'usercustomer WHERE user = "' . $User . '"';
		$errorMessage = 'Error Code-UCM' . __LINE__ . ' Unable to delete user - customer records.';
		$Customers = modCARTwebERPHelper::getDelete($query, 'Joomla', $errorMessage, NULL, 0);
		return;
	}
	function getCustomers() {
		$weberp = modCARTwebERPHelper::getwebERP();
		$query = "SELECT name,debtorno FROM " . $weberp['database'] . ".debtorsmaster";
		$errorMessage = 'Error Code-UCM' . __LINE__ . ' Unable to get customer names and codes.';
		$Customers = modCARTwebERPHelper::getRowList($query, 'webERP', $errorMessage, NULL, 0);
		return $Customers;
	}
	function getBranches() {
		$weberp = modCARTwebERPHelper::getwebERP();
		$dbw = JFactory::getDBO();
		$dbw = JDatabase::getInstance($weberp);
		$query = "SELECT brname,debtorno FROM " . $weberp['database'] . ".custbranch";
		$errorMessage = 'Error Code-UCM' . __LINE__ . ' Unable to get branceh names and codes.';
		$Branches = modCARTwebERPHelper::getRowList($query, 'webERP', $errorMessage);

		return $Branches;
	}
	public function getTable($type = 'UserCustomer', $prefix = 'UserCustomerTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
}
?>