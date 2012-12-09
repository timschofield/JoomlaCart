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
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js',false,true);
If(isset($this->customername)){
	$CustomerName = $this->customername;
}else{
	$CustomerName ='';
}
$UserName =& $this->username;  
// echo '<pre>';var_dump($UserName , '<br><br> <b style="color:brown">UserName in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
// echo '<pre>';var_dump($CustomerName , '<br><br> <b style="color:brown">CustomerName in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
$ID = '';
?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_cartweberp&view=usercross');?>" name="adminForm" id="adminForm">

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="" />
		<input type="hidden" name="boxchecked" value="0" />
	</div>	
	<div id="editcell">
		<table class="adminlist" width="50%">
		<thead>
			<tr>
				<th width="5%">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="5%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
				</th>
				<th width="15%" nowrap="nowrap">
					<?php echo JText::_('ID') ?>
			 	</th>		 
				<th width="20%" nowrap="nowrap">
					<?php echo JText::_('USER_ID') ?>
				</th>		 
				<th width="20%" nowrap="nowrap" style="text-align:left">
					<?php echo JText::_('USER') ?>
				</th>
				<th width="15%"  class="title">
					<?php echo JText::_('CUSTOMER') ?>
				</th>
				<th width="30%"  class="title" style="text-align:left">
					<?php echo JText::_('Customer_Name') ?>
				</th>
			</tr>
		</thead>	
		<tfoot>
			<td colspan="9">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tfoot>
	<?php
	$k = 0;
		for ($i=0, $n=count($this->items); $i < $n; $i++)
		{
			$row = &$this->items[$i];
			$ID 					= $row->id;
			$checked 			= JHTML::_('grid.checkedout',   $row, $i );
			
			?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center">
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td align="center">
				<?php echo $checked; ?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
			<td align="center">
				<?php echo $row->user; ?>
			</td>
			<td>
				<?php echo $UserName[$row->user]['name']; ?>
			</td>
			<td align="center">
				<?php echo $row->customer; ?>
			</td>	
			<td>
				<?php echo $CustomerName[$row->customer]['name']; ?>
			</td>			
		</tr>
			<?php
			$k = 1 - $k;
		}
?>
	</table>


	<input type="hidden" name="id" value="<?php echo $ID  ?>" />
	</form>
</div>
