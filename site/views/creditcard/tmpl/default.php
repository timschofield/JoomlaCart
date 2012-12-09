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
$CustomerInformation =& $this->customer;
global $mainframe;
$StateArray =& $this->statecodearray;
$CountryArray =& $this->countrycodearray;
If(!array_key_exists('Name',$CustomerInformation)){
	$CustomerInformation['Name'] = "Click home and login to see your customer name here.";
}
$StateSelected = array();
If($mainframe->getUserState( "GatewayResponse")){
	$Error = $mainframe->getUserState( "GatewayResponse");
	$mainframe->setUserState( "GatewayResponse",null);
	echo "<strong style='color:red;font-size:large'>" . $Error['LongMsg'] . "</strong>";
	$creditcardform =& $mainframe->getUserState( "creditcardform");
	$mainframe->setUserState( "creditcardform",null);
	$FirstName 			= $creditcardform['FirstName'];
	$LastName 			= $creditcardform['LastName'];
	$EMail	 			= $creditcardform['EMail'];
	$Phone	 			= $creditcardform['Phone'];
	$CreditCardType 	= $creditcardform['CreditCardType'];
	$CreditCardNumber	= $creditcardform['CreditCardNumber'];
	$ExpirationMonth 	= $creditcardform['ExpirationMonth'];
	$ExpirationYear 	= $creditcardform['ExpirationYear'];
	$CVVNumber 			= $creditcardform['CVVNumber'];
	$Address1 			= $creditcardform['Address1'];
	$Address2			= $creditcardform['Address2'];
	$City 				= $creditcardform['City'];
	$State 				= $creditcardform['State'];
	$Zip 					= $creditcardform['Zip'];
	$Country 			= $creditcardform['Country'];
	
	Foreach($StateArray as $code=>$StateName){
		If($code == $State){
			$StateSelected[$code] = "SELECTED";
		}else{
			$StateSelected[$code] = "";
		}
	}
	Foreach($CountryArray as $code=>$CountryName){
		If($code == $Country){
			$CountrySelected[$code] = "SELECTED";
		}else{
			$CountrySelected[$code] = "";
		}
	}
}else{
	$FirstName 			= '';
	$LastName 			= '';
	$EMail	 			= '';
	$Phone	 			= '';
	$CreditCardType 	= '';
	$CreditCardNumber	= '';
	$ExpirationMonth 	= '';
	$ExpirationYear 	= '';
	$CVVNumber 			= '';
	$Address1 			= '';
	$Address2			= '';
	$City 				= '';
	$State 				= '';
	$Zip 					= '';
	$Country 			= '';
}
$SelectedVisa			= '';
$SelectedMasterCard	= '';
$SelectedDiscover		= '';
$SelectedAmex			= '';
If(strlen(trim($CreditCardType)) > 0){
	If($CreditCardType == 'Visa'){
		$SelectedVisa			= 'SELECTED';
	}
	If($CreditCardType == 'MasterCard'){
		$SelectedMasterCard	= 'SELECTED';
	}
	If($CreditCardType == 'Discover'){
		$SelectedDiscover		= 'SELECTED';
	}
	If($CreditCardType == 'Amex'){
		$SelectedAmex			= 'SELECTED';
	}

}
?>
<fieldset>
	<legend><?php echo JText::_('CREDIT_CARD_INFORMATION') ?></legend> <BR>
<?php
	// If(isset($CustomerInformation['Name'])){
?>
	Customer: <font Style="color:blue;font-size:18px;"><?php echo $CustomerInformation['Name']?></font>
<?php	
// Month must be padded with leading zero move this code to the controller that gets the data
// $padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
$CartTotal['price']  = $mainframe->getUserState( "Payment_Amount") ;

?>

