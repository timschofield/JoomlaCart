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
$Option 	=& JRequest::getVar('option');
$params 	=  JComponentHelper::getParams($Option);
$mainframe = JFactory::getApplication();
If(gettype($this->items) == 'array' AND count( $this->items ) > .1){
	$CartTotal = $this->carttotal;
	If(array_key_exists('price', $CartTotal)){
		$_SESSION["Payment_Amount"] = $CartTotal['price'];
		$mainframe->setUserState( "Payment_Amount", number_format($CartTotal['price'],2)) ; 
	}else{
		$_SESSION["Payment_Amount"] = 0;
		$mainframe->setUserState( "Payment_Amount", 0) ; 
	}
	$coresales 						=  $params->get('coresales');
	$Freight 						=& $this->freight;
	$DestinationZipCode			=  $this->destinationzipcode;
	$SalesTaxRate					=& $this->salestaxrate;
	$FreightAmount					=  $this->freightamount; 
	$TotalWeight					= 	$this->totalweight;
	$TotalVolume					=	$this->totalvolume;
}
$mainframe->setUserState( "InvalidZip", 0) ;
$mainframe->setUserState( "OutofState", 0) ;
$mainframe->setUserState( "DestinationZipError", 0) ;
$CustomerInformation =& $this->customer;
$Branches 				=& $this->branches;
$DebtorInformation 	=& $this->debtorinfo;
$LoggedUser				=& $this->user;
$post						= JRequest::get('request');
$weberp					= modwebERPHelper::getweberp();
global $limit,$totalitems,$mainframe;
?>

<fieldset>
	<legend><?php echo JText::_('CART_SELECTIONS') ?></legend>
	<center>
		<A HREF='index.php?option=com_cartweberp&controller=cart&task=remove'><?php echo JText::_('REMOVE_ALL_FROM_CART') ?></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<A HREF='index.php?option=com_cartweberp&controller=cartcatalog&donotresetcategory=true'><?php echo JText::_('BACK_TO_CATALOG') ?></A>
	</center>
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'CUSTOMER_INFORMATION' ); ?></legend>
		<table width="100%">
			<tr>
<?php
If(isset($CustomerInformation['Name'])){
?>
				<td align="right"><?php echo JText::_('CUSTOMER') ?>:</td> 
				<td align="left" Style="color:blue;font-size:18px;"><?php echo $CustomerInformation['Name']?></td>
<?php
}
?>		

<?php
// var_dump($LoggedUser);echo "<br><br>LoggedUser<BR><BR><BR>";exit;
// IF($mainframe->getUserState( "chargeforfreight") or $mainframe->getUserState( "chargeforsalestax") or $LoggedUser->name==NULL){
?>
<form action="index.php" method="post" name="adminForm" >
<?php
If(count($Branches) > 1){
?>
			</tr>
			<tr>
				<td align="right" class="key">
					<label for="Branch">
						<?php echo JText::_( 'CHOOSE_BRANCH' ); ?>:
					</label>
				</td>
				<td colspan="2" align="left">
				<SELECT name="branch" onchange="this.form.submit('adminForm')">
					<OPTION value="" ><?php echo JText::_('SELECT_BRANCH') ?></OPTION>
<?php
	foreach($Branches as $count=>$Name){ 
		If($post['branch'] == $Name['branchcode']){
			$Selected = 'SELECTED';
			$DestinationZipCode = substr($Name['zip'],0,5);
		}else{
			$Selected = '';
		}
?>		
					<OPTION value="<?php echo $Name['branchcode'] ?>" <?php echo $Name ?> <?php echo $Selected  ?>><?php echo $Name['brname'] ?></OPTION>
<?php
	}
?>
				</SELECT>
			</td>
		</tr>
<?php
}else{
?>
				<input type="hidden" name="branch" value="<?php echo $Branches['branchcode']  ?>" />
<?php
}

?>	
		<TR>
			<td align="right">			
				<?php echo JText::_('DESTINATION_ZIP_CODE') ?>:
			</td>
			<td align="left">
				<input type="text" name="DestinationZipCode" value="<?php echo $DestinationZipCode  ?>" size="5" maxlength="5">
			</td>
