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

class cartweberpModelcheckout extends JModel
{
	var $_data = null;

	var $_total = null;

	var $_pagination = null;

	var $_table_prefix = null;


	function __construct()
	{
		parent::__construct();

		global $mainframe, $context,$limitstart,$limit;
		
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
			// echo $query  . '=query   in  checkout model Line #' . __LINE__ . '  <br>';			
			$errorMessage = 'Error Code-OM' .  __LINE__  . ' Unable to select from orders file. <span style="color:brown">OK if nothing in cart yet</span>';
			$this->_data = modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0);
		
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
		$mainframe =& JFactory::getApplication();
		$SalesOrderNumber = $mainframe->getUserState( "SalesOrderNumber" ) ;
		$weberp	= modwebERPHelper::getweberp();
		$query = "SELECT 	stkcode,quantity,unitprice
			 				FROM " . $weberp['database'] . ".salesorderdetails
			 			  WHERE orderno =" . $SalesOrderNumber ;
		return $query;			
	}
	function getCartTotal(){
		$HardwareAddress = modwebERPHelper::_getCartID();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT partnumber,price,quantityordered FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'" ;	                 
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Cart Records Found';
		$rows =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage);
		$carttotal['price'] 		= 0;
		$carttotal['cartcount'] = 0;
		$price=$this->getPrice();
		foreach ( $rows as $row ) {			
    		$carttotal['price'] 		= $carttotal['price'] + ($price[$row->partnumber] * $row->quantityordered);
    		$carttotal['cartcount'] = $carttotal['cartcount'] + 1;
    	}
	  	return $carttotal;
	}	
	function store($SO)
	{
		global $weberp;		
		If(!array_key_exists('database', $weberp) OR strlen(trim($weberp['database'])) == 0	){
			$weberp	= modwebERPHelper::getweberp();
		}
		$user 		=& JFactory::getUser();
		$username 	= $user->get('username'); 
		$query = "INSERT INTO " . $weberp['database'] . ".salesorders 
		                  SET 	orderno						=  "  . $SO['orderno'] . " ,
		                  		debtorno 					= '"  . $SO['debtorno'] . "',
									 	branchcode					= '"  . $SO['branchcode'] . "',
										customerref					= '"  . $SO['customerref'] . "',
										buyername					= '"  . $SO['buyername'] . "',
										comments						= '"  . $SO['comments'] . "',
										orddate						= '"  . $SO['orddate'] . "',
										ordertype					= '"  . $SO['ordertype'] . "',
										shipvia						= '"  . $SO['shipvia'] . "',
										deladd1						= '"  . $SO['deladd1'] . "',
										deladd2						= '"  . $SO['deladd2'] . "',
										deladd3						= '"  . $SO['deladd3'] . "',
										deladd4						= '"  . $SO['deladd4'] . "',
										deladd5						= '"  . $SO['deladd5'] . "',
										deladd6						= '"  . $SO['deladd6'] . "',
										contactphone				= '"  . $SO['contactphone'] . "',
										contactemail				= '"  . $SO['contactemail'] . "',
										deliverto					= '"  . $SO['deliverto'] . "',
										deliverblind				= '"  . $SO['deliverblind'] . "',
										freightcost					= '"  . $SO['freightcost'] . "',
										fromstkloc					= '"  . $SO['fromstkloc'] . "',
										deliverydate				= '"  . $SO['deliverydate'] . "',
										printedpackingslip		= '"  . $SO['printedpackingslip'] . "',
										datepackingslipprinted	= '"  . $SO['datepackingslipprinted'] . "',
										quotation					= '"  . $SO['quotation'] . "'";										
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' Sales order not added';
		If($result = modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage)){
	   	return true;
	   }else{
	   	return false;
	   }
	}
	function storedetail($SOD)
	{
		global $weberp, $SalesOrderNumber;	 
		$query = "INSERT INTO " . $weberp['database'] . ".salesorderdetails 
		                  SET 	orderlineno 			=  "  . $SOD['orderlineno']  . ",
		                      	orderno					=  "  . $SOD['orderno']  . ",
									 	stkcode					= '"  . $SOD['stkcode'] . "',
										qtyinvoiced				= '"  . $SOD['qtyinvoiced'] . "',
										unitprice				= '"  . $SOD['unitprice'] . "',
										quantity					= '"  . $SOD['quantity'] . "',
										estimate					= 0,
										discountpercent		= '"  . $SOD['discountpercent'] . "',
										actualdispatchdate	= '',
										completed				= 0,
										narrative				= '',
										itemdue					= '',
										poline					= ''";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' Sales order detail not added';
		// echo $query  . '=query   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';exit;
		If($result = modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage)){	
	   	return true;
	   }else{
	   	return false;
	   }
	}
	function receipt($post, $debtorno, $branchcode, $salestype, $orderno,$paymentAmount)
	{
		global $weberp, $mainframe;
		$checkouttype 	= $mainframe->getUserState( "CheckOutType" );
		$TransactionID = $post['PurchaseOrderNumber'];
		$mainframe =& JFactory::getApplication();
		// get General Ledger period number
		$query = "SELECT periodno, lastdate_in_period 
		            FROM " . $weberp['database'] . ".periods 
		         ORDER BY periodno";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Accounting Period Records Found';
		$rows =& modwebERPHelper::getRowList($query,'webERP',$errorMessage);
 		foreach($rows as $row){
			$PeriodEndDate = strtotime($row['lastdate_in_period']);
			$TodaysDate		= strtotime(date('Y-m-d'));
			If($TodaysDate < $PeriodEndDate ){
				$PeriodNumber = $row['periodno'];
			}
		}         
		$receiptno 		= $this->getReceiptNumber();
		If($mainframe->getUserState( "Payment_Amount")){
			$paymentAmount = $mainframe->getUserState( "Payment_Amount") ;
		}else{
			$RelayResponse = $mainframe->getUserState( "Authorization");
			$paymentAmount = $RelayResponse['x_amount'] ;
		}
		
		$query = "INSERT INTO " . $weberp['database'] . ".banktrans 
		                  SET 	type 					= 12,
									 	transno				=  "  . $receiptno  . ",
										bankact				= '"  . $weberp['bankaccount'] . "',
										ref					= '"  . $checkouttype . " " . $TransactionID . "',
										amountcleared		= 0,
										exrate				= 1,
										functionalexrate	= 1,
										transdate			= '"  . date('Y-m-d') . "',
										banktranstype		= 'Credit Card',
										amount				= '"  . $paymentAmount . "',
										currcode				= '"  . $weberp['currencycode'] . "'";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' Receipt not added to banktrans';
		If(!$result = modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage)){	
	   	return false;
	   }
		$negativepaymentamount = $paymentAmount*-1;
		$query = 'INSERT INTO 	 ' . $weberp['database'] . '.debtortrans (
										transno,
										type,
										debtorno,
										branchcode,
										trandate,
										prd,
										reference,
										tpe,
										order_,
										rate,
										ovamount,
										ovdiscount,
										invtext)
						VALUES ("' . $receiptno . '",
										12,
										"' . $debtorno     . '",
										"' . $branchcode   . '",
										"' . date('Y-m-d') . '",
										'  . $PeriodNumber . ',
										"' . $TransactionID . '",
										"' . $salestype    . '",
										'  . $orderno      . ',
										1,
										'  . $negativepaymentamount. ',
										0,
										"Auto Payment ' . $checkouttype . '"
		)';		 
	   $errorMessage = 'Error Code-OM' .  __LINE__  . ' Receipt not added to debtortrans';
		$result = modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage);
	   $query = 'INSERT INTO 	 ' . $weberp['database'] . '.gltrans (
										type,
										typeno,
										chequeno,
										trandate,
										periodno,
										account,
										narrative,
										amount,
										posted)
						VALUES (		12,
										"' . $receiptno     		. '",
										"' . $TransactionID   	. '",
										"' . date('Y-m-d') 		. '",
										'  . $PeriodNumber 		. ',
										"1100",
										"' . $checkouttype . " " . " " . $TransactionID . '",
										'  . $negativepaymentamount     . ',
										0)';		 
	   
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' G/L Transaction 1 not added';
		$result = modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage);
		$query = 'INSERT INTO 	 ' . $weberp['database'] . '.gltrans (
										type,
										typeno,
										chequeno,
										trandate,
										periodno,
										account,
										narrative,
										amount,
										posted)
						VALUES (		12,
										"' . $receiptno     				. '",
										"' . $TransactionID   			. '",
										"' . date('Y-m-d') 				. '",
										'  . $PeriodNumber 				. ',
										"' . $weberp['bankaccount'] 	. '",
										"' . $checkouttype . " " . " " . $TransactionID . '",
										'  . $paymentAmount      		. ',
										0)';		 
	   $errorMessage = 'Error Code-OM' .  __LINE__  . ' G/L Transaction 2 not added';
		$result = modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage);
		return true;
	}	
	function getReceiptNumber(){
		global $weberp, $ReceiptNumber;
		$query = "SELECT typeno FROM " . $weberp['database'] . ".systypes WHERE typeid = 12";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' Could not get receipt number';
		$row = modwebERPHelper::getResult($query,'webERP',$errorMessage);
		$receiptno = $row['typeno'] + 1;
		$query = "UPDATE " . $weberp['database'] . ".systypes SET typeno = " . ($receiptno) .  " WHERE typeid = 12";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' Could not update receipt number';
		$result = modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage);		
		return $receiptno;	
	}
	function getDebtorInformation($BranchEmail){
		global $weberp; $mainframe;
		$mainframe =& JFactory::getApplication('site');
		$mainframe->initialise();
		$user 		=& JFactory::getUser();
		$this->_table_prefix = '#__cart_';
		If(isset($user->username) and $user->username <>''){
			$query = "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->username . "'" ;
		}else{
			$query = "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . substr($BranchEmail,0,25) . "'" ;
		}
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No User-Customer cross reference Records Found';
		$CustomerCross =& modwebERPHelper::getRowArray($query,'Joomla',$errorMessage);
	  	If($mainframe->getUserState( "branch")){
	  		$BranchCode = $mainframe->getUserState( "branch");
	  	}elseif(isset($CustomerCross['customer'])){
	  		$BranchCode = $CustomerCross['customer']; 
	  	}
	  	If(isset($CustomerCross['customer'])){
	  		$CustomerCode = $CustomerCross['customer']; 	  	
			$query = "SELECT 	*
							FROM " . $weberp['database'] . ".debtorsmaster,
								  " . $weberp['database'] . ".custbranch
				        WHERE 	debtorsmaster.debtorno ='" . $CustomerCode . "' AND 
				        			custbranch.debtorno = '" . $CustomerCode . "' AND
				        			custbranch.branchcode = '" . $BranchCode . "'";
			$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Customer Records Found for user';
			$row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage);
		}else{
		   $row = array();
		}
		return $row;
	}
	function getSalesOrderNumber(){
		global $weberp, $SalesOrderNumber;
		// get next order number;
		$query = "SELECT typeno FROM " . $weberp['database'] . ".systypes WHERE typeid = 30";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' Sales order for this sales order not found';
		$row = modwebERPHelper::getResult($query,'webERP',$errorMessage);
		$orderno = $row['typeno'] + 1;
		$query = "UPDATE " . $weberp['database'] . ".systypes SET typeno = " . ($orderno) .  " WHERE typeid = 30";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' Sales order number could not be incremented';
		modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage);		
		$SalesOrderNumber = $orderno;
		return $orderno;	
	}	
	function getPrice(){
		global $weberp;
		$parts = "'";
		$HardwareAddress = modwebERPHelper::_getCartID();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'" ;
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Cart Records Found';
		If($rows =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage)){
			foreach ( $rows as $row ) {
				$part 		= $row->partnumber;
				$parts = $parts . $part . "', '";
			}
		}
		$parts = substr($parts,0,strrpos($parts, ","));
		$price = array();
		If(strlen(trim($parts)) > 0){
			// get sales type from customer record or parameter table
			$salestype = $this->getSalesType();
			$query = "SELECT 	price,stockid
							FROM " . $weberp['database'] . ".prices 
			        	  WHERE 	stockid IN(" . $parts . ") AND
			              		typeabbrev ='" . $salestype . "'";
			$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Price Records Found';
			$rows =& modwebERPHelper::getRowList($query,'webERP',$errorMessage);
			Foreach($rows as $row)
			{				
				$stockid 				= $row['stockid'];
				$price[$stockid] 		= (float)$row['price'];
			}
		}
		return $price;
	}	
		
	function getDescription(){
		global $weberp;
		$parts = "'";
		$HardwareAddress = trim(modwebERPHelper::_getCartID());
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Cart Records Found';
		If($rows =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage)){
			foreach ( $rows as $row ) {
				$part 		= $row['partnumber'];
				$parts = $parts . $part . "', '";
			}
		}
		$parts = substr($parts,0,strrpos($parts, ","));
		$description = array();
		If(strlen(trim($parts)) > 0){
			$query = "SELECT 	description,stockid
							FROM " . $weberp['database'] . ".stockmaster 
			        	  WHERE 	stockid IN(" . $parts . ") ";
			$errorMessage = 'Error Code-OM' .  __LINE__  . ' No stockmaster Records Found';
			$rows =& modwebERPHelper::getRowList($query,'webERP',$errorMessage);
			Foreach($rows as $row)
			{				
				$stockid 						= $row['stockid'];
				$description[$stockid] 		= $row['description'];
			}
		}
		return $description;
	}		
	function getSalesType(){	
		global $weberp;	
		$user 		=& JFactory::getUser();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->username . "'" ;
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No User-Customer cross reference Records Found';
		$CustomerCross =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage);
	  	If(array_key_exists(0,$CustomerCross)){
	  		$CustomerCode = $CustomerCross[0][2];
			// get sales type from customer record or parameter table
			// $salestype = "LI";
			$query = "SELECT 	salestype
							FROM " . $weberp['database'] . ".debtorsmaster 
			        	  WHERE 	debtorno ='" . $CustomerCode . "'";
			$errorMessage = 'Error Code-OM' .  __LINE__  . ' Sales type for customer not found';
			$rows = modwebERPHelper::getResult($query,'webERP',$errorMessage);
			Foreach($rows as $row)
			{				
				$salestype 		= $row['salestype'];
			}
	  	}else{
	  			$CustomerCode = '';
		}
		If(!isset($salestype)){
			$salestype = "LI";
		}
		return $salestype;
	}
	function getCustomer(){	
		global $weberp;	
		$user 		=& JFactory::getUser();
		If(isset($user)){
			$this->_table_prefix = '#__cart_';
			$query = "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->id . "'" ;
	  		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No User Customer Cross Records Found';
			$CustomerCross =& modwebERPHelper::getRowArray($query,'Joomla',$errorMessage);
	  		If(array_key_exists('customer',$CustomerCross)){
	  			$CustomerCode = $CustomerCross['customer'];
				$query = "SELECT 	name,debtorno
								FROM " . $weberp['database'] . ".debtorsmaster
				        	  WHERE 	debtorno ='" . $CustomerCode . "'";
				$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Customer Records Found for user - ' . $user->username;
				
				If($CustomerCross =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage)){				
					$CustomerName	= $CustomerCross['name'];
				}
			}else{
				$CustomerName	= '' ;
			}
		}else{
			$CustomerName	= '' ;
		}
		return $CustomerName;
	}
	function getDebtorInformationFromUser(){	
		global $weberp;	
		$user =& JFactory::getUser();
		$row 	= array();
		If(isset($user)){
			$this->_table_prefix = '#__cart_';
			$query = "SELECT customer FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->id . "'" ;
			// echo $query  . '=query   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
	  		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No User Customer Cross Records Found';
			If($CustomerCode =& modwebERPHelper::getResult($query,'Joomla',$errorMessage)){
				$query = "SELECT 	*
								FROM " . $weberp['database'] . ".debtorsmaster,
									  " . $weberp['database'] . ".custbranch
					        WHERE 	debtorsmaster.debtorno 	= '" . $CustomerCode . "' AND 
					        			custbranch.debtorno 		= '" . $CustomerCode . "' AND
					        			custbranch.branchcode 	= '" . $CustomerCode 	. "'";
				// echo $query  . '=query   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';exit;
				$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Customer Data Found for - ' . $CustomerCode;
				If($row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage)){				
					return $row;
				}
			}				
		}	
		echo '<pre>';var_dump($row , '<br><br> <b style="color:brown">row in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';exit;	
		return $row;
	}
	function getShipAddress($SalesOrderNumber){	
		global $weberp;
		$query = "SELECT 	buyername,deladd1,deladd2,deladd3,deladd4,deladd5,deladd6,contactemail,contactphone,debtorno,branchcode
						FROM " . $weberp['database'] . ".salesorders
		        	  WHERE 	orderno ='" . $SalesOrderNumber . "'";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Sales Order Record Found';
		If($row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage)){
			$ShipAddress['buyername']		= $row['buyername'];
			$ShipAddress['deladd1']			= $row['deladd1'];
			$ShipAddress['deladd2']			= $row['deladd2'];
			$ShipAddress['deladd3']			= $row['deladd3'];
			$ShipAddress['deladd4']			= $row['deladd4'];
			$ShipAddress['deladd5']			= $row['deladd5'];
			$ShipAddress['deladd6']			= $row['deladd6'];
			$ShipAddress['email']			= $row['contactemail'];
			$ShipAddress['phone']			= $row['contactphone'];
			$ShipAddress['debtorno']		= $row['debtorno'];
			$ShipAddress['branchcode']		= $row['branchcode'];
		}
		return $ShipAddress;
	}
	function getOneCorePrice($StockID){
		global $weberp;
		$salestype = $this->getSalesType();	
		$CoreStockID = $this->getCoreStockID($StockID);
		$price = '';
		IF(strlen(trim($salestype)) > 0){
			$query = "SELECT 	price
							FROM " . $weberp['database'] . ".prices 
				        WHERE 	stockid ='" . $CoreStockID . "' AND
				        			typeabbrev='" . $salestype . "'";
			$errorMessage = 'Error Code-OM' .  __LINE__  . ' Select price failed for Core Stock id - ' . $CoreStockID . ' Price List ' . $salestype;
			if($row = modwebERPHelper::getResult($query,'webERP',$errorMessage))
			{				
				$price 		= (float)$row['price'];
			}
		}
		return $price;
	}
	function getCorePrice($items){
		global $weberp;
		$coreprice = array();
		If(!isset($weberp) OR !array_key_exists('coresales',$weberp)){
			$weberp	= modwebERPHelper::getweberp();
		}
		If($weberp['coresales'] == 'Y'){
			$parts = "'";
			$salestype = cartweberpModelcheckout::getSalesType();
			$coreprice = array();
			If(count($items) > 0){
				foreach ( $items as $irow ) {
					If(array_key_exists('partnumber',$irow)){
						$partnumber = $irow['partnumber'];
					}
					If(array_key_exists('stkcode',$irow)){
						$partnumber = $irow['stkcode'];
					}
					$corepartnumber = cartweberpModelcheckout::getCoreStockID($partnumber);
					$query = "SELECT 	price
									FROM " . $weberp['database'] . ".prices 
					        	  WHERE 	stockid = '" . $corepartnumber . "'AND
					              		typeabbrev ='" . $salestype . "'";
					$errorMessage = 'Error Code-OM' .  __LINE__  . ' Could not find price for core partnumber ' . $corepartnumber . ' and price list ' . $salestype ;
					$coreprice[$partnumber] = modwebERPHelper::getResult($query,'webERP',$errorMessage);
				}	
			}
		}	
		return $coreprice;
	}	
	function getCoreStockID($StockID){
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$AssociatedCount = 0;
		$AssociatedParts=array();
		// get associated substitute parts for items
		$query = "SELECT 	associatedpart
						FROM " . $this->_table_prefix . "associatedparts 
		       	  WHERE 	partnumber ='" . $StockID . "' AND
		       	  			associationtype = 33 ";		       	  			
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Associated Parts Records Found';
		$CoreStockID =&  modwebERPHelper::getRowList($query,'webERP',$errorMessage);
		return $CoreStockID;
	}
	function getFreight(){
		global $weberp, $mainframe;
		$query = "SELECT 	*
						FROM " . $weberp['database'] . ".shippers";
		$errorMessage = 'Error Code-OM' .  __LINE__  . ' No Cart Records Found';
		$rows =& modwebERPHelper::getRowList($query,'webERP',$errorMessage);
		Foreach($rows as $row)
		{				
			$shipperid 				= $row['shipper_id'];
			$Freight[$shipperid] = $row['shippername'];
			$FreightSelected		= $mainframe->getUserState("FreightSelected");
			// set freight to first value as default incase the cart is not updated with new freightselected
			If($FreightSelected == NULL or $FreightSelected==''){
				foreach($Freight as $count=>$Shipper){
					$mainframe->setUserState( "FreightSelected", $Shipper) ;
					break;
				}
			}
		}
		return $Freight;
	}
}	
?>

  		