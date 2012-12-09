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
defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.tooltip');
global $weberp;
If(gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)){
	$weberp	= modwebERPHelper::getwebERP();
}
$Choice 							=& $this->choices;
$CategoryDescriptions 		=  $this->categorydescriptions;
$SalesCategoryDescriptions	=  $this->salescategorydescriptions;
// echo '<pre>';var_dump($SalesCategoryDescriptions , '<br><br> <b style="color:brown">SalesCategoryDescriptions in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
$db 								=& JFactory::getDBO();
$Option 							=& JRequest::getVar('option');
$params 							=  JComponentHelper::getParams($Option);
$pathtopics 					=  $params->get( 'pathtopics');
$coresales 						=  $params->get( 'coresales');
$price0							=  $params->get( 'price0');
$coreprice0						=  $params->get( 'coreprice');
$KitPrice						=& $this->get( 'kitprice');
$QuantityOnHand				=& $this->quantityonhand;
$listsalestype 				=  $params->get( 'listsalestype');
$priceheadingoverride		=  $params->get( 'priceheadingoverride');
$pricecolumn					=  $params->get( 'pricecolumn');
$catalogonly					=  $params->get( 'catalogonly');
$customersalestype			=  $this->customersalestype;
$ShowQuantityOnHandColumn	=  $params->get( 'showquantityonhandcolumn');
$ShowQuantityOnHand			=  $params->get( 'showquantityonhand');
$Freight 						=& $this->freight;
$DestinationZipCode			=  $this->get( 'destinationzipcode' );
If($UserArray = JFactory::getUser()->groups){
	$checkuser = JFactory::getUser()->groups["Super Users"];
}else{
	$checkuser = 0;
}
$FirstColumn = 1;
$NumberOfColumsCoresales = 3;
global $limit,$totalitems,$mainframe;
jimport('joomla.filesystem.file');
If(isset($Choice)){
	If(isset($Choice['make'])){
		$MakeName = cartweberpModelcartcatalog::getMakeName($Choice['make']);
		If(strlen(trim($MakeName)) > 1){
?>

<fieldset>
	<legend><?php echo JText::_('SELECTION_CRITERIA'); ?></legend> 
	<TABLE width="100%">
		<THEAD>
			<TH width="10%"><H4><?php echo JText::_('YEAR')  ?></H4></TH>
			<TH width="20%"><H4><?php echo JText::_('MAKE')  ?></H4></TH>
			<TH width="40%" colspan="2"><H4><?php echo JText::_('MODEL')  ?></H4></TH>
			<!-- <TH width="30%"><H4>Category</H4></TH> -->
		</THEAD>
		<THEAD>
			<TH style="color:blue"><H4><?php echo $Choice['year']?></H4></TH>
			<TH style="color:blue"><H4><?php echo $MakeName?></H4></TH>
			<TH style="color:blue" colspan="2"><H4><?php echo $Choice['modelname']?></H4></TH>
			<TH><a href="index.php?option=com_cartymm&controller=cartymm"><?php echo JText::_('SELECT_DIFFERENT_YEAR-MAKE-MODEL')  ?></a></TH>		
		</THEAD>
		</TABLE>	
</fieldset>

<?php
		}
	}
}
?>
<form action="<?php JURI::base()?>" method="post" name="categoryForm" >				

<?php	
If(count($SalesCategoryDescriptions) > 0){
?>
<br>
<fieldset>
	<legend><?php echo JText::_('SALES_CATEGORY_SELECTION')  ?></legend> 

<table>
	<tr>
<?php
}
If(count($CategoryDescriptions) > 0 AND count($SalesCategoryDescriptions) == 0){
?>

<br>
<fieldset>
	<legend><?php echo JText::_('CATEGORY_SELECTION')  ?></legend> 

<table width="75%">
	<tr>
		<td align="right" width="33%"><?php echo JText::_('INVENTORY_CATEGORY')  ?>:</td>
		<td align="left" width="67%">
			<SELECT name="category" onchange="this.form.submit('categoryForm')">
					<OPTION value="ALL"><?php echo JText::_('ALL_CATEGORIES')  ?></OPTION>
<?php
	$post	= JRequest::get('post');
	foreach ($CategoryDescriptions as $ID=>$Desc){ 
		If(!array_key_exists('category',$post)){
			$post['category'] = $Choice['category'];
		}
		If(trim($ID) == trim($post['category'])){
			$IsSelected = 'SELECTED';
		}else{
			$IsSelected = '';
		}
?>		
					<OPTION value="<?php echo $ID ?>" <?php echo $IsSelected ?>><?php echo $Desc ?></OPTION>
<?php
	}	
?>
				</SELECT>
				<input type="hidden" name="controller" value="cartcatalog" />
				<input type="hidden" name="view" value="cartcatalog" />
				<input type="hidden" name="option" value="com_cartweberp" />
			</form> 
		</td>
<?php
}

