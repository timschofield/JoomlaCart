<?php
/**
* @package CartwebERP
* @version 1.5
* @copyright Copyright (C) 2008 Mo Kelly. All rights reserved.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class cartweberpModelcart extends JModel
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
		global $totalitems;
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query 	= $this->_buildQuery();
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' No Cart Records Found';
			$this->_data =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage,NULL,0);
			$totalitems = count($this->_data);
			$query = $query . " LIMIT " . $this->getState('limitstart') . ", " .  $this->getState('limit');
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' Unable to select from the cart records limited. ';		
			$this->_data=&modwebERPHelper::getRowList($query,'Joomla',$errorMessage,NULL,0);
		}
		// echo '<pre>';var_dump($this->_data , '<br><br> <b style="color:brown">this->_data   </b><br><br>');echo '</pre>';
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
		$HardwareAddress = modwebERPHelper::_getCartID();
		$this->_table_prefix = '#__cart_';	  	
		$query = "SELECT * FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'";	          
		return $query;		
	}
	function getCartTotal(){
		$HardwareAddress = modwebERPHelper::_getCartID();
		$carttotal = array();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT partnumber,price,quantityordered FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'" ;	                 
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' No Cart Records Found';
		If($rows  =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage,NULL,0)){
			$carttotal['price'] 		= 0;
			$carttotal['cartcount'] = 0;
			$price=$this->getPrice();
			foreach ( $rows as $row ) {
				If(isset($price[$row['partnumber']]))	{		
	    			$carttotal['price'] 		= $carttotal['price'] + ($price[$row['partnumber']] * $row['quantityordered']);
	    		}	
	    		$carttotal['cartcount'] = $carttotal['cartcount'] + 1;    		
	    	}
	    }
	  	return $carttotal;
	}	
	function store($SO)
	{
		global $weberp;
		$weberp	= modwebERPHelper::getweberp();
		$user 		=& JFactory::getUser();
		$username 	= $user->get('username'); 
		$query = "INSERT INTO " . $weberp['database'] . ".salesorders 
		                  SET 	orderno 						=  "  . $SO['orderno']  . ",
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
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' Sales order not added';
		$this->_data =& modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage);
		 
	   return true;
	}
	function storedetail($SOD)
	{
		global $weberp;
		$user 						=& JFactory::getUser();
		$username 					= $user->get('username'); 
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
										
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' Sales order detail not added';
		$this->_data =& modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage);
				 
	   return true;
	}
	function getFreight(){
		// get list of shippers
		global $weberp,$mainframe;
		
		$FreightSelected		= $mainframe->getUserState("FreightSelected");
		$query = "SELECT 	*
						FROM " . $weberp['database'] . ".shippers";
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' No Freight Records Found';
		$rows =& modwebERPHelper::getRowList($query,'webERP',$errorMessage);
		Foreach($rows as $row)
		{				
			$shipperid 				= $row['shipper_id'];
			$Freight[$shipperid] = $row['shippername'];
			// set freight to first value as default incase the cart is not updated with new freightselected
			If($FreightSelected == NULL or $FreightSelected==''){
				$mainframe->setUserState( "FreightSelected", $row['shipper_id']) ;
				$FreightSelected		= $row['shipper_id'];
			}
		}
		$mainframe->setUserState( "FreightSelectedName",$Freight[$FreightSelected]);
		return $Freight;
	}	
	function getDebtorInformation(){
		global $weberp;
		$row = array();
		$user 		=& JFactory::getUser();
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->id . "'" ;
	  	$errorMessage = 'Error Code-TM' .  __LINE__  . ' User-Customer cross reference record cound not be found <span style="color:brown">OK if user customer cross reference does not exist</span>';
		$CustomerCross = modwebERPHelper::getResult($query,'Joomla',$errorMessage,NULL,0);	
	  	If(isset($CustomerCross) AND strlen(trim($CustomerCross)) > 0){
			$query = "SELECT 	*
							FROM " . $weberp['database'] . ".debtorsmaster,
								  " . $weberp['database'] . ".custbranch
				        WHERE 	debtorsmaster.debtorno ='" . $CustomerCross . "' AND 
				        			custbranch.debtorno = debtorsmaster.debtorno";
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' Customer record cound not be found <span style="color:brown">OK if user customer cross reference does not exist</span>';
			$row = modwebERPHelper::getRowArray($query,'webERP',$errorMessage);	
	  	}else{
		   $row['debtorno'] = '';
		}
		return $row;
	}
	function getSalesOrderNumber(){
		// get next order number;
		global $weberp;
		$query = "SELECT typeno FROM " . $weberp['database'] . ".systypes WHERE typeid = 30";
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' No Cart Records Found';
		$row  =& modwebERPHelper::getRowList($query,'webERP',$errorMessage);
		$orderno = $row['typeno'] + 1;
		$query = "UPDATE " . $weberp['database'] . ".systypes SET typeno = " . ($orderno) .  " WHERE typeid = 30";
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' Sales order number not incremented';
		$this->_data =& modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage);	
	   return $orderno;	
	}	
	function getPrice(){
		global $weberp;
		$CurrencyRate = cartweberpModelcart::getCurrencyRate();
		$weberp	= modwebERPHelper::getweberp();
		$parts = "'";
		$HardwareAddress = modwebERPHelper::_getCartID();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'" ;
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' No Cart Records Found';
		If($rows =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage,NULL,0)){
			foreach ( $rows as $row ) {
				$part 		= $row['partnumber'];
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
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' Query in getPrice failed';
			If($rows =& modwebERPHelper::getRowList($query,'webERP',$errorMessage)){
				foreach($rows as $row)
				{				
					$stockid 				= $row['stockid'];
					$price[$stockid] 		= (float)$row['price']*$CurrencyRate;
				}
			}
		}
		return $price;
	}	
	function getCorePrice($price){
		global $weberp;
		$parts = "'";
		$salestype = cartweberpModelcart::getSalesType();
		$coreprice = array();
		foreach ( $price as $partnumber=>$amount ) {
			$corepartnumber = cartweberpModelcart::getCoreStockID($partnumber);
			$query = "SELECT 	price
							FROM " . $weberp['database'] . ".prices 
			        	  WHERE 	stockid = '" . $corepartnumber . "'AND
			              		typeabbrev ='" . $salestype . "'";
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' Query in getPrice failed';
			$rows =& modwebERPHelper::getRowList($query,'webERP',$errorMessage);
			Foreach($rows as $row)
			{				
				$coreprice[$partnumber] = (float)$row['price'];
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
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' Associated Part not found';
		$CoreStockID =& modwebERPHelper::getResult($query,'Joomla',$errorMessage);				
		return $CoreStockID;
	}	
	function getSalesType(){
		global $weberp;
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}		
		$user 		=& JFactory::getUser();	
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->id . "'" ;
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' No User Customer Cross Records Found <span style="color:brown">OK if user customer cross reference does not exist</span>';
		$CustomerCross =& modwebERPHelper::getRowArray($query,'Joomla',$errorMessage,NULL,0);
		// echo '<pre>';var_dump($CustomerCross , '<br><br> <b style="color:brown"> CustomerCross  </b><br><br>');echo '</pre>';
	  	If(gettype($CustomerCross) == 'array'){
	  		$CustomerCode = $CustomerCross["customer"];	
			// get sales type from customer record or parameter table
			// $salestype = "LI";			
			$query = "SELECT 	salestype
							FROM " . $weberp['database'] . ".debtorsmaster 
			        	  WHERE 	debtorno ='" . $CustomerCode . "'";
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' Debtormaster not found';
			$salestype =& modwebERPHelper::getResult($query,'webERP',$errorMessage);	
	  	}else{
	  			$CustomerCode = '';
		}
		If(!isset($salestype) or $salestype == ''){
			$salestype = $weberp['listsalestype'];
		}
		return $salestype;
	}
	function getCustomer(){		
		global $weberp;
		$user 		=& JFactory::getUser();
		If(isset($user)){
			$this->_table_prefix = '#__cart_';
			$query = "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->id . "'" ;
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' User Customer Cross not found <span style="color:brown">OK if user customer cross reference does not exist</span>';
	  		$CustomerCross =& modwebERPHelper::getRowArray($query,'Joomla',$errorMessage,NULL,0);
	  		If(isset($CustomerCross) AND gettype($CustomerCross) == 'array' AND array_key_exists("customer", $CustomerCross)){
	  			$CustomerCode = $CustomerCross["customer"];
	  			$Customer['DebtorNo'] = $CustomerCode;	
				$query = "SELECT 	name,debtorno,paymentterms,holdreason,creditlimit
								FROM " . $weberp['database'] . ".debtorsmaster
				        	  WHERE 	debtorno ='" . $CustomerCode . "'";
				$errorMessage = 'Error Code-TM' .  __LINE__  . ' Debtormaster not found';
				If($row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage)){				
					$Customer['Name']				= $row['name'];		
					$Customer['Terms']			= $row['paymentterms'];
					$Customer['CreditLimit']	= $row['creditlimit'];
					$Customer['DebtorNo']		= $row['debtorno'];
					$shrinkingstring = $weberp['allowedterms'];
					$i=0;
					While(strpos($shrinkingstring,",") > 0){
						$AllowedTerms[$i] 	= substr($shrinkingstring,0,strpos($shrinkingstring,","));
						$shrinkingstring 		= substr($shrinkingstring,strpos($shrinkingstring,",")+1);
						$i=$i+1;
					}
					$AllowedTerms[$i] 	= $shrinkingstring;
					$Customer['OpenAccount']=False;
					If(in_array($Customer['Terms'],$AllowedTerms)){ 
						// Check to see if we use credit limits
						$SQL = "SELECT config.confvalue
						          FROM " . $weberp['database'] . ".config
						            WHERE  config.confname = 'CheckCreditLimits'";
						$errorMessage = 'Error Code-TM' .  __LINE__  . ' Configuration Parameter Not Found';
						If($row =& modwebERPHelper::getResult($query,'webERP',$errorMessage)){	
							$CheckCreditLimits	=	$row['confvalue'];
						}	
						If($Customer['CreditLimit'] > .001 and $CheckCreditLimits > .001){
							// find account balance to compare to credit limit  							
							$SQL = "SELECT SUM(debtortrans.ovamount + 
							                   debtortrans.ovgst + 
							                   debtortrans.ovfreight + 
							                   debtortrans.ovdiscount	- 
							                   debtortrans.alloc) AS balance
							              FROM " . $weberp['database'] . ".debtortrans
							            WHERE  debtortrans.debtorno = '" . $CustomerCode . "'";
							$errorMessage = 'Error Code-TM' .  __LINE__  . ' Query in getCustomer failed';
							If($row & modwebERPHelper::getResult($query,'webERP',$errorMessage)){	
								$Customer['Balance']				=	$row['balance'];
								If($Customer['CreditLimit'] 	> $row['balance']){
									$Customer['OpenAccount']	=	True;
								}else{									
									$Customer['Terms']='Balance Exceeds Credit Limit';
								}
							}else{
								// if no records then balance not over limit
								$Customer['Balance']				=	0;
								$Customer['OpenAccount']=True;	
							}					
						}else{
							// no credit limit for this customer so open account is true						
							$Customer['OpenAccount']=True;			
						}												
					}else{					
						$Customer['Terms']='<BR>No Terms<BR><BR>';
					}
				}else{					
					$Customer['Terms']='Customer not found&nbsp;&nbsp;';
				}
			}else{
				$Customer	= array() ;
				$Customer['Terms']='User not associated with Customer';
			}
		}else{
				$Customer	= array() ;
		}
		// echo '<pre>';var_dump($Customer , '<br><br> <b style="color:brown">Customer in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		return $Customer;
	}
	function getBranches($debtorno)
	{		
		global $weberp;
		$query 	= "SELECT branchcode,debtorno,brname,braddress4 as zip 
						  FROM " . $weberp['database'] . ".custbranch 
						 WHERE debtorno = '" . $debtorno . "'";
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' No Branch Records Found <span style="color:brown">OK if user customer cross reference does not exist</span>';	 
		$Branches =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0);	 
		return $Branches;
	}
	function getDestinationZipCode(){
		global $mainframe;
		// first if the user just entered and posted a zip code use it	
		$post	= JRequest::get('post');
		If(isset($post['DestinationZipCode']) AND strlen(trim($post['DestinationZipCode'])) > 0 ){
			$DestinationZipCode = $post['DestinationZipCode'];
			$mainframe->setUserState( "DestinationZipCodeOverride", $post['DestinationZipCode']) ;
			$mainframe->setUserState( "DestinationZipCode", $post['DestinationZipCode']) ;
		}elseIf(strlen(trim($mainframe->getUserState( "DestinationZipCodeOverride")))> 0){
			// or posted a zip code before use it
			$DestinationZipCode = $mainframe->getUserState( "DestinationZipCodeOverride");
			$mainframe->setUserState( "DestinationZipCode", $mainframe->getUserState( "DestinationZipCodeOverride")) ;
		}else{
			// next look for user login cross to branch record
			$DestinationZipCode = $this->getCustomerZip();
		   // }else{ then look up ip address for zip default in the ipgeo files
			// save zip code found here for future use
		}
		If(strlen(trim($DestinationZipCode)) > 0 ){					
			$mainframe->setUserState( "DestinationZipCode", $DestinationZipCode) ;
		}elseif(strlen(trim($mainframe->getUserState( "DestinationZipCode"))) > 0){
			$DestinationZipCode = $mainframe->getUserState( "DestinationZipCode");
		}
		return $DestinationZipCode	;
	}
	function getCustomerZip(){	
		global $weberp;
		$user 		=& JFactory::getUser();	
		If(isset($user)){
			$CustomerZip='';	
			$post	= JRequest::get('post');
			$And = '';
			If(array_key_exists('branch',$post)){
				$And = " AND branchcode = '" . $post['branch'] . " ' ";
			}
			$this->_table_prefix = '#__cart_';
			$query = "SELECT customer 
			                  FROM " . $this->_table_prefix . "usercustomer 
			                 WHERE user = '" . $user->username . "'"  . $And;
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' User Customer Cross record not found <span style="color:brown">OK if user customer cross reference does not exist</span>';
			$CustomerCross =& modwebERPHelper::getResult($query,'Joomla',$errorMessage,NULL,0);
			$query = "SELECT 	braddress4,braddress5
								FROM " . $weberp['database'] . ".custbranch
				        	  WHERE 	debtorno ='" . $CustomerCross . "'";
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' No Customer Record Found <span style="color:brown">OK if user customer cross reference does not exist</span>';
			If($row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage,NULL,0)){		
				If(strlen($row['braddress5']) > 4){		
					$CustomerZip = 	substr($row['braddress5'], 0, 5);
				}else{
					$CustomerZip = 	substr($row['braddress4'], 0, 5);
				}
			}
		}
		return $CustomerZip;
	}	
	function getSalesTaxRate($stockid){	
		global $weberp, $mainframe;	
		$SalesTaxRate 	= 0;
		$post				=& JRequest::get('post');
		$app 				=& JFactory::getApplication();
		$user 			=& JFactory::getUser();
		// echo '<pre>';var_dump($user , '<br><br> <b style="color:brown">user in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		// echo $user->id  . '=user->id   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';	
		$db 				= &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		If(strlen(trim($user->id)) > 0 ){
			$query =  "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->id . "'";			
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' No User Customer Cross Records Found';
			$CustomerCross =& modwebERPHelper::getRowArray($query,'Joomla',$errorMessage,NULL,0);
	  	}
	  	If(!isset($weberp['host'])){
	  		$weberp = modwebERPHelper::getwebERP();
	  	}
		$query = "SELECT 	taxcatid
							FROM " . $weberp['database'] . ".stockmaster
			        	  WHERE 	stockid ='" . $stockid . "'";		
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' Tax Category ID not found';
		$taxcategory =& modwebERPHelper::getResult($query,'webERP',$errorMessage);
	  	If(isset($CustomerCross)){
	  		// echo '<pre>';var_dump($CustomerCross , '<br><br> <b style="color:brown">CustomerCross   </b><br><br>');echo '</pre>';
	  		$CustomerCode = $CustomerCross["customer"];	
			// get salestax authority from customer record
			$query = "SELECT 	custbranch.taxgroupid,custbranch.defaultlocation
							FROM " . $weberp['database'] . ".custbranch
			        	  WHERE 	custbranch.debtorno ='" . $CustomerCode . "'";			        	  
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' No User Customer Found';
			$row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage,NULL,0);							
			$CustomerDefaultLocation = $row['defaultlocation'];
			$taxgroupid 		= $row['taxgroupid'];
			$query= "SELECT locations.taxprovinceid FROM " . $weberp['database'] . ".locations WHERE loccode ='" .  $row['defaultlocation'] . "'";
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' Failed to Find Default Location';
			$taxprovinceid =& modwebERPHelper::getResult($query,'webERP',$errorMessage,NULL,0);
			If(isset($taxgroupid) AND strlen(trim($taxgroupid)) > 0 AND isset($taxprovinceid) AND strlen(trim($taxprovinceid)) > 0 AND isset($taxcategory) AND strlen(trim($taxcategory)) > 0){
				$SalesTaxRate = cartweberpModelcart::getTaxRate($taxgroupid,$taxprovinceid,$taxcategory);
			}
	  	}else{
	  		// IF OUT OF STATE - EXEMPT ELSE - DEFAULTTAXGROUP
	  		$DefaultLocation = $weberp['locationcode'];
	  		$query= "SELECT locations.taxprovinceid,locations.deladd4 FROM " . $weberp['database'] . ".locations WHERE loccode ='" . $DefaultLocation . "'";
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' Failed to Find Location Tax Province';
			If($row2 =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage)){
				$taxprovinceid		= $row2['taxprovinceid'];
				$LocationZip		= trim(substr($row2['deladd4'],strrpos($row2['deladd4'],' ')));
			}
			If(!$OurState = cartweberpModelcart::getAState($LocationZip)){					  			
	  			If(!$mainframe->getUserState( "LocationZipError") OR $mainframe->getUserState( "LocationZipError") == 0){  			
					$app->enqueueMessage(JText::_('Please enter a zip code for the inventory location in webERP'));
					$mainframe->setUserState( "LocationZipError", 1) ;
				}
	  			return 0;
	  		}
	  		If(isset($post['DestinationZipCode']) OR $mainframe->getUserState( "DestinationZipCodeOverride")){
	  			If(isset($post['DestinationZipCode'])){
	  				$CustomerZip = $post['DestinationZipCode'];
	  			}else{
	  				$CustomerZip = $mainframe->getUserState( "DestinationZipCodeOverride");
	  			}
	  			$CustomerState = cartweberpModelcart::getAState($CustomerZip);
	  		}else{
	  			If(!$mainframe->getUserState( "DestinationZipError") OR $mainframe->getUserState( "DestinationZipError") == 0){  			
					$app->enqueueMessage(JText::_('Please enter a zip code for the destination'));
					$mainframe->setUserState( "DestinationZipError", 1) ;
				}
	  			return 0;
	  		}
	  		If($CustomerState <> $OurState){
	  			If(!$mainframe->getUserState( "OutofState") OR $mainframe->getUserState( "OutofState") == 0){  			
					$app->enqueueMessage(JText::_('Out of State - Exempt from Sales Tax'));
					$mainframe->setUserState( "OutofState", 1) ;
				}
	  			$SalesTaxRate = 0;
	  		}else{
	  			$taxgroupid = $weberp['taxgroupid'];
				$SalesTaxRate = cartweberpModelcart::getTaxRate($taxgroupid,$taxprovinceid,$taxcategory);  				  			
	  		}
		}
		return $SalesTaxRate;
	}
	function getAState($Zip){
    	global $mainframe;	
		$app=& JFactory::getApplication();
		$this->_table_prefix = '#__cart_';
		$query= "SELECT region FROM " . $this->_table_prefix . "zip WHERE zip ='" . $Zip . "'";
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' Region not found';
		If(!$State = modwebERPHelper::getResult($query,'Joomla',$errorMessage)){	
			If(!$mainframe->getUserState( "InvalidZip") OR $mainframe->getUserState( "InvalidZip") == 0){  			
				$app->enqueueMessage(JText::_('Zip code not in our list - Please enter nearby zip code ' . $Zip));
				$mainframe->setUserState( "InvalidZip", 1) ;
			}
	  		return False;
		}else{
			return $State;
		}
	}
	function GetTaxRate ($TaxGroup, $DispatchTaxProvince, $TaxCategory){	
		global $weberp, $mainframe;	
		/*Gets the Tax rate applicable to an item from the TaxAuthority of the branch and TaxLevel of the item */
		$query = "SELECT taxauthid FROM " . $weberp['database'] . ". `taxgrouptaxes` WHERE taxgroupid =" . $TaxGroup ;
	  	$errorMessage = 'Error Code-TM' .  __LINE__  . ' TaxAuthorityID not found';
		$rows = modwebERPHelper::getCollumnArray($query,'webERP',$errorMessage);
	  	$TaxRate = 0;
	  	$count = 0;
		Foreach($rows as $row){	
			$TaxAuthority[$count] = $row['taxauthid'];
			$count = $count+1;
			// echo $TaxAuthority[$count]  . "=TaxAuthority[$count]<br>";
		}
		If(isset($TaxAuthority)){
			$mainframe->setUserState( "chargeforsalestax",TRUE);
			Foreach($TaxAuthority as $count=>$TA){
				// echo $TA  . "=TA id<br>";
				$query = "SELECT taxrate,taxauthority
						FROM " . $weberp['database'] . ".taxauthrates
						WHERE taxauthority=" . $TA . "
						AND dispatchtaxprovince=" . $DispatchTaxProvince . "
						AND taxcatid = " . $TaxCategory;
				$errorMessage = 'Error Code-TM' .  __LINE__  . ' No Tax Rate/Authority Records Found';
				$row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage);
				$TaxRate = $TaxRate + $row['taxrate'];				
			}
		}
		return $TaxRate;	
	}	
	function getFrieghtAmount(	$items,$TotalValue){
		global $weberp, $mainframe;
		// see if user cross record exists			
		$query = "SELECT confvalue 
						FROM " . $weberp['database'] . ".config 
					  WHERE confname = 'DoFreightCalc'";
		$errorMessage = 'Error Code-TM' .  __LINE__  . ' Configuration Value for Freight not found';
		If($DoFreightCalc = modwebERPHelper::getResult($query,'webERP',$errorMessage)){
			$mainframe->setUserState( "chargeforfreight",$DoFreightCalc['confvalue']);
		}
		If($mainframe->getUserState( "chargeforfreight")){
			$post				= JRequest::get('post');
			$app 				=& JFactory::getApplication();
		  	$FindCity 		= array();
		  	$CalcFreight 	= array();
			$user 			=& JFactory::getUser();
			$FromLocation 	= $weberp['locationcode'];
			If(strlen(trim($user->username)) > 0){
				$this->_table_prefix = '#__cart_';
				$query = "SELECT * 
								FROM " . $this->_table_prefix . "usercustomer 
							  WHERE user = '" . $user->username . "'" ;
				$errorMessage = 'Error Code-TM' .  __LINE__  . ' No User Customer Cross Records Found <span style="color:brown">OK if user has no customer cross reference</span>';
		  		If($CustomerCross =& modwebERPHelper::getRowArray($query,'Joomla',$errorMessage,NULL,0)){
		  			$CustomerCode = $CustomerCross['customer'];	
					// get from and to zip codes
					$query = "SELECT 	defaultlocation,braddress2,braddress3,braddress4
									FROM " . $weberp['database'] . ".custbranch 
				        	  	  WHERE 	debtorno ='" . $CustomerCode . "' Limit 1";
					$errorMessage = 'Error Code-TM' .  __LINE__  . ' Select location and address from customer record failed';
					If($row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage)){	
						$FromLocation 	= $row['defaultlocation'];
						$BrAdd2 			= $row['braddress2'];
						$BrAdd3 			= $row['braddress3'];
						$BrAdd4 			= $row['braddress4'];					
	  					$FindCity = explode(' ', $BrAdd2 . ' ' . $BrAdd3 . ' ' . $BrAdd4);
					}
				}else{
					$post	= JRequest::get('post');
					If(isset($post['DestinationZipCode'])){
						$FindCity["A"] = $post['DestinationZipCode'];
					}elseif($mainframe->getUserState( "DestinationZipCode")){
						$FindCity["A"] = $mainframe->getUserState( "DestinationZipCode");
					}
					If(isset($post['DestinationZipCode'])){
						array_push($FindCity, $post['DestinationZipCode']);
					}
					$CalcFreight['Cost'] =0;
				}
			}else{
				$post	= JRequest::get('post');
				If(isset($post['DestinationZipCode'])){
					$FindCity["A"] = $post['DestinationZipCode'];
					array_push($FindCity, $post['DestinationZipCode']);
				}elseif($mainframe->getUserState( "DestinationZipCode")){
					$FindCity["A"] = $mainframe->getUserState( "DestinationZipCode");
				}		
			}
			$FreightQuantity = cartweberpModelcart::getFreightQuantity($items);
			$TotalWeight = $FreightQuantity['Weight'];
			$TotalVolume = $FreightQuantity['Volume'];
			$query = 'Select shipperid,
					kgrate *' . $TotalWeight . ' AS kgcost,
					cubrate * ' . $TotalVolume . ' AS cubcost,
					fixedprice, minimumchg
				FROM ' . $weberp['database'] . '.freightcosts
				WHERE locationfrom = "' . $FromLocation . '"
				AND maxkgs > ' . $TotalWeight . '
				AND maxcub >' . $TotalVolume . '
				AND (';
			If(count($FindCity) > 0){
				$MessageCity = '';
				foreach ($FindCity as $City) {	
					$query = $query . " destination LIKE '" . ucwords($City) . "%' OR";
					$MessageCity = $MessageCity . " - " . ucwords($City);
				}
				$query = substr($query, 0, strrpos($query,' OR')) . ')';
				// echo $sql  . "=sql<br>";
				$errorMessage = 'Error Code-TM' .  __LINE__  . ' The freight calculation for the destination city cannot be performed. No Freight rate records for product leaving our warehouse locatation ' . $FromLocation . ' to destination city ' . $MessageCity;
				if ($rows =& modwebERPHelper::getRowList($query,'webERP',$errorMessage)) {
					$CalcFreightCost =9999999;
					Foreach($rows as $row) {
			
				/**********      FREIGHT CALCULATION
				IF FIXED PRICE TAKE IT IF BEST PRICE SO FAR OTHERWISE
				TAKE HIGHER OF CUBE, KG OR MINIMUM CHARGE COST 	**********/
			
						if ($row['fixedprice']!=0) {
							if ($row['fixedprice'] < $CalcFreightCost) {
								$CalcFreight['Cost']		=$row['fixedprice'];
								$CalcFreight['Shipper'] =$row['shipperid'];
							}
						} elseif ($row['cubcost'] > $row['kgcost'] && $row['cubcost'] > $row['minimumchg'] && $row['cubcost'] < $CalcFreightCost) {
			
							$CalcFreight['Cost']		=$myrow['cubcost'];
							$CalcFreight['Shipper'] =$myrow['shipperid'];
			
						} elseif ($row['kgcost']>$row['cubcost'] && $row['kgcost'] > $row['minimumchg'] && $row['kgcost'] < $CalcFreightCost) {
			
							$CalcFreight['Cost']		=$row['kgcost'];
							$CalcFreight['Shipper'] =$row['shipperid'];
			
						} elseif ($row['minimumchg']< $CalcFreightCost){
			
							$CalcFreight['Cost']		=$row['minimumchg'];
							$CalcFreight['Shipper'] =$row['shipperid'];
			
						}
					}
				} else {
					$CalcFreight['Cost'] = 0;
				}
			}else{
				$CalcFreight['Cost'] = 0;			
			}
			$query = "SELECT confvalue 
							FROM " . $weberp['database'] . ".config 
						  WHERE confname = 'FreightChargeAppliesIfLessThan'";
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' Get Freight Amount Failed';
			$FreightChargeAppliesIfLessThan = modwebERPHelper::getResult($query,'webERP',$errorMessage);
			if ($TotalValue >= $FreightChargeAppliesIfLessThan){	
				/*Even though the order is over the freight free threshold - 
				still need to calculate the best shipper to ensure get best deal*/	
				$CalcFreight['Cost'] =0;
			}
		}else{
			$CalcFreight['Cost'] =0;
		}
		return $CalcFreight;
	}	
	function getFreightQuantity($items){
		global $weberp;
		$FreightQuantity['Volume'] = 0;	
		$FreightQuantity['Weight'] = 0;
		Foreach($items as $item){
			$StockID  = $item['partnumber'];
			$Quantity = $item['quantityordered'];
			$query = "SELECT 	volume,kgs
							FROM " . $weberp['database'] . ".stockmaster
			        	  WHERE 	stockid ='" . $StockID . "'";
			$errorMessage = 'Error Code-TM' .  __LINE__  . ' Volume and weight information not found in stock record';
			If($row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage))
			{				
				$FreightQuantity['Volume'] = $FreightQuantity['Volume'] + $row['volume'] * $Quantity;	
				$FreightQuantity['Weight'] = $FreightQuantity['Weight'] + $row['kgs']    * $Quantity;				
			}
		}
		return $FreightQuantity;
	}
	function getCurrencyRate(){	
		global $weberp,$mainframe;	
		If($mainframe->getUserState( "ChosenCurrency")){
			$weberp	= modwebERPHelper::getweberp();		
			$dbw 		=& JFactory::getDBO();
			$dbw = & JDatabase::getInstance( $weberp );
			$CurrencyAbbreviation = $mainframe->getUserState( "ChosenCurrency");
			$query = "SELECT rate
	 				      FROM " . $weberp['database'] . ".currencies 
	 				     WHERE currabrev = '" . $CurrencyAbbreviation . "'";
	 		$errorMessage = 'Error Code-TM' .  __LINE__  . ' Get Currency Rate Failed';
			$Rate = modwebERPHelper::getResult($query,'webERP',$errorMessage);
	 	}else{
	 		$Rate = 1;
	 	}
 		return $Rate;
	}
}	
?>

  		