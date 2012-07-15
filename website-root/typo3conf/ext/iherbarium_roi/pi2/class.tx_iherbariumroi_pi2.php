<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010  <>
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

$balPath = PATH_tslib . "../../../../boiteauxlettres/";

require_once($balPath . "myPhpLib.php");

require_once($balPath . "debug.php");
require_once($balPath . "config.php");
require_once($balPath . "logger.php");

require_once($balPath . "transferableModel.php");
require_once($balPath . "typoherbariumModel.php");
require_once($balPath . "dbConnection.php");

require_once($balPath . "persistentObject.php");

//iHerbarium\Logger::$logDirSetting = "";
iHerbarium\Debug::init("iHerbariumROIplugin", False);


require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_tslib.'../../../../bibliotheque/common_functions.php');

/**
 * Plugin 'définir les tags d'une images' for the 'iherbarium_roi' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbariumroi
 */
class tx_iherbariumroi_pi2 extends tslib_pibase {
  var $prefixId      = 'tx_iherbariumroi_pi2';		// Same as class name
  var $scriptRelPath = 'pi2/class.tx_iherbariumroi_pi2.php';	// Path to this script relative to the extension dir.
  var $extKey        = 'iherbarium_roi';	// The extension key.

  private function redirect($link) {
    header('Location: ' . $link);
  }
	
  private function optionsValuesForm($optionsValues) {
    $lines = array();
		
    // Beginning
    $lines[] = '<div id="form" style="display:none">';
    $lines[] = '<form>';
		
    // Inputs
    $once = True;
    foreach($optionsValues as $option => $value) {
      if($once) {
	// Do it once.
	$lines[] = '<input name="type" value="' . $value . '" checked="checked" type="radio">';
	$once = False;
      }
      else {
	// Already did.
	$lines[] = '<input name="type" value="' . $value . '" type="radio">';
      }
			
      $lines[] = '<span>' . $option . '</span><br>';
    }
     
    // Ok Delete Cancel
    $lines[] = '<div class="ok">Ok</div>';
    $lines[] = '<a class="delete">Delete</a>';
    $lines[] = '<a class="cancel">Cancel</a>';

    // End
    $lines[] = '</form>';
    $lines[] = '</div>';
		
    $content = implode("\n", $lines);
    return $content;
  }
	
  private function variablesValuesForm($variablesValues) {
    $lines = array();
		
    // Beginning
    $lines[] = '<div id="variablesDiv" style="display:none">';
    $lines[] = '<form id="variablesForm">';
		
    // Fields for passage of variables
    foreach($variablesValues as $variable => $value) {
      $lines[] = '<input name="' . $variable . '" value="' . $value . '" type="text">';
    }

    // End
    $lines[] = '</form>';
    $lines[] = '</div>';
		
    $content = implode("\n", $lines);
    return $content;
  }
	
  private function areaRectanglesForm() {
    $lines = array();
	
    $link = $this->pi_getPageLink(30, '', array("submitted"  => True));
		
    // Beginning
    $lines[] = '<div id="rectanglesDiv" style="display:none">';
    $lines[] = '<form id="rectanglesForm" method="post" action="' . $link . '">';
		
    // Field with rectangles
    $lines[] = '<input id="rectangles" name="rectangles" value="" type="hidden">';

    // Options
    $action = "replace";
    if(isset($_GET["action"])) {
      $action = $_GET["action"];
    }
      
    $lines[] = '<input id="action" name="action" value="' . $action . '" type="hidden">';

    // End
    $lines[] = '</form>';
    $lines[] = '</div>';
		
    $content = implode("\n", $lines);
    return $content;
  }
	
  private function imageDiv($imagePath, $width, $height) {
    $lines = array();
		
    $lines[] = '<div id="global" style="position:relative;border:2px solid red;float:left">';
    $lines[] = '<img src="' . $imagePath . '" width="' . $width . '" height="' . $height . '" />';
    $lines[] = '<div id="annotation" style="width:' . $width . 'px; height:' . $height . 'px; position:absolute; top:0; left:0;"></div>';
    $lines[] = '</div>';
    $lines[] = '<div id="send_button">Send</div>';
		
    $content = implode("\n", $lines);
    return $content;
  }
	