$post	= JRequest::get('request');
$CategoryDescriptionsCount = 0;
// echo isset($post['salescategory'])  . '=isset($post[salescategory])   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
If(isset($post['salescategory']) AND strlen(trim($post['salescategory'])) > 0 ){
	// echo 'stop     in ' . $_SERVER["SCRIPT_NAME"] . ' Line #' . __LINE__ . '  <br>';exit;
	$paththispic = $pathtopics . "cat_" . $post['salescategory'] . '.jpg';
	// echo $paththispic  . '=paththispic   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
	IF(strtoupper(substr($paththispic,0,4)) <> 'HTTP' AND !file_exists($paththispic)){
		$paththispic = "components" . DS . "com_cartweberp" . DS . "images" . DS . "door.jpg";
	}
	// find parent category to bo back one level
	$ParentCategory = '';
	asort($SalesCategoryDescriptions);
	Foreach($SalesCategoryDescriptions as $ParentID=>$IDArray){
		Foreach($IDArray as $ID=>$Desc){
			If($ID == $post['salescategory']){
				$ParentCategory = $ParentID;
			}
		}
	}
	If(isset($SalesCategoryDescriptions[""][$ParentCategory])){
		$ParentDescription = $SalesCategoryDescriptions[""][$ParentCategory];
	}
	If(!isset($ParentCategory)){
		$ParentCategory = '';
	}
	If(!isset($ParentDescription)){
		$ParentDescription = '';		
	}
?>
<table cellpadding="5" cellspacing="0" border="0">
<tr>
	<td valign="bottom" align="center">
		<table cellpadding="0" cellspacing="0" border="4">
			<tr>
				<td align="center">
					<a href="<?php echo JURI::root() ?>/index.php?option=com_cartweberp&controller=cartcatalog&salescategory=<?php echo $ParentCategory ?>&salescategorydesc=<?php echo $ParentDescription ?>">
							<?php echo JText::_('GO_BACK')  ?><br>
						<IMG SRC="<?php echo $paththispic?>" width="70px" ALT="No Image"><br>	
						<?php echo $post['salescategorydesc']; ?>
					</a>
				</td>
			</tr>
		</table>
	</td>
<?php
	$CategoryDescriptionsCount = 1;
}
$TestCount = 0;
// echo '<pre>';var_dump($SalesCategoryDescriptions , '<br><br> <b style="color:brown">SalesCategoryDescriptions in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
If(isset($SalesCategoryDescriptions) AND count($SalesCategoryDescriptions)>0){
	Foreach($SalesCategoryDescriptions as $Parent=>$IDArray){
		asort($IDArray);
		// echo '<pre>';var_dump($IDArray , '<br><br> <b style="color:brown">IDArray in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
		// Foreach($IDArray as $Key=>$Desc){
		If(array_key_exists("salescatid", $IDArray)){
			$ID = $IDArray["salescatid"];
		}else{
			$ID = 'not found';
			continue;
		}
		If(array_key_exists("salescatname", $IDArray)){
			$Desc = $IDArray["salescatname"];
		}else{
			$Desc = 'Not Found';
		}
		If(array_key_exists("parentcatid", $IDArray)){
			$ParentID = $IDArray["parentcatid"];
		}else{
			$ParentID = 'not found';
		}
		$TestCount = $TestCount +1;
		// echo $TestCount  . '=TestCount   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
		// echo $post['salescategory']  . '=post[salescategory]   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
		If(isset($post['salescategory'])){
			$SelectedSalesCategory = $post['salescategory'];
			$mainframe->setUserState( 'SalesCategoryURL',$SelectedSalesCategory);
		}elseif($mainframe->getUserState( 'SalesCategoryURL') AND isset($post['donotresetcategory']) AND $post['donotresetcategory']='true'){
			$SelectedSalesCategory = $mainframe->getUserState( 'SalesCategoryURL');
		}else{
			$SelectedSalesCategory = '';
			$mainframe->setUserState( 'SalesCategoryURL','');
		}
		// echo $SelectedSalesCategory  . '=SelectedSalesCategory   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
		// echo $ParentID  . '=ParentID   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
		// echo $ID  . '=ID   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
		If(	$ParentID==$SelectedSalesCategory OR 
			(	$ParentID==0 AND $SelectedSalesCategory=='' ) OR 	
			(	isset($ID) AND $ID == $SelectedSalesCategory)){
			$CategoryDescriptionsCount = $CategoryDescriptionsCount + 1	;		
			$paththispic = $pathtopics . "cat_" . $ID . '.jpg';
			// echo $paththispic  . "=paththispic<br>";
			IF(strtoupper(substr($paththispic,0,4)) <> 'HTTP' AND !file_exists($paththispic)){
			 	$paththispic = "components" . DS . "com_cartweberp" . DS . "images" . DS . "door.jpg";
			}
			If($SelectedSalesCategory == $ID){
				$CategoryBorder = 4;
			}else{
				$CategoryBorder = 0;
			}
			if( strlen(trim($weberp['includesalescategories'])) >0  AND strlen(trim($ParentID)) == 0
				AND strpos(" " . $weberp['includesalescategories'],strval($ID)) == '' ){
				continue;
			}
			If((isset($post['salescategory']) AND $post['salescategory'] <> $ID) OR !isset($post['salescategory'])){
				
?>
	<td valign="bottom" align="center">
		<table cellpadding="10" cellspacing="10" border="<?php echo $CategoryBorder ?>">
			<tr>
				<td align="center">
					<a href="<?php echo JURI::root() ?>/index.php?option=com_cartweberp&controller=cartcatalog&salescategory=<?php echo $ID ?>&salescategorydesc=<?php echo $Desc?>">
						<IMG SRC="<?php echo $paththispic?>" width="70px" ALT="No Image"><br>	
						<?php echo $Desc; ?>
					</a>
				</td>
			</tr>
		</table>
	</td>
<?php
				}
			}
			If($CategoryDescriptionsCount > 4){
				$CategoryDescriptionsCount = 0;
?>	
			</tr>
			<tr>
<?php	
		}
		// }
	}	
}
jimport( 'joomla.environment.uri' );
?>		
	</tr>
