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

class Tableweberpsalesorder extends JTable
{
	var $orderno						=	0;
	var $debtorno						= 	null;
	var $branchcode					=	null;
	var $customerref					=	null;
   var $buyername						=  null;
   var $comments						=  null;
   var $orddate						=  null;
	var $ordertype						=  null;
	var $shipvia						=  null;
	var $deladd1						=  null;
	var $deladd2						=  null;
	var $deladd3						=  null;
	var $deladd4						=  null;
	var $deladd5						=  null;
	var $deladd6						=  null;
	var $contactphone					=  null;
	var $contactemail					=  null;
	var $deliverto						=  null;
	var $deliverblind					=  null;
	var $freightcost					=  null;
	var $fromstkloc					=  null;
	var $deliverydate					=  null;
	var $printedpackingslip  		=  null;
	var $datepackingslipprinted	=  null;
	var $quotation						=  null;
	
	var $params = null;

	function Tableweberpsalesorder(& $db) {
	  	$db = & JFactory::getDBO();
		$option = JRequest::getVar('option');
		$cid = JRequest::getVar('cid');
		$params = JComponentHelper::getParams($option);
		$option['host'] 	  		= $params->get( 'host');
		$option['database'] 		= $params->get( 'database');
		$option['databaseuser'] = $params->get( 'user');
		$option['userpassword'] = $params->get( 'password');
		$option['driver']   		= 'mysql';        // Database driver name
		$ARDataBase 				= $params->get( 'database');
		//initialize class property
	  	$this->_table_prefix = $ARDataBase;
			
		parent::__construct($this->_table_prefix.'.salesorder', 'orderno', $db);
	}
	
	function bind($array, $ignore = '')
	{
		if (key_exists( 'params', $array ) && is_array( $array['params'] )) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	function check()
	{	
		// use this to check input
		return true;
	}
}
?>
