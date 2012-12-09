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

jimport('joomla.application.component.model');

class cartweberpModelcreditcard extends JModel
{
	var $_data = null;

	var $_total = null;

	var $_pagination = null;

	var $_table_prefix = null;


	function __construct()
	{
		parent::__construct();

		global $mainframe, $context,$limitstart,$limit;
		
	  	$this->_table_prefix = '#__cart_';			
	  	$limit		= 10;
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);	
	}
	
	function getDebtorInformation(){
		global $weberp;
		$weberp	= modwebERPHelper::getweberp();
		$user 		=& JFactory::getUser();
		$db = &JFactory::getDBO();
		$this->_table_prefix = '#__cart_';
		$db->setQuery( "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->username . "'" );
	  	$CustomerCross = $db->loadRowList() ;
	  	$CustomerCode = $CustomerCross[0][2];
		$query = "SELECT 	*
						FROM " . $weberp['database'] . ".debtorsmaster,
							  " . $weberp['database'] . ".custbranch
			        WHERE 	debtorsmaster.debtorno ='" . $CustomerCode . "' AND 
			        			custbranch.debtorno = debtorsmaster.debtorno";
		$result = mysql_query($query) or die("$query Query in getDebtorInformation failed : " . mysql_error());
		$row = mysql_fetch_array($result);
		return $row;
	}
	function getCustomer(){		
		global $weberp;
		If(gettype($weberp)<>'ARRAY' OR !array_key_exists('database', $weberp) OR strlen(trim($weberp['database'])) == 0	){
			$weberp	= modwebERPHelper::getweberp();
		}
		$user 		=& JFactory::getUser();
		If(isset($user)){
			$db = &JFactory::getDBO();
			$this->_table_prefix = '#__cart_';
			$db->setQuery( "SELECT * FROM " . $this->_table_prefix . "usercustomer WHERE user = '" . $user->username . "'" );
	  		$CustomerCross = $db->loadRowList() ;
	  		If(array_key_exists(0,$CustomerCross)){
	  			$CustomerCode = $CustomerCross[0][2];
				$query = "SELECT 	name,debtorno,paymentterms,holdreason,creditlimit
								FROM " . $weberp['database'] . ".debtorsmaster
				        	  WHERE 	debtorno ='" . $CustomerCode . "'";
				$result = mysql_query($query) or die("$query Query in getCustomer failed : " . mysql_error());
				If($row = mysql_fetch_array($result)){				
					$Customer['Name']				= $row['name'];		
					$Customer['Terms']			= $row['paymentterms'];
					$Customer['CreditLimit']	= $row['creditlimit'];
					$shrinkingstring = $weberp['allowedterms'];
					$i=0;
					While(strpos($shrinkingstring,",") > 0){
						$AllowedTerms[$i] 	= substr($shrinkingstring,0,strpos($shrinkingstring,","));
						$shrinkingstring 		= substr($shrinkingstring,strpos($shrinkingstring,",")+1);
						$i=$i+1;
					}
					$AllowedTerms[$i] 	= $shrinkingstring;
					$Customer['OpenAccount']=False;
					If(in_array($Customer['Terms'],$AllowedTerms)){ 
						// Check to see if we use credit limits
						$SQL = "SELECT config.confvalue
						          FROM " . $weberp['database'] . ".config
						            WHERE  config.confname = 'CheckCreditLimits'";
						$result2 = mysql_query($SQL) or die("$SQL Query in getCustomer failed : " . mysql_error());
						If($row = mysql_fetch_array($result2)){	
							$CheckCreditLimits	=	$row['confvalue'];
						}	
						If($Customer['CreditLimit'] > .001 and $CheckCreditLimits > .001){
							// find account balance to compare to credit limit  							
							$SQL = "SELECT SUM(debtortrans.ovamount + 
							                   debtortrans.ovgst + 
							                   debtortrans.ovfreight + 
							                   debtortrans.ovdiscount	- 
							                   debtortrans.alloc) AS balance
							              FROM " . $weberp['database'] . ".debtortrans
							            WHERE  debtortrans.debtorno = '" . $CustomerCode . "'";
							$result1 = mysql_query($SQL) or die("$SQL Query in getCustomer failed : " . mysql_error());
							If($row = mysql_fetch_array($result1)){	
								$Customer['Balance']				=	$row['balance'];
								If($Customer['CreditLimit'] 	> $row['balance']){
									$Customer['OpenAccount']	=	True;
								}else{									
									$Customer['Terms']='Balance Exceeds Credit Limit';
								}
							}else{
								// if no records then balance not over limit
								$Customer['Balance']				=	0;
								$Customer['OpenAccount']=True;	
							}					
						}else{
							// no credit limit for this customer so open account is true						
							$Customer['OpenAccount']=True;			
						}												
					}else{					
						$Customer['Terms']='<BR>No Terms<BR><BR>';
					}
				}else{					
					$Customer['Terms']='Customer not found&nbsp;&nbsp;';
				}
			}else{
				$Customer	= array() ;
				$Customer['Terms']='User not associated with Customer';
			}
		}else{
				$Customer	= array() ;
		}
		return $Customer;
	}
	//------------- Function ----------------
   // @Description:    Find out a country's name from the 2 char ISO country code.
   //                    This is what PayPal requires.
   //-------------------------------------
   function getCountryCodeArray() {
   
      $countriesArray = array();
      
      // A
      $countriesArray['AL'] = 'ALBANIA';
      $countriesArray['DZ'] = 'ALGERIA';
      $countriesArray['AS'] = 'AMERICAN SAMOA';
      $countriesArray['AD'] = 'ANDORRA';
      $countriesArray['AI'] = 'ANGUILLA';
      $countriesArray['AG'] = 'ANTIGUA AND BARBUDA';
      $countriesArray['AR'] = 'ARGENTINA';
      $countriesArray['AM'] = 'ARMENIA';
      $countriesArray['AW'] = 'ARUBA';
      $countriesArray['AU'] = 'AUSTRALIA';
      $countriesArray['AT'] = 'AUSTRIA';
      $countriesArray['AZ'] = 'AZERBAIJAN';
      
      // B
      $countriesArray['BS'] = 'BAHAMAS';
      $countriesArray['BH'] = 'BAHRAIN';
      $countriesArray['BD'] = 'BANGLADESH';
      $countriesArray['BB'] = 'BARBADOS';
      $countriesArray['BY'] = 'BELARUS';
      $countriesArray['BE'] = 'BELGIUM';
      $countriesArray['BZ'] = 'BELIZE';
      $countriesArray['BJ'] = 'BENIN';
      $countriesArray['BM'] = 'BERMUDA';
      $countriesArray['BO'] = 'BOLIVIA';
      $countriesArray['BA'] = 'BOSNIA AND HERZEGOVINA';
      $countriesArray['BW'] = 'BOTSWANA';
      $countriesArray['BR'] = 'BRAZIL';
      $countriesArray['VG'] = 'BRITISH VIRGIN ISLANDS';
      $countriesArray['BN'] = 'BRUNEI';
      $countriesArray['BG'] = 'BULGARIA';
      $countriesArray['BF'] = 'BURKINA FASO';
      
      // C
      $countriesArray['KH'] = 'CAMBODIA';
      $countriesArray['CM'] = 'CAMEROON';
      $countriesArray['CA'] = 'CANADA';
      $countriesArray['CV'] = 'CAPE VERDE';
      $countriesArray['KY'] = 'CAYMAN ISLANDS';
      $countriesArray['CL'] = 'CHILE';
      $countriesArray['CN'] = 'CHINA';
      $countriesArray['CO'] = 'COLOMBIA';
      $countriesArray['CK'] = 'COOK ISLANDS';
      $countriesArray['HR'] = 'CROATIA';
      $countriesArray['CY'] = 'CYPRUS';
      $countriesArray['CZ'] = 'CZECH REPUBLIC';
      
      // D
      $countriesArray['DK'] = 'DENMARK';
      $countriesArray['DJ'] = 'DJIBOUTI';
      $countriesArray['DM'] = 'DOMINICA';
      $countriesArray['DO'] = 'DOMINICAN REPUBLIC';
      
      // E
      $countriesArray['TP'] = 'EAST TIMOR';
      $countriesArray['EG'] = 'EGYPT';
      $countriesArray['SV'] = 'EL SALVADOR';
      $countriesArray['EE'] = 'ESTONIA';
      
      // F
      $countriesArray['FJ'] = 'FIJI';
      $countriesArray['FI'] = 'FINLAND';
      $countriesArray['FR'] = 'FRANCE';
      $countriesArray['GF'] = 'FRENCH GUIANA';
      $countriesArray['PF'] = 'FRENCH POLYNESIA';
      
      // G
      $countriesArray['GA'] = 'GABON';
      $countriesArray['GE'] = 'GEORGIA';
      $countriesArray['DE'] = 'GERMANY';
      $countriesArray['GH'] = 'GHANA';
      $countriesArray['GI'] = 'GIBRALTAR';
      $countriesArray['GR'] = 'GREECE';
      $countriesArray['GD'] = 'GRENADA';
      $countriesArray['GP'] = 'GUADELOUPE';
      $countriesArray['GU'] = 'GUAM';
      $countriesArray['GT'] = 'GUATEMALA';
      $countriesArray['GN'] = 'GUINEA';
      $countriesArray['GY'] = 'GUYANA';
      
      // H
      $countriesArray['HT'] = 'HAITI';
      $countriesArray['HN'] = 'HONDURAS';
      $countriesArray['HK'] = 'HONG KONG';
      $countriesArray['HU'] = 'HUNGARY';
      
      // I
      $countriesArray['IS'] = 'ICELAND';
      $countriesArray['IN'] = 'INDIA';
      $countriesArray['ID'] = 'INDONESIA';
      $countriesArray['IE'] = 'IRELAND';
      $countriesArray['IL'] = 'ISRAEL';
      $countriesArray['IT'] = 'ITALY';
      $countriesArray['CI'] = 'IVORY COAST';
      
      // J
      $countriesArray['JM'] = 'JAMAICA';
      $countriesArray['JP'] = 'JAPAN';
      $countriesArray['JO'] = 'JORDAN';
      
      // K
      $countriesArray['KZ'] = 'KAZAKHSTAN';
      $countriesArray['KE'] = 'KENYA';
      $countriesArray['KW'] = 'KUWAIT';
      
      // L
      $countriesArray['LA'] = 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC';
      $countriesArray['LV'] = 'LATVIA';
      $countriesArray['LB'] = 'LEBANON';
      $countriesArray['LS'] = 'LESOTHO';
      $countriesArray['LT'] = 'LITHUANIA';
      $countriesArray['LU'] = 'LUXEMBOURG';
      
      // M
      $countriesArray['MO'] = 'MACAO';
      $countriesArray['MK'] = 'MACEDONIA';
      $countriesArray['MG'] = 'MADAGASCAR';
      $countriesArray['MY'] = 'MALAYSIA';
      $countriesArray['MV'] = 'MALDIVES';
      $countriesArray['ML'] = 'MALI';
      $countriesArray['MT'] = 'MALTA';
      $countriesArray['MH'] = 'MARSHALL ISLANDS';
      $countriesArray['MQ'] = 'MARTINIQUE';
      $countriesArray['MU'] = 'MAURITIUS';
      $countriesArray['MX'] = 'MEXICO';
      $countriesArray['FM'] = 'MICRONESIA, FEDERATED STATES OF';
      $countriesArray['MD'] = 'MOLDOVA';
      $countriesArray['MN'] = 'MONGOLIA';
      $countriesArray['MS'] = 'MONTSERRAT';
      $countriesArray['MA'] = 'MOROCCO';
      $countriesArray['MZ'] = 'MOZAMBIQUE';
      
      // N
      $countriesArray['NA'] = 'NAMIBIA';
      $countriesArray['NP'] = 'NEPAL';
      $countriesArray['NL'] = 'NETHERLANDS';
      $countriesArray['AN'] = 'NETHERLANDS ANTILLES';
      $countriesArray['NZ'] = 'NEW ZEALAND';
      $countriesArray['NI'] = 'NICARAGUA';
      $countriesArray['MP'] = 'NORTHERN MARIANA ISLANDS';
      $countriesArray['NO'] = 'NORWAY';
      
      // O
      $countriesArray['OM'] = 'OMAN';
      
      // P
      $countriesArray['PK'] = 'PAKISTAN';
      $countriesArray['PW'] = 'PALAU';
      $countriesArray['PS'] = 'PALESTINE';
      $countriesArray['PA'] = 'PANAMA';
      $countriesArray['PG'] = 'PAPUA NEW GUINEA';
      $countriesArray['PY'] = 'PARAGUAY';
      $countriesArray['PE'] = 'PERU';
      $countriesArray['PH'] = 'PHILIPPINES, REPUBLIC OF';
      $countriesArray['PL'] = 'POLAND';
      $countriesArray['PT'] = 'PORTUGAL';
      $countriesArray['PR'] = 'PUERTO RICO';
      
      // Q
      $countriesArray['QA'] = 'QATAR';
      
      // R
      $countriesArray['RO'] = 'ROMANIA';
      $countriesArray['RU'] = 'RUSSIAN FEDERATION';
      $countriesArray['RW'] = 'RWANDA';
      
      // S
      $countriesArray['KN'] = 'SAINT KITTS AND NEVIS';
      $countriesArray['LC'] = 'SAINT LUCIA';
      $countriesArray['VC'] = 'SAINT VINCENT AND THE GRENDINES';
      $countriesArray['WS'] = 'SAMOA';
      $countriesArray['SA'] = 'SAUDI ARABIA';
      $countriesArray['CS'] = 'SERBIA AND MONTENEGRO';
      $countriesArray['SC'] = 'SEYCHELLES';
      $countriesArray['SG'] = 'SINGAPORE';
      $countriesArray['SK'] = 'SLOVAKIA';
      $countriesArray['SI'] = 'SLOVENIA';
      $countriesArray['SB'] = 'SOLOMON ISLANDS';
      $countriesArray['ZA'] = 'SOUTH AFRICA';
      $countriesArray['KR'] = 'SOUTH KOREA';
      $countriesArray['ES'] = 'SPAIN';
      $countriesArray['LK'] = 'SRI LANKA';
      $countriesArray['SZ'] = 'SWAZILAND';
      $countriesArray['SE'] = 'SWEDEN';

      $countriesArray['CH'] = 'SWITZERLAND';
      
      // T
      $countriesArray['TW'] = 'TAIWAN';
      $countriesArray['TZ'] = 'TANZANIA, UNITED REPUBLIC OF';
      $countriesArray['TH'] = 'THAILAND';
      $countriesArray['TG'] = 'TOGO';
      $countriesArray['TO'] = 'TONGA';
      $countriesArray['TT'] = 'TRINIDAD AND TOBAGO';
      $countriesArray['TN'] = 'TUNISIA';
      $countriesArray['TR'] = 'TURKEY';
      $countriesArray['TM'] = 'TURKMENISTAN';
      $countriesArray['TC'] = 'TURKS AND CAICOS ISLANDS';
      
      // U
      $countriesArray['UG'] = 'UGANDA';
      $countriesArray['UA'] = 'UKRAINE';
      $countriesArray['AE'] = 'UNITED ARAB EMIRATES';
      $countriesArray['GB'] = 'UNITED KINGDOM';
      $countriesArray['US'] = 'UNITED STATES OF AMERICA';
      $countriesArray['UY'] = 'URUGUAY';
      $countriesArray['UZ'] = 'UZBEKISTAN';
      
      // V
      $countriesArray['VU'] = 'VANUATU';
      $countriesArray['VE'] = 'VENEZUELA';
      $countriesArray['VN'] = 'VIETNAM';
      $countriesArray['VI'] = 'VIRGIN ISLANDS, U.S.';
      
      // W, X, Y, Z
      $countriesArray['YE'] = 'YEMEN ARAB REPUBLIC';
      $countriesArray['ZM'] = 'ZAMBIA';
      
      return $countriesArray;
   }
   function getStateCodeArray() {   
	   $StateArray[" "]  = " ";
		$StateArray["AL"] = "Alabama";
		$StateArray["AK"] = "Alaska";
		$StateArray["AZ"] = "Arizona";
		$StateArray["AR"] = "Arkansas";
		$StateArray["CA"] = "California";
		$StateArray["CO"] = "Colorado";
		$StateArray["CT"] = "Connecticut";
		$StateArray["DE"] = "Delaware";
		$StateArray["DC"] = "District of Columbia";
		$StateArray["FL"] = "Florida";
		$StateArray["GA"] = "Georgia";
		$StateArray["HI"] = "Hawaii";
		$StateArray["ID"] = "Idaho";
		$StateArray["IL"] = "Illinois";
		$StateArray["IN"] = "Indiana";
		$StateArray["IA"] = "Iowa";
		$StateArray["KS"] = "Kansas";
		$StateArray["KY"] = "Kentucky";
		$StateArray["LA"] = "Louisiana";
		$StateArray["ME"] = "Maine";
		$StateArray["MD"] = "Maryland";
		$StateArray["MA"] = "Massachusetts";
		$StateArray["MI"] = "Michigan";
		$StateArray["MN"] = "Minnesota";
		$StateArray["MS"] = "Mississippi";
		$StateArray["MO"] = "Missouri";
		$StateArray["MT"] = "Montana";
		$StateArray["NE"] = "Nebraska";
		$StateArray["NV"] = "Nevada";
		$StateArray["NH"] = "New Hampshire";
		$StateArray["NJ"] = "New Jersey";
		$StateArray["NM"] = "New Mexico";
		$StateArray["NY"] = "New York";
		$StateArray["NC"] = "North Carolina";
		$StateArray["ND"] = "North Dakota";
		$StateArray["OH"] = "Ohio";
		$StateArray["OK"] = "Oklahoma";
		$StateArray["OR"] = "Oregon";
		$StateArray["PA"] = "Pennsylvania";
		$StateArray["RI"] = "Rhode Island";
		$StateArray["SC"] = "South Carolina";
		$StateArray["SD"] = "South Dakota";
		$StateArray["TN"] = "Tennessee";
		$StateArray["TX"] = "Texas";
		$StateArray["UT"] = "Utah";
		$StateArray["VT"] = "Vermont";
		$StateArray["VA"] = "Virginia";
		$StateArray["WA"] = "Washington";
		$StateArray["WV"] = "West Virginia";
		$StateArray["WI"] = "Wisconsin";
		$StateArray["WY"] = "Wyoming";
      return $StateArray;	
   }	
}	
?> 
   
  		
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   