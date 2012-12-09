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

class cartweberpViewcreditcard extends JView
{
	function __construct( $config = array())
	{	 
    	global $mainframe, $context; 
	 	$context = 'cartcatalog.list.';
 	 	parent::__construct( $config );
	} 
	function display($tpl = null)
	{		 
    	global $mainframe, $context;
		$document 			= & JFactory::getDocument();
		$document->setTitle( JText::_('Credit Card Display') );		
		$uri 					=& JFactory::getURI();
		$customer 			=& $this->get( 'Customer' );
		$countrycodearray =& $this->get( 'CountryCodeArray' );
		$statecodearray 	=& $this->get( 'StateCodeArray' );
		$this->assignRef('user',				JFactory::getUser());	
  		$this->assignRef('customer',			$customer);			
  		$this->assignRef('countrycodearray',$countrycodearray);
  		$this->assignRef('statecodearray',	$statecodearray);		
    	$this->assignRef('request_url',		$uri->toString());
		parent::display($tpl);
  	}
}
?>