<?php
If($mainframe->getUserState( "chargeforfreight")){
?>		
		</tr><tr>
			<TD colspan="1" align="right"> <?php echo JText::_('CHOOSE_SHIPPING_METHOD') ?>:</TD>
			<TD colspan="2" align="left">
				<SELECT name="freightselected" onchange="this.form.submit()">
<?php
	
	foreach($Freight as $FID=>$FDescription){
		// IF form is posted use $post['freight'] else 
		// IF session variable exists use it else $CustomerInformation for default select
		$post	= JRequest::get('post');
		IF(isset($post['freightselected'])){
			$SelectionCompare = $post['freightselected'];
			$mainframe->setUserState( "FreightSelected", $post['freightselected']) ;
		}else{
			If($mainframe->getUserState('FreightSelected')){
				$SelectionCompare = 	$mainframe->getUserState('FreightSelected');
			}else{			
				$SelectionCompare = $DebtorInformation['defaultshipvia'];
				$mainframe->setUserState( "FreightSelected",  $SelectionCompare) ;
			}
		}
		IF($SelectionCompare == $FID){
			$Selected = 'SELECTED';
			$_SESSION['FreightSelected'] = $FID;
			$mainframe->setUserState( "FreightSelected", $FID) ;
		}else{
			$Selected = '';		
		}
?>		
					<OPTION value="<?php echo $FID?>" <?php echo $Selected?>><?php echo $FDescription?></OPTION>
<?php
	}
?>
				</SELECT>
			</TD>
<?php
}
?>		
		</TR>
	</table>
</fieldset>
<?php
// }
?>
<TABLE style="border:none;padding:10px">
<?php
If(gettype($this->items) == 'array' AND count( $this->items ) > .1){
?>
	
	<THEAD>
		<TH><H4><?php echo JText::_('PART_NUMBER') ?></H4>&nbsp;</TH> 
		<TH align="right"><b><?php echo JText::_('QUANTITY') ?></b>&nbsp;</TH>  
		<TH align="right"><b><?php echo JText::_('PRICE') ?></b>&nbsp;</TH> 
<?php
	If($coresales=='Y'){
		$TotalLineColspan = 3;
?>
		<TH align="right"><b><?php echo JText::_('CORE') ?></b></TH> 
		<TH align="right"><b><?php echo JText::_('TOTAL') ?></b></TH>
<?php
	}else{
		$TotalLineColspan = 1;
	}
?>		
		<TH align="right"><b><?php echo JText::_('EXTENSION') ?></b>&nbsp;</TH>
		<TH><H4><?php echo JText::_('DESCRIPTION') ?> - <SMALL><?php echo JText::_('CLICK_FOR_MORE') ?></SMALL></H4></TH>
	</THEAD>
	
<?php
	$price 			=& $this->price;
	$coreprice 		= $this->coreprice;
	$description 	= $this->description;
	$TotalQuantity = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++){
		$row 			=& $this->items[$i];
		If(isset($coreprice[$row['partnumber']])){
			$CorePrice = $coreprice[$row['partnumber']];
		}else{
			$CorePrice = 0;
		}
		If(isset($price[$row['partnumber']])){
			$extension 	= $price[$row['partnumber']] * $row['quantityordered'];
			$netprice	= $price[$row['partnumber']] - $CorePrice;
			$salestax	= $SalesTaxRate[$i]*$extension;
			$SalesTaxArray[$row['partnumber']]	= $SalesTaxRate[$i]*$extension;
		}else{
			$extension 							= 0;
			$price[$row['partnumber']]		= 0;
			$coreprice[$row['partnumber']] 	= 0;
			$netprice							= 0;
			$salestax							= 0;
		}
		$TotalQuantity = $TotalQuantity + $row['quantityordered'];
?>		
	<TR>
		<TD><?php echo $row['partnumber']?></TD> 
		<TD align="right"><input type="text" name="QuantityOrdered[<?php echo $row['partnumber']?>]" id="QuantityOrdered[<?php echo $row['partnumber']?>]" size="4" maxlength="6" value="<?php echo $row['quantityordered']?>" /></TD>
<?php
		If($coresales=='Y'){
?>
		<TD align="right"><?php echo number_format($netprice,2)?></TD>
		<TD align="right"><?php echo number_format($CorePrice,2)?></TD>
<?php
		}
?>		
		<TD align="right"><?php echo number_format($price[$row['partnumber']],2)?></TD>
		<TD align="right"><?php echo number_format($extension,2)?></TD> 
		<TD>
			<A HREF="<?php echo 'index.php?option=com_cartweberp&controller=stockinformation&stockid=' . $row['partnumber'] ; ?>"><?php echo $description[$i]?></A>
			<input type="hidden" name="Price[<?php echo $row['partnumber']?>]" value="<?php echo $price[$row['partnumber']]?>" />
			<input type="hidden" name="ID[<?php echo $row['partnumber']?>]" value="<?php echo $row['id']?>" />
		</TD> 
	</tr>
<?php
	}
	If(isset($SalesTaxArray)){
		$TotalAmount = $CartTotal['price'] + $FreightAmount['Cost'] + array_sum($SalesTaxArray);
		$mainframe->setUserState( "SalesTaxAmount",array_sum($SalesTaxArray)) ;
	}else{
		$TotalAmount = $CartTotal['price'] + $FreightAmount['Cost'];
		$mainframe->setUserState( "SalesTaxAmount",0) ;
	}
	// echo $TotalAmount  . "=TotalAmount<br>";
	if(isset($FreightAmount['Shipper'])){
		$mainframe->setUserState( "Shipper",$FreightAmount['Shipper']) ;
	}
	$mainframe->setUserState( "CartTotal",$CartTotal['price']) ;
	$mainframe->setUserState( "FreightAmount",$FreightAmount['Cost']) ;
	
	$mainframe->setUserState( "Payment_Amount",$TotalAmount) ;
