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
$weberp	= modwebERPHelper::getweberp();
$row 									=& $this->items[0];
$models 								=& $this->models;
$j = 1;
$n = round(count($models)/2+.4999);
$mainframe =& JFactory::getApplication();
If(gettype($models) == 'array' AND count($models) > 0){
	Foreach($models as $Count=>$ModelRow){
		If($ModelRow['makercarid'] 	== $mainframe->getUserState( 'carmodel')){
			$model['beginyear'] 	= $ModelRow['beginyear'];
			$model['endyear']		= $ModelRow['endyear'];
			$model['beginmonth'] = $ModelRow['beginmonth'];
			$model['endmonth']	= $ModelRow['endmonth'];
			$ModelName			   = $ModelRow['modelname'];
		}
	}
}
$cart 								= $this->cart;
$AssociatedParts					= $this->associatedparts;
$AssociationTypes					= $this->associationtypes;
// echo gettype($this->price)  . "=gettype(this->price)<br>";
// echo $this->price  . "=this->price<br>";
If(is_numeric($this->price) ){
	$Amount 							= number_format($this->price,$weberp['currencydecimalplaces']);
}else{
	$Amount 							= $this->price;
}
If(is_numeric($this->coreprice)){
	$CoreAmount 						= number_format($this->coreprice,$weberp['currencydecimalplaces']);
}else{
	$CoreAmount 							= $this->coreprice;
}	
If(!is_numeric($this->coreprice) ){
	$NetAmount						= $this->price;
}else{
	$NetAmount 						= number_format($Amount-$CoreAmount,$weberp['currencydecimalplaces']);
}	
$db 									=& JFactory::getDBO();
$Option 								=& JRequest::getVar('option');
$params 								= JComponentHelper::getParams($Option);
$pathtopics 						= $params->get( 'pathtopics');
$pathtopdfs 						= $params->get( 'pathtopdfs') . $row['appendfile'];
$ShowQuantityOnHandColumn		= $params->get( 'showquantityonhandcolumn');
$ShowQuantityOnHand				= $params->get( 'showquantityonhand');
$coresales 							= $params->get( 'coresales');
$pricecolumn						=  $params->get( 'pricecolumn');
$catalogonly						=  $params->get( 'catalogonly');
$user 								=& JFactory::getUser();
$username							=	$user->name;
$QuantityOnHand					= $this->get( 'quantityonhand');
If($QuantityOnHand > .01 or $params->get( 'adjustquantity')== 0){	
	$GoGray = '';
}else{
	$GoGray = 'disabled';
}
IF(isset($model)){
	If($model['beginmonth'] > 0){
		$Begining = $model['beginmonth'] . "/" . $model['beginyear']; 
	}else{
		$Begining = $model['beginyear']; 
	}
	If($model['endmonth'] > 0){
		$Ending = $model['endmonth'] . "/" . $model['endyear']; 
	}else{
		$Ending = $model['endyear']; 
	}
?>		
<fieldset>
	<legend><?php echo JText::_('SELECTION_CRITERIA') ?></legend> 
	<TABLE width="100%">
		<THEAD>
			<TH width="10%"><H4><?php echo JText::_('YEAR') ?></H4></TH>
			<TH width="20%"><H4><?php echo JText::_('MAKE') ?></H4></TH>
			<TH width="40%" colspan="2"><?php echo JText::_('MODEL') ?></H4></TH>
			<TH width="40%" colspan="2"><H4><?php echo JText::_('BEGINING') ?></H4></TH>
			<TH width="30%"><H4><?php echo JText::_('ENDING') ?></H4></TH>
		</THEAD>
		<THEAD>
			<TH style="color:blue"><H4><?php echo $mainframe->getUserState( 'year'); ?></H4></TH>
			<TH style="color:blue"><H4><?php echo $mainframe->getUserState( 'makename'); ?></H4></TH>
			<TH style="color:blue" colspan="2"><H4><?php echo $ModelName; ?></H4></TH>
			<TH width="40%" colspan="2"><H4><?php echo $Begining; ?></H4></TH>
			<TH width="30%"><H4><?php echo $Ending; ?></H4></TH>
		</THEAD>
	</TABLE>	
</fieldset>
<?php
}
?>
<fieldset>
	<legend>Part Information</legend> 		
	<TABLE cellpadding="4" width="100%">
		<TR>
			<TD align="right" width="30%"><?php echo JText::_('PART_NUMBER') ?>:</TD>
			<TD align="left" width="30%"><?php echo $row['stockid'];?></TD>
			<TD nowrap width="40%">