  /**
   * The main method of the PlugIn
   *
   * @param	string		$content: The PlugIn content
   * @param	array		$conf: The PlugIn configuration
   * @return	The content that is displayed on the website
   */
  function main($content,$conf)	{
    $this->conf=$conf;
    $this->pi_setPiVarDefaults();
    $this->pi_loadLL();
    $this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
	
    if($_GET['submitted'] == True) {
      $content .= "<h1>SUBMITTED!</h1>";
			
      $uid = $GLOBALS['TSFE']->fe_user->user['uid'];
      //$content .= "<h2>UID : $uid</h2>";

      // Options
      $action = "replace";
      if(isset($_POST["action"])) {
	$action = $_POST["action"];
      }
			
      assert(isset($_POST['rectangles']));
      //$content .= "POST: <pre>" . var_export($_POST, True) . "</pre>";
      $jsonRectangles = preg_replace('/\\\\"/', '"', $_POST['rectangles']); // Aargh! Apparently POST request passes JSON code with \ before each "
      $rectangles = json_decode($jsonRectangles);
			
      //$content .= "Rectangles: <pre>" . var_export($rectangles, True) . "</pre>";
      
      $photoId    = $rectangles->photoId;
      $areaWidth  = $rectangles->areaWidth;
      $areaHeight = $rectangles->areaHeight;

      // Connect to database.
      debug("Debug", "pi2", "Connecting");
      $localTypoherbarium = iHerbarium\LocalTypoherbariumDB::get();

      // Load Photo.
      $photo = $localTypoherbarium->loadPhoto($photoId);
      //$content .= $photo->__toString();
      
      if($action == "replace") {
	// Delete all Photo's ROIs.
	$localTypoherbarium->deleteROIsByPhoto($photoId);
      }

      // Prepare new ROIs from rectangles.
      $rois =
	array_map( function($rectangle) use ($photo, $areaWidth, $areaHeight, &$content) {
	    // Extract ROI's tag.
	    $roiTag = $rectangle->value;

	    // Convert our rectangle to ROI Rectangle.
	    $roiRectangle =&
	    iHerbarium\ROIRectangle::fromStdObjRectangleAndArea($rectangle, $areaWidth, $areaHeight);
	    
	    // Get original Photo's rotation.
	    $rotationAngle = $photo->rotationAngle();
	    
	    // Rotate the Rectangle to match the original Photo's rotation.
	    $roiRectangle->rotate(360 - $rotationAngle);

	    // Prepare and fill ROI.
	    $roi = new iHerbarium\TypoherbariumROI();
	    $roi->rectangle = $roiRectangle;
	    $roi->tag       = $roiTag;

	    //$content .= $roi->__toString();
	    return $roi;
	  },
	  $rectangles->rectangles
	  );      

      // Add new ROIs to Photo.
      iHerbarium\array_iter( 
			    function($roi) 
			    use ($localTypoherbarium, $uid, $photoId, $photo, &$content) {
			      $localTypoherbarium->addROIToPhoto($roi, $roi->tag, $photoId, $uid);
			    },
			    
			    $rois);
      
      
      // Notify the Determination Protocol
      $p = iHerbarium\DeterminationProtocol::getProtocol("Standard");
      $reObs = $localTypoherbarium->loadObservation($photo->obsId);
      $p->modifiedObservation($reObs);
    

      // Add link back.
      $link = $this->pi_getPageLink(29, '', array("numero_observation"  => $photo->obsId));
      $content .= "<a href='" . $link . "'>BACK</a>";

      // Redirect.
      $this->redirect($link);
      
      return $this->pi_wrapInBaseClass($content);
    }
    else {

      $lang = $GLOBALS['TSFE']->config["config"]["language"];

      $numero_observation=$_GET['numero_observation'];
      $identifiant_photo=$_GET['identifiant_photo'];

      // Connect to database (dirty).
      debug("Debug", "pi2", "Connecting");
      $dbName = iHerbarium\Config::get("observationReceiverTypoherbariumDatabase");
      $db = iHerbarium\dbConnection::get($dbName);

      // Cleanup
      $obsId = $numero_observation;
      $photoId = $identifiant_photo;
      
      // Load the Observation and the Photo.
      $local = iHerbarium\LocalTypoherbariumDB::get();
      $obs = $local->loadObservation($obsId);
      $photo = $obs->photos[$photoId];
	       
      // Image and size manipulations
      $photoPath = $photo->fileVersions["big"]->path();
      $photoUrl  = $photo->fileVersions["big"]->url();

      $dim = getimagesize($photoPath);
      $src_w = $dim[0];
      $src_h = $dim[1];

      $maxSize = 512;
      $dst_w_and_dst_h = iHerbarium\ImageManipulator::shrink($maxSize, $src_w, $src_h);
      $dst_w = $dst_w_and_dst_h[0];
      $dst_h = $dst_w_and_dst_h[1];

      // Image Div
      $content .= $this->imageDiv($photoUrl, $dst_w, $dst_h);
		
      // Script
      $content .= file_get_contents('typo3conf/ext/iherbarium_roi/pi2/zone_tool_script.html');
      
      $tagsQuery = 
	"SELECT iherba_tags.id_tag AS tagId, iherba_tags_translation.texte AS tagText" .
	" FROM iherba_tags, iherba_tags_translation" .
	" WHERE iherba_tags.id_tag = iherba_tags_translation.id_tag" .
	" AND iherba_tags.id_genre = " . $db->quote($obs->kind) .
	" AND iherba_tags_translation.id_langue = " . $db->quote($lang);

      $result = $db->query($tagsQuery);
      assert($result != NULL);
      
      $optionsValues = array();
      while( ($row = $result->fetchRow()) ) {
	$optionsValues[$row->tagtext] = $row->tagid;
      }
      
      // OptionsValues
      $content .= $this->optionsValuesForm($optionsValues);
	
      // Variables for JavaScript
      $content .= $this->variablesValuesForm(
					     array(
						   "photoId"    => $photoId,
						   "areaWidth"  => $dst_w,
						   "areaHeight" => $dst_h
						   )
					     );
		
      // Form to send
      $content .= $this->areaRectanglesForm();
		
      return $this->pi_wrapInBaseClass($content);
		
    }
  	
  }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_roi/pi2/class.tx_iherbariumroi_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_roi/pi2/class.tx_iherbariumroi_pi2.php']);
}
?>
