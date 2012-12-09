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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT_SITE . DS . 'models' .DS.'helper.php' );


class cartweberpControllerGooglexml extends JController
{
	function __construct( $default = array())
	{    	  
		JRequest::setVar('view', 'googlexml');
      JRequest::setVar('layout', 'default');
		parent::__construct( $default );
	}
	function display()
	{
		// echo "<BR>display - controller<BR>";
		parent::display();
	}
	function createfile(){		
		$dbw 			=& JFactory::getDBO();
		$weberp		= modwebERPHelper::getwebERP();
		$model = $this->getModel('googlexml');
		$GoogleDescription = "Google Sale Prices";
		// $conn = mysql_connect($weberp['host'], $weberp['user'] , $weberp['password']);
		// If(!mysql_select_db($weberp['database'], $conn)){
			// $Locations['error'] = 'Access Denied.  Click Parameters to set up webERP access';
			// return $Locations;
		// }
		If(strlen(trim($weberp['database'])) > 0){
			$dbw = & JDatabase::getInstance( $weberp );
			if(!is_a($dbw, 'JDatabase')){
				$weberp['connected']	= FALSE;	
				$weberp['opendatabase']	= FALSE;	
	      	$Locations['error'] = 'Access Denied.  Click Parameters to set up webERP access';
				return $Locations;
	      }		
	      if(!is_a($dbw, 'JDatabase')){
				$weberp['opendatabase']	= FALSE;	
      	}
      	$n = "\n";   //New line separator to get data correctly listed 
			$gt= "&amp;";  //repace & in the links
			$data = array();
			$count = 0;
			jimport( 'joomla.environment.uri' );
			$uri	=& JFactory::getURI();
			$MyURL = substr($uri->_uri, 0, strpos($uri->_uri, '/admin'));
			$linkprod = $MyURL . DS . "index.php?option=com_cartweberp".$gt."controller=stockinformation".$gt."stockid=";
			$linkpics = $MyURL . DS . $weberp['pathtopics'];
			$sql="SELECT stockmaster.stockid, 
							 stockmaster.description, 
							 stockmaster.longdescription, 
							 stockmaster.units, 
							 stockmaster.actualcost, 
							 stockmaster.kgs,
							 prices.price,  
							 prices.typeabbrev, 
							 stockmaster.pansize, 
							 stockmaster.lastcost, 
							 stockmaster.materialcost
 					  FROM  " . $weberp['database'] . ".stockmaster 
 			  INNER JOIN  " . $weberp['database'] . ".locstock 
 			  			 ON stockmaster.stockid=locstock.stockid 
 			  INNER JOIN  " . $weberp['database'] . ".prices 
 			  			 ON stockmaster.stockid=prices.stockid
 					 WHERE prices.typeabbrev='" . $weberp['listsalestype'] . "'
 				 ORDER BY stockmaster.stockid";
			$result = mysql_query($sql) or die($sql . " Query in getListPrice failed : " . mysql_error());
			while($row = mysql_fetch_array($result))
			{	
				echo $count;
				echo '</br>';
				$Line[$count] = $row;
				$count++;

			}
			$columns='';
			for($i=0;$i < count($Line);$i++){
				//The columns are defined here and later concatenated with the header. 
				//Clear the CRLF details from the longdescription.
				$my_longdescnocrlf = str_replace("\r\n", "", $Line[$i]['longdescription']);
				$columns.='<item>'.$n;
				$columns.='<title>';
				$columns.=$Line[$i]['description'];
				$columns.='</title>'.$n;
				
				$columns.='<link>';
				$columns.= $linkprod.$Line[$i]['stockid'];				
				$columns.='</link>'.$n;
				
				$columns.='<description>';
				$columns.=$my_longdescnocrlf;
				$columns.='</description>'.$n;
				
				$columns.='<g:image_link>';
				$columns.=$linkpics.$Line[$i]['stockid'].'.jpg';
				$columns.='</g:image_link>'.$n;
				
				$columns.='<g:price>';
				$columns.=$Line[$i]['price'];
				$columns.='</g:price>'.$n;
				
				$columns.='<g:brand>';
				$columns.='FuPro';
				$columns.='</g:brand>'.$n;				
				
				$columns.='<g:mpn>';
				$columns.=$Line[$i]['stockid'];
				$columns.='</g:mpn>'.$n;			
				
				$columns.='<g:shipping_weight>';
				$columns.=$Line[$i]['kgs'] . ' pound ';
				$columns.='</g:shipping_weight>'.$n;

				$columns.='<g:product_type>';
				$columns.='Diesel Fuel Filter System';
				$columns.='</g:product_type>'.$n;
				
				$columns.='<g:condition>';
				$columns.='new';
				$columns.='</g:condition>'.$n;
								
				$columns.='<g:id>';
				$columns.=$Line[$i]['stockid'];
				$columns.='</g:id>'.$n;		
						
				$columns.='</item>'.$n;				
			}
			$content='<?xml version="1.0"?><rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'.$n;
			$content.='<channel>'.$n;
			$content.='<title>' . modwebERPHelper::getCompanyName() . '.V0.1.</title>'.$n;
			$content.='<link>' . $MyURL . DS . 'index.php?option=com_cartweberp'.$gt.'view=cartcatalog'.$gt.'Itemid=63</link>'.$n;
			$content.='<description>' . $GoogleDescription . '</description>'.$n;
			$content.=$columns;
			$content.='</channel>'.$n;
			$content.='</rss>'.$n;
			
			echo $content;
			$myFile = JPATH_SITE . DS . 'components' . DS . 'com_cartweberp' . DS. 'google.xml';
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = $content;
			fwrite($fh, $stringData);
			fclose($fh);
			
			echo "Completed";
		}
	}	
}
?>