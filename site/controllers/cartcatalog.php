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
require_once( JPATH_COMPONENT . DS . 'models' .DS.'helper.php' );
// echo "stop22<br>";exit;
jimport( 'joomla.application.component.controller' );
$post	= JRequest::get('request');
global $mainframe;
If(isset($post['token']) OR isset($post['x_response_code']) OR isset($post['financial-order-state']) OR isset($post['ACK'])){
	$mainframe->setUserState( "Authorization",$post);
	header("Location: ". JURI::base().'index.php?option=com_cartweberp&controller=checkout&view=checkout&task=updatecheckout');
	echo 'No Redirect';exit;
}
class cartweberpControllercartcatalog extends JController
{
	function __construct( $default = array())
	{
		parent::__construct( $default );
		JRequest::setVar( 'view', 'cartcatalog' );
		JRequest::setVar( 'layout', 'default'  );
	}
	function display()
	{		
    	global $mainframe, $weberp;    	
		$mainframe = JFactory::getApplication();
    	$weberp	= modwebERPHelper::getweberp();
    	$msg='';
		If($weberp['connected']	= FALSE OR $weberp['opendatabase']	= FALSE){
			$link = 'index.php';
			If($weberp['opendatabase']	= FALSE){
				$msg = JText::_('COULD_NOT_CONNECT_TO_WEBERP_DATABASE') . " " . $weberp['database'];
			}
			if($weberp['connected']	= FALSE) {
				$msg = $msg . JText::_('COULD_NOT_CONNECT_TO_HOST') . " ". $weberp['host'] . " ". JText::_('USER') ." ".  $weberp['user']         ;
			}
        	$this->setRedirect($link, $msg);
		}
    	// check connection 
		$AddToCart = False;
		$post	= JRequest::get('post');
		If(isset($post['DestinationZipCode'])){
			$mainframe->setUserState( "DestinationZipCodeOverride",$post['DestinationZipCode']);
		}
		If(array_key_exists('QuantityToAdd',$post)){			
			$PartsOnPage =& $post['QuantityToAdd'];
			Foreach($PartsOnPage as $Part=>$Quantity){
				If(($Quantity > .01 OR $Quantity < .01) AND $AddToCart==False){
					$AddToCart = True;				
				}
			}
		}
		If($AddToCart==1){			
			$model = $this->getModel('cartcatalog');
			$model->AddPartsToCart($PartsOnPage);
		}	
		parent::display();
	}
	
}	
?>


