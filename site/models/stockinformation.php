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

jimport('joomla.application.component.model');

class cartweberpModelstockinformation extends JModel
{
	var $_data = null;

	var $_total = null;

	var $_pagination = null;

	var $_table_prefix = null;


	function __construct()
	{
		parent::__construct();

		global $mainframe, $context,$limitstart,$limit;
		$AddToCart = False;
		$post	= JRequest::get('post');
		If(array_key_exists('QuantityToAdd',$post)){			
			$PartsOnPage =& $post['QuantityToAdd'];
			Foreach($PartsOnPage as $Part=>$Quantity){
				If(($Quantity > .01 OR $Quantity < .01) AND $AddToCart==False){
					$AddToCart = True;				
				}
			}
		}
		If($AddToCart==1){
			$this->AddPartsToCart($PartsOnPage);
		}
	  	$this->_table_prefix = '#__cart_';			
	  	$limit		= 10;
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);	
	}

	function getData()
	{
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();			
			$errorMessage = 'Error Code-SM' .  __LINE__  . ' Unable to select from the stock file. ';		
			$this->_data=&modwebERPHelper::getRowArray($query,'webERP',$errorMessage);
			$totalitems = count($this->_data);
			$query = $query . " LIMIT " . $this->getState('limitstart') . ", " .  $this->getState('limit');
			$errorMessage = 'Error Code-SM' .  __LINE__  . ' Unable to select from the stock file limited. ';		
			$this->_data=&modwebERPHelper::getRowList($query,'webERP',$errorMessage);
			
			
			// $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			//Found no part numbers check for description
		}
		return $this->_data;
	}
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	function getTotal()
	{
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	function _buildQuery()
	{
		global $weberp;
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();        
		}
		$post			= JRequest::get('request');
		$StockID 	= $post['stockid'];
    	$query 		= 	"SELECT 	stockid,description,longdescription,categoryid,appendfile,units
			 				FROM " . $weberp['database'] . ".stockmaster
			 			  WHERE stockid ='" . $StockID . "'";  
		return $query;	
	}
	function getModels(){
		$post	= JRequest::get('request');
		$modellist = array();
		$StockID = $post['stockid'];
		$this->_table_prefix = '#__cart_';
		$query = "SELECT  " . $this->_table_prefix . "partcrossmodel.id, 
								" . $this->_table_prefix . "partcrossmodel.partnumber,
								" . $this->_table_prefix . "partcrossmodel.makercarid,
								" . $this->_table_prefix . "partcrossmodel.maker,
								" . $this->_table_prefix . "partcrossmodel.beginyear,
								" . $this->_table_prefix . "partcrossmodel.endyear,
  								" . $this->_table_prefix . "partcrossmodel.beginmonth,
    							" . $this->_table_prefix . "partcrossmodel.endmonth,
      						" . $this->_table_prefix . "models.model,
      						" . $this->_table_prefix . "models.make,
      						" . $this->_table_prefix . "models.series,
      						" . $this->_table_prefix . "models.body,
      						" . $this->_table_prefix . "models.engine
								FROM " . $this->_table_prefix . "partcrossmodel, " . $this->_table_prefix . "models  
		             	  WHERE " . $this->_table_prefix . "partcrossmodel.partnumber = '" . $StockID . "' AND
		                       " . $this->_table_prefix . "partcrossmodel.makercarid= " . $this->_table_prefix . "models.id
		                     ORDER BY " . $this->_table_prefix . "models.model";
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' No PartCrossModel Records Found <span style="color:brown">OK if not using Year - Make - Model Selection</span>';
		If($modellist =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage,NULL,0)){		
			Foreach($modellist as $Count=>$ModelRow){
				$modellist[$Count]['modelname'] = $this->getModelDescription($modellist[$Count]['makercarid']);
				$modellist[$Count]['makername'] = $this->getMakerName($modellist[$Count]['maker']);
			}
		}
		return $modellist;
	}
	function getModelDescription($modelid){
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
	  	$modeldesc = "";
		$query = "SELECT model,series,body,engine FROM " . $this->_table_prefix . "models WHERE id = " . $modelid ;
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' No Model Record Found';
		If($row =& modwebERPHelper::getRowArray($query,'Joomla',$errorMessage)){
	  		$modeldesc = $row['model'] . " " . $row['series'] . " " . $row['body'] . " " . $row['engine'];
	  	}
		return $modeldesc;
	}	
	function getMakerName($make){
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT manufacturer FROM " . $this->_table_prefix . "manufacturer WHERE id = " . $make;
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' Manufacturer not found';
		$makename = modwebERPHelper::getResult($query,'Joomla',$errorMessage);	
		return $makename;
	}	
	function getModelEndYear($modelid){
		$db = &JFactory::getDBO();
		$modelendyear='';
		$this->_table_prefix = '#__cart_';
		$query = "SELECT endyear FROM " . $this->_table_prefix . "models WHERE id = " . $modelid ;
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' Model End Year not found';
		$modelendyear = modwebERPHelper::getResult($query,'Joomla',$errorMessage);
		return $modelendyear;
	}
	function getModelBeginYear($modelid){
		$db = &JFactory::getDBO();
		$modelbeginyear = '';
		$this->_table_prefix = '#__cart_';
		$query = "SELECT beginyear FROM " . $this->_table_prefix . "models WHERE id = " . $modelid ;
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' Model Begin Year not found';
		$modelbeginyear = modwebERPHelper::getResult($query,'Joomla',$errorMessage);	  	
		return $modelbeginyear;
	}
	function getCart(){
		$HardwareAddress = modwebERPHelper::_getCartID();		
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'";
	  	$errorMessage = 'Error Code-SM' .  __LINE__  . ' Unable to select from orders file. <span style="color:brown">OK if nothing in cart yet</span>';
		If(!$cart = modwebERPHelper::getRowList($query,'Joomla',$errorMessage,'partnumber',0)){
			$cart = array();
		}
		return $cart;
	}
	function CheckStartDate(){
		global $weberp;		
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}
		$query	 = "SELECT startdate
							FROM " . $weberp['database'] . ".prices LIMIT 1";
		// echo $query  . "=query 1636<br>";
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' Unable to select from prices file. ';			
		If($collums = modwebERPHelper::getRowArray($query,'webERP',$errorMessage)){
			// echo '<pre>';var_dump($collums , '<br><br> <b style="color:brown"> collums  </b><br><br>');echo '</pre>';
			If(array_key_exists("startdate", $collums)){
				$CheckStartDate = 1;
				$query = "UPDATE  " . $weberp['database'] . ".prices SET enddate = '2050-12-31' WHERE enddate = '0000-00-00'";
				$errorMessage = 'Error Code-SM' .  __LINE__  . ' Unable to update end dates in prices file. ';
				$this->_data =& modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage);
			}else{
				$CheckStartDate = 0;
			}
		}else{
				$CheckStartDate = 0;
		}	
		return $CheckStartDate;	
	}	
	
	function getPrice(){
		global $weberp;
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}
		$post	= JRequest::get('request');
		$StockID = $post['stockid'];
		$salestype = $this->getSalesType();	
		If(strlen(trim($salestype)) == 0 ){
			$salestype = $weberp['listsalestype'];
		}
		If(cartweberpModelstockinformation::CheckStartDate()){
			$WherePricelist = " AND NOW() BETWEEN startdate AND enddate ";
		}else{
			$WherePricelist = '';
		}	
		$price = '';
		IF(strlen(trim($salestype)) > 0){
			$query = "SELECT 	price
							FROM " . $weberp['database'] . ".prices 
				        WHERE 	stockid ='" . $StockID . "' AND
				        			typeabbrev='" . $salestype . "'" . $WherePricelist;
			$errorMessage = 'Error Code-SM' .  __LINE__  . ' Price not found';
			if(!$price = modwebERPHelper::getResult($query,'webERP',$errorMessage,NULL,0))
			{				
				$price 		= (float)0;
			}
		}
		// echo $query  . '=query   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
		return $price;
	}	
	function getCorePrice(){
		global $weberp;
		$post	= JRequest::get('request');
		$StockID = $post['stockid'];
		$salestype = $this->getSalesType();	
		$CoreStockID = $this->getCoreStockID($StockID);
		$price = '';
		IF(strlen(trim($salestype)) > 0 AND strlen(trim($CoreStockID)) > 0){
			$query = "SELECT 	price
							FROM " . $weberp['database'] . ".prices 
				        WHERE 	stockid ='" . $CoreStockID . "' AND
				        			typeabbrev='" . $salestype . "'";
			$errorMessage = 'Error Code-SM' .  __LINE__  . ' Price not found';
			if(!$price = modwebERPHelper::getResult($query,'webERP',$errorMessage,NULL,0))
			{				
				$price 		= (float)0;
			}			
		}
		return $price;
	}
	function getCoreStockID($StockID){
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$AssociatedCount = 0;
		$AssociatedParts=array();
		// get associated substitute parts for items
		$query = "SELECT 	associatedpart,
								associationtype
						FROM " . $this->_table_prefix . "associatedparts 
		       	  WHERE 	partnumber ='" . $StockID . "' AND
		       	  			associationtype = 33 ";		       	  			
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' No Associated Part Record for Core Not Found <span style="color:brown">OK if not using core deposits</span>';
		$CoreStockID = modwebERPHelper::getRowArray($query,'Joomla',$errorMessage,NULL,0);
		return $CoreStockID;
	}
	function getSalesType(){
		global $weberp;		
		$user 		=& JFactory::getUser();
		$salestype = null;
		If(strlen(trim($user->username)) > 0){
			
			$username = trim($user->username);
			$this->_table_prefix = '#__cart_';
			$query = "SELECT customer FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $username . "'" ;
			$errorMessage = 'Error Code-SM' .  __LINE__  . ' No User Customer Cross Reference Found. <span style="color:brown">OK if user is not assigned to a customer</span>';
	  		If($CustomerCode = modwebERPHelper::getResult($query,'Joomla',$errorMessage,NULL,0)){			
				If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
					$weberp	= modwebERPHelper::getwebERP();
				}
				$query = "SELECT 	salestype
								FROM " . $weberp['database'] . ".debtorsmaster 
					        WHERE 	debtorno ='" . $CustomerCode . "'";	
				$errorMessage = 'Error Code-SM' .  __LINE__  . ' No User Sales Type found for Customer Found';
				$salestype = modwebERPHelper::getResult($query,'webERP',$errorMessage,NULL,0);	
			}
		}
		return $salestype;
	}
	function getQuantityOnHand(){
		global $weberp;
		$post	= JRequest::get('request');
		$StockID = $post['stockid'];
		$query = "SELECT 	SUM(quantity) as onhand
						FROM " . $weberp['database'] . ".locstock 
			        WHERE 	stockid ='" . $StockID . "'";
		$result = mysql_query($query) or die("$query Query in getPrice failed : " . mysql_error());
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' No Quantity On Hand Found for Part <span style="color:brown">OK if items with zero on hand quantities are allowed</span>';
		$quantityonhand = modwebERPHelper::getResult($query,'webERP',$errorMessage,NULL,0);			
 		$Demand = 0;
 		$query = "SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
             							 FROM " . $weberp['database'] . ".salesorderdetails 
             							WHERE salesorderdetails.completed=0
             							  AND salesorderdetails.stkcode='" . $StockID . "'";
      $errorMessage = 'Error Code-SM' .  __LINE__  . ' No Quantity On Demand Found for Part <span style="color:brown">OK if there are no open orders for this item</span>';
		$Demand = modwebERPHelper::getResult($query,'webERP',$errorMessage,NULL,0);	 		
 		If($quantityonhand > $Demand){
 			$quantityonhand = (float)($quantityonhand - $Demand);
 		}else{
 			$quantityonhand = 0;
 		}	
		return $quantityonhand;
	}
	function AddPartsToCart($PartsOnPage){
		
		global $parts;
		
		$post	= JRequest::get('post');
		$price =& $post['Price'];
		$choices['hardwareaddress'] = modwebERPHelper::_getCartID();
		$HardwareAddress = $choices['hardwareaddress'];
		$parts = "'";
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'" ;	
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' No Cart Records Found';
		If($rows =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage)){
			foreach ( $rows as $row ) {
				$quantity 	= $row['quantityordered'];
				If($quantity > .1 OR $quantity < .1){
					$id 			= $row['id'];
					$part 		= $row['partnumber'];
					$PartOrdersID[$part] = $id;
					$PartOrdersQuantity[$part] = $quantity;
					$parts = $parts . $part . "', '";
				}
			}
		}
		$parts = substr($parts,0,strrpos($parts, ","));
		If(!isset($PartOrdersID)){
			$PartOrdersID = array();
		}		
		JTable::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_cartweberp'.DS.'tables');
		$row =& $this->getTable('orders');
		If(!isset($PartOrdersQuantity)){
			$PartOrdersQuantity = array();
		}
		foreach($PartsOnPage as $Part=>$Quantity){			
			If($Quantity > .1 OR $Quantity < -.1){
				If(array_key_exists($Part,$PartOrdersQuantity)){
					$order['id'] 	= $PartOrdersID[$Part];
					$order['quantityordered'] 	= $PartOrdersQuantity[$Part] + $Quantity;
				}else{
					$order['id'] 	= 0;
					$order['quantityordered'] 	= $Quantity;
				}
				$order['partnumber']			= $Part;
				$order['hardwareaddress'] 	= $HardwareAddress;
				$order['price']				= $price[$Part];
				If(array_key_exists($Part,$PartOrdersID)){
					$order['id'] 		= $PartOrdersID[$Part];
				}else{
					$order['id'] 		= 0;
				}   
				$order['date'] = date("Y-m-d G.i:s<br>", time());
				if (!$row->bind($order)) {
					$this->setError($this->_db->getErrorMsg());
					echo $this->_db->getErrorMsg(). "= this->_db->getErrorMsg()<BR>";
					return false;
				}
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					echo $this->_db->getErrorMsg(). "= this->_db->getErrorMsg()<BR>";
					return false;
				} 
			}
		}
		return true;
	}	
	function getDebtorInformation(){
		global $weberp;
		$user 		=& JFactory::getUser();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT customer FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->username . "'" ;
		$errorMessage = 'Error Code-M' .  __LINE__  . ' No User Customer Cross Records Found';
	  	$CustomerCode =& modwebERPHelper::getResult($query,'Joomla',$errorMessage);
	  	If(isset($CustomerCode)){	  			
			If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
				$weberp	= modwebERPHelper::getwebERP();
			}	  
			$query = "SELECT 	*
							FROM " . $weberp['database'] . ".debtorsmaster,
								  " . $weberp['database'] . ".custbranch
				        WHERE 	debtorsmaster.debtorno ='" . $CustomerCode . "' AND 
				        			custbranch.debtorno = debtorsmaster.debtorno";
			$errorMessage = 'Error Code-M' .  __LINE__  . ' No User Customer Cross Records Found';
			$row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage);
		}else{
		   $row = array();
		}
		return $row;
	}	
	function getAssociatedPartsDescription($stockid){
		global $weberp;  			
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}	  
		$query = "SELECT 	description
						FROM " . $weberp['database'] . ".stockmaster
			        WHERE 	stockid ='" . $stockid . "' ";			        
		$result = mysql_query($query) or die($query . " failed : " . mysql_error());
		$errorMessage = 'Error Code-M' .  __LINE__  . ' Description for Stock Item not found';
		$Description = modwebERPHelper::getResult($query,'webERP',$errorMessage);
		return $Description;
	}
	function getAssociatedParts($item){
		$mainframe =& JFactory::getApplication();
		If($mainframe->getUserState( 'carmodel')){
			$SearchModel = "AND
		       	  			model = " . $mainframe->getUserState( 'carmodel') ;
		}else{
			$SearchModel = "";
		}
		$this->_table_prefix = '#__cart_';
		$AssociatedCount = 0;
		$AssociatedParts=array();
		// get associated substitute parts for items
		$query = "SELECT 	id,
								partnumber,
								associatedpart,
								associationtype
						FROM " . $this->_table_prefix . "associatedparts 
		       	  WHERE 	partnumber ='" . $item . "' AND
		       	  			associatedpart <> '" . $item . "' " . $SearchModel . " 
		       	  ORDER BY associationtype,associatedpart";		
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' No Associated Part were found <span style="color:brown"> OK if not using Associated Parts or no associated parts assigned to this part</span>';
		IF($rows =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage,NULL,0)){
			foreach($rows as $row){
				$ID 													= $row['id'];
				$AssociatedParts[$ID]['partnumber']	 		= $row['partnumber'];
				$AssociatedParts[$ID]['associatedpart']	= $row['associatedpart'];
				$AssociatedParts[$ID]['associationtype'] 	= $row['associationtype'];
				$AssociatedParts[$ID]['description']	 	= cartweberpModelstockinformation::getAssociatedPartsDescription($AssociatedParts[$ID]['partnumber']);				
			}
		}
		return $AssociatedParts;
	}	
	function getAssociationTypes(){
		$this->_table_prefix = '#__cart_';
		// get associated substitute parts for items
		$query = "SELECT id,
								associationtype
						FROM " . $this->_table_prefix . "associationtype";	
		$errorMessage = 'Error Code-SM' .  __LINE__  . ' No Association Type Records Found <span style="color:brown">OK if not using Associated Parts</span>';
		IF(!$AssociationTypes =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage,'id',0)){
			$AssociationTypes=array();
		}				
		return $AssociationTypes;
	}	
}	
?>

  		