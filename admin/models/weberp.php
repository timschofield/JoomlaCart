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
 *  along with this program.  if not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class modCARTwebERPHelper extends JModel {
	static function getwebERP() {
		$Option = JRequest::getVar('option');
		$params = JComponentHelper::getParams($Option);
		$weberp['host'] = $params->get('host');
		$weberp['database'] = $params->get('database');
		$weberp['user'] = $params->get('databaseuser');
		$weberp['password'] = $params->get('userpassword');
		$weberp['driver'] = 'mysql'; // Database driver name

		return $weberp;
	}
	function getStock() {
		$db = JFactory::getDBO();
		$weberp = modCARTwebERPHelper::getwebERP();
		$query = "SELECT 	stockid,description
			 		 	FROM " . $weberp['database'] . ".stockmaster
			 	     ORDER BY stockid";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		while ($row = mysql_fetch_array($result)) {
			$StockID = $row['stockid'];
			$Stock[$StockID] = $row['description'];
		}
		return $Stock;
	}
	function getPartDescription() {
		$db = JFactory::getDBO();
		$weberp = modCARTwebERPHelper::getwebERP();
		$post = JRequest::get('request');
		if (isset($post['part'])) {
			$partnumber = $post['part'];
			$query = "SELECT 	description
			 					 FROM " . $weberp['database'] . ".stockmaster
			 	    			WHERE stockid = '" . $partnumber . "'";
			$db->setQuery($query);
			$PartDescription = $db->loadResult();
			if (strlen(trim($PartDescription)) == 0) {
				$PartDescription = "Invalid";
			}
		} else {
			$PartDescription = 'Invalid';
		}
		return $PartDescription;
	}
	function getCustomers() {
		$db = JFactory::getDBO();
		$weberp = modCARTwebERPHelper::getwebERP();
		$db->setQuery("SELECT name FROM " . $weberp['database'] . ".debtorsmaster");
		$Customer = $weberpdb->loadAssocList('debtorno');
		return $Customer;
	}
	function setErrorMessage($errorMessage) {
		$app = JFactory::getApplication();
		// echo gettype($errorMessage)  . "=gettype(errorMessage)<br>";
		if (gettype($errorMessage) <> 'array') {
			$app->enqueueMessage(JText::_($errorMessage));
			echo $errorMessage . "=errorMessage<br>";
		}
		Foreach ($errorMessage as $count => $message) {
			$app->enqueueMessage(JText::_($message));
		}
		return;
	}
	function getResult($query, $database, $errorMessage, $publicMessage = 1) {
		global $weberp;
		$UserType = JFactory::getUser()->groups[8];
		$error = array();
		if ($database == 'Joomla') {
			$dbj = JFactory::getDBO();
			$dbj->setQuery($query);
			if (!$Value = $dbj->loadResult()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbj->getErrorMsg());
					array_push($error, '<br>' . $query);
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Result Error<br><span style='color:brown'><hr></span>");
					}
				}
				if ($UserType == 8 OR strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Result Error<br><span style='color:brown'><hr></span>");
					}
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return $Value;
			}
		} elseif ($database == 'webERP') {
			if (gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)) {
				$weberp = modCARTwebERPHelper::getwebERP();
			}
			$dbw = JFactory::getDBO();
			$dbw = JDatabase::getInstance($weberp);
			if (isset($dbw->message) AND substr($dbw->message, 0, 17) == 'Unable to connect') {
				array_push($error, '<br>Error Code-H' . __LINE__ . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters<br><span style="color:brown"><hr></span>');
				array_push($error, $dbw->message);
				modCARTwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			if (!$Value = $dbw->loadResult()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Result Error<br><span style='color:brown'><hr></span>");
					}
				}
				if ($UserType == 8 OR strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage . '<br>');
					}
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return $Value;
			}
		}
	}
	function getRowArray($query, $database, $errorMessage, $index = NULL, $publicMessage = 1) {
		global $weberp;
		$UserType = JFactory::getUser()->groups[8];
		$error = array();
		if ($database == 'Joomla') {
			$dbj = JFactory::getDBO();
			$dbj->setQuery($query);
			if (!$RowArray = $dbj->loadAssoc()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbj->getErrorMsg());
					array_push($error, '<br>' . $query);
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Row Array Error<br><span style='color:brown'><hr></span>");
					}
				}
				if ($UserType == 8 OR strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Row Array Error<br><span style='color:brown'><hr></span>");
					}
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return $RowArray;
			}
		} elseif ($database == 'webERP') {
			if (gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)) {
				$weberp = modCARTwebERPHelper::getwebERP();
			}
			$dbw = JFactory::getDBO();
			$dbw =& JDatabase::getInstance($weberp);
			if (isset($dbw->message) AND substr($dbw->message, 0, 17) == 'Unable to connect') {
				array_push($error, $errorMessage);
				array_push($error, '<br>Error Code-H' . __LINE__ . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');
				array_push($error, '<br>' . $dbw->message . '<br>');
				modCARTwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			if (!$RowArray = $dbw->loadAssoc()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Row Array Error<br><span style='color:brown'><hr></span>");
					}
				}
				if ($UserType == 8 OR strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Row Array Error<br><span style='color:brown'><hr></span>");
					}
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return $RowArray;
			}
		}
	}
	function getCollumnArray($query, $database, $errorMessage, $publicMessage = 1) {
		global $weberp;
		$UserType = JFactory::getUser()->groups[8];
		$error = array();
		if ($database == 'Joomla') {
			$dbj = JFactory::getDBO();
			$dbj->setQuery($query);
			if (!$CollumnArray = $dbj->loadResultArray()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbj->getErrorMsg());
					array_push($error, '<br>' . $query);
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Collumn Array Error<br><span style='color:brown'><hr></span>");
					}
				}
				if ($UserType == 8 OR strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Collumn Array Error<br><span style='color:brown'><hr></span>");
					}
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return $CollumnArray;
			}
		} elseif ($database == 'webERP') {
			if (gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)) {
				$weberp = modCARTwebERPHelper::getwebERP();
			}
			$dbw = JFactory::getDBO();
			$dbw =& JDatabase::getInstance($weberp);
			if (isset($dbw->message) AND substr($dbw->message, 0, 17) == 'Unable to connect') {
				array_push($error, '<br>Error Code-H' . __LINE__ . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');
				array_push($error, '<br>' . $dbw->message . '<br>');
				modCARTwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			if (!$CollumnArray = $dbw->loadResultArray()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Collumn Array Error<br><span style='color:brown'><hr></span>");
					}
				}
				if ($UserType == 8 OR strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Collumn Array Error<br><span style='color:brown'><hr></span>");
					}
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return $CollumnArray;
			}
		}
	}
	static function getRowList($query, $database, $errorMessage, $index = NULL, $publicMessage = 1) {
		global $weberp;

		$UserType = JFactory::getUser()->groups[8];
		$error = array();
		if ($database == 'Joomla') {
			$dbj = JFactory::getDBO();
			$dbj->setQuery($query);
			if (!$RowArray = $dbj->loadAssocList($index)) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbj->getErrorMsg());
					array_push($error, '<br>' . $query);
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Row List Error<br><span style='color:brown'><hr></span>");
					}
				}
				if (strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage . '<br>');
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Row List Error<br><span style='color:brown'><hr></span>");
					}
					// echo gettype($error)  . "=gettype(error)<br>";
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return $RowArray;
			}
		} elseif ($database == 'webERP') {
			// echo '<pre>';var_dump($weberp , '<br><br> <b style="color:brown">weberp   389</b><br><br>');echo '</pre>';
			// echo gettype($weberp)  . "=gettypeweberp<br>";
			// echo $weberp["database"]  . "=weberp['database']<br>";
			if (gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)) {
				$weberp = modCARTwebERPHelper::getwebERP();
			}
			$dbw = JFactory::getDBO();
			if (isset($dbw->message) AND substr($dbw->message, 0, 17) == 'Unable to connect') {
				array_push($error, '<br>Error Code-H' . __LINE__ . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');
				array_push($error, $dbw->message);
				modCARTwebERPHelper::setErrorMessage($error);
				if ($publicMessage == 0) {
					array_push($error, '<br>' . $errorMessage . '<br>');
				}
				return FALSE;
			}
			$dbw = JDatabase::getInstance($weberp);
			// echo '<pre>';var_dump($weberp , '<br><br> <b style="color:brown">weberp   398</b><br><br>');echo '</pre>';
			if (isset($dbw->message) AND substr($dbw->message, 0, 17) == 'Unable to connect') {
				array_push($error, '<br>Error Code-H' . __LINE__ . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');
				array_push($error, $dbw->message);
				modCARTwebERPHelper::setErrorMessage($error);
				if ($publicMessage == 0) {
					array_push($error, '<b1128r>' . $errorMessage . '<br>');
				}
				return FALSE;
			}
			$dbw->setQuery($query);
			// echo $publicMessage  . "=publicMessage m382<br>";
			if (!$RowArray = $dbw->loadAssocList($index)) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Get Row List Error<br><span style='color:brown'><hr></span>");
					}
				}
				if ($UserType == 8 OR strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, '<br>Error Code-H' . __LINE__ . ' Get Row List Error<br><span style="color:brown"><hr></span>');
					}
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return $RowArray;
			}
		}
	}
	function getInsertUpdate($query, $database, $errorMessage, $publicMessage = 1) {
		global $weberp;
		$UserType = JFactory::getUser()->groups[8];
		$error = array();
		if ($database == 'Joomla') {
			$dbj = JFactory::getDBO();
			$dbj->setQuery($query);
			if (!$Result = $dbj->query()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbj->getErrorMsg());
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
					array_push($error, '<br>' . $query);
				}
				if (strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return TRUE;
			}
		} elseif ($database == 'webERP') {
			if (gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)) {
				$weberp = modCARTwebERPHelper::getwebERP();
			}
			$dbw = JFactory::getDBO();
			$dbw =& JDatabase::getInstance($weberp);
			if (isset($dbw->message) AND substr($dbw->message, 0, 17) == 'Unable to connect') {
				if ($publicMessage == 0) {
					array_push($error, '<br>' . $errorMessage);
					array_push($error, "Error Code-H" . __LINE__ . " Insert/Update Error<br><span style='color:brown'><hr></span>");
				}
				array_push($error, 'Error Code-H' . __LINE__ . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');
				array_push($error, $dbw->message);
				modCARTwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			if (!$Result = $dbw->query()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
				}
				if (strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
					}
					array_push($error, "Insert/Update Error");
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}
	function getDelete($query, $database, $errorMessage, $publicMessage = 1) {
		global $weberp;
		$UserType = JFactory::getUser()->groups[8];
		$error = array();
		if ($database == 'Joomla') {
			$dbj = JFactory::getDBO();
			$dbj->setQuery($query);
			if (!$Result = $dbj->query()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbj->getErrorMsg());
					if ($publicMessage == 0) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
					array_push($error, '<br>' . $query);
				}
				if (strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
						array_push($error, "<br>Error Code-H" . __LINE__ . " Insert/Update Error<br><span style='color:brown'><hr></span>");
					}
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return TRUE;
			}
		} elseif ($database == 'webERP') {
			if (gettype($weberp) <> 'array' OR !array_key_exists('database', $weberp)) {
				$weberp = modCARTwebERPHelper::getwebERP();
			}
			$dbw = JFactory::getDBO();
			$dbw =& JDatabase::getInstance($weberp);
			if (isset($dbw->message) AND substr($dbw->message, 0, 17) == 'Unable to connect') {
				if ($publicMessage == 0) {
					array_push($error, '<br>' . $errorMessage);
					array_push($error, "Error Code-H" . __LINE__ . " Insert/Update Error<br><span style='color:brown'><hr></span>");
				}
				array_push($error, 'Error Code-H' . __LINE__ . ' Trying to connect to webERP database.  Check Host, Username, and Password in CARTwebERP parameters');
				array_push($error, $dbw->message);
				modCARTwebERPHelper::setErrorMessage($error);
				return FALSE;
			}
			$dbw->setQuery($query);
			if (!$Result = $dbw->query()) {
				if ($UserType == 8) {
					array_push($error, '<br>' . $dbw->getErrorMsg());
					array_push($error, '<br>' . $query);
				}
				if (strlen(trim($errorMessage)) > 0) {
					if ($publicMessage == 1) {
						array_push($error, '<br>' . $errorMessage);
					}
					array_push($error, "Insert/Update Error");
					modCARTwebERPHelper::setErrorMessage($error);
				}
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}
}
?>