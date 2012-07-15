<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011  <>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

$balPath = PATH_tslib . "../../../../boiteauxlettres/";

require_once($balPath . "myPhpLib.php");

require_once($balPath . "debug.php");
require_once($balPath . "config.php");
require_once($balPath . "logger.php");

require_once($balPath . "transferableModel.php");
require_once($balPath . "dbConnection.php");

require_once($balPath . "persistentObject.php");

//iHerbarium\Logger::$logDirSetting = "";
iHerbarium\Debug::init("iHerbariumAddObservationPlugin", False);
//iHerbarium\Config::init("Development");
//iHerbarium\Config::init("Production");


require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_tslib.'../../../../bibliotheque/common_functions.php');

/**
 * Plugin 'participants_list' for the 'competition' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_competition
 */
class tx_competition_pi1 extends tslib_pibase {
  var $prefixId      = 'tx_competition_pi1';		// Same as class name
  var $scriptRelPath = 'pi1/class.tx_competition_pi1.php';	// Path to this script relative to the extension dir.
  var $extKey        = 'competition';	// The extension key.
	
  private $periodIdParameterName = 'periodId';

  private function getPeriodIdParameter() {
    $periodId = NULL; // Default

    if(isset($_GET[$this->periodIdParameterName]))
      $periodId = desamorcer($_GET[$this->periodIdParameterName]);

    return $periodId;
  } 

  private function displayPeriod($period) {

    // PERIOD DISPLAY
    $lines = array();	  

    $lines[] = '<div id="bloc_contenu">';
    $lines[] = '<h1>' . $period->competition_name . '</h1>';
    $lines[] = '<div id="bloc_contenu_texte">';
    
    $lines[] = '<h2>' . $period->comment . '</h2>';
    $lines[] = '<div>Id: ' . $period->uid . '</div>';
    $lines[] = '<div>Beginning: ' . date("d.m.Y", $period->beginning_timestamp) . '</div>';
    $lines[] = '<div>End      : ' . date("d.m.Y", $period->end_timestamp) . '</div>';    
	  
    $lines[] = '</div>';
    $lines[] = '</div>';	  
	  
    return implode("\n", $lines);
  }


  private function displayPeriodInList($period) {

    // LINK
    $linkTarget = $GLOBALS['TSFE']->id;
	  
    $linkParameters = 
      array($this->periodIdParameterName => $period->uid);

    $link = $this->pi_getPageLink($linkTarget, '', $linkParameters);


    // PERIOD DISPLAY
    $lines = array();	  


    $lines[] = '<div id="bloc_contenu">';
    $lines[] = '<h1>' . $period->competition_name . '</h1>';
    $lines[] = '<div id="bloc_contenu_texte">';
    $lines[] = '<h2>' . $period->comment . '</h2>';
    $lines[] = '<div>Beginning: ' . date("d.m.Y", $period->beginning_timestamp) . '</div>';
    $lines[] = '<div>End      : ' . date("d.m.Y", $period->end_timestamp) . '</div>';    
    $lines[] = '<a id="resultsLink' . $period->uid . '" href="' . $link . '">Results</a>';
    $lines[] = '<script>$("#resultsLink' . $period->uid . '").button();</script>';

    
    $lines[] = '</div>';  
    $lines[] = '</div>';
	  
    return implode("\n", $lines);
  }

  private function displayPeriodsList($periods) {
    $lines = array();
	  
    $lines[] = '<div><ul id="periodsList">';
	  
    $periodsLines = array_map(array($this, 'displayPeriodInList'), $periods);
    $lines = array_merge($lines, $periodsLines);
	  
    $lines[] = '</ul></div>';
	  
    return implode("\n", $lines);
  }

  private function displayParticipantInList($participant) {
    
    // PARTICIPANT DISPLAY
    $lines = array();	  
	  
    $lines[] = '<li class="participant">';
	  
    $lines[] = '<span>' . $participant->obscount . '</span>';
    $lines[] = '<span>observations</span>';
    $lines[] = '<span>' . $participant->username . ' (uid=' . $participant->uid . ')</span>';
    
    $lines[] = '</li>';
	  	  
    return implode("\n", $lines);
  }