<?php
If($row['appendfile'] <> "0" and trim($row['appendfile']) <> "none" ){
	If( strpos($pathtopdfs, 'ttp://') > 0 OR (file_exists($pathtopdfs) AND fopen ($pathtopdfs , "r"))){
?>
				<A HREF="<?php echo $pathtopdfs?>" target='_blank'>More Information - PDF File</A>
<?php			
	}
}
$paththispic = $pathtopics . $row['stockid'] . '.jpg';
$paththispicL = $pathtopics . $row['stockid'] . 'L.jpg';
If(!file_exists($paththispicL)){
	$paththispicL = $paththispic;
}
$paththispictrans = $pathtopics . $row['stockid'] . '.png';
If(strtoupper(substr($paththispictrans,0,4)) <> 'HTTP' ){
	// echo 'stop     in ' . $_SERVER["SCRIPT_NAME"] . ' Line #' . __LINE__ . '  <br>';
	$paththispic = $paththispictrans;
}
If(file_exists($paththispictrans)){
	// echo 'stop     in ' . $_SERVER["SCRIPT_NAME"] . ' Line #' . __LINE__ . '  <br>';
	$paththispic = $paththispictrans;
}
If(strtoupper(substr($paththispictrans,0,4)) <> 'HTTP' AND file_exists($paththispictrans)){
	$paththispic = $paththispictrans;
}
// echo $paththispic  . '=paththispic   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
// echo $paththispictrans  . '=paththispictrans   in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
?>					
			</TD>
		</TR>
		<TR>
			<TD align="right"><?php echo JText::_('DESCRIPTION') ?>:</TD>
			<TD align="left"><?php echo $row['description']?></TD>
			<td rowspan="5">
				Click to enlarge photo<br>
				<a href="<?php echo $paththispicL ?>"  rel="shadowbox"><IMG SRC="<?php echo $paththispic?>" width="200"></a>
			</td>
		</TR>
<?php
If(isset($QuantityOnHand)){
	If($ShowQuantityOnHandColumn){
?>	
		<TR>
			<TD align="right" nowrap><?php echo JText::_('IN_STOCK') ?>:</TD>		
<?php

		If($ShowQuantityOnHand==1){
?>							
			<TD align="left" ><?php echo $QuantityOnHand?> <?php echo $row['units']  ?></TD>	
<?php
		}else{
			If($QuantityOnHand > 0.001){			
?>
			<TD align="left" ><?php echo JText::_('YES') ?></TD>	
<?php		
			}else{
?>
			<TD align="left"><?php echo JText::_('NO') ?></TD>	
<?php				
			}
		}
?>									
		</TR>
<?php
	}
}
If(!$catalogonly){
?>				
		<TR>
			<TD align="right" nowrap><?php echo JText::_('QUANTITY_IN_CART') ?>:</TD>
<?php
// echo '<pre>';var_dump($cart , '<br><br> <b style="color:brown">cart in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
	If(isset($cart[$row['stockid']]['quantityordered'])){
?>					
			<TD align="left"><?php echo $cart[$row['stockid']]['quantityordered']?></TD>
<?php
	}else{
?>			
			&nbsp;
<?php
}
?>					
		</TR>
<?php
}
?>		
		<TR>
			<TD align="right">
<?php
If(!$catalogonly){
?>			
				<form action="index.php" method="post" name="adminForm" >
					<input type="text" name="QuantityToAdd[<?php echo $row['stockid']?>]" id="QuantityToAdd<?php echo $row['stockid']?>" size="4" maxlength="6" <?php echo $GoGray?> />
					<input type="hidden" name="Price[<?php echo $row['stockid']?>]" id="Price<?php echo $row['stockid']?>" value="<?php echo $Amount?>"/>	
					<input type="hidden" name="controller" value="stockinformation" />
					<input type="hidden" name="view" value="stockinformation" />	
<?php
}else{
	echo "&nbsp;";
}
?>					
			</TD>
			<TD align="left" nowrap>
<?php
If(!$catalogonly){
?>			
				<button name="Search" onClick="this.form.submit()" class="button">
					<?php echo JText::_( 'ADD_TO_CART' );?>
				</button>
				</form>									
<?php
}
?>					
			</TD>
		</TR>
		
		<TR>
			<TD align="right"> 
<?php
				If($pricecolumn){
					echo JText::_('PRICE') . ":";
				}else{
					echo "&nbsp;";
				}
?>				
			</TD>
<?php
If($coresales=='Y'){
?>						
			<TD align="left"><?php echo $NetAmount?> <?php echo $row['units']  ?></TD>
<?php
}else{
?>						
			<TD align="left">
<?php
				If($pricecolumn){
					echo $Amount . " " . $weberp['currencycode'];
				}else{
					echo "&nbsp;";
				}
?>					
			</TD>
<?php
}
?>						
		</TR>
