<?php
/**
* @package CartwebERP
* @version 1.5
* @copyright Copyright (C) 2008 Mo Kelly. All rights reserved.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class cartweberpViewcart extends JView
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
		$mainframe = JFactory::getApplication();
		$post	= JRequest::get('post');
    	$Option 			=& JRequest::getVar('option');
		$params 			= JComponentHelper::getParams($Option);
		$coresales 		= $params->get( 'coresales');
		$document 		= & JFactory::getDocument();
		$document->setTitle( JText::_('Cart Display') );		
		$uri 				=& JFactory::getURI();
		$items			=& $this->get( 'Data');
		$mainframe->setUserState( "OutOfStatExemption",0);
		If(gettype($items) == 'array'){
			foreach($items as $RecordNumber=>$Record){
				foreach($Record as $FieldName=>$FieldValue){
					If($FieldName == 'partnumber'){
						$description[$RecordNumber] = $this->getPartDescription($FieldValue);
						// echo $description[$RecordNumber]  . "=description[$RecordNumber]<br>";
						If($mainframe->getUserState( "DestinationZipCode") or isset($post['DestinationZipCode'])){
							$SalesTaxRate[$RecordNumber]	=  cartweberpModelcart::getSalesTaxRate($FieldValue);	
						}
					}
				}
			}
			$carttotal				=& $this->get( 'CartTotal');
			$pagination 			=& $this->get( 'Pagination' );
			$freight 				=& $this->get( 'freight' );
			$destinationzipcode	=  $this->get( 'destinationzipcode' );
			$currencyrate			=  $this->get( 'CurrencyRate' );
			$FreightQuantity = cartweberpModelcart::getFreightQuantity($items);
			$TotalWeight = $FreightQuantity['Weight'];
			$TotalVolume = $FreightQuantity['Volume'];	
			If(array_key_exists('price',$carttotal)){
				$freightamount			=& cartweberpModelcart::getFrieghtAmount($items,$carttotal['price']);
			}else{			
				$freightamount	['Cost'] =0;
			}
			$price 					=& $this->get( 'Price' );
			If($coresales=='Y'){
				$coreprice			=& cartweberpModelcart::getCorePrice($price);
			}
		}
		$customer 				=& $this->get( 'Customer' );
		If(isset($customer['DebtorNo'])){			
			$branches	 			=& cartweberpModelcart::getBranches($customer['DebtorNo']);	
  			$this->assignRef('branches',				$branches);	
		}
		$debtorinfo				=& $this->get( 'DebtorInformation' );
		$this->assignRef('user',					JFactory::getUser());	
  		$this->assignRef('items',					$items); 		
		If(gettype($items) == 'array'){
	  		$this->assignRef('price',					$price); 		
	  		$this->assignRef('coreprice',				$coreprice); 		
	  		$this->assignRef('carttotal',				$carttotal); 	
	  		$this->assignRef('description',			$description); 
	  		$this->assignRef('pagination',			$pagination);
	    	$this->assignRef('freight',				$freight);
	    	$this->assignRef('destinationzipcode',	$destinationzipcode);
	    	$this->assignRef('currencyrate',			$currencyrate);
	    	$this->assignRef('salestaxrate',			$SalesTaxRate);
	    	$this->assignRef('freightamount',		$freightamount);
	  		$this->assignRef('totalweight',			$TotalWeight);
	  		$this->assignRef('totalvolume',			$TotalVolume);
	    	
	  	}
  		$this->assignRef('customer',				$customer);		
  		$this->assignRef('branches',				$branches);		
  		$this->assignRef('debtorinfo',			$debtorinfo);		
	   $this->assignRef('request_url',			$uri->toString());
		parent::display($tpl);
  	} 
  	
	function getPartDescription($stockid){
		$weberp	= modwebERPHelper::getweberp();
		$weberpdb = & JDatabase::getInstance( $weberp );
		$description = '';
		$query = "SELECT description FROM " . $weberp['database'] . ".stockmaster WHERE stockid = '" . $stockid . "'";
		$result = mysql_query($query) or die("$query Query failed : " . mysql_error());
		if($row = mysql_fetch_array($result))
		{
			$description = $row['description'];
		}
		return $description;
	}
}
?>
