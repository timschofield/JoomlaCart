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

jimport( 'joomla.application.component.view' );

class cartweberpViewusercustomer extends JView
{
	function __construct( $config = array())
	{	 
    	global $mainframe, $context; 
 	 	parent::__construct( $config );
	} 
	function display($tpl = null)
	{		 
    	global $mainframe, $context;
		$document 		= & JFactory::getDocument();
   	$this->addToolBar();
		$uri					=& JFactory::getURI();
		$pagination 		=& $this->get( 'Pagination' );
		$users				=& $this->get( 'Data');
		$customers			=  $this->get( 'Customers');
		$branches			=  $this->get( 'Branches');
		// echo '<pre>';var_dump($branches , '<br><br> <b style="color:brown">branches in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
    	$this->assignRef('user',				JFactory::getUser());	
  		$this->assignRef('users',				$users); 			
  		$this->assignRef('usercustomer',		$usercustomer); 			
  		$this->assignRef('customers',			$customers); 	 			
  		$this->assignRef('branches',			$branches); 		
    	$this->assignRef('pagination',		$pagination);
    	$this->assignRef('request_url',		$uri->toString());
		parent::display($tpl);
  	}
  	protected function addToolBar() 
	{
		$doc =& JFactory::getDocument();
		$style = " .icon-48-cartweberp {background-image:url(components/com_cartweberp/assets/images/cartweberp.png); no-repeat; }";
		$doc->addStyleDeclaration( $style );

		JToolBarHelper::title(JText::_( 'User-Customer Cross Reference' ), 'cartweberp.png');
		JToolBarHelper::save('usercustomer.save');
		JToolBarHelper::apply('usercustomer.apply');
		JToolBarHelper::cancel('usercustomer.cancel');
	}
}
?>
