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

class cartweberpModelcartcatalog extends JModel
{
	var $_data = null;

	var $_total = null;

	var $_pagination = null;

	var $_table_prefix = null;
	
	
	var $totalitems = null;


	function __construct()
	{
		parent::__construct();

		global $mainframe, $context,$limitstart,$limit,$PartsOnPage,$totalitems;

		$post	= JRequest::get('request');
	  	$this->_table_prefix = '#__cart_';	

		$config = JFactory::getConfig();

		// Get the pagination request variables
		$this->setState('limit', $mainframe->getUserStateFromRequest('com_cartweberp.limit', 'limit', $config->getValue('config.list_limit'), 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		// In case limit has been changed, adjust limitstart accordingly
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));
		// If category has changed start over.
		If(isset($post['category'])){
			$this->setState('limitstart', 0);
			$this->storeCategoryParameter($post['category']);	
		}	
		// If sort order has changed start over.
		If(array_key_exists('sortcriteria',$post) AND $post['sortcriteria'] == 'price' AND $mainframe->getUserState('PriceSelected') <> 'CHECKED'){
			$this->setState('limitstart', 0);	
		}elseif(array_key_exists('sortcriteria',$post) AND $post['sortcriteria'] == 'description' AND $mainframe->getUserState('DescriptionSelected') <> 'CHECKED') {
			$this->setState('limitstart', 0);
		}elseif(array_key_exists('sortcriteria',$post) AND $post['sortcriteria'] == 'stockid' AND $mainframe->getUserState('PartNumberSelected') <> 'CHECKED') {
			$this->setState('limitstart', 0);
		}	
	}