<?php
If($coresales=='Y'){
?>				
		<TR>
			<TD align="right">Core:</TD>
			<TD align="left"><?php echo $CoreAmount?></TD>
		</TR>					
		<TR>
			<TD align="right">Total:</TD>
			<TD align="left"><?php echo $Amount?></TD>
		</TR>
<?php
}
?>				
		<TR>
			<TD colspan="2" align="justify"><?php echo nl2br($row['longdescription'])?></TD>
		</TR>
	</TABLE>	
<?php
If(gettype($models) == 'array' AND count($models)>0){
?>	
	<table width="100%">	
	<THEAD><TH colspan="2" align="left"><?php echo JText::_('THIS_PART_FITS_ON_THE_FOLLOWING_MODELS') ?></TH></THEAD>
	<tr>
		<td valign="top">
			<table>

<?php
	$j = 1;
	$n = round(count($models)/2+.4999);
	If(gettype($models) == 'array'){
		Foreach($models as $Count=>$ModelRow){
			If($ModelRow['beginmonth'] > 0){
				$Begining = $ModelRow['beginmonth'] . "/" . $ModelRow['beginyear']; 
			}else{
				$Begining = $ModelRow['beginyear']; 
			}
			If($ModelRow['endmonth'] > 0){
				$Ending = $ModelRow['endmonth'] . "/" . $ModelRow['endyear']; 
			}else{
				$Ending = $ModelRow['endyear']; 
			}
?>	

				<TR>
					<TD><?php echo $ModelRow['makername']?> model - <?php echo $ModelRow['modelname']?> from <?php echo $Begining ?>-<?php echo $Ending ?></TD> 		
				</tr>
<?php
			if($j == $n){
				$j=0;
?>				


<?php
			}
			$j=$j+1;
		}
	}
?>
			</table>
		</td>
	<TR>
	</table>
<?php
}
?>	
	<table width="100%">
<?php
If(count($AssociatedParts) > 0 ){
?>	
	<THEAD><TH colspan="2" align="left"><?php echo JText::_('THE_FOLLOWING_ARE_ASSOCIATED_ITEMS') ?></TH></THEAD>
	
<?php
	foreach($AssociatedParts as $ID=>$AP){
		// var_dump_pre($AP);exit;
		$AssociatedPartNumber 		= $AP['associatedpart'];
		If(isset($AssociationTypes[$AP['associationtype']])){
			$AssociationDescription 	= $AssociationTypes[$AP['associationtype']];
		}else{
			$AssociationDescription 	= '';
		}
		If(isset($AP['description'])){
			$AssociatedPartDescription = $AP['description'];
		}else{
			$AssociatedPartDescription = '';
		}
		$paththispic = $pathtopics . $AssociatedPartNumber . '.jpg';
		// echo $paththispic  . "=paththispic<br>";
?>	
	
		<TR>
			<TD>
				<?php echo $AssociationDescription ?> - 
				<A HREF='index.php?option=com_cartweberp&controller=stockinformation&stockid=<?php echo $AssociatedPartNumber  ?>'>
					<?php echo $AssociatedPartNumber   ?>
				</A>
			</td>
			<td rowspan="2">
<?php				
		If(strtoupper(substr($paththispic,0,4)) == 'HTTP' OR file_exists($paththispic)){		
?>		
					<a href="<?php echo $paththispicL ?>   rel="shadowbox"?>"><IMG SRC="<?php echo $paththispic?>" width="120"></a>
				
<?php
		}
?>
			</TD>
		</tr>
		<tr>			
			<td><?php echo $AssociatedPartDescription  ?></td>
		</tr>
<?php

		if($j == $n){
			$j=0;
?>				


<?php
		}
		$j=$j+1;
	}
}

?>	
		<tr><td>&nbsp;</td><td></td>&nbsp;</tr>
		<THEAD style="font-size:16px;"> 
			<TH align="left">
				<A HREF="<?php echo 'index.php?option=com_cartweberp&controller=cart'; ?>"><?php echo JText::_('DISPLAY_CART_-_CHECK_OUT') ?></A>
			</TH>
			<TH align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;		
				<A HREF="index.php?option=com_cartweberp&controller=cartcatalog" onClick="history.back();return false;"><?php echo JText::_('BACK_TO_CATALOG') ?></A>
			</TH>
		</THEAD>
</TABLE>
<center>Powered by <a href="http://joomlamo.com" target="blank">CARTwebERP</a></center>
<?php
$app    =& JFactory::getApplication();
$router =& $app->getRouter();
$router->setVar( 'view', 'cartcatalog' );
$router->setVar( 'controller', 'cartcatalog' );
$router->setVar( 'model', 'cartcatalog' );
function var_dump_pre($mixed = null) {
  echo '<pre>';
  var_dump($mixed);
  echo '</pre>';
  return null;
}
?>
</fieldset>