</table>
<table  width="75%">	
	<tr>
		<td align="right" width="33%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('SEARCH_PART_OR_DESCRIPTION') ?>:</td>
		<td align="left" width="34%">
			<form action="index.php" method="post" name="search" >
				<input type="text" name="stockid" id="stockid" size="22" maxlength="40" />
				<input type="hidden" name="controller" value="cartcatalog" />
				<input type="hidden" name="view" value="cartcatalog" />
				<input type="hidden" name="option" value="com_cartweberp" />				
		</td>
		<td align="left" width="33%"><input type="submit" value="search" /></td>
			</form>	
	</tr>
</table>
</fieldset>

<form action="<?php echo JURI::base() . 'index.php?option=com_cartweberp&controller=cartcatalog&view=cartcatalog'?>" method="post" name="adminForm" >
<?php
If(!$catalogonly){
	$CatalogPurpose = JText::_('ADD_ITEMS_TO_CART');
}else{	
	$CatalogPurpose = 'Catalog';
}
$DescriptionSelected = "";
$PartNumberSelected = '';
$PriceSelected = '';
If(array_key_exists('sortcriteria',$post)){
	If($post['sortcriteria'] == 'stockid'){
		$PartNumberSelected = 'CHECKED';
		$mainframe->setUserState( 'PartNumberSelected','CHECKED');
		$mainframe->setUserState( 'DescriptionSelected','');
		$mainframe->setUserState( 'PriceSelected','');
	}elseif($post['sortcriteria'] == 'description') {
		$DescriptionSelected = "CHECKED";
		$mainframe->setUserState( 'DescriptionSelected','CHECKED');
		$mainframe->setUserState( 'PriceSelected','');
		$mainframe->setUserState( 'PartNumberSelected','');
	}elseif($post['sortcriteria'] == 'price') {
		$PriceSelected = 'CHECKED';
		$mainframe->setUserState( 'PriceSelected','CHECKED');
		$mainframe->setUserState( 'DescriptionSelected','');
		$mainframe->setUserState( 'PartNumberSelected','');
	}
}else{
	If($mainframe->getUserState( 'PartNumberSelected') == 'CHECKED'){
		$PartNumberSelected = 'CHECKED';
	}elseif($mainframe->getUserState('DescriptionSelected') == 'CHECKED') {
		$DescriptionSelected = "CHECKED";
	}elseif($mainframe->getUserState('PriceSelected') == 'CHECKED') {
		$PriceSelected = 'CHECKED';
	}else{
		$DescriptionSelected = "CHECKED";
	}
}	
?>
<fieldset>
	<legend><?php echo $CatalogPurpose  ?></legend> 
