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

require_once(PATH_tslib.'class.tslib_pibase.php');

require_once(PATH_tslib.'../../../../bibliotheque/common_functions.php');

$balPath = PATH_tslib . "../../../../boiteauxlettres/";

require_once($balPath . "myPhpLib.php");

require_once($balPath . "debug.php");
require_once($balPath . "config.php");
iHerbarium\Debug::init("iHerbariumGroupsPlugin", false);

require_once($balPath . "transferableModel.php");
require_once($balPath . "persistentObject.php");


/**
 * Plugin 'Groups' for the 'iherbarium_groups' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbariumgroups
 */
class tx_iherbariumgroups_pi1 extends tslib_pibase {
  var $prefixId      = 'tx_iherbariumgroups_pi1';		// Same as class name
  var $scriptRelPath = 'pi1/class.tx_iherbariumgroups_pi1.php';	// Path to this script relative to the extension dir.
  var $extKey        = 'iherbarium_groups';	// The extension key.
  var $pi_checkCHash = true;
  
  private function getLang() { return $this->LLkey; }

  private function showGroups() {

    // Fetch Groups.
    $local = iHerbarium\LocalTypoherbariumDB::get();      
    $groups = $local->loadGroups();

    if($this->myDebug)
      $content = "<h3>Groups:</h3><pre>" . var_export($groups, true). "</pre>";

    $skin = new iHerbarium\TypoherbariumSkin( $this->getLang() );
      
    // COMMANDS

    // Text
    $selectGroupText                              = $this->pi_getLL('selectGroup', 'selectGroup', True);
    $selectActionText                             = $this->pi_getLL('selectAction', 'selectAction', True);
    $showGroupButtonText                          = $this->pi_getLL('showGroupButton', 'showGroupButton', True);
    $regenerateGroupQuestionsButtonText           = $this->pi_getLL('regenerateGroupQuestionsButton', 'regenerateGroupQuestionsButton', True);
    $regenerateAreYouSureText                     = $this->pi_getLL('regenerateAreYouSure', 'regenerateAreYouSure', True);
    $recomputeAsIfQuestionsFinishedButtonText     = $this->pi_getLL('recomputeAsIfQuestionsFinishedButton', 'recomputeAsIfQuestionsFinishedButton', True);
    $recomputeAsIfQuestionsFinishedAreYouSureText = $this->pi_getLL('recomputeAsIfQuestionsFinishedAreYouSure', 'recomputeAsIfQuestionsFinishedAreYouSure', True);
    
    // Group Form Beginning
    $groupActionLink = $this->pi_getPageLink(76, '');
    $lines[] = "<form id='GroupsForm' method='post' action='" . $groupActionLink . "'>";

    // Select Group

    $groupsSelect = 
      iHerbarium\viewArrayAsSelect(
				   "GroupId",
				   iHerbarium\extractObjectFieldFunction('id'),
				   function($group) use ($skin) { return $group->id . " : ". $skin->group($group)->name; },
				   $groups
				   );
    
    $lines[] = "<div>" . $selectGroupText . "<br/>" . $groupsSelect . "</div>";

    // Show Button
    $lines[] = "<div>" . $selectActionText . "<br/><button type='submit' id='ShowGroupButton' name='GroupAction' value='ShowGroup'>" . $showGroupButtonText . "</button></div>";

    // Regenerate Button
    $lines[] = "<button type='submit' id='RegenerateGroupQuestionsButton' name='GroupAction' value='RegenerateGroupQuestions'>" . $regenerateGroupQuestionsButtonText . "</button>";
    $lines[] = "<script>$('#RegenerateGroupQuestionsButton').submit(function(e) { var answer = confirm('" . $regenerateAreYouSureText . "'); return answer; })</script>";

    $lines[] = "<br/>";

    // Schedule Recompute As If Questions Finished Button
    $lines[] = "<button type='submit' id='RecomputeAsIfQuestionsFinishedButton' name='GroupAction' value='RecomputeAsIfQuestionsFinished'>" . $recomputeAsIfQuestionsFinishedButtonText . "</button>";
    $lines[] = "<script>$('#RecomputeAsIfQuestionsFinishedButton').submit(function(e) { var answer = confirm('" . $recomputeAsIfQuestionsFinishedAreYouSureText . "'); return answer; })</script>";

    // Group Form End
    $lines[] = "</form>";

    $content .= implode("\n", $lines);
    return $content;
  }

  private function showGroupDetails(iHerbarium\TypoherbariumGroup $group, $groupTranslations, $lang = "fr") {

    // Prepare translations.
    $skin = new iHerbarium\TypoherbariumSkin( $this->getLang() );
    $groupTranslation = $skin->group($group);

    // Show Group details.
    $lines[] = "<div>";
    $lines[] = "<h2>" . $this->pi_getLL('group', 'group', True) . " " . $group->id . "</h2>";

    $lines[] = "<p>" . $this->pi_getLL('name', 'name', True) . " : " . $groupTranslation->name . "</p>";
    
    if($groupTranslation->description) 
      $lines[] = "<p>" . $this->pi_getLL('description', 'description', True) . " : " . $groupTranslation->description . "</p>";
    
    $lines[] = "</div>";

    $content .= implode("\n", $lines);
    return $content;
  }