?>	
	
	<TR>
		<TD><?php echo JText::_('ITEMS_IN_CART') ?></TD> 
		<TD><?php echo $TotalQuantity?></TD>
		<TD align="center">				
			<input type="submit" name="Update" class="button" value="<?php echo JText::_('UPDATE_CART') ?>" />
		</TD>
		<TD align="right" colspan="<?php echo $TotalLineColspan;?>" no wrap>
			<?php echo number_format($CartTotal['price'],2)?>
		</TD> 
		<TD align="left" no wrap>
<?php
	If(isset($SalesTaxArray) AND (array_sum($SalesTaxArray) > .001 OR $FreightAmount > .001)){
?>		
			Subtotal&nbsp;&nbsp;&nbsp;	
<?php
	}else{
?>			
			Total&nbsp;&nbsp;&nbsp;
<?php
	}
?>					
		</TD>
	</tr>	
<?php
	$GTColSpan = 3+$TotalLineColspan;
?>	
	<tr>
		<td colspan="<?php echo $GTColSpan;?>" align="right">
<?php
	If($FreightAmount['Cost'] > .001){
?>		
			<?php echo number_format($FreightAmount['Cost'],2)  ?>
<?php
}
?>			
		</td>
		<td>
			<?php echo JText::_('FREIGHT_WEIGHT') ?>:<?php echo $TotalWeight  ?> Volume:<?php echo $TotalVolume  ?> 
		</td>
	</tr>
<?php
	If(isset($SalesTaxArray) AND array_sum($SalesTaxArray) > .001){
?>	
	<tr>
		<td colspan="<?php echo $GTColSpan;?>" align="right">
			<?php echo number_format(array_sum($SalesTaxArray),2)  ?>
		</td>
		<td>
			<?php echo JText::_('SALES_TAX') ?>
		</td>
	</tr>
<?php
	}
	If(isset($SalesTaxArray) AND (array_sum($SalesTaxArray) > .001 OR $FreightAmount['Cost'] > .001)){
?>	
	<tr>
		<td colspan="<?php echo $GTColSpan;?>" align="right">
			<?php echo number_format($TotalAmount,2)  ?>
		</td>
		<td>
			<?php echo JText::_('TOTAL') ?>
		</td>
	</tr>
<?php
	}
?>	
</TABLE>
<input type="hidden" name="controller" value="cart" />
<input type="hidden" name="view" value="cart" />
<input type="hidden" name="task" value="updatecart" />
</form>
<DIV align="center">
	<fieldset>
		<legend><?php echo JText::_('PAYMENT_TYPES') ?></legend>
			<table cellpadding="4">
				<tr>