	function getData()
	{
		global $totalitems,$weberp;
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{			
			$query = $this->_buildQuery();
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the stock file. Perhaps some categories assigned to be displayed in the cart do not have stock assigned?';		
			$this->_data=&modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0);
			$totalitems = count($this->_data);
			$query = $query . " LIMIT " . $this->getState('limitstart') . ", " .  $this->getState('limit');
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the stock file limited. ';		
			$this->_data=&modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0);
		}
		return $this->_data;
	}
	function getPagination()
	{
		global $totalitems;
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $totalitems, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	function _buildQuery()
	{
		global $parts,$mainframe,$weberp,$CustomerPriceList;
		$errorMessage = '';
		$post	= JRequest::get('request');
		$db 	= &JFactory::getDBO();
	  	$parts = "'";
	  	$Option 							=& JRequest::getVar('option');
		$params 							=  JComponentHelper::getParams($Option);
		$price0							=  $params->get( 'price0');
		$CustomerPriceList			= cartweberpModelcartcatalog::getSalesType();
		$WherePricelist  = '';
		If(array_key_exists('stockid',$post)){
			$Search = $post['stockid'];
				$query = "SELECT 	prices.stockid,
										prices.price,
										stockmaster.stockid,
										stockmaster.description,
										stockmaster.units
 				      	FROM " . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
						 					  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid  
						 	   WHERE ((lower(description) LIKE  lower('%" . $Search . "%') OR
 				        	  			lower(stockid)     LIKE  lower('%" . $Search . "%'))) " .  $AndShowonline . $SortCriteria;
 	     		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select parts from stock master file.  Make sure you have these records created before trying to select parts by year, make, and model.';
			$partlist =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);
		}
  		$DebtorInformation = cartweberpModelcartcatalog::getDebtorInformation();
		$PriceDebtorNo = $DebtorInformation['debtorno'];
		// echo '<pre>';var_dump($post , '<br><br> <b style="color:brown"> post  </b><br><br>');echo '</pre>';
  			If(array_key_exists('sortcriteria',$post)){
  				If($post['sortcriteria']=='price'){
  					$SortCriteria = " ORDER BY prices." . $post['sortcriteria'];
  				}else{
				$SortCriteria = " ORDER BY stockmaster." . $post['sortcriteria'];
			}					

			If($SortCriteria == ' ORDER BY prices.price'){
				$DebtorInformation = cartweberpModelcartcatalog::getDebtorInformation();
				$PriceDebtorNo = $DebtorInformation['debtorno'];
				$WherePricelist = " AND prices.typeabbrev = '" . $CustomerPriceList . "' 
										  AND (prices.debtorno = '" .$PriceDebtorNo ."' 
										  OR  prices.debtorno = '')";
			}else{
				$WherePricelist = " AND prices.typeabbrev = '" . $CustomerPriceList . "' 
										  AND (prices.debtorno = '" .$PriceDebtorNo ."' 
										  OR  prices.debtorno = '')";
			}
		}else{
			
			If($mainframe->getUserState( 'PartNumberSelected') == 'CHECKED'){
				$SortCriteria =' ORDER BY stockmaster.stockid';
				$WherePricelist = " AND prices.typeabbrev = '" . $CustomerPriceList . "' 
										  AND (prices.debtorno = '" .$PriceDebtorNo ."' 
										  OR  prices.debtorno = '')";
			}elseif($mainframe->getUserState('DescriptionSelected') == 'CHECKED') {
				$SortCriteria =' ORDER BY stockmaster.description';
				$WherePricelist = " AND prices.typeabbrev = '" . $CustomerPriceList . "' 
										  AND (prices.debtorno = '" .$PriceDebtorNo ."' 
										  OR  prices.debtorno = '')";
			}elseif($mainframe->getUserState('PriceSelected') == 'CHECKED') {
				$SortCriteria =' ORDER BY prices.price';
				$DebtorInformation = cartweberpModelcartcatalog::getDebtorInformation();
				$PriceDebtorNo = $DebtorInformation['debtorno'];
				$WherePricelist = " AND prices.typeabbrev = '" . $CustomerPriceList . "' 
										  AND (prices.debtorno = '" .$PriceDebtorNo ."' 
										  OR  prices.debtorno = '')";
			}else{
				$SortCriteria =' ORDER BY stockmaster.description';
				$WherePricelist = " AND prices.typeabbrev = '" . $CustomerPriceList . "' 
										  AND (prices.debtorno = '" .$PriceDebtorNo ."' 
										  OR  prices.debtorno = '')";
			}
			
		}	
		// echo $SortCriteria  . "=SortCriteria<br>";
		// echo $WherePricelist  . "=WherePricelist<br>";
		If(strlen(trim($weberp['includelocations'])) > 0){
			$includelocations = ' AND (SELECT SUM(quantity) FROM ' . $weberp['database'] . '.locstock WHERE locstock.stockid = stockmaster.stockid AND locstock.loccode IN ("' . str_replace(',','","' , $weberp['includelocations']) . '")) > 0';
		}else{
			$includelocations = '';
		}			
		If(strlen(trim($weberp['includesalescategories'])) > 0){
			$includesalescategories = ' AND (SELECT salescatprod.stockid FROM ' . $weberp['database'] . '.salescatprod WHERE salescatprod.stockid = stockmaster.stockid AND salescatprod.salescatid IN ("' . str_replace(',','","' , $weberp['includesalescategories']) . '") Limit 1)IS NOT NULL';
		}else{
			$includesalescategories = '';
		}
		If(cartweberpModelcartcatalog::CheckStartDate()){
			$WherePricelist = $WherePricelist . " AND ((NOW() BETWEEN startdate AND enddate) OR (enddate = '0000-00-00') OR (enddate >= NOW())) ";
		}		
		$Showonline = $this->CheckShowOnline();
		If(strlen(trim($Showonline)) > 0){
			$AndShowonline = " AND " . $Showonline;
		}else{
			$AndShowonline = "";
		}
		If($mainframe->getUserState( 'SalesCategoryURL') AND !array_key_exists('salescategory',$post)){
			$post['salescategory'] = $mainframe->getUserState( 'SalesCategoryURL');
		}
		// echo '<pre>';var_dump($post , '<br><br> <b style="color:brown">  post 502</b><br><br>');echo '</pre>';
			If(array_key_exists('stockid',$post) AND strlen(trim($post['stockid'])) > 0){
			$Search = $post['stockid'];
			// fuzzy search for partnumber and description
			If(strlen(trim($weberp['includesalescategories'])) > 0){
				$query = "SELECT 	salescatid
 					      	FROM " . $weberp['database'] . ".salescat 
 					     	  WHERE parentcatid  IN(" . $weberp['includesalescategories']. ")";
 	     		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category file.  Maybe this is the end of the category tree. Make sure you have these records created before trying to select parts by sales category.';
					$this->catlist =& modwebERPHelper::getCollumnArray($query,'webERP',$errorMessage,null,0);
					// echo '<pre>';var_dump($weberp['includesalescategories'] , '<br><br> <b style="color:brown">includesalescategories in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
					$IncludeSalesCategories = explode(",",$weberp['includesalescategories']);
		  			// echo '<pre>';var_dump($IncludeSalesCategories , '<br><br> <b style="color:brown">includesalescategories in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		  			// echo '<pre>';var_dump($weberp['includesalescategories'] , '<br><br> <b style="color:brown">includesalescategories in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
					// echo $weberp['includesalescategories']  . '= includesalescategories  in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
					$cats = "'";
		  			if(isset($this->catlist)){
		 	     		Foreach($this->catlist as $key=>$salescat){
		 	     			$cats = $cats . $salescat . "','";
		 	     		}
		 	     	}
		 	     	// echo '<pre>';var_dump($cats , '<br><br> <b style="color:brown"> cats  </b><br><br>');echo '</pre>';
	 	     		Foreach($IncludeSalesCategories as $key=>$salescat){
	 	     			$cats = $cats . $salescat . "','";
	 	     		}
	 	     		$EndCats = strrpos($cats,",");
	 	     		$cats = substr($cats,0,$EndCats);
			  		$query = "SELECT 	stockid as partnumber
		 					      FROM " . $weberp['database'] . ".salescatprod 
		 					     WHERE salescatid IN(" . $cats . ")
		 				    ORDER BY stockid";
	 	     		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category product file.  Make sure you have assigned parts to categories before trying to select parts by sales category.';
					$this->partlist =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);
				$query = "SELECT 	prices.stockid,
										prices.price,
										stockmaster.stockid as partnumber,
										stockmaster.description,
										stockmaster.units
	 				      	FROM " . $weberp['database'] . ".stockmaster
	 				      	JOIN " . $weberp['database'] . ".salescatprod LEFT OUTER JOIN 
					 		  		  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
					 		  	AND  (prices.typeabbrev = '" . $CustomerPriceList . "' 
					  			OR   prices.typeabbrev = NULL)
					         AND  (prices.debtorno = '" .$PriceDebtorNo ."' 
					  			OR   prices.debtorno = ''
					  			OR   prices.debtorno = NULL)
					 	     WHERE  ((	lower(description) 		LIKE  lower('%" . $Search . "%') OR
	 				        	  		lower(stockmaster.stockid)     		LIKE  lower('%" . $Search . "%') OR
	 				        	  		lower(longdescription)  LIKE  lower('%" . $Search . "%') )" .  $AndShowonline . " AND 
	 				        	  		salescatprod.stockid = stockmaster.stockid AND
	 				        	  		salescatid IN(" . $cats . ")) " . $WherePricelist . $includelocations . $includesalescategories . $SortCriteria;
 	     	}else{
 	     	  	$query = "SELECT 	prices.stockid,
										prices.price,
 	     	  							stockmaster.stockid as partnumber,
										stockmaster.description,
										stockmaster.units
 				      		FROM " . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
					 		  		  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
					 		  	AND  (prices.typeabbrev = '" . $CustomerPriceList . "' 
					  			OR   prices.typeabbrev = NULL)
					         AND  (prices.debtorno = '" .$PriceDebtorNo ."' 
					  			OR   prices.debtorno = ''
					  			OR   prices.debtorno = NULL) 
					 	     WHERE (   lower(stockmaster.description) 		LIKE  lower('%" . $Search . "%') OR
 				        	  				lower(stockmaster.stockid)     		LIKE  lower('%" . $Search . "%') OR
 				        	  				lower(stockmaster.longdescription)  LIKE  lower('%" . $Search . "%') ))" .  $AndShowonline .   $WherePricelist . $includelocations . $includesalescategories . $SortCriteria; 
 	     	}
 	     	  	// exact search for part number 
 	     	   // $query = "SELECT 	stockid,
			// 						description
 				// 	      FROM " . $weberp['database'] . ".stockmaster
 				//         WHERE stockid ='" . $Search . "' 
 	     	  	// 	  ORDER BY stockid";	
 	     	  	$errorMessage = 'Error Code-CM' .  __LINE__  . ' No items found for ' . $Search;
				If(!$this->partlist =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0) AND isset($Search)){
 	     			$searchapp =& JFactory::getApplication();
					$searchapp->enqueueMessage('No items found for ' . $Search);
	  			}	
			}elseIf(isset($post['category']) AND $post['category'] <> 'ALL'){
				$Where = " categoryid = '" . $post['category'] . "'" .  $AndShowonline ;
		  		$query = "SELECT 	prices.stockid,
										prices.price,
		  								stockmaster.stockid as partnumber,
										stockmaster.description,
										stockmaster.showonline,
										stockmaster.units
	 					     FROM " . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
						 		  		  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
						 		  	AND  (prices.typeabbrev = '" . $CustomerPriceList . "' 
						  			OR   prices.typeabbrev = NULL)
						         AND  (prices.debtorno = '" .$PriceDebtorNo ."' 
						  			OR   prices.debtorno = ''
						  			OR   prices.debtorno = NULL)
						 	  WHERE  " . $Where .  $WherePricelist . $includelocations . $includesalescategories . $SortCriteria;
 	     		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category product file.  Make sure you have assigned parts to categories before trying to select parts by sales category.';
				$this->partlist =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);
				// echo '<pre>';var_dump($this->partlist , '<br><br> <b style="color:brown">  this->partlist </b><br><br>');echo '</pre>';
	  		}elseIf((isset($post['salescategory']) AND strlen(trim($post['salescategory'])) > 0) ){
	  			// find sales categories if this is a parent
	  			$query = "SELECT 	salescatid
	 					      FROM " . $weberp['database'] . ".salescat 
	 					     WHERE parentcatid = " . $post['salescategory'];
 	     		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category file.  <span style="color:brown">This is OK if this category is not a parent</span>';
				If($this->catlist =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0)){
		  			$cats = "'" . $post['salescategory']  . "', '";
	 	     		Foreach($this->catlist as $key=>$salescat){
	 	     			// echo '<pre>';var_dump($salescat , '<br><br> <b style="color:brown">salescat in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
	 	     			$cats = $cats . $salescat["salescatid"] . "', '";
	 	     		}
	 	     		$EndCats = strrpos($cats,",");
	 	     		$cats = substr($cats,0,$EndCats);
	 	     		// echo $cats  . '=cats   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
	 	     		// find sub categories where these cats are parents
  					$query = "SELECT 	salescatid
	 					      	FROM " . $weberp['database'] . ".salescat 
	 					    	  WHERE parentcatid  IN(" . $cats . ")";
	 				// echo $query  . '=query   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';exit;
 	     			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category file.  <span style="color:brown">This is OK if this category is not a parent</span>';
					If($subcats =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0)){	
						$cats = $cats . ",'";
	 	     			Foreach($subcats as $key=>$salescat){
	 	     				// echo '<pre>';var_dump($salescat , '<br><br> <b style="color:brown">salescat in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
	 	     				$cats = $cats . $salescat["salescatid"] . "', '";
	 	     			}					
					} 	 
	 	     		$EndCats = strrpos($cats,",");
	 	     		$cats = substr($cats,0,$EndCats); 
	 	     		// echo $cats  . '=cats   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';exit;   		
	 	     		$query = "SELECT 	stockid as partnumber
		 					      FROM " . $weberp['database'] . ".salescatprod 
		 					     WHERE salescatid IN(" . $cats . ")
		 				    ORDER BY stockid";
		 			// echo $query  . '=query   in  cartcatalog.php models Line #' . __LINE__ . '  <br>';exit;
	 	     		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category product file.  Maybe these are not parent - the leaves on the tree';
					$this->partlist =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);
					// echo '<pre>';var_dump($this->partlist , '<br><br> <b style="color:brown">  this->partlist </b><br><br>');echo '</pre>';exit;
		  			// If there are no parts check parent then check all of parents subs
		  			If(!$this->partlist){
		  				$query = "SELECT 	stockid as partnumber
		 					      	FROM " . $weberp['database'] . ".salescatprod 
		 					     	  WHERE salescatid = " . $post['salescategory'] . "  
		 				    	  ORDER BY stockid";
		 				// echo $query  . "=query 630<br>";
	 	     			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category product file.  Make sure you have assigned parts to categories before trying to select parts by sales category.';
						$this->partlist =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);
		  			}
		  		}else{
		  				$query = "SELECT 	stockid as partnumber
		 					      	FROM " . $weberp['database'] . ".salescatprod 
		 					     	  WHERE salescatid = " . $post['salescategory'] . "  
		 				    	  ORDER BY stockid";
		 				// echo $query  . "=query 630<br>";
	 	     			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category product file.  Make sure you have assigned parts to categories before trying to select parts by sales category.';
						$this->partlist =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);
		  		}
			}else{				
				If(strlen(trim($Showonline)) > 0){
					$Where =' AND ' . $Showonline;
				}else{
					$Where = "";
				}		
	  			If(strlen(trim($weberp['includesalescategories'])) > 0){
	  				$parts =& $this->getPartsInSalesCategories();
	  				// echo '<pre>';var_dump($parts , '<br><br> <b style="color:brown">  parts 644</b><br><br>');echo '</pre>';
	  				If(strlen(trim($Where)) > 0){
	  					$Where = " AND stockmaster.stockid IN(" . $parts . ") ";
	  				}else{
	  					$Where = " stockmaster.stockid IN(" . $parts . ")"; 
	  				}	  			
				}	
				// IF THERE ARE NO SUBCATEGORIES FOR THE CATEGORIES THEN DO NOT LOOK UP 
				// $querycheck = "SELECT COUNT(stockid) FROM " . $weberp['database'] . ".stockcatprod WHERE stockid IN(" . $parts . ")"; 
				$parts = "'";	
				$query = "SELECT 	DISTINCT prices.stockid,
										stockmaster.stockid as partnumber,
										prices.price,
										stockmaster.description,
										stockmaster.units
	 					     FROM " . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
						 		  		  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
						 		  	AND  (prices.typeabbrev = '" . $CustomerPriceList . "' 
						  			OR   prices.typeabbrev = NULL)
						         AND  (prices.debtorno = '" .$PriceDebtorNo ."' 
						  			OR   prices.debtorno = ''
						  			OR   prices.debtorno = NULL)  
						 	  WHERE  " .  $Where . $WherePricelist . $includelocations . $includesalescategories . $SortCriteria;
	 			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the stockmaster or prices file.  <span style="color:brown">This always happens at the end of the tree of categories and causes an sql error about "DISTINCT.".</span> If not make sure you have assigned parts to categories before trying to select parts by sales category.';
				$this->partlist =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0);
				// echo $query  . "=query 669<br>";	  		
	  			// echo '<pre>';var_dump($this->partlist , '<br><br> <b style="color:brown">  this->partlist 667</b><br><br>');echo '</pre>';
			}	
  		// }	
	  	// do we need associated parts for catalog? 
	  	If(isset($this->partlist) AND gettype($this->partlist) == 'array'){
	  		Foreach($this->partlist as $key=>$stockrecord){
	  			IF(isset($stockrecord['partnumber'])){ 		
	  		 		$parts = $parts . $stockrecord['partnumber'] . "', '";
	  		 	}
	  		// 	If(isset($stockrecord['partnumber']) and trim($stockrecord['partnumber']) <> ''){
	  		// 		$associatedpart =& cartweberpModelcartcatalog::getAssociatedParts($stockrecord['partnumber']);
	  		// 		Foreach($associatedpart as $count=>$Code){	  				
	  		// 			$parts = $parts . $Code . "', '";
	  		// 		}
	  		// 	}
	  		}
	  	}
	  	If(gettype($parts) == 'array'){
	  		$parts = implode(",",$parts);
	  	}
		// echo $parts  . "=PARTS 687<br>";
	  	$parts = substr($parts,0,strrpos($parts, ","));
	  	If (!isset($AndShowonline)){
	  		$AndShowonline = '';
	  	}
	  	If(strlen(trim($parts)) > 0){
	  		// If parts with 0 price are not show then get rid of them.
	  		If($price0 == 0){
	  			$NewPartsList = $this->getPrice($parts);
	  			$parts = "'";
	  			Foreach($NewPartsList as $PartNumber=>$Price){
	  				$parts = $parts . $PartNumber  . "','";
	  			}
				$LastComma = strrpos($parts, ",");
				$parts = substr($parts, 0, $LastComma);
	  		}
	    	If(isset($category) AND $category == 'ALL'){
				$query = "SELECT 	DISTINCT prices.stockid,
										prices.price,
										stockmaster.stockid,
										stockmaster.description,
										stockmaster.categoryid,
										stockmaster.units,
										stockmaster.discountcategory
				 				FROM " . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
						 		  		  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
						 		  	AND  (prices.typeabbrev = '" . $CustomerPriceList . "' 
						  			OR   prices.typeabbrev = NULL)
						         AND  (prices.debtorno = '" .$PriceDebtorNo ."' 
						  			OR   prices.debtorno = ''
						  			OR   prices.debtorno = NULL)
						 	  WHERE  stockmaster.stockid IN(" . $parts . ") ";
				If(isset($weberp['dontselltypes']) AND $weberp['dontselltypes'] <> ''){
					$query = $query . "  AND stockmaster.mbflag NOT IN( '" . $weberp['dontselltypes']	 . "')  ";
				}
				$query = $query . " AND  stockmaster.discontinued = 0 " . $AndShowonline . $WherePricelist . $includelocations . $includesalescategories . $SortCriteria;	 
			}else{
				If(isset($category) AND !$category == ''){
					$query = "SELECT 	DISTINCT prices.stockid,
											prices.price,
											stockmaster.stockid,
											stockmaster.description,
											stockmaster.categoryid,
											stockmaster.units,
											stockmaster.discountcategory
					 				FROM " . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
						 		  		  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
						 		  	AND  (prices.typeabbrev = '" . $CustomerPriceList . "' 
						  			OR   prices.typeabbrev = NULL)
						         AND  (prices.debtorno = '" .$PriceDebtorNo ."' 
						  			OR   prices.debtorno = ''
						  			OR   prices.debtorno = NULL)
						 	     WHERE  stockmaster.stockid IN(" . $parts . ") AND 
					 			  			stockmaster.categoryid = " . $category . " 	AND ";
					If(isset($weberp['dontselltypes']) AND $weberp['dontselltypes'] <> ''){
						$query = $query . " stockmaster.mbflag NOT IN( '" . $weberp['dontselltypes']	 . "')   AND ";
					}
					$query = $query . " stockmaster.discontinued = 0 " . $AndShowonline . $WherePricelist .  $includelocations . $includesalescategories . $SortCriteria;
				}else{
					$query = "SELECT 	DISTINCT prices.stockid,
											prices.price,
											stockmaster.stockid,
											stockmaster.description,
											stockmaster.categoryid,
											stockmaster.units,
											stockmaster.discountcategory
					 				FROM " . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
						 		  		  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
						 		  	AND  (prices.typeabbrev = '" . $CustomerPriceList . "' 
						  			OR   prices.typeabbrev = NULL)
						         AND  (prices.debtorno = '" .$PriceDebtorNo ."' 
						  			OR   prices.debtorno = ''
						  			OR   prices.debtorno = NULL)
						 	     WHERE  stockmaster.stockid IN(" . $parts . ")	AND ";
					If(isset($weberp['dontselltypes']) AND $weberp['dontselltypes'] <> ''){
						$query = $query . " stockmaster.mbflag NOT IN( '" . $weberp['dontselltypes']	 . "')   AND ";
					}
					$query = $query . " stockmaster.discontinued = 0  " . $AndShowonline . $WherePricelist .  $includelocations . $includesalescategories . $SortCriteria;	
				}	          
			}
		}else{
			If($price0 == "1"){
				$query = "SELECT 	DISTINCT prices.stockid,
										prices.price,
										stockmaster.stockid,
										stockmaster.description,
										stockmaster.categoryid,
										stockmaster.units,
										stockmaster.discountcategory
						 				FROM " . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
						 		  		  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
						 		  	AND  (prices.typeabbrev = '" . $CustomerPriceList . "' 
						  			OR   prices.typeabbrev = NULL)
						         AND  (prices.debtorno = '" .$PriceDebtorNo ."' 
						  			OR   prices.debtorno = ''
						  			OR   prices.debtorno = NULL)
						 	     WHERE  ";						 			  
						If(isset($weberp['dontselltypes']) AND $weberp['dontselltypes'] <> ''){
							$query = $query . "  stockmaster.mbflag NOT IN( '" . $weberp['dontselltypes']	 . "')   AND ";
						}
						$query = $query . " stockmaster.discontinued = 0  " . $AndShowonline . $WherePricelist .  $includelocations . $includesalescategories . $SortCriteria;
			}else{
				$PriceList = $this->getSalesType();
				$query = "SELECT 	DISTINCT prices.stockid,
										prices.price,
										stockmaster.stockid,
										stockmaster.description,
										stockmaster.categoryid,
										stockmaster.units,
										stockmaster.discountcategory
						 				FROM " . $weberp['database'] . ".stockmaster LEFT OUTER JOIN 
						 		  		  " . $weberp['database'] . ".prices ON stockmaster.stockid = prices.stockid 
						 		  	AND  (prices.typeabbrev = '" . $CustomerPriceList . "' 
						  			OR   prices.typeabbrev = NULL)
						         AND  (prices.debtorno = '" .$PriceDebtorNo ."' 
						  			OR   prices.debtorno = ''
						  			OR   prices.debtorno = NULL)
						 	     WHERE prices.price <> 0 AND ";						 	
						 			  
						If(isset($weberp['dontselltypes']) AND $weberp['dontselltypes'] <> ''){
							$query = $query . "  mbflag NOT IN( '" . $weberp['dontselltypes']	 . "')   AND ";
						}
						$query = $query . " discontinued = 0  " . $AndShowonline .  $WherePricelist . $includelocations . $includesalescategories . $SortCriteria;
			}
		} 
		If($weberp['debug']){
			echo $query  . '=query   in  cartcatalog.php models Line #' . __LINE__ . '  <br>';
		}
		return $query;	
	}
	function getPartsInSalesCategories(){
		global $weberp;		
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}
		$errorMessage = '';
		$SearchCategories 	= trim($weberp['includesalescategories']);
		$IncludedCategories 	= trim($weberp['includesalescategories']);
		// $SearchCategories		= "'" . str_replace(",", "','", $SearchCategories) . "'";
		$parts = '';
		// echo '<pre>';var_dump($parts , '<br><br> <b style="color:brown"> parts 798</b><br><br>');echo '</pre>';
		// there are no category restrictions - get all categories
		If(strlen(trim($IncludedCategories)) == 0){
			$query = "SELECT 	stockid
	 				      FROM " . $weberp['database'] . ".salescatprod ";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category file.  <span style="color:brown">This always occurs at the end of the tree.</span>  Make sure you have categories before trying to select parts by category.'; 
			$parts =&  modwebERPHelper::getCollumnArray($query,'webERP',$errorMessage,NULL,0);
		}else{
			
			// echo '<pre>';var_dump($parts , '<br><br> <b style="color:brown"> parts  806</b><br><br>');echo '</pre>';
			// add all sub categories to list
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category file.  Make sure you have categories before trying to select parts by category.';
			While(strlen(trim($SearchCategories)) > 0){
		  		$query = "SELECT 	salescatid
		 				      FROM " . $weberp['database'] . ".salescat 
		 				     WHERE parentcatid 	IN(" . $SearchCategories . ")";
				$SearchCategories = '';
				// echo $query  . "=query 841<br>";
				$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category file.  <span style="color:brown">This always occurs at the end of the tree.</span>  Make sure you have categories before trying to select parts by category.'; 
				If($SearchArray =&  modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0)){	
					Foreach($SearchArray as $count=>$categoryArray){
						$category = $categoryArray["salescatid"];
						// echo '<pre>';var_dump($category , '<br><br> <b style="color:brown"> category  </b><br><br>');echo '</pre>';
						If(strlen(trim($category)) > 0){
							If(strlen(trim($SearchCategories)) > 0){
								$SearchCategories = $SearchCategories . "," .  $category;
							}else{
								$SearchCategories = $category;
							}
						}else{
							$SearchCategories = $category;
						}					
					}
				}
				If(strlen(trim($SearchCategories)) > 0){
			  		$IncludedCategories 	= $IncludedCategories . "," . $SearchCategories;
			  		// echo $IncludedCategories  . "=IncludedCategories 560<br>";
			  	}
			}
			// echo $IncludedCategories  . "=IncludedCategories563<br>";
			$query = "SELECT 	stockid
	 				      FROM " . $weberp['database'] . ".salescatprod 
	 				     WHERE salescatid 	IN(" . $IncludedCategories . ")";
	 		// echo $query  . "=query 867<br>";
	 		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category file.  Make sure you have categories before trying to select parts by category.';
			If($SearchArray =&  modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0)){	 
				Foreach($SearchArray as $count=>$stockid){
					// echo '<pre>';var_dump($parts , '<br><br> <b style="color:brown"> parts  841</b><br><br>');echo '</pre>';
					// echo '<pre>';var_dump($stockid , '<br><br> <b style="color:brown"> stockid  844</b><br><br>');echo '</pre>';
		 			If(isset($parts[0]) AND strlen(trim($parts[0])) > 0){
						$parts = $parts . "," .  $stockid;
					}else{
						$parts = $stockid;
					}
				}	
			}
			$parts = "'" . str_replace(",", "','", $parts) . "'" ;
		}		
		return $parts;
	}
	function _buildQuerydescription()
	{
		global $weberp;
		$post			= JRequest::get('request');
		$StockID 	= $post['stockid'];
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}
    	$query 		= 	"SELECT 	stockid LIMIT 0,1,description,longdescription,categoryid
			 				   FROM " . $weberp['database'] . ".stockmaster
			 			     WHERE description LIKE ='%" . $StockID . "%'";	          
		return $query;		
	}
	function getStockIdFromParameters(){
		global $HardwareAddress;
		$HardwareAddress = modwebERPHelper::_getCartID();
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT stockid FROM " . $this->_table_prefix . "parameters WHERE hardwareaddress = '" . $HardwareAddress . "'" ;
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category file.  Make sure you have categories before trying to select parts by category.';
		$row =&  modwebERPHelper::getRowList($query,'Joomla',$errorMessage,null,0);	 
		$stockid='';
		Foreach($row as $key=>$value){
			Foreach($value as $Key=>$Value){
				If($Key == 'stockid'){
					$stockid 		= $Value;
				}
			}
		}
	  	return $stockid;
	}	
	function getParameters(){
		global $HardwareAddress,$mainframe;
		$HardwareAddress = modwebERPHelper::_getCartID();
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "parameters WHERE hardwareaddress = '" . $HardwareAddress . "'" ;	                 
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the parameters file. <span style="color:brown">This is OK if no parameters like year make and model have been selected by user</span>';
		$row =&  modwebERPHelper::getRowArray($query,'Joomla',$errorMessage,NULL,0);
		$parameterlist = array();
  		// foreach ( $rows as $row ) {
  			// echo '<pre>';var_dump($row , '<br><br> <b style="color:brown">row   </b><br><br>');echo '</pre>';
    		$parameterlist['year'] 		 	= $row['year'];
    		$parameterlist['make'] 			= $row['make'];    		
			If(strlen(trim($parameterlist['make'])) > 1){
    			$parameterlist['makename']	= $this->getMakeName($parameterlist['make']);
    		}
    		$parameterlist['model'] 		= $row['model'];
    		$parameterlist['modelname']	= $row['modelname'];
    		$parameterlist['category'] 	= $row['category'];
    		$parameterlist['categoryname']= $this->getCategoryDesc($row['category']);
    		$parameterlist['stockid'] 		= $row['stockid'];
			$mainframe->setUserState( "make", $row['make']) ;
			If(isset($parameterlist['makename'])){
				$mainframe->setUserState( "makename", $parameterlist['makename']) ;
			}
			$mainframe->setUserState( "carmodel", $row['model']) ;
			$mainframe->setUserState( "year", $row['year']) ;
			$mainframe->setUserState( "category", $row['category']) ;
			$mainframe->setUserState( "stockid", $row['stockid']) ;
  		// }
	  	return $parameterlist;
	}
	function storeParameters($choices){ 
		$choices['hardwareaddress'] = modwebERPHelper::_getCartID();
		$HardwareAddress = $choices['hardwareaddress'];
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT id FROM " . $this->_table_prefix . "parameters WHERE hardwareaddress = '" . $HardwareAddress . "'" ;	                 
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the parameters file.';
		$choices['id'] =&  modwebERPHelper::getResult($query,'Joomla',$errorMessage,null,0);
		$row =& $this->getTable('parameters');
		if (!$row->bind($choices)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	function storeCategoryParameter($category){ 
		global $mainframe;
		$choices['hardwareaddress'] = modwebERPHelper::_getCartID();
		$HardwareAddress = $choices['hardwareaddress'];
		$post			= JRequest::get('request');
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the parameters file.';
		$query = "SELECT * FROM " . $this->_table_prefix . "parameters WHERE hardwareaddress = '" . $HardwareAddress . "'" ;	                 
		If($rows =&  modwebERPHelper::getRowList($query,'Joomla',$errorMessage,null,0)){
			foreach ( $rows as $row ) {
				$choices['id'] 		= $row->id;
				$choices['category']	= $post['category'];
				$choices['make'] 		= $row->make;
				$choices['model'] 	= $row->model;
				$choices['year'] 		= $row->year;
				$mainframe->setUserState( "id", $row->id) ;
				$mainframe->setUserState( "make", $row->make) ;				 		
				If(strlen(trim($row->make)) > 1){
					$mainframe->setUserState( "makename", $this->getMakeName($row->make)) ;
				}
				$mainframe->setUserState( "carmodel", $row->model) ;
				$mainframe->setUserState( "year", $row->year) ;
				$mainframe->setUserState( "category", $row->category) ;
			}
		}
		$row =& $this->getTable('parameters');
		if (!$row->bind($choices)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	function getMakeModelCategoryParameters(){
		global $mainframe;
		$post	= JRequest::get('post');
		If(!isset($post['make'])){
			$parameterlist =& $this->getParameters();
			$choices=array();
			If(isset($parameterlist['make']) AND strlen(trim($parameterlist['make'])) > 0){
				Foreach($parameterlist as $VariableName=>$VariableValue){
					$choices[$VariableName] = $VariableValue;
				}
			}
		}else{
			$choices['year'] 		 	= $post['year'];
    		$choices['make'] 			= $post['make'];
    		$choices['model'] 		= $post['version'];
    		If(strlen(trim($post['version'])) > 1){
    			$choices['modelname']	= $this->getModelDesc($post['version']);
    		}
    		$choices['category'] 	= $post['category'];
    		$choices['stockid'] 	= '';
    		$choices['categoryname']= $this->getCategoryDesc($post['category']);
			$mainframe->setUserState( "make", 			$choices['make']) ;			 		
			If(strlen(trim($post['make'])) > 1){			
				$mainframe->setUserState( "makename", 		$this->getMakeName($post['make'])) ;
			}
			$mainframe->setUserState( "carmodel", 		$choices['model']) ;
			$mainframe->setUserState( "year", 			$choices['year']) ;
			$mainframe->setUserState( "category", 		$choices['category']) ;
			$mainframe->setUserState( "categoryname", $choices['categoryname']) ;
			$mainframe->setUserState( "stockid", '') ;
			$this->storeParameters($choices);
		}
		return $choices;
	}
	function getMakeModelCategoryPost(){
		global $mainframe;
		$post	= JRequest::get('post');
		$mainframe->setUserState( "make", $post['make']) ;
		If(strlen(trim($post['make'])) > 1){
			$mainframe->setUserState( "makename",$this->getMakeName($post['make'])) ;
		}
		If(strlen(trim($post['version'])) > 1){
			$mainframe->setUserState( "carmodel", $post['version']) ;
			$mainframe->setUserState( "modelname", $this->getModelDesc($post['version']));
		}
		$mainframe->setUserState( "year", $post['year']) ;
		$mainframe->setUserState( "category", $post['category']) ;
		$mainframe->setUserState( "stockid",  $this->getStockIdFromParameters()) ;		
		$choices['model'] 		= $post['version'];
		$choices['make'] 			= $post['make'];
		$choices['category'] 	= $post['category'];
		$choices['year'] 			= $post['year'];
		If(strlen(trim($post['version'])) > 1){
			$choices['modelname']	= $this->getModelDesc($post['version']);
		}
		$choices['categoryname']= $this->getCategoryDesc($post['category']);
		$choices['stockid'] 		= $this->getStockIdFromParameters();
		$this->storeParameters($choices);
		return $choices;
	}	
	function getModelDesc($modelid){
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT model,series,body,engine FROM " . $this->_table_prefix . "models WHERE id = " . $modelid ;
	  	$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the models file. Make sure you have entered models before trying to select parts using make, model, and year.';
		$modelid =&  modwebERPHelper::getResult($query,'Joomla',$errorMessage,null,0);
	  	$modeldesc = "";
	  	Foreach($modelid as $key1=>$value1){
	  		Foreach($value1 as $key=>$value){
	  			$modeldesc = $modeldesc . " " . $value;
	  		}
	  	}
		return $modeldesc;
	}
	function getMakeName($makeid){
		$makename = '';
		If(strlen(trim($makeid)) > 1){
			$db = &JFactory::getDBO();
			$this->_table_prefix = '#__cart_';
			$query = "SELECT manufacturer FROM " . $this->_table_prefix . "manufacturer WHERE id = " . $makeid ;
		  	$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the manufacturer file. ';
			$makename =  modwebERPHelper::getResult($query,'Joomla',$errorMessage,NULL,0);
		}
		return $makename;
	}
	function getCategoryDesc($categoryid){
		global $weberp;
		$post	= JRequest::get('post');
		$categorydescription=array();
		If($categoryid == 'ALL' or isset($post['stockid']) or !isset($categoryid) or strlen(trim($categoryid)) == 0){
			$categorydescription = 'ALL';
		}else{
			$query = "SELECT categorydescription FROM " . $weberp['database'] . ".stockcategory WHERE categoryid = '" . $categoryid . "'";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the stockcategory file. Make sure you have entered stock categorys before trying to select parts this way.';
			$categorydescription =  modwebERPHelper::getResult($query,'webERP',$errorMessage,null,0);			
		}
		return $categorydescription;
	}
	function getCategoryDescriptions(){
		global $weberp;
		$categorydescriptions = array();
		$query = "SELECT categorydescription,categoryid FROM " . $weberp['database'] . ".stockcategory ";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the stockcategory file. Make sure you have entered stock categorys before trying to select parts this way.';
		If($rows =  modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0)){
			// echo '<pre>';var_dump($rows , '<br><br> <b style="color:brown">  rows </b><br><br>');echo '</pre>';		
			Foreach($rows as $row){	
				// echo '<pre>';var_dump($row , '<br><br> <b style="color:brown">  row </b><br><br>');echo '</pre>';			
				$id 								= $row['categoryid'];
				$categorydescriptions[$id] = $row['categorydescription'];
			}
		}
		return $categorydescriptions;
	}
	function getSalesCategoryDescriptions(){
		global $weberp;
		$salescategorydescriptions = array();
    	$db 			=& JFactory::getDBO();
		$db = & JDatabase::getInstance( $weberp );
		$IncludeCategories = $weberp['includesalescategories'];
		$LastLengthOfCatagories = 0;   
		While(strlen(trim($IncludeCategories)) <> $LastLengthOfCatagories ){			
			If(isset($LengthOfCatagories)){
				$LastLengthOfCatagories = $LengthOfCatagories ;
			}
			$query = "SELECT parentcatid,salescatid,salescatname FROM " . $weberp['database'] . ".salescat 
																			 WHERE salescatid IN(" . $IncludeCategories . ") OR
																			 		 parentcatid IN(" . $IncludeCategories . ")" ;
			$IncludeCategories = '';               
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the sales category file. Make sure you have entered sales categories before trying to select parts this way.';
			If($salescategorydescriptions =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0)){ 
				Foreach($salescategorydescriptions as $recordcount=>$row){						 
					$IncludeCategories = $IncludeCategories .  $row['salescatid'] . ",";              			
					$parentcatid 					= $row['parentcatid'];
					$salescatid 					= $row['salescatid'];
					$salescategorydescriptions[$parentcatid][$salescatid] = $row['salescatname'];	
				}
			}
			$LengthOfCatagories = strlen($IncludeCategories)-1;
			$IncludeCategories = substr($IncludeCategories, 0, $LengthOfCatagories);
		}
		return $salescategorydescriptions;
	}
	function getCategory(){
		global $weberp;
		If(!isset($weberp['database'])){
			echo "Set up webERP parameters in CARTwebERP";exit;
		}
		$query = "SELECT 	categoryid,
						categorydescription
						FROM " . $weberp['database'] . ".stockcategory 
			     ORDER BY categorydescription";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the stock category file. Make sure you have entered stock categories before trying to select parts this way.';
		$CategoryDescription =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,'categoryid')	;
		return $CategoryDescription;
	}
	function AddPartsToCart($PartsOnPage){
		
		// global $parts;				// if quantity is more than on hand check to see if it gets adjusted
		$Option 							=& JRequest::getVar('option');
		$params 							= JComponentHelper::getParams($Option);
		$adjustquantity 				= $params->get( 'adjustquantity');
		$post	= JRequest::get('post');
		$price =& $post['Price'];
		$choices['hardwareaddress'] = modwebERPHelper::_getCartID();
		$HardwareAddress = $choices['hardwareaddress'];
		$parts = "'";
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query =  "SELECT * FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'" ;
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the orders file.';
		If($rows =& modwebERPHelper::getRowList($query,'Joomla',$errorMessage,NULL,0)){
			foreach ( $rows as $row ) {
				$quantity 	= $row['quantityordered'];				
				If($quantity > .1 OR $quantity < -.1){
					$id 								= $row['id'];
					$part 							= $row['partnumber'];
					$PartOrdersID[$part] 		= $id;
					$PartOrdersQuantity[$part] = $quantity;
					$parts = $parts . $part . "', '";
				}
			}
		}
		$parts = substr($parts,0,strrpos($parts, ","));
		If(!isset($PartOrdersID)){
			$PartOrdersID = array();
		}

		foreach($PartsOnPage as $Part=>$Quantity){		
			If($Quantity > .1 OR $Quantity < -.1){
				// if part is a kit then add components not kit
				If($this->PartIsAKit($Part)){
					$this->AddKitToOrder($Part,$Quantity);
				}else{
					If(isset($PartOrdersQuantity) AND array_key_exists($Part,$PartOrdersQuantity)){
						$order['id'] 	= $PartOrdersID[$Part];
						$order['quantityordered'] 	= $PartOrdersQuantity[$Part] + $Quantity;
					}else{
						$order['id'] 	= 0;
						$order['quantityordered'] 	= $Quantity;
					}
					// if quantity is more than on hand check to see if it gets adjusted
					If($adjustquantity){
						$onhandquantity				= $this->getQuantityOnHand($Part);
						If($order['quantityordered'] > $onhandquantity and $adjustquantity == True){
							$order['quantityordered'] = $onhandquantity;
						}
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
					$this->AddPartToCart($order);
				}
			}
		}
		return true;
	}
	function PartIsAKit($Part){
		global $weberp;		
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}
		$db = &JFactory::getDBO();
		$db = & JDatabase::getInstance( $weberp );
		$query = "SELECT mbflag
						FROM " . $weberp['database'] . ".stockmaster
			        WHERE stockid ='" . $Part . "'";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from the stockmaster file. Make sure you have entered stockmaster before trying to select parts as kit.';
		$PartIsAKit =  modwebERPHelper::getResult($query,'webERP',$errorMessage,null,0);			
		if($PartIsAKit == 'K'){
			return TRUE;
		}else{
			return FALSE;
		}		
	}
	function AddKitToOrder($part,$quantity){
		global $weberp;
		// get sales type from customer record or parameter table
		$salestype 					= $this->getSalesType();
		$Option 						=& JRequest::getVar('option');
		$params 						= JComponentHelper::getParams($Option);
		$adjustquantity 			= $params->get( 'adjustquantity');
		$listsalestype 			= $params->get( 'listsalestype');
		$choices['hardwareaddress'] = modwebERPHelper::_getCartID();
		$HardwareAddress = $choices['hardwareaddress'];
		$parts = "'";
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'" ;	                 
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to selcartcatalog.php on line 815ect from the orders file.';
		If($rows = modwebERPHelper::getRowList($query,'Joomla',$errorMessage,null,0)){
			foreach ( $rows as $row ) {
				$quantityordered 	= $row['quantityordered'];				
				If($quantityordered > .1 OR $quantityordered < -.1){
					$id 								= $row['id'];
					$orderedpart 					= $row['partnumber'];
					$PartOrdersID[$orderedpart] = $id;
				}
			}
		}
		$query = "SELECT component,quantity
						FROM " . $weberp['database'] . ".bom 
			        WHERE parent ='" . $part . "'";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from BOM file.';
		$rows = modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);
		Foreach($rows as $row)
		{		
			// var_dump($row);echo "<br><br>row<BR><BR><BR>";	
			$order['partnumber']					= $row['component'];
			$order['price']						= $this->getAPrice($order['partnumber']);
			$order['quantityordered']			= $row['quantity'] * $quantity;
			If($adjustquantity){
				$onhandquantity				= $this->getQuantityOnHand($component);
				If($order['quantityordered'] > $onhandquantity){
					$order['quantityordered'] = $onhandquantity;
				}
			}
			$order['hardwareaddress'] 	= $HardwareAddress;
			$order['date'] = date("Y-m-d G.i:s<br>", time());					
			If(isset($PartOrdersID) AND array_key_exists($order['partnumber'],$PartOrdersID)){
				$order['id'] 		= $PartOrdersID[$order['partnumber']];
			}else{
				$order['id'] 		= 0;
			}
			$this->AddPartToCart($order);			
		}
		return TRUE;
	}
	function AddPartToCart($order){	
		// echo '<pre>';var_dump($order , '<br><br> <b style="color:brown"> order  </b><br><br>');echo '</pre>';	
		JTable::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_cartweberp'.DS.'tables');
		$row =& $this->getTable('orders');
		// echo '<pre>';var_dump($row , '<br><br> <b style="color:brown"> row  </b><br><br>');echo '</pre>';
		if (!$row->bind($order)) {
			$this->setError($this->_db->getErrorMsg());
			echo $this->_db->getErrorMsg(). "=this->_db->getErrorMsg() <BR>";exit;
			return false;
		}
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			echo $this->_db->getErrorMsg(). "=this->_db->getErrorMsg() <BR>";exit;
			return false;
		}
	}
	function getPrice($items){
		global $weberp;
		$parts = "'";
		If(gettype($items)=='array'){
			For($i=0;$i<count($items);$i++){
				If(strlen(trim($items[$i]['stockid'])) > 0 ){
					$parts = $parts . $items[$i]['stockid'] . "','";
				}
			}
			$LastComma = strrpos($parts, ",");
			$parts = substr($parts, 0, $LastComma);
		}else{
			$parts = $items;
		}
		$price = array();
		$salestype = cartweberpModelcartcatalog::getSalesType();
		If(cartweberpModelcatalog::CheckStartDate()){
			$WherePricelist = " AND NOW() BETWEEN startdate AND enddate ";
		}else{
			$WherePricelist = '';
		}
		$query = "SELECT price,stockid
						FROM " . $weberp['database'] . ".prices 
			        WHERE stockid IN(" . $parts . ") AND
			              typeabbrev ='" . $salestype . "'" . $WherePricelist;
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from prices file. Make sure prices are entered correctly.';
		$rows = modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);		
		$result = mysql_query($query) or die($query . " Query in getPrice  Line 1050 failed : " . mysql_error());
		Foreach($rows as $row)
		{				
			$stockid 				= $row['stockid'];
			$price[$stockid] 		= (float)$row['price'];
		}
		return $price;
	}		
	function getAPrice($part){
		global $weberp;
		$price = 0;
		If(strlen(trim($part)) > 0){
			// get sales type from customer record or parameter table
			$salestype = $this->getSalesType();
			$query = "SELECT price,stockid
							FROM " . $weberp['database'] . ".prices 
				        WHERE stockid ='" . $part . "' AND
				              typeabbrev ='" . $salestype . "'";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from prices file. Make sure prices are entered correctly.';
			$price = modwebERPHelper::getResult($query,'webERP',$errorMessage,null,0);		
		}
		return $price;
	}
	function getAListPriceKit($part){
		global $weberp;
		// echo $part  . "=part<br>";exit;
		$price = 0;
		If(strlen(trim($part)) > 0){
			// get sales type from customer record or parameter table
			$salestype = cartweberpModelcartcatalog::getSalesType();
			$query = "SELECT price,stockid
							FROM " . $weberp['database'] . ".prices 
				        WHERE stockid ='" . $part . "' AND
				              typeabbrev ='" . $salestype . "'";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from prices file. Make sure prices are entered correctly.';
			$price = modwebERPHelper::getResult($query,'webERP',$errorMessage,null,0);				
		}
		return $price;
	}
	function getKitPrice(){
		global $weberp,$parts;
		$kitprice = ARRAY();
		If(strlen(trim($parts)) > 0){
			// echo $parts  . "=parts<br>";
			// get sales type from customer record or parameter table
			$salestype 					= $this->getSalesType();
			$listsalestype 			= $weberp['listsalestype'];
			$query = "SELECT parent,component,quantity
							FROM " . $weberp['database'] . ".bom 
				        WHERE parent IN(" . $parts . ")";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from bom file. <span style="color:brown">Ok if there are no kits it cart</span>';
			If($rows = modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0)){				
				foreach($rows as $row)
				{		
					// var_dump($row);echo "<br><br>row<BR><BR><BR>";		
					$parent 									= $row['parent'];
					$component 								= $row['component'];
					$quantity 								= $row['quantity'];
					$query = "SELECT price,stockid
								FROM " . $weberp['database'] . ".prices 
					        WHERE stockid ='" . $component . "' AND
					              typeabbrev ='" . $salestype . "'";
					$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from prices file. ';
					$rows2 = modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0);				
					Foreach($rows2 as $row2)
					{				
					// var_dump($row2);echo "<br><br>row2<BR><BR><BR>";
						$stockid 				= $row2['stockid'];
						$kitprice['customerprice'][$parent][$component] = (float)$row2['price'] * $quantity;
					}
					$query = "SELECT price,stockid
								FROM " . $weberp['database'] . ".prices 
					        WHERE stockid ='" . $component . "' AND
					              typeabbrev ='" . $listsalestype . "'";
					$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from prices file. ';
					$rows3 = modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0);				
					Foreach($rows3 as $row3)
					{				
					// var_dump($row3);echo "<br><br>row3<BR><BR><BR>";
						$stockid 				= $row3['stockid'];
						$kitprice['listprice'][$parent][$component] = (float)$row3['price'] * $quantity;
					}
				}
			}
		}
		// var_dump($kitprice);echo "<br><br>kitprice<BR><BR><BR>";exit;
		return $kitprice;
	}
	function getCurrencies(){	
		global $weberp;	
		$dbw 		=& JFactory::getDBO();
		$dbw = & JDatabase::getInstance( $weberp );
		$query = "SELECT 	*
 				      FROM " . $weberp['database'] . ".currencies";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from currencies file. ';
		$currencies = modwebERPHelper::getRowList($query,'webERP',$errorMessage,'currabrev');	
		// echo '<pre>';var_dump($currencies , '<br><br> <b style="color:brown">  currencies </b><br><br>');echo '</pre>';			
		return $currencies;
	}
	function getListPrice(){
		global $weberp,$parts;
		$Option 						=& JRequest::getVar('option');
		$params 						= JComponentHelper::getParams($Option);
		$salestype 					= $params->get( 'listsalestype');
		$listprice = ARRAY();
		If(strlen(trim($parts)) > 0){
			$query = "SELECT price,stockid
							FROM " . $weberp['database'] . ".prices 
				        WHERE stockid IN(" . $parts . ") AND
				              typeabbrev ='" . $salestype . "'";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from prices file. ';
			$rows = modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);				
			Foreach($rows as $row)
			{				
				$stockid 				= $row['stockid'];
				$listprice[$stockid] = (float)$row['price'];
			}
		}
		return $listprice;
	}
	function getQuantityOnHand(){		
		global $weberp, $parts;
		$quantityonhand = array();		
		If(strlen(trim($parts)) > 0){
			If(strlen(trim($weberp['includelocations'])) > 0){
				$includelocations = " AND loccode IN ('" . str_replace(",","','" , $weberp["includelocations"]) . "')";
			}else{
				$includelocations = '';
			}
			$query = "SELECT 	SUM(quantity) as onhand,
									stockid
							FROM " . $weberp['database'] . ".locstock 
				        WHERE stockid IN(" . $parts . ") " . $includelocations . " 
				     GROUP BY stockid";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from locstock file. ';
			$rows = modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);				
			$result = mysql_query($query) or die("$query Query in getQuantityOnHand failed : " . mysql_error());
			Foreach($rows as $row)
			{		
				$stockid 							= $row['stockid'];
				$quantityonhand[$stockid] 		= (float)$row['onhand'];
				//Look up demand
 				$Demand = 0;
 				$query = "SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
             							 FROM " . $weberp['database'] . ".salesorderdetails 
             							WHERE salesorderdetails.completed=0
             							  AND salesorderdetails.stkcode='" . $stockid . "'";
      		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from salesorderdetails file. <span style="color:brown">This is OK if there are no open sales orders with this part</span>';
				$Demand = modwebERPHelper::getResult($query,'webERP',$errorMessage,NULL,0);
 				If($quantityonhand[$stockid] > $Demand){
 					$quantityonhand[$stockid] = (float)($quantityonhand[$stockid] - $Demand);
 				}else{
 					$quantityonhand[$stockid] = 0;
 				}					
			}
		}
		return $quantityonhand;
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
	function getCart(){
		$HardwareAddress = modwebERPHelper::_getCartID();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT * FROM " . $this->_table_prefix . "orders WHERE hardwareaddress = '" . $HardwareAddress . "'";
	  	$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from orders file. <span style="color:brown">OK if nothing in cart yet</span>';
		$cart = modwebERPHelper::getRowList($query,'Joomla',$errorMessage,'partnumber',0);
		// echo '<pre>';var_dump($cart , '<br><br> <b style="color:brown">  cart </b><br><br>');echo '</pre>';
		return $cart;
	}

	function getDebtorInformation(){
		global $weberp;
		$row = array();
		$user 		=& JFactory::getUser();
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT customer FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->id . "'" ;
	  	$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from usercustomer file. <span style="color:brown">OK if user - customer cross reference does not exist</span>';
		$CustomerCross = modwebERPHelper::getResult($query,'Joomla',$errorMessage,NULL,0);	
	  	If(isset($CustomerCross) and strlen(trim($CustomerCross)) > 0){
			$query = "SELECT 	*
							FROM " . $weberp['database'] . ".debtorsmaster,
								  " . $weberp['database'] . ".custbranch
				        WHERE 	debtorsmaster.debtorno ='" . $CustomerCross . "' AND 
				        			custbranch.debtorno = debtorsmaster.debtorno";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from debtormaster and custbranch files. ';
			$row = modwebERPHelper::getRowArray($query,'webERP',$errorMessage,NULL,0);	
	  	}else{
		   $row['debtorno'] = '';
		}
		return $row;
	}
	function getDebtorCurrency(){
		global $weberp;
		$DebtorInfo =& cartweberpModelcartcatalog::getDebtorInformation();
		// echo '<pre>';var_dump($DebtorInfo , '<br><br> <b style="color:brown"> DebtorInfo  </b><br><br>');echo '</pre>';
		If($DebtorInfo AND array_key_exists('currcode', $DebtorInfo)){
			$DebtorCurrency = $DebtorInfo['currcode'];
		}else{
			$DebtorCurrency = $weberp['currencycode'];
		}
		return $DebtorCurrency;
	}
	function getAssociatedParts($item){
		$this->_table_prefix = '#__cart_';
		$AssociatedCount = 0;
		$AssociatedParts=array();
		// get associated substitute parts for items
		$query = "SELECT 	partnumber,
								associatedpart,
								associationtype
						FROM " . $this->_table_prefix . "associatedparts 
		       	  WHERE 	associatedpart ='" . $item . "' AND
		       	  			(associationtype = 13 OR
		       	  			associationtype = 14)";		       	  			
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from associatedparts file. ';
		$rows = modwebERPHelper::getRowList($query,'Joomla',$errorMessage,null,0);	
		If(isset($rows) > 0){
			foreach($rows as $row){							
				$AssociatedParts[$AssociatedCount]	= $row['partnumber'];
				$AssociatedCount = $AssociatedCount + 1;
			}	
		}	
		// get associated parts for items
		$query = "SELECT 	partnumber,
								associatedpart,
								associationtype
						FROM " . $this->_table_prefix . "associatedparts 
		       	  WHERE 	partnumber ='" . $item . "' AND
		       	  			associationtype > 10";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from associatedparts file. ';
		$rows = modwebERPHelper::getRowList($query,'Joomla',$errorMessage,null,0);	
		$AssociatedParts = array();
		If(isset($rows) > 0){
			foreach($rows as $row){							
				$AssociatedParts[$AssociatedCount]	= $row['associatedpart'];
				$AssociatedCount = $AssociatedCount + 1;
			}
		}
		return $AssociatedParts;
	}	
	function getMakeModelStatus(){
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$query = "SELECT model FROM " . $this->_table_prefix . "models" ;
	  	$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from models file.  <span style="color:brown">This is OK if Year - Make - Models are not used to select parts</span>';
		If ($modelid = modwebERPHelper::getRowList($query,'Joomla',$errorMessage,NULL,0)){
	  		$MakeModelStatus = true;
	  	}else{
	  		$MakeModelStatus = false;
	  	}
	  	return $MakeModelStatus;
	}
	function getCorePart($stockid){	
		$this->_table_prefix = '#__cart_';
		$query = "SELECT 	associatedpart
						FROM " . $this->_table_prefix . "associatedparts 
		       	  WHERE 	partnumber ='" . $stockid . "' AND
		       	  			associationtype = 33";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from associatedparts file. ';
		$CorePart = modwebERPHelper::getResult($query,'Joomla',$errorMessage,null,0);	
		return $CorePart;
	}
	function CheckShowOnline(){
		global $weberp;
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}
	  	$query = "SELECT *
						FROM " . $weberp['database'] . ".stockmaster LIMIT 1";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from stockmaster file. ';
		If($collums = modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0)){	
			If(array_key_exists("showonline", $collums)){
				$ShowOnline = " showonline = 1";
			}else{
				$ShowOnline = "";
			}	
		}else{
			$ShowOnline = "";
		}
		return $ShowOnline;	
	}	
	function CheckStartDate(){
		global $weberp;		
		If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
			$weberp	= modwebERPHelper::getwebERP();
		}
		$query	 = "SELECT *
							FROM " . $weberp['database'] . ".prices LIMIT 1";
		// echo $query  . "=query 1636<br>";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from prices file. ';			
		If($collums = modwebERPHelper::getRowArray($query,'webERP',$errorMessage,null,0)){
			// echo '<pre>';var_dump($collums , '<br><br> <b style="color:brown"> collums  </b><br><br>');echo '</pre>';
			If(array_key_exists("startdate", $collums)){				
				$query = "UPDATE  " . $weberp['database'] . ".prices SET enddate = '2050-12-31' WHERE enddate = '0000-00-00'";
				$errorMessage = 'Error Code-SM' .  __LINE__  . ' Unable to update end dates in prices file. ';
				$this->_data =& modwebERPHelper::getInsertUpdate($query,'webERP',$errorMessage,null,0);
				$CheckStartDate = 1;
			}else{
				$CheckStartDate = 0;
			}
		}else{
				$CheckStartDate = 0;
		}	
		// echo $CheckStartDate  . "=CheckStartDate<br>";
		return $CheckStartDate;	
	}	
	function getFreight(){
		global $weberp, $mainframe;
		$query = "SELECT 	*
						FROM " . $weberp['database'] . ".shippers";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from shippers file. ';			
		$rows = modwebERPHelper::getRowList($query,'webERP',$errorMessage,null,0);
		Foreach($rows as $row)
		{				
				$shipperid 				= $row['shipper_id'];
				$Freight[$shipperid] = $row['shippername'];
				$FreightSelected		= $mainframe->getUserState("FreightSelected");
				// set freight to first value as default incase the cart is not updated with new freightselected
				If($FreightSelected == NULL or $FreightSelected==''){
					$mainframe->setUserState( "FreightSelected", $row['shipper_id']) ;
				}
		}
		return $Freight;
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
			$this->_table_prefix = '#__cart_';
			$query = "SELECT customer FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->id . "'" ;
	  		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from usercustomer file. <span style="color:brown">OK if user customer cross reference does not exist</span>';			
			$CustomerCross = modwebERPHelper::getResult($query,'Joomla',$errorMessage,NULL,0);
			// echo '<pre>';var_dump($CustomerCross , '<br><br> <b style="color:brown">  CustomerCross </b><br><br>');echo '</pre>';
			$query = "SELECT 	braddress4,braddress5
								FROM " . $weberp['database'] . ".custbranch
				        	  WHERE 	debtorno ='" . $CustomerCross . "'";
			$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from custbranch file. <span style="color:brown">OK if user customer cross reference does not exist</span>';	
			If($row =& modwebERPHelper::getRowArray($query,'webERP',$errorMessage,NULL,0)){		
				If(strlen($row['braddress5']) > 4){		
					$CustomerZip = 	$row['braddress5'];
				}else{
					$CustomerZip = 	$row['braddress4'];
				}
			}
		}
		return $CustomerZip;
	}
	function getDiscountPercents(){	
		global $weberp;	
		$query = "SELECT discountcategory, 	quantitybreak, 	discountrate
								FROM " . $weberp['database'] . ".discountmatrix";
		$errorMessage = 'Error Code-CM' .  __LINE__  . ' Unable to select from discountmatrix file. <span style="color:brown">OK if no discount matrix records exist</span>';	
		If($DiscountPercent =& modwebERPHelper::getRowList($query,'webERP',$errorMessage,NULL,0)){	
			Foreach($DiscountPercent as $count=>$DiscountPercentArray){	
				$DiscountPercents[$DiscountPercentArray['discountcategory']][$DiscountPercentArray['quantitybreak']] = $DiscountPercentArray['discountrate'];
			}
			return $DiscountPercents;
		}
		return FALSE;
	}
}	
?>

