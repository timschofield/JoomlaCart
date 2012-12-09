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

class cartweberpModelgooglexml extends JModel
{     

	var $_data = null;

	var $_total = null;

	var $_pagination = null;

	var $_table_prefix = null;

	function __construct()
	{
		parent::__construct();

		global $mainframe, $context;
	  	$this->_table_prefix = '#__cart_';				
	}
	
	function getData()
	{
		$DataColumn = array();
		$filename = JPATH_SITE . DS . 'components' . DS . 'com_cartweberp' . DS. 'google.xml';
		If(file_exists($filename) AND $handle = fopen($filename, "r")){
			$count = 0;
			while ($data = fgets($handle, 1000)) {
				$DataColumn[$count]	= trim($data);
				$count = $count +1;
	      }
	   }
      return $DataColumn;
	}
	function getCompanyName() {
		$dbw 		=& JFactory::getDBO();
		$weberp	=& modwebERPHelper::getwebERP();
		$dbw 		=& JDatabase::getInstance( $weberp );
		$query = "SELECT 	coyname
 				      FROM " . $weberp['database'] . ".companies";	
 		$dbw->setQuery($query);	
		If(!$CompanyName = $dbw->loadResult()){
			return False;
		} 
 		return $CompanyName;
	}
}	
?>