<?php
	$DataCellCount =0;
	If(isset($weberp['authorizenettestapiloginid']) AND strlen(trim($weberp['authorizenettestapiloginid'])) > 0){
		$DataCellCount = $DataCellCount + 1;
		If($DataCellCount >= 4){
			$DataCellCount =0;
			echo "</tr><tr>";
		}
		If($weberp['authorizenet'] == 0){
			$url					= "https://test.authorize.net/gateway/transact.dll";
			$LegendText 		= 'Test Pay with Authorize.net';
			$testMode			= "true";
		}else{
			$url					= "https://secure.authorize.net/gateway/transact.dll";
			$LegendText 		= 'Pay with Credit Card';
			$testMode			= "false";
		}
?>	
					<td width="50%">
						<fieldset>
							<legend style="font-size:12px"><?php echo $LegendText  ?></legend>
							<BR>									
							<!-- This section generates the "Submit Payment" button using PHP           -->
<?php
		$loginID				= $weberp['authorizenettestapiloginid'];
		$transactionKey	= $weberp['authorizenettesttransactionkey'];
		$amount 				= number_format($TotalAmount,2);
		$AuthNetDesc 		= "Transaction";
		$label 				= "Submit Payment"; // The is the label on the 'submit' button

		$ReturnURL			= $this->request_url;
		$ReceiptURL			= $this->request_url;
		$invoice	= intval(date('Y') . date('m') . date('d') . date('H') . date('i') . date('s'));
		$sequence	= rand(1, 1000);
		$timeStamp	= time ();
		// The following lines generate the SIM fingerprint.  PHP versions 5.1.2 and
		// newer have the necessary hmac function built in.  For older versions, it
		// will try to use the mhash library.
		if( phpversion() >= '5.1.2' )
		{	$fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey); }
		else 
		{ $fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey)); }

		
		// Create the HTML form containing necessary SIM post values
		echo "<FORM method='post' action='$url' >";
		// Additional fields can be added here as outlined in the SIM integration guide
		// at: http://developer.authorize.net
		$RespondURL = substr($this->request_url, 0, strpos($this->request_url, '&'));
?>							
							<INPUT type='hidden' name='x_login' 			value='<?php echo $loginID  ?>' />
							<INPUT type='hidden' name='x_amount' 			value='<?php echo $amount  ?>' />
							<INPUT type='hidden' name='x_description' 	value='<?php echo $AuthNetDesc  ?>' />
							<INPUT type='hidden' name='x_invoice_num' 	value='<?php echo $invoice  ?>' />
							<INPUT type='hidden' name='x_fp_sequence' 	value='<?php echo $sequence  ?>' />
							<INPUT type='hidden' name='x_fp_timestamp' 	value='<?php echo $timeStamp  ?>' />
							<INPUT type='hidden' name='x_fp_hash' 			value='<?php echo $fingerprint  ?>' />
							<INPUT type='hidden' name='x_test_request' 	value='<?php echo $testMode  ?>' />
							<INPUT type='hidden' name='x_show_form' 		value='PAYMENT_FORM' />
							<INPUT TYPE='hidden' name='x_method' 			value='CC' />
							<INPUT TYPE='HIDDEN' NAME='x_version' 			VALUE='3.1'>							
							<INPUT TYPE='HIDDEN' NAME='x_relay_response' VALUE='TRUE'>															
							<INPUT TYPE='HIDDEN' NAME='x_relay_url' 		VALUE='<?php echo $RespondURL  ?>'>	
							<input type="hidden" name="x_receipt_link_method" value="POST">
							<input type="hidden" name="x_receipt_link_text" value="Click Here To Return">
							<input type="hidden" name="x_receipt_link_url" value="<?php echo $RespondURL  ?>">
<?php
		for ($i=0, $n=count( $this->items ); $i < $n; $i++){		
			$row =& $this->items[$i];
			$extension 	= $price[$row['partnumber']] * $row['quantityordered'];
			$salestax	= $SalesTaxRate[$i]*$extension;
			$SalesTaxArray[$row['partnumber']]	= $SalesTaxRate[$i]*$extension;
			IF($SalesTaxRate[$i] <> 0){
				$Taxable = 'Y';
			}else{
				$Taxable = 'N';
			}
?>				
							<INPUT TYPE="HIDDEN" name="x_line_item" VALUE="item<?php echo $i  ?><|><?php echo $row['partnumber']  ?><|><?php echo $description[$i]  ?><|><?php echo $row['quantityordered']  ?><|><?php echo $price[$row['partnumber']]  ?><|><?php echo $Taxable  ?>">
<?php
		}
		If(isset($SalesTaxArray) AND array_sum($SalesTaxArray) <> 0){
?>		
		<INPUT TYPE="HIDDEN" name="x_tax" VALUE="Tax<|>state tax<|><?php echo array_sum($SalesTaxArray)  ?>">
<?php
		}
		If(array_key_exists('Cost',$FreightAmount) AND $FreightAmount['Cost'] <> 0){
?>		
		<INPUT TYPE="HIDDEN" name="x_freight" VALUE="Freight1<|><|><?php echo $FreightAmount['Cost']  ?>">
<?php
		}
