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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT . DS . 'models' .DS.'helper.php' );
class cartweberpControllercreditcard extends JController
{
	function __construct( $default = array())
	{
		parent::__construct( $default );
		JRequest::setVar( 'view', 'creditcard' );
		JRequest::setVar( 'layout', 'default'  );
	}
	function display()
	{
		
		$app    =& JFactory::getApplication();
		$router =& $app->getRouter();
		$router->setVar( 'view', 'creditcard' );
		$router->setVar( 'model', 'creditcard' );
		parent::display();
	}	
}	
?>
