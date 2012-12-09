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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class Tableparameters extends JTable
{
	var $id 						= 0;
	var $make 					= null;
	var $makename				= null;
	var $model 					= null;
	var $modelname 			= null;
	var $category 				= null;
	var $year 					= null;
	var $hardwareaddress 	= null;
	var $stockid 				= null;
	var $params 				= null;
	

	function Tableparameters(& $db){	
		//initialize class property
	  $this->_table_prefix = '#__cart_';
			
		parent::__construct($this->_table_prefix.'parameters', 'id', $db);		
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
}
?>
