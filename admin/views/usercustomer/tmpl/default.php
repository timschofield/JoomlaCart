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
If(count($this->users) > 40){
	$columns = 3;
	$Width = 'width="25%"';
}elseif(count($this->users) > 20){
	$columns = 2;
	$Width = 'width="33%"';
}else{
	$Width = 'width="50%"';
	$columns = 1;
}
?>

<Fieldset>
	<legend><?php echo JText::_('CHOOSE_USER_AND_CORRESPONDING_CUSTOMER')  ?> </legend>
	<DIV>
		<form action="<?php echo JRoute::_('index.php?option=com_cartweberp&view=usercustomer');?>" method="post" name="adminForm" id="adminForm" >
			<TABLE width="50%">
				<tr>
					<TD <?php echo $Width  ?> align="right" nowrap><H3><U><?php echo JText::_('USERS')  ?></U></H3></TD>
					<td>
						<select name="User">
<?php
// echo '<pre>';var_dump($this->users , '<br><br> <b style="color:brown">this->users in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
Foreach($this->users as $ID=>$NameObject){
	$NameArray = (array) $NameObject;
	echo '<pre>';var_dump($NameArray , '<br><br> <b style="color:brown">NameArray in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
?>
							<option value="<?php echo $NameArray['id']  ?>"><?php echo $NameArray["name"] ?></option>
<?php
}
?>
						</select>
					</td>
				</tr>
				<tr>
					<TD <?php echo $Width  ?> align="right" nowrap><H3><U><?php echo JText::_('CUSTOMERS')  ?></U></H3></TD>
					<td>
						<select name="Customer">
<?php
Foreach($this->customers as $DebtorNo=>$CustomerName){
	// var_dump($CustomerName);echo "<br><br>CustomerName<BR><BR><BR>";exit;
?>
							<option value="<?php echo $CustomerName['debtorno']  ?>"><?php echo $CustomerName['name']  ?></option>
<?php
}
?>
						</select>
					</td>
				</tr>									
				<tr>
					<TD <?php echo $Width  ?> align="right" nowrap><H3><U><?php echo JText::_('BRANCHES')  ?></U></H3></TD>
					<td>
						<select name="Branch">
<?php
Foreach($this->branches as $BranchCode=>$BranchName){
?>
							<option value="<?php echo $BranchCode  ?>"><?php echo $BranchName['brname']  ?></option>
<?php
}
?>
						</select>
					</td>
				</tr>
			</table>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="controller" value="" />
		</form>
	</div>
</fieldset>