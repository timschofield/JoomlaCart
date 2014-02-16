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
jimport('joomla.application.component.controller');
$post = JRequest::get('post');
// echo '<pre>';var_dump($post , '<br><br> <b style="color:brown">post in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'weberp.php');
$weberp = modCARTwebERPHelper::getweberp();
// If(strlen(trim($weberp['host'])) == 0){
// header("Location: ".JURI::base() . "?option=com_config&controller=component&component=com_cartweberp" );
// }

class cartweberpControllerUsercross extends JController {
	public function display($cachable = false, $urlparams = false) {
		JRequest::setVar('view', 'usercross');
		JRequest::setVar('layout', 'default');
		$app = JFactory::getApplication();
		// echo "<BR>display - controller<BR>";
		parent::display();
	}

	function remove() {
		$app = JFactory::getApplication();
		$this->_table_prefix = '#__cart_';
		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid');
		// echo '<pre>';var_dump($cid , '<br><br> <b style="color:brown">cid in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		foreach ($cid as $count => $id) {
			$query = "DELETE FROM " . $this->_table_prefix . "usercustomer WHERE id='" . $id . "'";
			// echo $query  . '=   in  usercross controller Line #' . __LINE__ . '  <br>';
			$errorMessage = 'Error Code-UC' . __LINE__ . ' Unable to delete User Customer Cross Reference';
			If (modCARTwebERPHelper::getDelete($query, 'Joomla', $errorMessage, 1)) {
				JError::raiseWarning(500, JText::_("DELETED_CUSTOMER") . " " . $id);
			} else {
				JError::raiseWarning(500, JText::_("COULD_NOT_DELETE_CUSTOMER") . " " . $id);
			}
		}
		header("Location: " . JURI::base() . 'index.php?option=com_cartweberp&controller=usercross&view=usercross');
	}
}
?>