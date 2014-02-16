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

class cartweberpViewusercross extends JView
{
	function display($tpl = null)
	{
		$this->items				= $this->get( 'Data');
		If(count($this->items) > 0){
			$this->customername	=& $this->get( 'CustomerName');
		}
		$this->pagination = $this->get( 'Pagination' );
		$this->state		=  $this->get('State');
    	$this->user			= JFactory::getUser();
		$this->username	= $this->get( 'UserName');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
   	$this->addToolBar();
		parent::display($tpl);
  	}
  	protected function addToolBar()
	{
		$doc = JFactory::getDocument();
		$style = " .icon-48-cartweberp {background-image:url(components/com_cartweberp/assets/images/cartweberp.png); no-repeat; }";
		$doc->addStyleDeclaration( $style );

  	 	JToolBarHelper::title(JText::_( 'USER-CUSTOMER_MANAGER' ),'cartweberp.png');
    	JToolBarHelper::addNew('usercustomer.add');
		JToolBarHelper::deleteList('usercross.delete');
		JToolBarHelper::preferences('com_cartweberp', '250');
		JToolBarHelper::help( 'CARTwebERP.usercross',true );
	}
}
?>
