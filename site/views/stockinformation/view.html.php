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

class cartweberpViewStockinformation extends JView
{
	function __construct( $config = array())
	{	 
    	global $mainframe, $context; 
 	 	parent::__construct( $config );
	} 
	function display($tpl = null)
	{		 
    	global $mainframe, $context;
    	
		$post	= JRequest::get('request');
		$document 				= & JFactory::getDocument();
		$document->setTitle( JText::_('Stock Information') );		
		$uri 						=& JFactory::getURI();
		$items					=& $this->get('Data');
		$models					=& $this->get('Models');
		$cart						=& $this->get('Cart');
		$price					= $this->get('Price');
		$coreprice				= $this->get('CorePrice');
		$AssociatedParts		= cartweberpModelstockinformation::getAssociatedParts($items['0']["stockid"]);
		$AssociationTypes		= $this->get('AssociationTypes');
		$quantityonhand		= $this->get('QuantityOnHand');
		$i = 0;
		// $pagination 	=& $this->get( 'Pagination' );
		$this->assignRef('user',					JFactory::getUser());	
  		$this->assignRef('items',					$items); 		 			
  		$this->assignRef('make',					$make); 		 		
  		$this->assignRef('models',					$models); 	 		
  		$this->assignRef('beginyear',				$beginyear); 	 		
  		$this->assignRef('endyear',				$endyear); 		 		
  		$this->assignRef('cart',					$cart); 				
    	$this->assignRef('price',					$price);				
    	$this->assignRef('coreprice',				$coreprice);				
    	$this->assignRef('quantityonhand',		$quantityonhand);
    	$this->assignRef('associatedparts',		$AssociatedParts);	
    	$this->assignRef('associationtypes',	$AssociationTypes);		
    	// $this->assignRef('pagination',			$pagination);
    	$this->assignRef('request_url',			$uri->toString());
		parent::display($tpl);
  	}  	
	function getModelDescription($modelid){
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$db->setQuery( "SELECT * FROM " . $this->_table_prefix . "models WHERE id = '" . $modelid . "'" );	                 
		$rows = $db->loadObjectList();
  		foreach ( $rows as $row ) {
    		$modellist = $row->model;
    	}
	  	return $modellist;
	}
	function getwebERP(){
		$db 							=& JFactory::getDBO();
		$Option 						=& JRequest::getVar('option');
		$params 						= JComponentHelper::getParams($Option);
		$weberp['host'] 	  		= $params->get( 'host');
		$weberp['database'] 		= $params->get( 'database');
		$weberp['databaseuser'] = $params->get( 'user');
		$weberp['userpassword'] = $params->get( 'password');
		$weberp['driver']   		= 'mysql';        // Database driver name
		return $weberp;
	} 	
}
?>