?>									
							<input type='submit' 								value='<?php echo $label  ?>'/>
							</FORM>
							<!-- This is the end of the code generating the "submit payment" button.    -->
						</fieldset>
					</td>
<?php
	}
	// echo '<pre>';var_dump($CustomerInformation , '<br><br> <b style="color:brown"> CustomerInformation  </b><br><br>');echo '</pre>';
	$DataCellCount = 0;
	// echo $CustomerInformation['OpenAccount']  . '= CustomerInformation[OpenAccount]  in  YourScriptNameHere Line #' . __LINE__ . '  <br>';
	If(isset($CustomerInformation['OpenAccount']) AND $CustomerInformation['OpenAccount'] == TRUE){
		$DataCellCount = $DataCellCount + 1;		
		If($DataCellCount >= 4){
			$DataCellCount =0;
			echo "</tr><tr>";
		}
?>				
				<td>
					<form action="index.php?option=com_cartweberp" method="post" name="PaymentForm" >
							<fieldset>
								<legend style="font-size:12px"><?php echo JText::_('OPEN_CHARGE_AND_COD_ACCOUNT') ?></legend>
							
								Purchase Order Number 
								<input type="text" name="PurchaseOrderNumber" id="PurchaseOrderNumber" size="14" maxlength="45"  />
								<input type="submit" name="Charge" class="button" value="<?php echo JText::_('CHARGE_TO_ACCOUNT') ?>" />
							</fieldset>
						<input type="hidden" name="controller" value="checkout" />
						<input type="hidden" name="view" value="checkout" />
						<input type="hidden" name="task" value="updatecheckout" />
						<input type="hidden" name="checkouttype" value="AccountsReceivable" />
					</form>
				</td>

<?php
		$DataCellCount = $DataCellCount + 1;
	} 
	$post	= JRequest::get('request');
	If(array_key_exists('TOKEN',$post)){
		$tokenValue = $post['TOKEN'];
	}
	If(strlen(trim($weberp['paypalpassword'])) > 0){		
		$DataCellCount = $DataCellCount + 2;		
		If($DataCellCount >= 4){
			$DataCellCount =0;
			echo "</tr><tr>";
		}
?>	
					<td width="50%">
						<fieldset>
							<legend style="font-size:12px"><?php echo JText::_('PAYPAL_ACCOUNT_REQUIRED') ?></legend>
							<BR>
							<form action="index.php?option=com_cartweberp" method="post" name="ExpressForm" >
								<input type='image' name='submit' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif' border='0' align='top' alt='PayPal'/>
								<input type="hidden" name="controller" value="checkout" />
								<input type="hidden" name="view" value="checkout" />
								<input type="hidden" name="task" value="expresscheckout" />
								<input type="hidden" name="checkouttype" value="Express" />
							</form>
						</fieldset>
					</td>
					<td>
						<fieldset>
							<legend style="font-size:12px"><?php echo JText::_('PAY_WITH_ANY_CREDIT_CARD') ?></legend><BR>
							<form action="index.php?option=com_cartweberp" method="post" name="CreditCardForm" >
								<input type='image' name='submit' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif' border='0' align='top' alt='PayPal'/>
								<input type="hidden" name="controller" value="creditcard" />
								<input type="hidden" name="view" value="creditcard" />
								<input type="hidden" name="task" value="display" />
							</form>	
						</fieldset>
					</td>
<?php
	}
	If((isset($weberp['googlekey']) AND strlen(trim($weberp['googlekey'])) > 0) OR (isset($weberp['googlesndboxkey']) AND strlen(trim($weberp['googlesndboxkey'])) > 0)){
		If($weberp['googlecheckout'] == 0){
			$url					= "https://sandbox.google.com/checkout/api/checkout/v2/merchantCheckout/Merchant/" . $weberp['googlemerchantid'] . "  ?>";
			$LegendText 		= 'Test Pay - Google';
			$testMode			= "true";
		}else{
			$url					= "https://checkout.google.com/api/checkout/v2/merchantCheckout/Merchant/" . $weberp['googlemerchantid'] . "  ?>";
			$LegendText 		= 'Pay with Credit Card';
			$testMode			= "false";
		}
?>
					<td>
						<fieldset>
							<legend style="font-size:12px"><?php echo $LegendText  ?></legend>
							<BR>
						<form method="POST" action="<?php echo $url  ?>" accept-charset="utf-8">
							<!-- Sell digital goods with description-based delivery of download instructions (with tax, no shipping) -->
<?php
		$ReturnURL	= $this->request_url;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++){		
			$row =& $this->items[$i];
			$extension 	= $price[$row['partnumber']] * $row['quantityordered'];
			$salestax	= $SalesTaxRate[$i]*$extension;
			$amount = number_format($TotalAmount,2);
			$SalesTaxArray[$row['partnumber']]	= $SalesTaxRate[$i]*$extension;
			IF($SalesTaxRate[$i] <> 0){
				$Taxable = 'Y';
			}else{
				$Taxable = 'N';
			}
?>									
							<input type="hidden" name="item_name_<?php echo $i  ?>" 			value="<?php echo $row['partnumber']  		?>"/>
						  	<input type="hidden" name="item_description_<?php echo $i?>" 	value="<?php echo $description[$i]  		?>"/>
						  	<input type="hidden" name="item_price_<?php echo $i  ?>" 		value="<?php echo $price[$row['partnumber']]?>"/>
						  	<input type="hidden" name="item_currency_<?php echo $i  ?>" 	value="<?php echo $$mainframe->getUserState( 'ChosenCurrency')  ?>"												/>
						  	<input type="hidden" name="item_quantity_<?php echo $i  ?>" 	value="<?php echo $row['quantityordered']   ?>"/>
<?php
		}
