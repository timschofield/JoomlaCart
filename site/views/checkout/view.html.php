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

class cartweberpViewcheckout extends JView
{
	function __construct( $config = array())
	{	 
    	global $mainframe, $context; 
	 	$context = 'checkoutcatalog.list.';
 	 	parent::__construct( $config );
	} 
	function display($tpl = null)
	{		 
    	global $mainframe, $context;
		$document 		= & JFactory::getDocument();
		$document->setTitle( JText::_('Checkout Display') );		
		$uri 				=& JFactory::getURI();
		$items			=& $this->get('Data');
		$coreprice		=& cartweberpModelcheckout::getCorePrice($items);
		// $carttotal		=& $this->get( 'CartTotal');
		$mainframe 		=& JFactory::getApplication();
		$SalesOrderNumber = $mainframe->getUserState( "SalesOrderNumber" ) ;		
		$shipaddress   =& cartweberpModelcheckout::getShipAddress($SalesOrderNumber);
		If(count($items) > 0){
			foreach($items as $RecordNumber=>$Record){
				foreach($Record as $FieldName=>$FieldValue){
					If($FieldName == 'stkcode'){
						$description[$FieldValue] = $this->getPartDescription($FieldValue);
					}
				}
			}
		}
		$freight 		=& $this->get( 'freight' );
		// $pagination 	=& $this->get( 'Pagination' );
		$customer 		=& $this->get( 'Customer' );
		$mainframe =& JFactory::getApplication();
		If($mainframe->getUserState( "TransactionID")){
			$TransactionID = $mainframe->getUserState( "TransactionID") ;
		}else{
			$TransactionID = 'None';
		}	
		$mainframe->setUserState( "TransactionID", '') ;
		$this->assignRef('user',			JFactory::getUser());	
  		$this->assignRef('items',			$items); 		
  		$this->assignRef('coreprice',		$coreprice);	
  		$this->assignRef('carttotal',		$carttotal); 		
  		$this->assignRef('description',	$description); 
  		$this->assignRef('customer',		$customer);		
  		$this->assignRef('freight',		$freight);		
    	$this->assignRef('pagination',	$pagination);	
    	$this->assignRef('shipaddress',	$shipaddress);
    	$this->assignRef('TransactionID',$TransactionID);
    	$this->assignRef('request_url',	$uri->toString());
		parent::display($tpl);
  	} 
  	
	function getPartDescription($stockid){
		$weberp 					=& $this->getwebERP();
		$query = "SELECT description FROM " . $weberp['database'] . ".stockmaster WHERE stockid = '" . $stockid . "'";
		$result = mysql_query($query) or die("$query Query failed : " . mysql_error());
		if($row = mysql_fetch_array($result))
		{
			$description = $row['description'];
		}
		return $description;
	}
	function getwebERP(){
		$db 								=& JFactory::getDBO();
		$Option 							=& JRequest::getVar('option');
		$params 							= JComponentHelper::getParams($Option);
		$weberp['host'] 	  			= $params->get( 'host');
		$weberp['database'] 			= $params->get( 'database');
		$weberp['databaseuser'] 	= $params->get( 'user');
		$weberp['userpassword'] 	= $params->get( 'password');
		$weberp['paypalname'] 		= $params->get( 'paypalname');
		$weberp['paypalpassword'] 	= $params->get( 'paypalpassword');
		$weberp['paypalsignature'] = $params->get( 'paypalsignature');
		$weberp['sandboxname'] 		= $params->get( 'sandboxname');
		$weberp['sandboxpassword'] = $params->get( 'sandboxpassword');
		$weberp['sandboxsignature']= $params->get( 'sandboxsignature');
		$weberp['environment'] 		= $params->get( 'environment');
		$weberp['driver']   			= 'mysql';        // Database driver name
		return $weberp;
	} 	
	function PPHttpPost($methodName_, $nvpStr_) {
	
		// Set up your API credentials, PayPal end point, and API version.
		$weberp 	=& $this->getwebERP();
		If($weberp['environment'] == 1){
			$environment = "live";
			$API_Endpoint = "https://api-3t.paypal.com/nvp";
			$API_UserName=$weberp['paypalname'];
			$API_Password=$weberp['paypalpassword'];
			$API_Signature=$weberp['paypalsignature'];
		}else{
			$environment = "sandbox";
			$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
			$API_UserName=$weberp['sandboxname'];
			$API_Password=$weberp['sandboxpassword'];
			$API_Signature=$weberp['sandboxsignature'];
		}
		$version = urlencode('51.0');
	
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
	
		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
	
		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
	
		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	
		// Get response from the server.
		$httpResponse = curl_exec($ch);
	
		if(!$httpResponse) {
			exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}
	
		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);
	
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
	
		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}
	
		return $httpParsedResponseAr;
	}
}
?>
