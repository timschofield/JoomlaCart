<?php 
/**
* @package CARTwebERP
* @Joomla version 1.5
* @copyright Copyright (C) 2010 Mo Kelly. All rights reserved.
   
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
// echo '<pre>';var_dump($this->items , '<br><br> <b style="color:brown">this->items in ' . $_SERVER["SCRIPT_NAME"] . '    Line #' . __LINE__ . '  </b><br><br>');echo '</pre>';
?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" >
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th>
				<?php echo JText::_( 'FILE_LINES' ); ?>
			</th>			
		</tr>
	</thead>	
<?php
	$k = 0;
		foreach($this->items as $count=>$data)
		{			
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $data; ?>
				</td>				
			</tr>
<?php
		}
?>
	</table>
</div>
<input type="hidden" name="controller" value="googlexml" />
<input type="hidden" name="task" value="createfile" />
<input type="submit" name="Create" value="Create New File" />
</form>