  private function showGroupObservations(iHerbarium\TypoherbariumGroup $group) {

    // Workaround...
    $thisPlugin = $this;

    // Show each Observation.
    $lines =
      array_map(
		function($obs) use ($thisPlugin) {
		  return affiche_une_observation_dans_liste($thisPlugin, $obs->id, "public");
		},
		$group->getAllObservations()
		);

    $content .= implode("\n", $lines);
    return $content;

  }

  private function showBackToShowGroups() {

    // Back button.
    $backToShowGroupsButtonText = $this->pi_getLL('backToShowGroupsButton', 'backToShowGroupsButton', True);

    $backToShowGroupsFormLink = $this->pi_getPageLink($GLOBALS['TSFE']->id);
    $lines[] = "<form id='BackToShowGroupsForm' method='post' action='" . $backToShowGroupsFormLink . "'>";
    $lines[] = "<div><button id='BackToShowGroupsButton' name='GroupAction' type='submit' value='ShowGroups' >" . $backToShowGroupsButtonText . "</button></div>";
    $lines[] = "</form>";
    
    $content .= implode("\n", $lines);
    return $content;      
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
    
    // Prepare my debug.
    $uid = $GLOBALS['TSFE']->fe_user->user['uid'];

    if($uid == 33) 
      $this->myDebug = true;
    else
      $this->myDebug = false;
   
    // Little debug...
    if($this->myDebug) {
      
      $content .= "<div style='border: 1px solid; margin: 10px; padding: 10px;'>";
      $content .= "<h1>Groups plugin says: 'Hello world!'</h1>";
      $content .= "<h3>POST data:</h3><pre>" . var_export($_POST, True). "</pre>";
      $content .= "<h3>LLkey : " . $this->LLkey . "</h3>";
      $content .= "</div>";

    }

    // Extract requested Action.
    $actionRequested = "None";
    if(isset($_POST["GroupAction"])) {
      $actionRequested = $_POST["GroupAction"];
    }

    // Choose the Action, extract and check required parameters.
    switch($actionRequested) {
      
    case "ShowGroups" :
      $action = $actionRequested;
      break;
      
    case "ShowGroup" :
    case "RegenerateGroupQuestions" :
    case "RecomputeAsIfQuestionsFinished" :
      if(isset($_POST["GroupId"])) {
	$action = $actionRequested;
	$groupId = $_POST["GroupId"];
      } else {
	$action = "None";
      }
      break;

    default:
      $action = "ShowGroups";
      break;
    }

    if($this->myDebug) {      
      $content .= "<h3>Action : " . $action . "</h3>";
    }

    // Action!
    if($action == "ShowGroups") {

      /*
      // Database.
      $local = iHerbarium\LocalTypoherbariumDB::get();      
      $groups = $local->loadGroups();
      $content .= "<h3>Groups:</h3><pre>" . var_export($groups, true). "</pre>";
      */

      $content .= $this->showGroups();

    } else if ($action == "ShowGroup") {

      $lines[] = $this->showBackToShowGroups() . "<br/>";

      // Database.
      $local = iHerbarium\LocalTypoherbariumDB::get();      

      // Group.
      $group = $local->loadGroup($groupId);
      $lines[] = $this->showGroupDetails($group);
      $lines[] = $this->showGroupObservations($group);

      $content .= implode("\n", $lines);
      
    } else if ($action == "RegenerateGroupQuestions") {

      $lines[] = $this->showBackToShowGroups() . "<br/>";

      // Database
      $local = iHerbarium\LocalTypoherbariumDB::get();      
      
      // Determination Protocol
      $protocol = iHerbarium\DeterminationProtocol::getProtocol();

      // Group.
      $group = $local->loadGroup($groupId);
      
      $lines[] = $this->showGroupDetails($group);
      
      // Regenerate questions for each Observation.
      $obsIds =
	array_map(
		  function($obs) use ($protocol) {
		    $protocol->modifiedObservation($obs);
		  },
		  $group->getAllObservations()
		  );
      
      $lines[] = "<p>Question Tasks for this Group have been (re)generated!</p>";
      
      $content .= implode("\n", $lines);

    } else if ($action == "RecomputeAsIfQuestionsFinished") {

      $lines[] = $this->showBackToShowGroups() . "<br/>";

      // Database
      $local = iHerbarium\LocalTypoherbariumDB::get();      
      
      // Determination Protocol
      $protocol = iHerbarium\DeterminationProtocol::getProtocol();

      // Group.
      $group = $local->loadGroup($groupId);
      
      $lines[] = $this->showGroupDetails($group);
      
      // Notify arificially all Observations in this Group that all their's Questions have been answered.
      $obsIds =
	array_map(
		  function($obs) use ($protocol) {
		    $protocol->noMoreQuestions($obs);
		  },
		  $group->getAllObservations()
		  );
      
      $lines[] = "<p>All Observations in this Group have been artificially notified that all their Questions have just been answered!</p>";
      
      $content .= implode("\n", $lines);

    } 

    return $this->pi_wrapInBaseClass($content);

  }
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_groups/pi1/class.tx_iherbariumgroups_pi1.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_groups/pi1/class.tx_iherbariumgroups_pi1.php']);
}

?>
