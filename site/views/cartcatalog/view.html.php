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
class cartweberpViewcartcatalog extends JView
{
	function __construct( $config = array())
	{	 
    	global $mainframe, $context,$totalitems; 
	 	$context = 'cartcatalog.list.';
 	 	parent::__construct( $config );
	} 
	function display($tpl = null)
	{		 
    	global $mainframe, $context;
    	
		$document 						= & JFactory::getDocument();
		$document->setTitle( JText::_('Part Display') );		
		$uri 								=& JFactory::getURI();	
		$items							=& $this->get('Data');
		$categorydescriptions		=& $this->get('CategoryDescriptions');
		$salescategorydescriptions	=& $this->get('SalesCategoryDescriptions');
		$choices							=& $this->get('MakeModelCategoryParameters');
		$pagination 					=& $this->get('Pagination' );
		If(count($items) > 0){
			$categories					=& $this->get('Category');
			$currencies					=& $this->get('Currencies');
			$listprice					=& $this->get('ListPrice');
			$kitprice					=& $this->get('KitPrice');
			$quantityonhand			=& $this->get('QuantityOnHand');
		}
		$customersalestype			=  $this->get('SalesType');
		$debotorcurrency				=  $this->get('DebtorCurrency');
		$cart								=& $this->get('Cart');	
		$freight 						=& $this->get( 'freight' );
		$destinationzipcode 			=& $this->get( 'destinationzipcode' );
    	$this->assignRef('choices',						$choices);	
    	$this->assignRef('categorydescriptions',		$categorydescriptions);	
    	$this->assignRef('salescategorydescriptions',$salescategorydescriptions);	
    	$this->assignRef('user',							JFactory::getUser());
  		$this->assignRef('items',							$items);  
		If(count($items) > 0){
    		$this->assignRef('pagination',				$pagination);
    		$this->assignRef('categories',				$categories);
    		// $this->assignRef('price',					$price);
    		$this->assignRef('currencies',				$currencies);
    		$this->assignRef('listprice',					$listprice);
    		$this->assignRef('kitprice',					$kitprice);
    		$this->assignRef('quantityonhand',			$quantityonhand);
    	}
    	$this->assignRef('customersalestype',			$customersalestype);
    	$this->assignRef('debotorcurrency',				$debotorcurrency);
    	$this->assignRef('cart',							$cart);
    	$this->assignRef('freight',						$freight);
    	$this->assignRef('destinationzipcode',			$destinationzipcode);
    	$this->assignRef('request_url',					$uri->toString());
		parent::display($tpl);
  	}  	


}
?>