<?php
If(count($this->items) > 0){
?>	
		<table width="100%">
			<TR>
<?php
	// echo '<pre>';var_dump($this->currencies , '<br><br> <b style="color:brown">  this->currencies </b><br><br>');echo '</pre>';
	// echo $weberp['currencycode']  . "=weberp['currencycode']<br>";
	If(count($this->currencies) > 1){
		If(array_key_exists('ChosenCurrency', $post)){
			$ChosenCurrency = $post['ChosenCurrency'];
			// echo $ChosenCurrency  . "=ChosenCurrency default 291<br>";
			$mainframe->setUserState( "ChosenCurrency",$ChosenCurrency ) ;
		}elseif($mainframe->getUserState( "ChosenCurrency")){
			$ChosenCurrency = $mainframe->getUserState( "ChosenCurrency");
			// echo $ChosenCurrency  . "=ChosenCurrency default 295<br>";
		}elseif($this->debotorcurrency){
			$ChosenCurrency = $this->debotorcurrency;
			$mainframe->setUserState( "ChosenCurrency",$ChosenCurrency ) ;
			// echo $ChosenCurrency  . "=ChosenCurrency default 299<br>";
		}else{
			$ChosenCurrency = $weberp['currencycode'];
			// echo $ChosenCurrency  . "=ChosenCurrency default 302<br>";
		}
		// echo $ChosenCurrency  . "=ChosenCurrency<br>";
		$mainframe->setUserState( "CurrencyRate",$this->currencies[$ChosenCurrency]['rate']);
?>
				<td>
					<select name="ChosenCurrency">
<?php

		while(list($key, $val)=each($this->currencies)) {		
			if (isset($ChosenCurrency) && ($ChosenCurrency == $key)) {
				echo '<option value="'.$key.'" SELECTED>'. $val['currency'] .'</option>';
			} else {
				echo '<option value="'.$key.'">'. $val['currency'] .'</option>';
			}
		}
?>
					</select>
				</td>
<?php
	}
?>				
				<TD align="left" colspan="5"><?php echo JText::_('SORT_BY'); ?>:<br>
					<input type="radio" name="sortcriteria" value="stockid" <?php echo $PartNumberSelected  ?>/><?php echo JText::_('ITEM_NUMBER')  ?><br>
					<input type="radio" name="sortcriteria" value="description" <?php echo $DescriptionSelected  ?>/><?php echo JText::_('DESCRIPTION')  ?><br>
					<input type="radio" name="sortcriteria" value="price" <?php echo $PriceSelected  ?>/><?php echo JText::_('PRICE')  ?>
				</TD>
<?php
	If(!$catalogonly){
?>				
				<TD align="right" colspan="1">
					<button name="Search" onClick="this.form.submit()" class="button">
						<?php echo JText::_( 'UPDATE_CART' );?>
					</button>
				</TD>
				<TD align="right" colspan="2" style="font-size:16px;">
					<A HREF="<?php echo 'index.php?option=com_cartweberp&controller=cart'; ?>"><img src="components/com_cartweberp/images/CheckOutsmall.png" alt="Check Out" longdesc="Check Out"></A>
				</TD>
<?php
	}
?>				
			</TR>
		</table>
		<table cellpadding="4">
<?php
	If($weberp['displaycolumns']==1){
?>		
			<THEAD>
				<TH><H4><?php echo JText::_('ITEM_NUMBER')  ?></H4></TH> 
<?php
		If(!$catalogonly){
?>				
				<TH align="right"><H4><?php echo JText::_('ADD')  ?></H4></TH>  
				<TH><H4><?php echo JText::_('IN_CART')  ?></H4></TH>
<?php
		}
?>				
				<TH colspan="2"><H4><?php echo JText::_('DESCRIPTION')  ?></H4></TH>
<?php
		If($pricecolumn){	
			If(strlen(trim($priceheadingoverride)) > 0){
				$ColumnPrice = $priceheadingoverride;
			}else{
				$ColumnPrice = JText::_('PRICE');
			}
?>			
				<TH align="right"><H4><?php echo $ColumnPrice  ?></H4></TH>
<?php
		}
		$NumberOfColumsCoresales = 3;
		If($coresales=='1'){
			$NumberOfColumsCoresales = 1;
?>
				<TH align="right"><H4><?php echo JText::_('CORE')  ?></H4></TH>
				<TH align="right"><H4><?php echo JText::_('TOTAL')  ?></H4></TH>
<?php
		}
		If($listsalestype <> $customersalestype){
?>		
				<TH align="right"><H4><?php echo JText::_('LIST')  ?></H4></TH>
<?php
		}		
		If($ShowQuantityOnHandColumn){
			
?>		
				<TH align="right"><H4><?php echo JText::_('IN_STOCK')  ?></H4></TH>
<?php
		}
		If($pricecolumn){
?>				
				<TH align="right"><H4><?php echo JText::_('U/M')  ?></H4></TH>
<?php	
		}
?>		
			</THEAD>	
<?php
	}	
	$Categories = $this->categories;
	$cart = $this->cart;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++){
		// echo $n  . "=n<br>";
		// echo $i  . "=i<br>";
		
		$row =& $this->items[$i];
		$category =$Categories[$row['categoryid']];	
		$currency = 'USD';
		$stockid = $row['stockid'];
		// echo $row['stockid']  . "=row['stockid']<br>";
		$Total		= 0;
		$CoreAmount = 0;
		If(isset($row['price']) OR isset($KitPrice['customerprice'][$stockid])){
			If($coresales=='1' AND substr($stockid,0,5) <> "DPISL"){
				$StockIDforCore								=	substr($stockid,3);
			}
			If($coresales=='1' AND substr($stockid,0,5) == "DPISL"){
				$StockIDforCore								=	substr($stockid,5);
			}
			IF(isset($row['price'])){
				$Amount = floatval($row['price']);
			}elseif(isset($KitPrice['customerprice'][$stockid][$StockIDforCore])){
				If(!isset($KitPrice['customerprice'][$stockid]["00".$StockIDforCore])){
					$KitPrice['customerprice'][$stockid]["00".$StockIDforCore] = 0;
				}
				$Amount = floatval($KitPrice['customerprice'][$stockid][$StockIDforCore] + $KitPrice['customerprice'][$stockid]["00".$StockIDforCore]);
				// echo $KitPrice['customerprice'][$stockid][$StockIDforCore]  . "=KitPrice['customerprice'][$stockid][$StockIDforCore]355<br>";
			}elseif(isset($KitPrice['customerprice'][$stockid][$StockIDforCore])){
				$Amount = floatval($KitPrice['customerprice'][$stockid][$StockIDforCore] + $KitPrice['customerprice'][$stockid]["00".$StockIDforCore]);
				// echo $KitPrice['customerprice'][$stockid][$StockIDforCore]  . "=KitPrice['customerprice'][$stockid][$StockIDforCore]355<br>";
			}else{
				$Amount =0;
			}
			$Total		=	$Amount;
			// echo $stockid  . "=stockid359<br>";
			// echo $Amount  . "=Amount360<br>";
			If($coresales=='1'){
				If(substr($row['description'],0,4) == "Core"){
					
					// echo $StockIDforCore  . "=StockIDforCore364<br>";
					$StockIDforCoreSemiLoaded					=	"SL" . substr($stockid,3);
					// echo $StockIDforCoreSemiLoaded  . "=StockIDforCoreSemiLoaded366<br>";
					$CorePrice[$StockIDforCore]				= $Amount;
					$CorePrice[$StockIDforCoreSemiLoaded]	= $Amount;
					// echo $Amount  . "=Amount369<br>";
					continue;
				}elseif(isset($CorePrice[$stockid])){
						$Amount 		= 	$Amount - $CorePrice[$stockid];
						$CoreAmount =	$CorePrice[$stockid];
						// echo $Amount  . "=Amount374<br>";
						// echo $CoreAmount  . "=CoreAmount375<br>";
				}else{
						$CorePart = cartweberpModelcartcatalog::getCorePart($stockid);
						If(isset($this->price[$CorePart])){
							$CorePrice[$stockid] = number_format($this->price[$CorePart],2);
						}elseif(isset($KitPrice['customerprice'][$stockid]["00".$StockIDforCore])){
							$CorePrice[$stockid] = $KitPrice['customerprice'][$stockid]["00".$StockIDforCore];
						}else{
							$CorePrice[$stockid] = 0;
						}
						$Amount 		= 	$Amount - $CorePrice[$stockid];
						$CoreAmount =	$CorePrice[$stockid];
						If($CoreAmount == 0 AND isset($KitPrice['customerprice'][$stockid]["00".$StockIDforCore])){
							$CoreAmount = $KitPrice['customerprice'][$stockid]["00".$StockIDforCore];
						}
						// echo $Amount  . "=Amount381<br>";
						// echo $CoreAmount  . "=CoreAmount382<br>";
				}
			}
		}else{
			$Amount = 'Call for Quote';
		}
		$Option 						=& JRequest::getVar('option');
		$params 						= JComponentHelper::getParams($Option);
		$adjustquantity 			= $params->get( 'adjustquantity');
		If(isset($QuantityOnHand[$stockid]) and $QuantityOnHand[$stockid] > .01){
			$Qoh = $QuantityOnHand[$stockid];
			$GoGray = '';
		}else{	
			If($adjustquantity){	
				$GoGray = 'disabled';
			}else{
				$GoGray = '';
			}
			$Qoh = 0;
		}
		// $fmt = numfmt_create( 'en_US', NumberFormatter::CURRENCY );
		// $price =  numfmt_format_currency($fmt, $this->getPrice($row['stockid']), "USD")."\n";
		// if we have no retail price, check to see if we are allowed to dispay this line.
		// the parameter to "Choose to include items with a 0 price" 
		If($Amount > .001 OR $price0==1){
			If(isset($this->listprice[$stockid])){
				$ListPrice = number_format($this->listprice[$stockid],2);
			}else{
				$ListPrice = 'Call for Quote';
			}
			If($weberp['displaycolumns']==1){
				// one column format starts here----------------------------------------
?>		
			<TR>
				<TD><?php echo $row['stockid']?></TD> 
<?php
				If(!$catalogonly){
?>				
				<TD align="right">
					<input type="text" name="QuantityToAdd[<?php echo $row['stockid']?>]" id="QuantityToAdd<?php echo $row['stockid']?>" size="2" maxlength="6"  <?php echo $GoGray?> />
					<input type="hidden" name="Price[<?php echo $row['stockid']?>]" id="Price<?php echo $row['stockid']?>" value="<?php echo $Total* $this->currencies[$ChosenCurrency]['rate']?>"/>
				</TD>
<?php
				}
				If(!$catalogonly){
?>				
				<TD align="right">
<?php
					If(isset($cart[$row['stockid']]["quantityordered"])){
?>		
			<?php echo $cart[$row['stockid']]["quantityordered"]?>
<?php
					}else{
?>			
			&nbsp;
<?php
					}
?>			
				</TD>
<?php
				}
?>				
				<TD>
					<?php echo $row['description']?><BR>
					<A HREF="<?php echo 'index.php?option=com_cartweberp&controller=stockinformation&stockid=' . $row['stockid']; ?>">
						<?php echo JText::_('PART_SPECS')  ?>
					</A>
<?php
				If($mainframe->getUserState( 'ExtraInfo' . $row['stockid']) ){
?>
			For - <?php echo $mainframe->getUserState('ExtraInfo' . $row['stockid'])?>
<?php
				}
?>			
				</TD>
				<TD height="125px">
<?php
				$paththispic = $pathtopics . $stockid . '.jpg';
				$paththispicL = $pathtopics . $stockid . 'L.jpg';
				If(!file_exists($paththispicL)){
					$paththispicL = $paththispic;
				}
				// echo $paththispic  . "=paththispic<br>";exit;
				If(strtoupper(substr($paththispic,0,4)) == 'HTTP' OR file_exists($paththispic)){		
?>		
					<?php echo JText::_('CLICK_TO_ENLARGE'); ?>
					<a href="<?php echo $paththispicL  ?>"  rel="shadowbox"><IMG SRC="<?php echo $paththispic?>" width="120px"></a>
<?php
				}
				// echo $paththispic  . "=paththispic<br>";exit;
				If(isset($CorePrice) AND array_key_exists($stockid, $CorePrice)){
					$CoreLinePrice = $CorePrice[$stockid];
				}elseif(isset($CoreAmount)){
					$CoreLinePrice = $CoreAmount;
				}else{
					$CoreLinePrice = 0;
				}
				If($Amount == 0 AND isset($KitPrice['customerprice']) AND array_key_exists($stockid, $KitPrice['customerprice'])){
					$Amount = array_sum($KitPrice['customerprice'][$stockid]);
				}
?>			
				</TD> 
<?php
				If($pricecolumn){
?>				
				<TD align="right">
<?php
					// echo $this->currencies[$ChosenCurrency]['rate']  . "=this->currencies[$ChosenCurrency]['rate']<br>";
					If(gettype($Amount) <> 'string'){
						If($mainframe->getUserState( "ChosenCurrency")){
							$Amount = $Amount * $this->currencies[$ChosenCurrency]['rate'];
							$AmountDecimalPlaces =  $this->currencies[$ChosenCurrency]['decimalplaces'];
						}else{
							$AmountDecimalPlaces = $weberp['currencydecimalplaces'];
						}
						echo number_format($Amount ,$AmountDecimalPlaces ) . " " . $ChosenCurrency; 
					}else{
					 	echo $Amount; 
					}
?>
				</TD>
<?php
				}
				If($coresales=='1'){
?>		
				<TD align="right"><?php echo number_format($CoreLinePrice ,$AmountDecimalPlaces); ?></TD>
				<TD align="right"><?php echo number_format($Total ,$AmountDecimalPlaces ); ?></TD>
<?php
				}
				If($listsalestype <> $customersalestype){
					// var_dump($KitPrice);echo "<br><br>KitPrice<BR><BR><BR>";exit;					
					If($ListPrice == 0 AND isset($KitPrice['listprice']) AND array_key_exists($stockid, $KitPrice['listprice'])){
						$ListPrice = array_sum($KitPrice['listprice'][$stockid]);
					}
?>			
				<TD align="right">
<?php
					If(gettype($ListPrice) == 'double'){
						If($mainframe->getUserState( "ChosenCurrency")){
							$ListPrice = $ListPrice * $this->currencies[$ChosenCurrency]['rate'];
						}
?>				
				<?php echo number_format($ListPrice ,$AmountDecimalPlaces ) . " " . $ChosenCurrency;?>
<?php
					}else{
?>		
				<?php echo $ListPrice?>
<?php
					}
?>		
				</TD>

<?php
				}			
				If($ShowQuantityOnHandColumn){		
					// if ($checkuser==8){
						If($ShowQuantityOnHand==1){			
?>		
				<TD align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Qoh?></TD>
<?php
						}else{
							If($Qoh > 0.001){
?>
				<TD align="center">Yes</TD>
<?php	
							}else{
?>
				<TD align="center">No</TD>
<?php		
							}
						}
					// }
				}
?>						
				<TD align="right" colspan="2">
<?php				
				If($pricecolumn){	
					echo $row['units'];
				}
?>	
				</TD>
			</TR>
<?php
			// two column format start here --------------------------------------------------------
			}else{
				// echo $i  . "=i two column top<br>";
				If($i==0){
?>

	<tr>
		<td valign="top">
			<table>	
			<THEAD>
				<TH><H4><?php echo JText::_('ITEM_NUMBER')  ?></H4></TH>
				<TH align="center"><H4>
<?php
					If(!$catalogonly){
						echo JText::_('ADD');
					}
?>								
				</H4></TH>  
				<TH align="right"><H4>
<?php				
					If(!$catalogonly){
						echo JText::_('IN_CART');
					}								
?>				
				</H4></TH>	
				<TH><H4>&nbsp;</H4></TH>			
			</THEAD>
<?php				
				}
?>			

			<TR>
				<TD><?php echo $row['stockid']?></TD> 
				<TD align="center">
<?php				
				If(!$catalogonly){
?>
					<input type="text" name="QuantityToAdd[<?php echo $row['stockid']?>]" id="QuantityToAdd<?php echo $row['stockid']?>" size="2" maxlength="6"  <?php echo $GoGray?> />
					<input type="hidden" name="Price[<?php echo $row['stockid']?>]" id="Price<?php echo $row['stockid']?>" value="<?php echo $Total * $this->currencies[$ChosenCurrency]['rate']?>"/>
<?php
				}
?>				
				</TD>
				<TD align="center">
<?php
				If(!$catalogonly){
					If(isset($cart[$row['stockid']]["quantityordered"])){
						echo $cart[$row['stockid']]["quantityordered"];
					}else{
?>			
						&nbsp;
<?php
					}
				}else{
?>			
						&nbsp;
<?php
				}
?>			
				</TD>				
				<TD rowspan="3" height="100px">
<?php
				$paththispic = $pathtopics . $stockid . '.jpg';
				$paththispicL = $pathtopics . $stockid . 'L.jpg';
				If(!file_exists($paththispicL)){
					$paththispicL = $paththispic;
				}
				// echo $paththispic  . "=paththispic<br>";
				If(strtoupper(substr($paththispic,0,4)) == 'HTTP' OR file_exists($paththispic)){		
?>		
					<?php echo JText::_('CLICK_TO_ENLARGE'); ?>
					<a href="<?php echo $paththispicL  ?>"  rel="shadowbox"><IMG SRC="<?php echo $paththispic?>" width="80px"></a>
<?php
				}
?>			
				</TD>
			</tr>
			<tr>
				<TD colspan="3">
					<?php echo $row['description']?><BR>
					<A HREF="<?php echo 'index.php?option=com_cartweberp&controller=stockinformation&stockid=' . $row['stockid']; ?>">
						<?php echo JText::_('PART_SPECS') ?>
					</A>
<?php
				If($mainframe->getUserState( 'ExtraInfo' . $row['stockid']) ){
?>
			For - <?php echo $mainframe->getUserState('ExtraInfo' . $row['stockid'])?>
<?php
				}
?>			
				</TD>
			</tr>
			<tr>
				<TD colspan="<?php echo $NumberOfColumsCoresales   ?>" align="left">
<?php 
				If(intval($pricecolumn) > 0){
					If(gettype($Amount) <> 'string'){
						If($mainframe->getUserState( "ChosenCurrency")){
							$Amount = $Amount * $this->currencies[$ChosenCurrency]['rate'];
							$AmountDecimalPlaces =  $this->currencies[$ChosenCurrency]['decimalplaces'];
						}else{
							$AmountDecimalPlaces = $weberp['currencydecimalplaces'];
						}
						echo number_format($Amount ,$AmountDecimalPlaces ) . " " . $ChosenCurrency; 
					}else{
					 	echo $Amount; 
					}
				}
?> 
				</TD>
<?php
				If($coresales=='1'){
					If(gettype($CoreLinePrice) <> 'string'){
?>		
				<TD align="right"><?php echo number_format($CoreLinePrice ,$AmountDecimalPlaces ); ?></TD>
<?php
					}else{
?>				
				<TD align="right"><?php echo $CoreLinePrice; ?></TD>
<?php
					}
?>

				<TD align="right"><?php echo number_format($Total ,$AmountDecimalPlaces ) . " " . $ChosenCurrency; ?></TD>
<?php
				}	
?>
			</tr>
			<tr>
<?php				
				If($listsalestype <> $customersalestype){
						If($mainframe->getUserState( "ChosenCurrency")){
							$ListPrice = $ListPrice * $this->currencies[$ChosenCurrency]['rate'];
						}
?>			
				<TD align="left"><?php echo JText::_('LIST_PRICE') ?>:<?php echo number_format($ListPrice, $AmountDecimalPlaces) . " " . $ChosenCurrency?></TD>
<?php
				}
				If($ShowQuantityOnHandColumn){		
					if ($checkuser==8){
						If($ShowQuantityOnHand==1){			
?>		
				<TD align="right"><?php echo JText::_('ON_HAND') ?>:<?php echo $Qoh?></TD>
				<TD align="left"><?php echo $row['units'] ?></Td>
<?php
						}else{
							If($Qoh > 0.001){
?>
				<TD align="left"><?php echo JText::_('ON_HAND-YES') ?></TD>
<?php	
							}else{
?>
				<TD align="left"><?php echo JText::_('ON_HAND-NO') ?></TD>
<?php		
							}
						}
					}
				}
?>		
			</TR>
			<tr><td colspan="4" align="center">__________________________________________</td></tr>
			<tr><td colspan="4" align="center">&nbsp;</td></tr>
<?php					
				If($i==round(count( $this->items )/2)-1){
?>
			</table>
		</td>
		<td valign="top">
			<table>			
				<THEAD>
					<TH><H4><?php echo JText::_('ITEM_NUMBER')  ?></H4></TH> 
					<TH align="center"><H4><?php echo JText::_('ADD')  ?></H4></TH>  
					<TH align="right"><H4><?php echo JText::_('IN_CART')  ?></H4></TH>	
					<TH><H4>&nbsp;</H4></TH>			
				</THEAD>
<?php				
				}
			}
		}
	}

	$post	= JRequest::get('request');
