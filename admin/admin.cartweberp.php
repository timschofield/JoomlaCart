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
//-- No direct access
defined('_JEXEC') || die('=;)');
//-- Dev mode - internal use =;)
// define('ECR_DEV_MODE', 1);//@@DEBUG
// xdebug_break();
$post	= JRequest::get('post');
// echo '<pre>';var_dump($post , '<br><br> <b style="color:brown">post in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
If(array_key_exists('task',$post) AND ($post['task'] == 'usercustomer.add' OR $post['task'] == 'usercustomer.save' OR $post['task'] == 'usercustomer.apply')){
	$controller = 'usercustomer';
	$PeriodPosition = strpos($post['task'],".");
	// echo $PeriodPosition  . '=PeriodPosition   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
	$task = substr($post['task'],$PeriodPosition+1);
	// echo $task  . '=task   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
}
If(!isset($controller) AND array_key_exists('controller',$post)){
	$controller = $post['controller'];
	$controllerlist = " usercross,usercustomer,about,googlexml ";
	If(strlen($controller) > 0 AND !strpos($controllerlist, $controller)){
		$controller = 'usercross';
	}elseif(strlen($controller) == 0){
		$controller = 'usercross';
	}
}elseif(!isset($controller)){
	$controller = 'usercross';
}
require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
$classname  = 'cartweberpcontroller'.$controller;
// echo $controller  . '=controller   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
$controller = new $classname( array('default_task' => 'display') );
// $controller->execute( JRequest::getVar('task' ));
$controller->redirect(); 
// echo '<pre>';var_dump($controller , '<br><br> <b style="color:brown">controller in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
// echo $task  . '=task   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
If(isset($task)){
	$controller->execute($task);
}else{
	$controller->execute(JRequest::getCmd('task'));
}
?>
