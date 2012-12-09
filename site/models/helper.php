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
jimport( 'joomla.application.component.model');		
// jimport( 'joomla.database.database');
// jimport( 'joomla.database.table' ); 
jimport( 'joomla.application.application' );
// jimport( 'joomla.error.exception' );
// jimport( 'joomla.error.error' );
// jimport( 'joomla.application.component.controller' );

class modwebERPHelper extends JModel
{
	function getwebERP(){	
		global $mainframe,$weberp;							
		$mainframe 	= JFactory::getApplication();	
		$Option 		='com_cartweberp';
		// get the components parameters
		$params 		= JComponentHelper::getParams($Option);
		$menuitemid = JRequest::getInt( 'Itemid' );
    	$menu 		= JSite::getMenu();
  		if ($menuitemid)
  		{
    		$menuparams = $menu->getParams( $menuitemid );
    		$params->merge( $menuparams );
    		$mainframe->setUserState( "menuitemid", $menuitemid) ;
  		}elseif($mainframe->getUserState( "menuitemid")){
    		$menuparams = $menu->getParams($mainframe->getUserState( "menuitemid"));
    		$params->merge( $menuparams );
  		}
		// create array of variables to use for query and other CARTwebERP configuration settings
		$weberp = array();
		$weberp['host'] 	  								= $params->get( 'host');
		$weberp['database'] 								= $params->get( 'database');
		$weberp['user'] 									= $params->get( 'databaseuser');
		$weberp['password'] 								= $params->get( 'userpassword');
		$weberp['dontselltypes']						= $params->get( 'dontselltypes');
		$weberp['salestype']								= $params->get( 'salestype');
		$weberp['showquantityonhandcolumn']			= $params->get( 'showquantityonhandcolumn');
		$weberp['showquantityonhand']					= $params->get( 'showquantityonhand');
		$weberp['driver']   								= 'mysql';        // Database driver name
		$weberp['allowedterms'] 						= $params->get( 'allowedterms');
		$weberp['listsalestype']						= $params->get( 'listsalestype');
		$weberp['bankaccount'] 							= $params->get( 'bankaccount');
		$weberp['currencycode'] 						= $params->get( 'currencycode');
		$weberp['paypalname'] 							= $params->get( 'paypalname');
		$weberp['paypalpassword'] 						= $params->get( 'paypalpassword');
		$weberp['paypalsignature'] 					= $params->get( 'paypalsignature');
		$weberp['sandboxname'] 							= $params->get( 'sandboxname');
		$weberp['sandboxpassword'] 					= $params->get( 'sandboxpassword');
		$weberp['sandboxsignature']					= $params->get( 'sandboxsignature');
		$weberp['environment'] 							= $params->get( 'environment');
		$weberp['debug'] 									= $params->get( 'debug');
		$weberp['terms'] 									= substr($params->get( 'allowedterms'),0,strpos($params->get( 'allowedterms'),","));
		$weberp['salesmancode']							= $params->get( 'salesmancode');
		$weberp['areas']									= $params->get( 'areas');
		$weberp['paymentterms']							= $params->get( 'paymentterms');
		$weberp['usertype']								= $params->get( 'usertype');
		$weberp['gid']										= $params->get( 'gid');
		$weberp['locationcode']							= $params->get( 'locationcode');
		$weberp['sendemail']								= $params->get( 'sendemail');
		$weberp['fromemail']								= $params->get( 'fromemail');
		$weberp['taxgroupid']							= $params->get( 'taxgroupid');
		$weberp['coresales']								= $params->get( 'coresales');
		$weberp['includesalescategories']			= $params->get( 'includesalescategories');
		$weberp['displaycolumns']						= $params->get( 'displaycolumns');
		$weberp['priceheadingoverride']				= $params->get( 'priceheadingoverride');
		$weberp['pricecolumn']							= $params->get( 'pricecolumn');
		$weberp['catalogonly']							= $params->get( 'catalogonly');
		$weberp['authorizenetapiloginid']			= $params->get( 'authorizenetapiloginid');
		$weberp['authorizenettestapiloginid']		= $params->get( 'authorizenettestapiloginid');
		$weberp['authorizenet']							= $params->get( 'authorizenet');
		$weberp['authorizenettransactionkey']		= $params->get( 'authorizenettransactionkey');
		$weberp['authorizenettesttransactionkey']	= $params->get( 'authorizenettesttransactionkey');
		$weberp['googlecheckout']						= $params->get( 'googlecheckout');
		$weberp['googlemerchantid']					= $params->get( 'googlemerchantid');
		$weberp['googlekey']								= $params->get( 'googlekey');
		$weberp['googlesandboxmerchantid']			= $params->get( 'googlesandboxmerchantid');
		$weberp['googlesandboxkey']					= $params->get( 'googlesandboxkey');
		$weberp['authorizenettesttransactionkey']	= $params->get( 'authorizenettesttransactionkey');
		$weberp['pathtopics']							= $params->get( 'pathtopics');
		$weberp['includelocations']					= $params->get( 'includelocations');
		$weberp['prefix']									= '';
		$weberp['connected']								= TRUE;	
		$weberp['opendatabase']							= TRUE;		
		// echo $weberp['listsalestype']	  . '=weberp[listsalestype]	   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';	
		$query = "SELECT 	decimalplaces
		 				      FROM " . $weberp['database'] . ".currencies 
		 				     WHERE currabrev ='" . $weberp['currencycode'] . "'";
		$errorMessage = 'Error Code-H' .  __LINE__  . ' Unable to select from currencies file. ';		
		If(!$weberp['currencydecimalplaces'] = modwebERPHelper::getResult($query,'webERP',$errorMessage,NULL,0)){
			$weberp['currencydecimalplaces']=2;
		}
		// echo $weberp['currencydecimalplaces']  . "=weberp['currencydecimalplaces']<br>";
		If(strlen(trim($weberp['includesalescategories'])) > 0){
			$FindingSalesCategories = TRUE;
			$SalesCategories = $weberp['includesalescategories'];
			$CountCheck = 0;
			While($FindingSalesCategories){
				$CountCheck = $CountCheck + 1;
				If($CountCheck > 1000){
					echo $CountCheck  . "=CountCheck<br>";exit;
				}
				$query = "SELECT 	salescatid
	 				      FROM " . $weberp['database'] . ".salescat 
	 				     WHERE parentcatid  IN('" . $SalesCategories . "')";
	 			$SalesCategories = '';	
	 			// echo $query  . "=query 122<br>";
	 			$errorMessage = 'Error Code-H' .  __LINE__  . ' Unable to select from salescat file.  <span style="color:brown">This is OK if this list of categories are not parents</span>';
		 		If($MoreSalesCategories = modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0)){
		 			// $SalesCategories = implode(',', $MoreSalesCategories);
		 			// echo $SalesCategories  . "=SalesCategories<br>";
		 			// echo '<pre>';var_dump($MoreSalesCategories , '<br><br> <b style="color:brown"> MoreSalesCategories  126</b><br><br>');echo '</pre>';
		 			Foreach($MoreSalesCategories as $SC){
		 				// echo '<pre>';var_dump($SC , '<br><br> <b style="color:brown">SC   128</b><br><br>');echo '</pre>';
		 				$weberp['includesalescategories'] = $weberp['includesalescategories'] . "," . $SC["salescatid"];
		 				$SalesCategories = $SalesCategories .  $SC["salescatid"] . ",";
		 				// echo $weberp['includesalescategories']  . "=weberp['includesalescategories'] helper 135<br>";
		 			}
		 			$SalesCategories = substr($SalesCategories, 0, strrpos($SalesCategories, ","));
		 		}else{
		 			$FindingSalesCategories = FALSE;
		 		}		 		
		 	}
		 	// echo $weberp['includesalescategories']  . "=weberp['includesalescategories']<br>";
		}
		return $weberp;
	}
	function _getCartID()
	{
		$output=$_SERVER['REMOTE_ADDR'];
		// echo $output[1]  . "=output[1]<br>";
		// echo strpos($output[1],":")  . "=strpos(output[1],':')<br>";
		If(strpos($output[1],":") > 4){
			$HardwareAddress = substr($output[1],strpos($output[1],":")-2,(strrpos($output[1],":")+5-strpos($output[1],":")));
		}else{
			$HardwareAddress = $output[1];
		}
		$CartID 					= $HardwareAddress;
		If(strlen(trim($CartID)) < 4){
			$session = &JSession::getInstance('none',array());
			$CartID = $session->getId();
		}	
		// echo $CartID  . "=CartID<br>";
		return $CartID;
	}
	function setErrorMessage($errorMessage){
		$app =& JFactory::getApplication();
		// echo gettype($errorMessage)  . "=gettype(errorMessage)<br>";
		If(gettype($errorMessage) <> 'array'){
			$app->enqueueMessage(JText::_($errorMessage));
			echo $errorMessage  . "=errorMessage<br>";
		}
		Foreach($errorMessage as $count=>$message){
				$app->enqueueMessage(JText::_($message));
		}
		return;
	}
	function getResult($query,$database,$errorMessage,$publicMessage=1){
		global $weberp;
		If($UserArray = JFactory::getUser()->groups){
			$UserType = JFactory::getUser()->groups["Super Users"];
		}else{
			$UserType = 0;
		}
		$error = array();
		If($database == 'Joomla'){
			$dbj 		=& JFactory::getDBO();
			$dbj->setQuery($query);
			If(!$Value = $dbj->loadResult()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbj->getErrorMsg());
					array_push($error, '<br>' . $query);
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Result Error<br><span style='color:brown'><hr></span>");
					}
				}
				if($UserType == 8 OR strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Result Error<br><span style='color:brown'><hr></span>");
					}
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return $Value;
			}
		}elseif($database == 'webERP'){
			If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
				$weberp	= modwebERPHelper::getwebERP();
			}
			$dbw 		=& JFactory::getDBO();
			$dbw 		=& JDatabase::getInstance( $weberp );
			If(isset($dbw->message) AND substr($dbw->message,0,17) == 'Unable to connect'){				
				array_push($error, '<br>Error Code-H' .  __LINE__  . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters<br><span style="color:brown"><hr></span>');			
				array_push($error, $dbw->message);
				modwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			If(!$Value = $dbw->loadResult()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Result Error<br><span style='color:brown'><hr></span>");
					}
				}
				if($UserType == 8 OR strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage . '<br>' );
					}
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return $Value;
			}
		}
	}
	function getRowArray($query,$database,$errorMessage,$index=NULL,$publicMessage=1){
		global $weberp;
		If($UserArray = JFactory::getUser()->groups){
			$UserType = JFactory::getUser()->groups["Super Users"];
		}else{
			$UserType = 0;
		}
		$error = array();
		If($database == 'Joomla'){
			$dbj 		=& JFactory::getDBO();
			$dbj->setQuery($query);
			If(!$RowArray = $dbj->loadAssoc()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbj->getErrorMsg());
					array_push($error, '<br>' . $query);
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Row Array Error<br><span style='color:brown'><hr></span>");
					}
				}
				if($UserType == 8 OR strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Row Array Error<br><span style='color:brown'><hr></span>");
					}
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return $RowArray;
			}
		}elseif($database == 'webERP'){			
			If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
				$weberp	= modwebERPHelper::getwebERP();
			}
			$dbw 		=& JFactory::getDBO();
			$dbw = & JDatabase::getInstance( $weberp );
			If(isset($dbw->message) AND substr($dbw->message,0,17) == 'Unable to connect'){	
				array_push($error,$errorMessage);			
				array_push($error, '<br>Error Code-H' .  __LINE__  . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');			
				array_push($error, '<br>' . $dbw->message . '<br>' );
				modwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			If(!$RowArray = $dbw->loadAssoc()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Row Array Error<br><span style='color:brown'><hr></span>");
					}
				}
				if($UserType == 8 OR strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Row Array Error<br><span style='color:brown'><hr></span>");
					}
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return $RowArray;
			}
		}
	}
	function getCollumnArray($query,$database,$errorMessage,$publicMessage=1){
		global $weberp;		
		If($UserArray = JFactory::getUser()->groups){
			$UserType = JFactory::getUser()->groups["Super Users"];
		}else{
			$UserType = 0;
		}
		$error = array();
		If($database == 'Joomla'){
			$dbj 		=& JFactory::getDBO();
			$dbj->setQuery($query);
			If(!$CollumnArray = $dbj->loadResultArray()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbj->getErrorMsg());
					array_push($error, '<br>' . $query);
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Collumn Array Error<br><span style='color:brown'><hr></span>");
					}
				}
				if($UserType == 8 OR strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Collumn Array Error<br><span style='color:brown'><hr></span>");
					}
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return $CollumnArray;
			}
		}elseif($database == 'webERP'){		
			If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
				$weberp	= modwebERPHelper::getwebERP();
			}
			$dbw 		=& JFactory::getDBO();
			$dbw = & JDatabase::getInstance( $weberp );
			If(isset($dbw->message) AND substr($dbw->message,0,17) == 'Unable to connect'){				
				array_push($error, '<br>Error Code-H' .  __LINE__  . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');			
				array_push($error, '<br>' . $dbw->message . '<br>');
				modwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			If(!$CollumnArray = $dbw->loadResultArray()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Collumn Array Error<br><span style='color:brown'><hr></span>");
					}
				}
				if($UserType == 8 OR strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Collumn Array Error<br><span style='color:brown'><hr></span>");
					}
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return $CollumnArray;
			}
		}
	}
	function getRowList($query,$database,$errorMessage,$index=NULL,$publicMessage=1){
		global $weberp;
		If($UserArray = JFactory::getUser()->groups){
			$UserType = JFactory::getUser()->groups["Super Users"];
		}else{
			$UserType = 0;
		}
		$error = array();
		If($database == 'Joomla'){
			$dbj 		=& JFactory::getDBO();
			$dbj->setQuery($query);
			If(!$RowArray = $dbj->loadAssocList($index)){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbj->getErrorMsg());
					array_push($error, '<br>' . $query);
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Row List Error<br><span style='color:brown'><hr></span>");
					}
				}
				if(strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage . '<br>');
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Row List Error<br><span style='color:brown'><hr></span>");
					}
					// echo gettype($error)  . "=gettype(error)<br>";
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return $RowArray;
			}
		}elseif($database == 'webERP'){		
			If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
				$weberp	= modwebERPHelper::getwebERP();        
			}
			$dbw 		=& JFactory::getDBO();
			If(isset($dbw->message) AND substr($dbw->message,0,17) == 'Unable to connect'){				
				array_push($error, '<br>Error Code-H' .  __LINE__  . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');			
				array_push($error, $dbw->message);
				modwebERPHelper::setErrorMessage($error);
				If($publicMessage==0){
					array_push($error,  '<br>' . $errorMessage . '<br>');
				}
				return FALSE;
			}			
			$dbw 		=& JDatabase::getInstance( $weberp );
			If(isset($dbw->message) AND substr($dbw->message,0,17) == 'Unable to connect'){				
				array_push($error, '<br>Error Code-H' .  __LINE__  . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');			
				array_push($error, $dbw->message);
				modwebERPHelper::setErrorMessage($error);
				If($publicMessage==0){
					array_push($error,  '<b1128r>' . $errorMessage . '<br>');
				}
				return FALSE;
			}
			$dbw->setQuery($query);
			If(!$RowArray = $dbw->loadAssocList($index)){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Get Row List Error<br><span style='color:brown'><hr></span>");
					}
				}
				if($UserType == 8 OR strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage );
						array_push($error, '<br>Error Code-H' .  __LINE__  . ' Get Row List Error<br><span style="color:brown"><hr></span>');
					}
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return $RowArray;
			}
		}
	}
	function getInsertUpdate($query,$database,$errorMessage,$publicMessage=1){
		global $weberp;
		If($UserArray = JFactory::getUser()->groups){
			$UserType = JFactory::getUser()->groups["Super Users"];
		}else{
			$UserType = 0;
		}
		$error = array();
		If($database == 'Joomla'){
			$dbj 		=& JFactory::getDBO();
			$dbj->setQuery($query);
			If(!$Result = $dbj->query()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbj->getErrorMsg());
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
					array_push($error, '<br>' . $query);
				}				
				if(strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return TRUE;
			}
		}elseif($database == 'webERP'){		
			If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
				$weberp	= modwebERPHelper::getwebERP();
			}
			$dbw 		=& JFactory::getDBO();
			$dbw = & JDatabase::getInstance( $weberp );
			If(isset($dbw->message) AND substr($dbw->message,0,17) == 'Unable to connect'){
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "Error Code-H" .  __LINE__  . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
				array_push($error, 'Error Code-H' .  __LINE__  . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');			
				array_push($error, $dbw->message);
				modwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			If(!$Result = $dbw->query()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
				}
				if(strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage);
					}
					array_push($error, "Insert/Update Error");
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return TRUE;
			}
		}
	}
	function getDelete($query,$database,$errorMessage,$publicMessage=1){
	global $weberp;
	If($UserArray = JFactory::getUser()->groups){
		$UserType = JFactory::getUser()->groups["Super Users"];
	}else{
		$UserType = 0;
	}
	$error = array();
		If($database == 'Joomla'){
			$dbj 		=& JFactory::getDBO();
			$dbj->setQuery($query);
			If(!$Result = $dbj->query()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbj->getErrorMsg());
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
					array_push($error, '<br>' . $query);
				}				
				if(strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" .  __LINE__  . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return TRUE;
			}
		}elseif($database == 'webERP'){		
			If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
				$weberp	= modwebERPHelper::getwebERP();
			}
			$dbw 		=& JFactory::getDBO();
			$dbw = & JDatabase::getInstance( $weberp );
			If(isset($dbw->message) AND substr($dbw->message,0,17) == 'Unable to connect'){
					If($publicMessage==0){
						array_push($error,  '<br>' . $errorMessage);
						array_push($error, "Error Code-H" .  __LINE__  . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
				array_push($error, 'Error Code-H' .  __LINE__  . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');			
				array_push($error, $dbw->message);
				modwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			If(!$Result = $dbw->query()){
				if($UserType == 8 AND $weberp['debug'] > 0){
					array_push($error,  '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
				}
				if(strlen(trim($errorMessage)) > 0){
					If($publicMessage==1){
						array_push($error,  '<br>' . $errorMessage);
					}
					array_push($error, "Insert/Update Error");
					modwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			}else{
				return TRUE;
			}
		}
	}
	function getCompanyName() {
		$dbw 		=& JFactory::getDBO();
		$weberp	=& modwebERPHelper::getwebERP();
		// echo '<pre>';var_dump($weberp , '<br><br> <b style="color:brown">weberp in googlexml    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		$dbw 		=& JDatabase::getInstance( $weberp );
		$query = "SELECT 	coyname
 				      FROM " . $weberp['database'] . ".companies";	
 		$dbw->setQuery($query);	
		If(!$CompanyName = $dbw->loadResult()){
			return False;
		} 
 		return $CompanyName;
	}
}	
?>