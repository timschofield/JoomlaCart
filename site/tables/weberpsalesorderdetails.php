<?php
/**
* @package CARTwebERP
* @Joomla version 1.5
* @copyright Copyright (C) 2010 Mo Kelly. All rights reserved.
   
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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: iInclude library dependencies
jimport('joomla.application.component.model');

class Tablesalesorderdetail extends JTable
{
	var orderlineno 			= 	0;
   var orderno					=  0;
   var stkcode					= 	null;
   var qtyinvoiced			= 	0;
   var unitprice				= 	0;
   var quantity				= 	0;
   var estimate				= 	0;
   var discountpercent		=  0;
   var actualdispatchdate	=  null;
	var completed				=  0;
   var narrative				=  null;
	var itemdue					=  null;
	var poline					=  0;
	var params					=  null;
	
ect Database connector object

	
	function Tablesalesorderdetail(& $db) {
	  	$db = & JFactory::getDBO();
		$option = JRequest::getVar('option');
		$cid = JRequest::getVar('cid');
		$params = JComponentHelper::getParams($option);
		$option['host'] 	  		= $params->get( 'host');
		$option['database'] 		= $params->get( 'database');
		$option['databaseuser'] = $params->get( 'user');
		$option['userpassword'] = $params->get( 'password');
		$option['wikipath'] 		= $params->get( 'wikipath');
		$option['driver']   		= 'mysql';        // Database driver name
		$ARDataBase 				= $params->get( 'database');
		//initialize class property
	  $this->_table_prefix = $ARDataBase;
			
		parent::__construct($this->_table_prefix.'.salesorderdetails', 'orderno', $db);
	}

	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/

	function bind($array, $ignore = '')
	{
		if (key_exists( 'params', $array ) && is_array( $array['params'] )) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	function check()
	{	
		// use this to check input
		return true;
	}
}
?>
