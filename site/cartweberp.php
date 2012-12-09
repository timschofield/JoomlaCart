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
$controller = JRequest::getVar('controller','cartcatalog' ); 
// echo $controller  . "=controller<br>";exit;
$controllerlist = " cartcatalog,cart,stockinformation,checkout,creditcard";
If(!strpos($controllerlist, $controller)){
	$controller = 'cartcatalog';
}
require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
JTable::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_cartweberp'.DS.'tables');
$classname  ='cartweberpcontroller' .  $controller;
$controller = new $classname( array('default_task' => 'display') );
$controller->execute( JRequest::getVar('task'));
$controller->redirect(); 
?>
