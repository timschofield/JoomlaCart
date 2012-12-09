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
global $mainframe;	
require_once( JPATH_COMPONENT . DS . 'models' .DS.'helper.php' );
jimport( 'joomla.application.component.controller' );
class CartweberpControllercart extends JController
{
	function __construct( $default = array())
	{
		parent::__construct( $default );
	}
	function display()
	{
		$app    =& JFactory::getApplication();
		$router =& $app->getRouter();
		$router->setVar( 'view', 'cart' );
		$router->setVar( 'model', 'cart' );
		JRequest::setVar( 'view', 'cart' );
		JRequest::setVar( 'layout', 'default'  );
		parent::display();
	}
	function updatecart(){	
		global $mainframe;	
		$mainframe = JFactory::getApplication();
		$post	= JRequest::get('post');
		If(isset($post["freightselected"])){
			$mainframe->setUserState( "FreightSelected",$post["freightselected"]) ;
		}else{
			$mainframe->setUserState( "FreightSelected",'') ;
		}
		If(isset($post["branch"]) AND $post["branch"] <> "" ){
			$mainframe->setUserState( "branch",$post["branch"]) ;
		}
		foreach($post['QuantityOrdered'] as $Part=>$Quantity){
			$QuantityOrdered[$Part] = (integer)$Quantity;
		}
		foreach($post['Price'] as $Part=>$Amount){
			$Price[$Part] = $Amount;
		}
		$this->_table_prefix = '#__cart_';			
		$db = &JFactory::getDBO();
		$result='';
		foreach($post['ID'] as $Part=>$ID){
			If($QuantityOrdered[$Part] == 0){
				$db->setQuery( "DELETE FROM " . $this->_table_prefix . "orders WHERE id = " . $ID  );	                 
				$db->query();				
			}else{	
				If(!$PriceOfPart = CartweberpControllercart::getDiscountPrice($Part,$QuantityOrdered[$Part])){
					$PriceOfPart = $Price[$Part];
				}				
				$db->setQuery( "UPDATE " . $this->_table_prefix . "orders  
				                   SET quantityordered = " . $QuantityOrdered[$Part] . ",
				                   	  price				= " . $PriceOfPart 				. " 
				                 WHERE id = " . $ID  );	                 
				$db->query();
			}		
		}
		parent::display();
	}
	function remove(){
		$model = $this->getModel('cart');
		$this->_table_prefix = '#__cart_';
		$HardwareAddress = modwebERPHelper::_getCartID();
		$db = &JFactory::getDBO();
		$db->setQuery( "DELETE FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'" );	                 
		$db->query();
		header("Location: ". JURI::base().'index.php?option=com_cartweberp&controller=cartcatalog&view=cartcatalog');
	}
	function getDiscountPrice($stockid,$quantity){
		global $weberp;		
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}
		If(!$CustomerPriceList = CartweberpControllercart::getSalesType()){
			//Default
			$CustomerPriceList = $weberp['salestype'];
		}
		$query = "SELECT 	prices.stockid,
								prices.price,
			  					stockmaster.discountcategory
		 				FROM 	" . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
							 	" . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
						AND  prices.typeabbrev = '" . $CustomerPriceList . "' 							  			
					  WHERE  stockmaster.stockid = '" . $stockid . "'";
					  // echo $query  . '=query   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from stockmaster and prices file. ';
		$PartMaster = modwebERPHelper::getRowArray($query,'webERP',$errorMessage,null,0);	
		$Discounts = CartweberpControllercart::getDiscountPercents($PartMaster['discountcategory']);
		$Price =$PartMaster['price'];
		// echo $  . '=   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
		// echo '<pre>';var_dump($Discounts , '<br><br> <b style="color:brown">Discounts in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		If(gettype($Discounts) == 'array'){
			Foreach($Discounts as $QuantityBreakPoint=>$BreakAmount){
				If($quantity >= $QuantityBreakPoint){
					$Price = $PartMaster['price'] * (1-$BreakAmount);
				}else{
					break;
				}
			}
		}
		// echo '<pre>';var_dump($Price , '<br><br> <b style="color:brown">Price in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		return $Price;
	}
	function getSalesType(){
		global $weberp;		
		$user 						=& JFactory::getUser();
		$db 							= &JFactory::getDBO();
		$this->_table_prefix 	= '#__cart_';
		$query = "SELECT customer FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->id . "'" ;
	  	$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from customer from user cross file. <span style="color:brown">OK if user - customer cross reference does not exist</span>';
		If($CustomerCross 	= modwebERPHelper::getResult($query,'Joomla',$errorMessage,NULL,0)){
			// get sales type from customer record or parameter table
			// $salestype = $weberp['salestype'];
			$query = "SELECT 	salestype
							FROM " . $weberp['database'] . ".debtorsmaster 
				        WHERE 	debtorno ='" . $CustomerCross . "'";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from debtorsmaster file. ';
			$salestype = modwebERPHelper::getResult($query,'webERP',$errorMessage,null,0);			
		}
		If(!isset($salestype)){
			$salestype = $weberp['listsalestype'];
		}
		return $salestype;
	}
	function getDiscountPercents($DiscountCategory){	
		global $weberp;	
		$query = "SELECT quantitybreak, 	discountrate
						FROM " . $weberp['database'] . ".discountmatrix 
					  WHERE discountcategory =  '" . $DiscountCategory . "'";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from discountmatrix file. <span style="color:brown">OK if no discount matrix records exist</span>';	
		If($DiscountPercent =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0)){	
			Foreach($DiscountPercent as $count=>$DiscountPercentArray){	
				$DiscountPercents[$DiscountPercentArray['quantitybreak']] = $DiscountPercentArray['discountrate'];
			}
			return $DiscountPercents;
		}
		return FALSE;
	}
}	
?>