?>						
							<INPUT type='hidden' name='total-tax ' value='<?php echo array_sum($SalesTaxArray)  ?>' />
							<INPUT type='hidden' name='total-charge-amount' 			value='<?php echo $amount  ?>' />
							<!-- Charging a single tax rate in one state -->
						  	<input type="hidden"
						          name="checkout-flow-support.merchant-checkout-flow-support.tax-tables.default-tax-table.tax-rules.default-tax-rule-1.shipping-taxed"
						          value="true"/>
						
						   <input type="hidden"
						          name="tax_rate"
						          value="0.0725"/>
						
						   <input type="hidden"
						         name="tax_us_state"
						         value="TX"/>
						
							<!-- No shipping code -->							
							<input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.continue-shopping-url" value="<?php echo $ReturnURL  ?>"/>
							<input type="hidden" name="checkout-flow-support.merchant-checkout-flow-support.edit-cart-url" value="<?php echo $ReturnURL  ?>"/> 
							<input type="hidden" name="_charset_" />
						
						  	<!-- Button code -->
						  	<input type="image"
						    		 name="Google Checkout"
						    		 alt="Fast checkout through Google"
						    		 src="http://sandbox.google.com/checkout/buttons/checkout.gif?merchant_id=<?php echo strlen(trim($weberp['googlemerchantid']))  ?>&w=180&h=46&style=white&variant=text&loc=en_US" 
						    		 height="46"
						    		 width="180" />
	
						</form>
					</td>
<?php
	}
	
?>					
				</tr>
<?php
}else{
	echo "<tr><td colspan='5'><?php echo JText::_('THERE_ARE_NO_ITEMS_IN_YOUR_CART') ?>.</td></tr>";
	echo "<tr><td><A HREF='index.php?option=com_cartweberp&controller=cartcatalog&donotresetcategory=true'><?php echo JText::_('BACK_TO_CATALOG') ?></A></td></tr>";
}
?>
			</table>
	</fieldset>	
</DIV>
<?php
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
</fieldset>
<center>Powered by <a href="http://joomlamo.com" target="blank">CARTwebERP</a></center>
<?php
}	
?>