<form action="index.php" method="post" name="adminForm" >
<TABLE cellpadding="4">
	<TR>
		<TD colspan="2"><?php echo JText::_('CREDIT_CARD_HOLDERS_BILLING_INFORMATION') ?>:</TD> 
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('FIRST_NAME') ?>:</TD> 
		<TD align="left"><input type="text" name="FirstName" id="FirstName" size="60" maxlength="60" value="<?php echo $FirstName ?>" /></TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('LAST_NAME') ?>:</TD> 
		<TD align="left"><input type="text" name="LastName" id="LastName" size="60" maxlength="60" value="<?php echo $LastName ?>" /></TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('EMAIL') ?>:</TD> 
		<TD align="left"><input type="text" name="EMail" id="EMail" size="60" maxlength="160" value="<?php echo $EMail ?>" /></TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('PHONE') ?>:</TD> 
		<TD align="left"><input type="text" name="Phone" id="Phone" size="20" maxlength="20" value="<?php echo $Phone ?>" /></TD>
	</TR>
	<TR>
		<TD align="right">Card Type:</TD> 
		<TD align="left">
			<SELECT name="CreditCardType">
				<OPTION value="Visa"><?php echo JText::_('VISA') ?></OPTION>
				<OPTION value="MasterCard"><?php echo JText::_('MASTER_CARD') ?></OPTION>
				<OPTION value="Discover"><?php echo JText::_('DISCOVER') ?></OPTION>
				<OPTION value="Amex"><?php echo JText::_('AMERICAN_EXPRESS') ?></OPTION>
			</SELECT>
		</TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('CREDIT_CARD_NUMBER') ?>:</TD> 
		<TD align="left"><input type="text" name="CreditCardNumber" id="CreditCardNumber" size="20" maxlength="20"  value="<?php echo $CreditCardNumber ?>"/></TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('EXPIRATION_MONTH') ?>:</TD> 
		<TD align="left"><input type="text" name="ExpirationMonth" id="ExpirationMonth" size="2" maxlength="2" value="<?php echo $ExpirationMonth ?>" /></TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('EXPIRATION_YEAR') ?>:</TD> 
		<TD align="left">
			<input type="text" name="ExpirationYear" id="ExpirationYear" size="4" maxlength="4"  value="<?php echo $ExpirationYear ?>"/> 
			yyyy
		</TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('CVV_NUMBER') ?>:</TD> 
		<TD align="left">
			<TABLE>
				<TR>
					<TD rowspan="2">
						<input type="text" name="CVVNumber" id="CVVNumber" size="4" maxlength="4"  value="<?php echo $CVVNumber ?>"/>
					</TD>	
					<TD><?php echo JText::_('4_DIGITS_ON_FRONT_OF_CARD_AMEX') ?></TD>
				</TR>
				<TR>
					<TD> <?php echo JText::_('3_DIGITS_ON_THE_BACK_FOR_ALL_OTHERS') ?></TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('ADDRESS') ?> 1:</TD> 
		<TD align="left"><input type="text" name="Address1" id="Address1" size="40" maxlength="60"  value="<?php echo $Address1 ?>"/></TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('ADDRESS') ?> 2:</TD> 
		<TD align="left"><input type="text" name="Address2" id="Address2" size="40" maxlength="60"  value="<?php echo $Address2 ?>"/></TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('CITY') ?>:</TD> 
		<TD align="left"><input type="text" name="City" id="City" size="40" maxlength="60" value="<?php echo $City ?>" /></TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('STATE') ?>:</TD> 
		<TD align="left">
			<select name="State">
<?php
Foreach($StateArray as $code=>$StateName){
	$SelectedState = '';
	if(array_key_exists($code, $StateSelected)){
		$SelectedState = $StateSelected[$code];
	}
?>
				<option value=<?php echo $code  ?> <?php echo $SelectedState ?>> <?php echo $StateName ?></option>
<?php
}
?>			

			</select>

		</TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('ZIP') ?>:</TD> 
		<TD align="left"><input type="text" name="Zip" id="Zip" size="15" maxlength="15" value="<?php echo $Zip ?>" /></TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('COUNTRY') ?>:</TD> 
		<TD align="left">
			<select name="Country">
<?php

Foreach($CountryArray as $code=>$CountryName){
	If(isset($CountrySelected[$code])){
		$SelectedCountry = $CountrySelected[$code];	
	}elseif($code == 'US'){
		$SelectedCountry = 'SELECTED';
	}else{
		$SelectedCountry = '';
	}
?>	
					<option value=<?php echo $code  ?> <?php echo $SelectedCountry ?>> <?php echo $CountryName ?></option>
<?php
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php echo JText::_('CURRENCY') ?>:</td>
		<td>
			<input type="hidden" name="CurrencyID" id="Country" value="USD" />
		</TD>
	</TR>
	<TR>
		<TD align="right"><?php echo JText::_('PAYMENT_AMOUNT') ?>:</TD> 
		<TD align="left"><?php echo number_format($CartTotal['price'],2)?>
			<input type="hidden" name="Amount" id="Amount" value="<?php echo $CartTotal['price']?>" />
		</TD>
		
	</tr>
<?php
// }else{
?>
<!--	<TR>
		<TD align="right">Error</TD> 
		<TD align="left">
			There is no webERP customer associated with this user.
		</TD>
		
	</tr>-->

<?php
// }
?>	
	<TR>
		<TD><CENTER><button name="Search" onClick="this.form.submit()" class="button"><?php echo JText::_( 'CONTINUE_CHECK_OUT' );?></button></CENTER></TD>
		<TD align="right">
			<A HREF='index.php?option=com_cartweberp&controller=cartcatalog'><?php echo JText::_('BACK_TO_CATALOG') ?></A>
		</TD>
	</tr>	

</TABLE>
<center>Powered by <a href="http://joomlamo.com" target="blank">CARTwebERP</a></center>
<input type="hidden" name="controller" value="checkout" />
<input type="hidden" name="view" value="checkout" />
<input type="hidden" name="checkouttype" value="CreditCard" />
<input type="hidden" name="task" value="expresscheckout" />
</form>

</fieldset>