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
global $limit,$totalitems,$mainframe;
$Option 							=& JRequest::getVar('option');
$params 							= JComponentHelper::getParams($Option);
$coresales 						= $params->get( 'coresales');
$CartTotal = 0;
$shipaddress 	=& $this->shipaddress;
$ShipperName 	=& $this->freight;
If($mainframe->getUserState( "ShipAddressMessage")){
	$ShipAddressMessage = $mainframe->getUserState( "ShipAddressMessage");
}else{
	$ShipAddressMessage = '';
}
If(count( $this->items ) > .1){
?>

<fieldset>
	<legend><?php echo JText::_('THANK_YOU_FOR_SHOPPING_WITH_US') ?></legend> <BR>
<?php
	If($mainframe->getUserState( "CheckOutType")=='Express'){
		echo JText::_('YOUR_PAYPAL_ACCOUNT_HAS_BEEN_CHARGED') . "<br>";
	}elseIf($mainframe->getUserState( "CheckOutType")=='CreditCard'){
		echo JText::_('YOUR_CREDIT_CARD_HAS_BEEN_CHARGED'). "<br>";
	}elseIf($mainframe->getUserState( "CheckOutType")=='Charge'){
		echo JText::_('YOUR_CUSTOMER_ACCOUNT_HAS_BEEN_CHARGED').  "<br>";
	}
	If(strlen(trim($this->customer)) > 0){
?>
	
	Customer: <font Style="color:blue;font-size:18px;"><?php echo $this->customer?></font>
<?php
	}
?>	
<font Style="color:brown;font-size:16px;float:right"> Sales Order <?php echo $mainframe->getUserState( "SalesOrderNumber")?></font>
<form action="index.php" method="post" name="adminForm" >
<TABLE cellpadding="1">
	<TR>
		<TD colspan="3"><B><?php echo JText::_('IF_THE_INFORMATION_BELOW') ?></B>
		</TD>
	</TR>
	<TR>	
		<TD align="right" style="color:green"><B><?php echo JText::_('NAME') ?>:</B></TD>    
		<TD><input class="text_area" type="text" name="brname" id="brname" size="30" maxlength="40" value="<?php echo $shipaddress['buyername'];?>" /></TD>
		<TD align="right" colspan="2"><B><?php echo $ShipAddressMessage?></B></TD>
	<TR>
	</TR>	
		<TD align="right" style="color:green"><?php echo JText::_('ADDRESS') ?>:</TD>
		<TD><input class="text_area" type="text" name="braddress1" id="braddress1" size="30" maxlength="40" value="<?php echo $shipaddress['deladd1'];?>" /></TD>
		<TD align="right" style="color:green">Phone:</TD>
		<TD><input class="text_area" type="text" name="phone" id="phone" size="20" maxlength="20" value="<?php echo $shipaddress['phone'];?>" /></TD>
	<TR>
	</TR>	
		<TD align="right" style="color:green"><?php echo JText::_('CITY') ?>:</TD>
		<TD><input class="text_area" type="text" name="braddress2" id="braddress2" size="30" maxlength="40" value="<?php echo $shipaddress['deladd2'];?>" /></TD>
		<TD align="right" style="color:green"><?php echo JText::_('EMAIL') ?>:</TD>
		<TD><input class="text_area" type="text" name="email" id="email" size="30" maxlength="200" value="<?php echo $shipaddress['email'];?>" /></TD>
	<TR>
	</TR>	
		<TD align="right" style="color:green"><?php echo JText::_('STATE') ?>:</TD>
		<TD><input class="text_area" type="text" name="braddress3" id="braddress3" size="30" maxlength="40" value="<?php echo $shipaddress['deladd3'];?>" /></TD>
		<TD align="right" style="color:green">&nbsp;</TD>
		<TD>&nbsp;</TD>
	<TR>
	</TR>	
		<TD align="right" style="color:green"><?php echo JText::_('ZIP') ?>:</TD>
		<TD><input class="text_area" type="text" name="braddress4" id="braddress4" size="30" maxlength="40" value="<?php echo $shipaddress['deladd4'];?>" /></TD>
		<TD align="right" style="color:green">&nbsp;</TD>
		<TD>&nbsp;</TD>
	<TR>
	</TR>	
		<TD align="right" style="color:green"><?php echo JText::_('COUNTRY') ?>:</TD>
		<TD><input class="text_area" type="text" name="braddress6" id="braddress6" size="30" maxlength="40" value="<?php echo $shipaddress['deladd6'];?>" /></TD>
		<TD align="right" style="color:green">&nbsp;</TD>
		<TD>&nbsp;</TD>
	<TR>
	<TR>
		<TD colspan="2" align="center"><input type="submit" name="modify" value="<?php echo JText::_('CLICK_AFTER_MODIFYING_SHIP_INFORMATION') ?>"></TD>
	</TR>
</TABLE>
<input type="hidden" name="salesorder" value="<?php echo $mainframe->getUserState( "SalesOrderNumber") ;?>" />
<input type="hidden" name="debtorno" value="<?php echo $shipaddress['debtorno']?>" />
<input type="hidden" name="branchcode" value="<?php echo $shipaddress['branchcode']?>" />
<input type="hidden" name="controller" value="checkout" />
<input type="hidden" name="view" value="checkout" />
<input type="hidden" name="task" value="updateshipaddress" />
</form>
<TABLE cellpadding="5" border="1">
	<TR>
		<TD><b><?php echo JText::_('PART_NUMBER') ?></b></TD> 
		<TD align="right"><b><?php echo JText::_('QUANTITY') ?></b></TD>  
		<TD align="right"><b><?php echo JText::_('PRICE') ?></b></TD>
<?php
	$TotalLineColspan = 5;
	If($coresales=='Y'){
		$TotalLineColspan = 7;
?>	
		<TD align="right"><b><?php echo JText::_('CORE') ?></b></TD>
		<TD align="right"><b><?php echo JText::_('TOTAL') ?></b></TD>
<?php
	}
?>		
		<TD align="right"><b><?php echo JText::_('EXTENSION') ?></b></TD>
		<TD><b><?php echo JText::_('DESCRIPTION') ?> - <SMALL><?php echo JText::_('CLICK_FOR_MORE') ?></SMALL></b></TH>
	</TR>
	
<?php
$price 			=& $this->price;
$coreprice 		=  $this->coreprice;
$description 	=& $this->description;
$TotalQuantity = 0;
for ($i=0, $n=count( $this->items ); $i < $n; $i++){
	$row 			=& $this->items[$i];
	$partnumberthisline = $row['stkcode'];
	If(isset($coreprice[$partnumberthisline])){
		$CorePrice = $coreprice[$partnumberthisline];
	}else{
		$CorePrice = 0;
	}
	$extension 	= $row['unitprice']*$row['quantity'];
	$netprice	= $row['unitprice'] - $CorePrice;
	$CartTotal 	= $CartTotal + $extension;
	$TotalQuantity = $TotalQuantity + $row['quantity'];
?>		
	<TR>
		<TD><?php echo $row['stkcode']?></TD> 
		<TD align="right"><?php echo $row['quantity']?></TD>
<?php
	$TotalLineColspan = 1;
	If($coresales=='Y'){
		$TotalLineColspan = 3;
?>		
		<TD align="right"><?php echo number_format($netprice,2)?></TD>
		<TD align="right"><?php echo number_format($CorePrice,2)?></TD>
<?php
	}
?>		
		<TD align="right"><?php echo number_format($row['unitprice'],2)?></TD>
		<TD align="right"><?php echo number_format($row['unitprice']*$row['quantity'],2)?></TD> 
		<TD>
			<A HREF="<?php echo 'index.php?option=com_cartweberp&controller=stockinformation&stockid=' . $row['stkcode'] ; ?>"><?php echo $description[$row['stkcode']]?></A>
			<input type="hidden" name="Price[<?php echo $row['stkcode']?>]" value="<?php echo $price[$row['stkcode']]?>" />
			<input type="hidden" name="ID[<?php echo $row['stkcode'];?>]" value="<?php echo $row['stkcode'];?>" />
		</TD> 
	</tr>
<?php
}
$Shipper 		= $mainframe->getUserState( "Shipper") ;
$FreightAmount = $mainframe->getUserState( "FreightAmount") ;
$SalesTaxAmount= $mainframe->getUserState( "SalesTaxAmount") ;
$TotalAmount 	= $mainframe->getUserState( "Payment_Amount") ;
?>	
	<TR>
		<TD><?php echo JText::_('ITEMS_IN_CHECKOUT') ?></TD> 
		<TD align="right"><?php echo $TotalQuantity ?></TD>
		<TD colspan="<?php echo $TotalLineColspan  ?>">&nbsp;</TD> 
		<TD align="right" ><?php echo number_format($CartTotal,2)?></TD> 
		<TD align="left">
<?php
If($SalesTaxAmount == 0 AND $FreightAmount == 0){
?>		
			Total
<?php
}else{
?>	
			Sub Total		
<?php
}
?>
		</TD>
	</tr>	

<?php
If($FreightAmount <> 0){
?>			

	<TR>
		<TD>&nbsp;</TD> 
		<TD>&nbsp;</TD>
		<TD align="right" colspan="<?php echo $TotalLineColspan  ?>"><?php echo $ShipperName[$Shipper]  ?></TD> 
		<TD align="right" ><?php echo number_format($FreightAmount,2)?></TD> 
		<TD align="left">
			Freight
		</TD>
	</tr>		
<?php
}
If($SalesTaxAmount <> 0){
?>	
	<TR>
		<TD>&nbsp;</TD> 
		<TD>&nbsp;</TD>
		<TD colspan="<?php echo $TotalLineColspan  ?>">&nbsp;</TD> 
		<TD align="right" ><?php echo number_format($SalesTaxAmount,2)?></TD> 
		<TD align="left">
			Sales Tax
		</TD>
	</tr>	
<?php
}
If($SalesTaxAmount <> 0 OR $FreightAmount <> 0){
?>
	<TR>
		<TD>&nbsp;</TD> 
		<TD>&nbsp;</TD>
		<TD colspan="<?php echo $TotalLineColspan  ?>">&nbsp;</TD> 
		<TD align="right" ><?php echo number_format($TotalAmount,2)?></TD> 
		<TD align="left">
			Total
		</TD>
	</tr>	
<?php
}
?>	
	<TR>
		<TD>&nbsp;</TD> 
		<TD>&nbsp;</TD>
		<TD>Order Number</TD>
		<TD><?php echo $mainframe->getUserState( "SalesOrderNumber") ;?></TD> 
		<TD>
			<A HREF='index.php?option=com_cartweberp&controller=cartcatalog&donotresetcategory=true'><?php echo JText::_('BACK_TO_CATALOG') ?></A>
		</TD>
	</tr>
</TABLE>

<?php
$app    =& JFactory::getApplication();
$router =& $app->getRouter();
$router->setVar( 'view', 'checkout' );
$router->setVar( 'controller', 'checkout' );
$router->setVar( 'model', 'checkout' );
// do not display the pagination unless there are more
If($totalitems > $limit){
?>

<DIV align="center" STYLE="border: 3px outset;
	color: Black;
	height:35;
	width:40;
	font-weight: bold;
	cursor: hand;
	text-decoration: none;	
	background: #d4d4d4 fixed;
	font-size: 14px;">
 	<form action="<?php echo $this->request_url ; ?>" method="post" enctype="multipart/form-data"  name="paganation">
<?php		
		jimport('joomla.html.pagination');
		echo  $this->pagination->getPagesLinks();
 		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo  $this->pagination->getLimitBox() . " items per screen ";
	 	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo  $this->pagination->getPagesCounter();
?>
	</form>
</DIV>

<?php
}
?>
</fieldset>
<center>Powered by <a href="http://joomlamo.com" target="blank">CARTwebERP</a></center>

<?php
}else{
?>
	<H3><?php echo JText::_('THANK_YOU_FOR_SHOPPING_WITH_US') ?></H3>
<center>Powered by <a href="http://joomlamo.com" target="blank">CARTwebERP</a></center>
<?php
}
?>