  private function displayParticipantsList($participants) {
    $lines = array();
	  
    $lines[] = '<div id="bloc_contenu">';
    $lines[] = '<h1>Participants\' Results:</h1>';
    $lines[] = '<div id="bloc_contenu_texte">';    
	  
    $participantsLines = array_map(array($this, 'displayParticipantInList'), $participants);
    $lines = array_merge($lines, $participantsLines);
	  
    $lines[] = '</div>';
    $lines[] = '</div>';
	  
    return implode("\n", $lines);
  }


  /**
   * The main method of the PlugIn
   *
   * @param	string		$content: The PlugIn content
   * @param	array		$conf: The PlugIn configuration
   * @return	The content that is displayed on the website
   */
  function main($content, $conf) {
    $this->conf = $conf;
    $this->pi_setPiVarDefaults();
    $this->pi_loadLL();
    $this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

    // jQuery.
    $content .= file_get_contents('typo3conf/ext/iherbarium_observations/pi4/scripts.html');

    // My PID
    $pid = $GLOBALS['TSFE']->id;  

    $periodId = $this->getPeriodIdParameter();
		
    // Database connection.
    $dbName = iHerbarium\Config::get("pluginsTypoherbariumDatabase");
    $db = iHerbarium\dbConnection::get($dbName);

    if($periodId == NULL) {
      // Display list of periods.

      $periodsQuery =
	" SELECT uid, pid, competition_name, beginning_timestamp, end_timestamp, comment" .
	" FROM tx_competition_periode" .
	" WHERE pid = " . $db->quote($pid) .
	" ORDER BY beginning_timestamp DESC";

      $result = $db->query($periodsQuery);
		  
      $periods = array();
      while( ($row = $result->fetchRow()) ) {
	// For each period.
	$period = $row;
	$periods[] = $period;
      }
		  
      $content .= $this->displayPeriodsList($periods);

    } else {
      // Display one period.
		  
      $periodsQuery =
	" SELECT uid, pid, competition_name, beginning_timestamp, end_timestamp, comment" .
	" FROM tx_competition_periode" .
	" WHERE uid = " . $db->quote($periodId);;

      $result = $db->query($periodsQuery);
		  
      // Did we get an answer?
      if( ($row = $result->fetchRow()) ) {
	// We fetched the Period!
	$period = $row;
		  
	// Display Period
	$content .= $this->displayPeriod($period);
		    
	// Fetch participants.
	$participantsQuery =
	  " SELECT User.uid AS uid, User.name AS userName, COUNT(*) AS obsCount, Period.beginning_timestamp AS periodBeginning, Period.end_timestamp AS periodEnd, UNIX_TIMESTAMP(Obs.deposit_timestamp) AS deposit " .
	  " FROM tx_competition_periode AS Period, iherba_observations AS Obs, fe_users AS User" .
	  " WHERE Period.uid = " . $db->quote($periodId) .
	  " AND User.uid = Obs.id_user" .
	  " AND UNIX_TIMESTAMP(Obs.deposit_timestamp) BETWEEN Period.beginning_timestamp AND Period.end_timestamp" .
	  " GROUP BY Obs.id_user" .
	  " ORDER BY obsCount DESC";

	$participantsResult = $db->query($participantsQuery);
	
	$participants = array();
	while( ($row = $participantsResult->fetchRow()) ) {
	  // For each row.
	  $participant = $row;
	  $participants[] = $participant;
	  //$content .= '<pre>' . var_export($row, True) . '</pre>';
	}
	
	$content .= $this->displayParticipantsList($participants);


	// LINK BACK

	$linkTarget = $GLOBALS['TSFE']->id;
	
	$linkParameters = 
	  array();

	$link = $this->pi_getPageLink($linkTarget, '', $linkParameters);

	$content .= '<a id="backLink" href="' . $link . '">BACK</a>';
	$content .= '<script>$("#backLink").button();</script>';


      }
		  
    }
    	
    return $this->pi_wrapInBaseClass($content);
  }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/competition/pi1/class.tx_competition_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/competition/pi1/class.tx_competition_pi1.php']);
}

?>