?>	
					</table>
				</td>
			</TR>
		</table>
<?php
	If(!$catalogonly AND count( $this->items )>4){
?>		
		<TABLE cellpadding="4" width="80%">
			<TR>
				<TD align="right" colspan="3">
					<button name="Search" onClick="this.form.submit()" class="button">
						<?php echo JText::_('UPDATE_CART');?>
					</button>
				</TD>
				<TD align="right" colspan="2" style="font-size:16px;">
					<A HREF="<?php echo 'index.php?option=com_cartweberp&controller=cart'; ?>"><img src="components/com_cartweberp/images/CheckOutsmall.png" alt="Check Out" longdesc="Check Out"></A>
				</TD>
			</TR>
		</table>
<?php
	}
?>		
		<input type="hidden" name="controller" value="cartcatalog" />
		<input type="hidden" name="view" value="cartcatalog" />
		<input type="hidden" name="option" value="com_cartweberp" />
<?php
	If(isset($choices['make'])){
?>
		<input type="hidden" name="make" 		value="<?php echo $Choice['make']?>" />
		<input type="hidden" name="makename" 	value="<?php echo $this->getMakeName($Choice['make'])?>" />
		<input type="hidden" name="version" 	value="<?php echo $Choice['model']?>" />
		<input type="hidden" name="category" 	value="<?php echo $post['category']?>" />
<?php
	}
		If(isset($Choice['year'])){
?>
		<input type="hidden" name="year" value="<?php echo$Choice['year'];?>" />
</form>
</fieldset>
<?php
}
}else{
?>
			<BR><BR>No Items Selected <BR><BR><BR><BR>
<?php
}
$app    =& JFactory::getApplication();
$router =& $app->getRouter();
$router->setVar( 'view', 			'cartcatalog' );
$router->setVar( 'controller', 	'cartcatalog' );
$router->setVar( 'model', 			'cartcatalog' );
// do not display the pagination unless there are more
If($totalitems > $limit){
?>

<DIV align="center" STYLE="border: 3px outset;
	color: Black;
	font-weight: bold;
	cursor: hand;
	text-decoration: none;	
	background: #d4d4d4 fixed;
	font-size: 12px;">
 	<form action="<?php echo $this->request_url ; ?>" method="post" enctype="multipart/form-data"  name="paganation">
<?php		
	jimport('joomla.html.pagination');
	If(isset($this->pagination)){
		$TableLinks = str_replace('<ul','<table style="float:left"',$this->pagination->getPagesLinks());
		$TableLinks = str_replace('</ul>','</table>',$TableLinks);
		$TableLinks = str_replace('<li','<td',$TableLinks);
		$TableLinks = str_replace('</li>','</td>',$TableLinks);
		$TableLimits = $this->pagination->getLimitBox();
		$TableCounter = $this->pagination->getPagesCounter();
		echo  $TableLinks;
 		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo  $TableLimits . " items per screen ";
	 	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo  $TableCounter;
	}
?>
	</form>
</DIV>
<center>Powered by <a href="http://joomlamo.com" target="blank">CARTwebERP</a></center>
<?php
}
?>
<script language="javascript" type="text/javascript">
function formsubmit(pressbutton){

var form = document.paganation;
	form.submit();
}
</script>