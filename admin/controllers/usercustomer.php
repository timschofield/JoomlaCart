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
require_once( JPATH_COMPONENT . DS . 'models' .DS.'weberp.php' );
class cartweberpcontrollerusercustomer extends JController
{
	function display()
	{
		JRequest::setVar('view', 'usercustomer');
      JRequest::setVar('layout', 'default');
      $post	= JRequest::get('post');
      // echo '<pre>';var_dump($post , '<br><br> <b style="color:brown">post in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		$app  =& JFactory::getApplication();
      // echo '<pre>';var_dump($post , '<br><br> <b style="color:brown">post in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';exit;
      If($post['task'] == "usercustomer.save"){
      	
      	If(!$this->save()){
      		$msg = 'User to Customer cross reference record could not be created';
	 			$this->setRedirect( "index.php?option=com_cartweberp&controller=usercross",$msg );
      	}
      }else{
			parent::display();
		}
	} 
	// function usercustomer.apply()
	// {	
	// 	$post	= JRequest::get('post');
	// 	$model = $this->getModel('usercustomer');
	// 	$model->createUserCustomer();
	// 	$this->setRedirect( "index.php?option=com_cartweberp&controller=usercustomer",$msg );
	// 	parent::display();
	// } 
	function save()
	{	
		$post	= JRequest::get('post');
		$app =& JFactory::getApplication();
	 	// echo '<pre>';var_dump($post , '<br><br> <b style="color:brown">post in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
      $model = $this->getModel('usercustomer');
		If($model->createUserCustomer()){
			$app->enqueueMessage(JText::_("Saved customer cross reference for " . $post['User'] . ' and ' . $post['Customer']));
			
		}else{
			$app->enqueueMessage(JText::_('User Customer Cross Reference could not created for ' . $post['User'] . ' and ' . $post['Customer']));
		}
	 	// $this->setRedirect( "index.php?option=com_cartweberp&controller=usercross&view=usercross",$msg );
	 	header("Location: ". JURI::base().'index.php?option=com_cartweberp&controller=usercross&view=usercross');
	}
	function apply()
	{	
		$post	= JRequest::get('post');
		$app =& JFactory::getApplication();
	 	// echo '<pre>';var_dump($post , '<br><br> <b style="color:brown">post in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';exit;
      $model = $this->getModel('usercustomer');
		If($model->createUserCustomer()){
			$app->enqueueMessage(JText::_("Saved customer cross reference for " . $post['User'] . ' and ' . $post['Customer']));
		}else{
			$app->enqueueMessage(JText::_('User Customer Cross Reference could not created for ' . $post['User'] . ' and ' . $post['Customer']));
		}
	 	parent::display();
	}
}	
?>
