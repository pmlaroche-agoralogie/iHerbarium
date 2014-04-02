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

require_once($balPath . "typoherbariumModel.php");
require_once($balPath . "persistentObject.php");

require_once($balPath . "protocolMessage.php");
require_once($balPath . "protocolMessageConsumer.php");
require_once($balPath . "mailFormFactory.php");
require_once($balPath . "contentTemplate.php");

require_once($balPath . "determinationProtocol.php");

//iHerbarium\Logger::$logDirSetting = "";
iHerbarium\Debug::init("iHerbariumAddObservationPlugin", False);


require_once(PATH_tslib.'class.tslib_pibase.php');
//require_once(PATH_tslib.'../../../../bibliotheque/programme.php');

/**
 * Plugin 'dépot d'une observation' for the 'iherbarium_observations' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbariumobservations
 */



	
class tx_iherbariumobservations_pi4 extends tslib_pibase {
  var $prefixId      = 'tx_iherbariumobservations_pi4';		// Same as class name
  var $scriptRelPath = 'pi4/class.tx_iherbariumobservations_pi4.php';	// Path to this script relative to the extension dir.
  var $extKey        = 'iherbarium_observations';	// The extension key.

  private $anonymousUid = 150;

  private $actionsPageIds =
    array(
	  "addFiles"     => "obsaddfiles",
	  "create"       => "obscreate",
	  "createNoUser" => "obscreate",
	  "edit"         => "obsedit",
	  "show"         => "obsshow",
	  "save"         => "obssave",
	  "delete"       => "obssave"
	  );

  private function makeLink($action, $obsId = NULL, $options = array()) {
    $linkTarget = $this->actionsPageIds[$action];
    
    $linkParameters = 
      array("obsAction" => $action);
    
    // obsId
    if($obsId)
      $linkParameters["obsId"] = $obsId;

    // addFiles
    if(isset($options["addFiles"]))
      $linkParameters["addFiles"] = $options["addFiles"];
    
    // nextAction
    if(isset($options["nextAction"]))
      $linkParameters["nextAction"] = $options["nextAction"];

    $link = $this->pi_getPageLink($linkTarget, '', $linkParameters);

    return $link;
  }

  private function redirect($link) {
    header('Location: ' . $link);
  }

  private function ll($key, $alt = NULL, $hsc = True) {
    if($alt == NULL)
      $alt = 'LL(' . $key . ')';

    return $this->pi_getLL($key, $alt , $hsc);
  }

  private function generateToken() {
    return "token_" . time() . "_" . rand();
  }

  private function checkToken($token) {
    return
      ($token != NULL &&
       is_string($token) &&
       preg_match('/^token_\d+_\d+$/', $token) == 1);
  }

  private function fileuploaddiv() {
    $lines = array();

    $lines[] = '<div id="fileupload">';
    $lines[] = '   <form action="typo3conf/ext/iherbarium_observations/pi4/upload.php" method="POST" enctype="multipart/form-data">';
    $lines[] = '      <div class="fileupload-buttonbar">';
    $lines[] = '         <label class="fileinput-button">';
    $lines[] = '            <span>' . $this->ll('addFilesAddButton') . '</span>';
    $lines[] = '            <input type="file" name="files[]" multiple>';
    $lines[] = '         </label>';
    $lines[] = '         <button type="button" class="done">' . $this->ll('addFilesDoneButton') . '</button>';    
    $lines[] = '      </div>';
    $lines[] = '   </form>';
    $lines[] = '   <div class="fileupload-content">';
    $lines[] = '       <table class="files"></table>';
    $lines[] = '       <div class="fileupload-progressbar"></div>';
    $lines[] = '   </div>';
    $lines[] = '</div>';

    /*
      <!--
      <button type="submit" class="start">Start upload</button>
      <button type="reset" class="cancel">Cancel upload</button>
      <button type="button" class="delete">Delete files</button>
      -->
    */

    $content = implode("\n", $lines);
    return $content;
  }

  private function viewAddFiles(iHerbarium\TypoherbariumObservation $obs, $nextAction) {
    
    $lines = array();
  
    // Files adding tool.
		
    // Div
    $lines[] = $this->fileuploaddiv();
	
    // Config
    $lines[] = file_get_contents('typo3conf/ext/iherbarium_observations/pi4/config_script.html');
    
    // Scripts
    $lines[] = file_get_contents('typo3conf/ext/iherbarium_observations/pi4/fileupload_scripts.html');


    // Form

    // Action Link

    if(! $nextAction)
      $nextAction = "create";
    
    switch($nextAction) {
    case "create" :
      $link = $this->makeLink("create", NULL, array("addFiles" => True));
      break;

    case "edit" :
      $link = $this->makeLink("save", $obs->id, array("addFiles" => True, "nextAction" => 'edit'));
      break;

    case "show" :
    case "save" :
      $link = $this->makeLink("save", $obs->id, array("addFiles" => True, "nextAction" => 'show'));
      break;

    }		

    // Beginning
    $lines[] = '<div id="addFilesFormDiv">';
    $lines[] = '<form id="addFilesForm" method="post" action="' . $link . '">';

    // Add Files Token (CREATE NEW)
    $lines[] = '<input id="addFilesToken" name="addFilesToken" value="' . $this->generateToken() . '" type="hidden">';
    
    // Submit
    //$lines[] = '<input id="addFilesSubmit" name="addFilesSubmit" type="submit">';
		
    // End
    $lines[] = '</form>';
    $lines[] = '</div>';
		
    $content = implode("\n", $lines);
    return $content;
  }

  private function viewObservationShow(iHerbarium\TypoherbariumObservation $obs) {
    $lines = array();

    // Beginning
    $lines[] = '<div id="obsShowDiv">';

    // Title
    $lines[] = '<h3>Observation' . ($obs->id != NULL ? (' ' . $obs->id) : '') . '</h3>';

    // Edit
    
    $editLink = $this->makeLink("edit", $obs->id);
    $lines[] = '<div><a href="' . $editLink . '">EDIT</a></div>';
    
    // Delete

    $deleteLink = $this->makeLink("delete", $obs->id, array("nextAction" => 'show'));
    $lines[] = '<div><a href="' . $deleteLink . '">DELETE</a></div>';


    // Photos
    $lines[] =
      iHerbarium\mkString(
			  array_map(
				    function($photo) { 
				      return 
					(
					 '<img src="' .
					 repertoire_vignettes . $photo->localFilename .
					 '" />'
					 );
				    }, 
				    $obs->photos),
			  "<div><span>", "</span><span>", "</span></div>"
			  );
    		

    // Map
    $lines[] = $this->viewGeolocShowMap($obs);

    // End
    $lines[] = '</div>';

    $content = implode("\n", $lines);


    // Old Show Observation redirection.
    
    $linkTarget = "detail";
    $linkParameters = array("numero_observation" => $obs->id);
    $oldShowLink = $this->pi_getPageLink($linkTarget, '', $linkParameters);
    
    $this->redirect($oldShowLink);

    return $content;
  }

  private function viewObservationsShowList() {
    $linkTarget = "15";
    $linkParameters = array();
    $oldListLink = $this->pi_getPageLink($linkTarget, '', $linkParameters);

    $this->redirect($oldListLink);
  }

  private function viewCommentaryEdit(iHerbarium\TypoherbariumObservation $obs) {
    $lines = array();

    $lines[] = '<div style="margin-top: 10px">';
    $lines[] = '<h2>' . $this->ll('commentaryLabel') . '</h2>';
    $lines[] = '<fieldset>';
    $lines[] = '<legend>' . $this->ll('commentaryInput','',1) . '</legend>';
    $lines[] = '<div style="padding: 10px;"><textarea style="width: 100%;" name="obsCommentary" id="com" value="" cols="30" rows="2"/>' . $obs->commentary . '</textarea></div>';
    $lines[] = '</fieldset>';
    $lines[] = '</div>';

    return implode("\n", $lines);
  }
  
  
 private function viewAddressEdit(iHerbarium\TypoherbariumObservation $obs) {
    $lines = array();

    $lines[] = '<div style="margin-top: 10px">';
    $lines[] = '<h2>' . $this->ll('addressLabel') . '</h2>';
    $lines[] = '<fieldset>';
    $lines[] = '<legend>' . $this->ll('addressInput','',1) . '</legend>';
    $lines[] = '<div style="padding: 10px;"><textarea style="width: 100%;" name="obsAddress" id="com" value="" cols="30" rows="2"/>' . $obs->address . '</textarea></div>';
    $lines[] = '</fieldset>';
    $lines[] = '</div>';

    return implode("\n", $lines);
  }
  
  private function viewPrivacyEdit(iHerbarium\TypoherbariumObservation $obs) {
    $lines = array();

    $lines[] = '<div style="margin-top: 10px;">';
    $lines[] = '<h2>' . $this->ll('privacyLabel') . '</h2>';
    $lines[] = '<fieldset id="obsPrivacy">';
    $lines[] = '<legend>' . $this->ll('privacyInput') . '</legend>';
    $lines[] = '<div style="overflow : hidden;" id="obsPrivacyRadio">' ;
    $lines[] = '<div>';
    $lines[] = '<input id="obsPrivacyPublic"  name="obsPrivacy" value="public" type="radio" ' . ($obs->privacy == "public" ? 'checked="checked"' : '') . ' />';
    $lines[] = '<label style="display: inline; float: none;" for="obsPrivacyPublic">' . $this->ll('privacyOptionPublic') . '</label>';
    $lines[] = '</div>';
    $lines[] = '<div>';
    $lines[] = '<input id="obsPrivacyPrivate" name="obsPrivacy" value="private" type="radio" ' . ($obs->privacy == "private" ? 'checked="checked"' : '') . ' />';
    $lines[] = '<label style="display: inline; float: none;" for="obsPrivacyPrivate" style="white-space: nowrap;">' . $this->ll('privacyOptionPrivate') . '</label>';
    $lines[] = '</div>';
    $lines[] = '</div>';
    $lines[] = '</fieldset>';
    //      $lines[] = '<script>$("#obsPrivacyPublic").button();</script>';
    //      $lines[] = '<script>$("#obsPrivacyPrivate").button();</script>';
    $lines[] = '</div>';      

    return implode("\n", $lines);
  }

  private function viewGeolocShowMap(iHerbarium\TypoherbariumObservation $obs) {
    $lines = array();

    $lines[] = '<div style="margin-top: 10px;">';

    $lines[] = '<h2>' . $this->ll('geolocalisationInput') . '</h2>';
      
    $lines[] = '<fieldset>';
    $lines[] = '<legend>' . $this->ll('geolocalisationShow') . '</legend>';
    $lines[] = file_get_contents('typo3conf/ext/iherbarium_observations/pi4/map_script.html');
    $lines[] = '<div id="mapCanvas" style="width:95%; height:400px; border: 4px solid green; margin: 4px; overflow: hidden;" />';
    $lines[] = '</fieldset>';
      
    // Latitude and Longitude (PASS ON)    
    $lines[] = '<input id="obsGeolocLatitude" name="obsGeolocLatitude" value="' . $obs->geolocation->latitude . '" type="hidden" />';
    $lines[] = '<input id="obsGeolocLongitude" name="obsGeolocLongitude" value="' . $obs->geolocation->longitude . '" type="hidden" />';

    $lines[] = '</div>';

    return implode("\n", $lines);
  }

  private function viewPhotosToAddRawShow($photosToAddRaw) {
    $lines = array();
    
    $lines[] = '<div id="uploadedPhotosDiv">';      
    $lines[] = '<h2>' . $this->ll("uploadedPhotosLabel") . '</h2>';
      
    $lines[] = '<div>';
    
    $photosLines = 
      array_map(
		function($photoToAddRaw) {
		  return (
			  '<a href="' . $photoToAddRaw['source'] . '">' .
			  '<img src="' . $photoToAddRaw['thumbnail'] . '"/>' .
			  '</a>'
			  );
		},
		$photosToAddRaw
		);

    $lines[] = 
      iHerbarium\mkString(
			  $photosLines, 
			  "<div>" . $this->ll('uploadedPhotosShow') . "</div><span>", "</span> <span>", "</span>\n");
    
    $lines[] = '</div>';
    $lines[] = '</div>';

    return implode("\n", $lines);
  }

  private function viewObservationCreate(iHerbarium\TypoherbariumObservation $obs, 
					 $addFilesToken = NULL,
					 $photosToAddRaw = NULL) {

    $lines = array();

    // Form

    // Action Link

    $saveLink = $this->makeLink("save", $obs->id, array(
							"addFiles"  => ($addFilesToken ? True : False), 
							"nextAction" => 'show'
							));

    // Beginning
    $lines[] = '<div id="obsFormDiv">';
    $lines[] = '<form id="obsForm" method="post" action="' . $saveLink . '">';

    // Show uploaded photos.
    if($photosToAddRaw)
      $lines[] = $this->viewPhotosToAddRawShow($photosToAddRaw);
    
    // Add Files Token (PASS ON)
    if($addFilesToken)
      $lines[] = '<input id="addFilesToken" name="addFilesToken" value="' . $addFilesToken . '" type="hidden">';

    // Commentary (EDIT)
    $lines[] = $this->viewCommentaryEdit($obs);
    $lines[] = $this->viewAddressEdit($obs);
    
    // Submit
    $lines[] = '<div style="margin: 30px;">';
    $lines[] = '<input id="obsSubmit" name="obsSubmit" type="submit"></input>';
    $lines[] = '<script>$("#obsSubmit").button({label : "' . $this->ll('createSubmit') . '"});</script>';
    $lines[] = '</div>';
    
    // Geoloc
    if($obs->geolocation && $obs->geolocation->isKnown()) {
     
      // Privacy (EDIT)
      $lines[] = $this->viewPrivacyEdit($obs);
      
      // Map (SHOW)
      $lines[] = $this->viewGeolocShowMap($obs);      
    }
    
    // End
    $lines[] = '</form>';
    $lines[] = '</div>';
		
    $content = implode("\n", $lines);
    return $content;
  }


  private function viewObservationCreateNoUser(iHerbarium\TypoherbariumObservation $obs, 
					       $addFilesToken = NULL,
					       $photosToAddRaw = NULL) {

    $lines = array();

    // Form

    // Action Link

    $saveLink = $this->makeLink("save", $obs->id, array(
							"addFiles"  => ($addFilesToken ? True : False), 
							"nextAction" => 'show'
							));

    // Beginning
    $lines[] = '<div id="obsFormDiv">';
    $lines[] = '<form id="obsForm" method="post" action="' . $saveLink . '">';

    // Show uploaded photos.
    if($photosToAddRaw)
      $lines[] = $this->viewPhotosToAddRawShow($photosToAddRaw);
    
    // Add Files Token (PASS ON)
    if($addFilesToken)
      $lines[] = '<input id="addFilesToken" name="addFilesToken" value="' . $addFilesToken . '" type="hidden">';

    // Commentary (EDIT)
    $lines[] = $this->viewCommentaryEdit($obs);
    $lines[] = $this->viewAddressEdit($obs);

    // E-mail
    $lines[] = '<div style="margin-top: 10px">';
    $lines[] = '<h2>' . $this->ll('noUserEmailLabel') . '</h2>';
    $lines[] = '<fieldset>';
    $lines[] = '<legend>' . $this->ll('noUserEmailInput','',1) . '</legend>';
    $lines[] = '<div style="padding: 10px;"><input type="text" name="noUserEmail" id="noUserEmail" value=""/></input></div>';
    $lines[] = '</fieldset>';
    $lines[] = '</div>';
        
    // Submit
    $lines[] = '<div style="margin: 10px;">';
    $lines[] = '<input id="obsSubmit" name="obsSubmit" type="submit"></input>';
    $lines[] = '<script>$("#obsSubmit").button({label : "' . $this->ll('createSubmit') . '"});</script>';
    $lines[] = '</div>';
    
    // End
    $lines[] = '</form>';
    $lines[] = '</div>';
		
    $content = implode("\n", $lines);
    return $content;
  }
	
  private function viewObservationEdit(iHerbarium\TypoherbariumObservation $obs, 
				   $addFilesToken = NULL,
				   $photosToAddRaw = NULL) {

    $lines = array();

    // Photos

    $addFilesLink = $this->makeLink("addFiles", $obs->id, array("nextAction" => 'show'));

    $lines[] = '<div>';

    $lines[] = '<h2>Photos</h2>';

    $lines[] = '<div id="photosDiv">';

    // Photos
    $lines[] =
      iHerbarium\mkString(
			  array_map(
				    function($photo) { 
				      return 
					(
					 '<img src="' .
					 // TODO -> take it away!
					 './medias/vignettes/' . $photo->localFilename .
					 // <- TODO
					 '" />'
					 );
				    }, 
				    $obs->photos),
			  "<span>", "</span><span>", "</span>"
			  );

    $lines[] = '</div>';

    $lines[] = '<div style="margin-top: 10px;">';
    $lines[] = '<form id="addFilesLinkForm" method="post" action="' . $addFilesLink . '"><input id="addFilesLink" type="submit"></input></form>';
    $lines[] = '<script>$("#addFilesLink").button({label : "' . $this->ll('editAddPhotosButton') . '"});</script>';
    $lines[] = '</div>';

    $lines[] = '</div>';

    // Form

    // Action Link

    $editLink = $this->makeLink("save", $obs->id, array(
							"nextAction" => 'show'
							));

    // Beginning
    $lines[] = '<div id="obsFormDiv">';
    $lines[] = '<form id="obsForm" method="post" action="' . $editLink . '">';

    // Show uploaded photos.
    if($photosToAddRaw)
      $lines[] = $this->viewPhotosToAddRawShow($photosToAddRaw);
    
    // Add Files Token (PASS ON)
    if($addFilesToken)
      $lines[] = '<input id="addFilesToken" name="addFilesToken" value="' . $addFilesToken . '" type="hidden">';

    // Commentary (EDIT)
    $lines[] = $this->viewCommentaryEdit($obs);
    $lines[] = $this->viewAddressEdit($obs);
        
    // Geoloc
    if($obs->geolocation && $obs->geolocation->isKnown()) {
     
      // Privacy (EDIT)
      $lines[] = $this->viewPrivacyEdit($obs);
      
      // Map (SHOW)
      $lines[] = $this->viewGeolocShowMap($obs);      
    }    
    
    // Submit
    $lines[] = '<div style="margin-top: 10px;">';
    $lines[] = '<input id="obsSubmit" name="obsSubmit" type="submit"></input>';
    $lines[] = '<script>$("#obsSubmit").button({label : "' . $this->ll('editSubmit') . '"});</script>';
    $lines[] = '</div>';

    // End
    $lines[] = '</form>';
    $lines[] = '</div>';

    
    // Delete Link

    /*    
    $deleteLink = $this->makeLink("delete", $obs->id, array("nextAction" => 'show'));
    $lines[] = '<div style="margin-top: 10px;">';
    $lines[] = '<form method="post" action="' . $deleteLink . '">';
    $lines[] = '<input id="obsDelete" name="obsDelete" value="obsDelete" type="submit"></input>';
    $lines[] = '<script>$("#obsDelete").button({label : "' . $this->ll('editDelete') . '"});</script>';
    $lines[] = '</form>';
    $lines[] = '</div>';
    */

    $content = implode("\n", $lines);
    return $content;
  }


  private function viewObservationSave(iHerbarium\TypoherbariumObservation $obs, $nextAction) {
    $lines = array();

    $lines[] = "<h1>" . $this->ll('saveObservationSavedWithId') . $obs->id . "</h1>";

    // Form

    $link = $this->makeLink($nextAction, $obs->id);
    
    // Beginning
    $lines[] = '<div id="obsFormDiv">';
    $lines[] = '<form id="obsForm" method="post" action="' . $link . '">';
    
    // Submit
    $lines[] = '<input id="obsSubmit" name="obsSubmit" type="submit">';
		
    // End
    $lines[] = '</form>';
    $lines[] = '</div>';

    $content = implode("\n", $lines);

    $this->redirect($link);

    return $content;
  }

  private function viewObservationDelete(iHerbarium\TypoherbariumObservation $obs, $nextAction) {
    $lines = array();

    $lines[] = "<h1>" . $this->ll('deleteObservationDeletedId') . $obs->id . "</h1>";

    // Form

    $link = $this->makeLink($nextAction);
    
    // Beginning
    $lines[] = '<div id="obsFormDiv">';
    $lines[] = '<form id="obsForm" method="post" action="' . $link . '">';
    
    // Submit
    $lines[] = '<input id="obsSubmit" name="obsSubmit" type="submit">';
		
    // End
    $lines[] = '</form>';
    $lines[] = '</div>';

    $content = implode("\n", $lines);

    $this->redirect($link);

    return $content;
  }
	

  private function deletePhotosToAdd($addFilesToken) {

    // Check the Token!
    if( ! $this->checkToken($addFilesToken) )
      return;
    
    // Get all photos paths.
    $photosDir = 'typo3conf/ext/iherbarium_observations/pi4/files/' . $addFilesToken . "/";
    $thumbnailsDir = 'typo3conf/ext/iherbarium_observations/pi4/thumbnails/' . $addFilesToken . "/";

    // Delete temporary uploaded files (and thumbnails) and temporary upload directories.
    iHerbarium\deleteDirWithFiles($photosDir);
    iHerbarium\deleteDirWithFiles($thumbnailsDir);

  }

  private function getPhotosToAdd($addFilesToken) {

    // Check the Token!
    if( ! $this->checkToken($addFilesToken) )
      return array();
  
    // Get all photos paths.
    $photosDir = 'typo3conf/ext/iherbarium_observations/pi4/files/' . $addFilesToken . "/";
    $thumbnailsDir = 'typo3conf/ext/iherbarium_observations/pi4/thumbnails/' . $addFilesToken . "/";
    
    $photosFilenames     = iHerbarium\filenamesFromDir($photosDir);
    $thumbnailsFilenames = iHerbarium\filenamesFromDir($thumbnailsDir);
    // They should contain the same sets of values!

    // Uploaded Photos
    $photosToAdd =
      array_map(
		function($photoFilename) use ($photosDir, $thumbnailsDir) {
		  
		  $photoPath = $photosDir . $photoFilename;

		  // Geoloc
		  $exif = exif_read_data($photoPath);
		  $photoGeoloc = iHerbarium\TypoherbariumGeolocation::fromExif($exif);

		  // Photo
		  $photo = new iHerbarium\TypoherbariumPhoto();
		  $photo->obsId            = NULL;
		  $photo->remoteDir        = $photosDir;
		  $photo->remoteFilename   = $photoFilename;
		  $photo->localDir         = NULL;
		  $photo->localFilename    = NULL;
		  $photo->depositTimestamp = time();
		  $photo->userTimestamp    = NULL;
		  $photo->exifTimestamp    = strtotime($exif['DateTimeOriginal']);
		  $photo->exifOrientation  = (isset($exif['Orientation']) ? $exif['Orientation'] : NULL);
		  $photo->exifGeolocation  = $photoGeoloc;
		  $photo->rois             = array();
		  
		  return array(
			       'source' => ($photosDir . $photoFilename),
			       'thumbnail' => ($thumbnailsDir . $photoFilename),
			       'typoherbariumPhoto' => $photo
			       );
		},
		$photosFilenames
		);
    
    return $photosToAdd;
  }

  private function getGeolocFromPhotos($photosToAdd) {
    // $photos is an array of TypoherbariumPhoto.

    // Filter only Photos with a Geolocation.
    $photosToAddWithGeoloc =
      array_filter($photosToAdd, function($photo) { return $photo->exifGeolocation->isKnown(); });
    
    // If there is any - set the Observation's Geolocation to this Photo's geolocation.
    if(count($photosToAddWithGeoloc) > 0 ) {
      $firstPhoto = iHerbarium\array_first($photosToAddWithGeoloc);
      return $firstPhoto->exifGeolocation;
    } else {
      return iHerbarium\TypoherbariumGeolocation::unknown();
    }
    
  }

  
  private function actionAddFiles($uid, $obs) {
    // A DIRTY WORKAROUND
    if(! $obs) {
      $obs = iHerbarium\TypoherbariumObservation::createFresh();
    }

    // nextAction
    $nextAction = NULL;
    if(isset($_GET['nextAction']) && $_GET['nextAction'])
      $nextAction = desamorcer($_GET['nextAction']);
    
    // Display
    $obsDisplay = $this->viewAddFiles($obs, $nextAction);

    return $obsDisplay;
  }
      

  private function actionCreate($uid, $obs) {
      
    assert($obs == NULL);

    // Connect to database.
    $localTypoherbarium = iHerbarium\LocalTypoherbariumDB::get();

    // Files to add.
    assert(isset($_GET['addFiles']) && $_GET['addFiles'] == True);
    
    // Add Files Token
    assert(isset($_POST['addFilesToken']));
    $addFilesToken = $_POST['addFilesToken'];
    //$content .= "<h2>ADD FILES TOKEN : $addFilesToken</h2>";

    $photosToAddRaw = $this->getPhotosToAdd($addFilesToken);
    $photosToAdd = array_map(function($photoToAddRaw) { return $photoToAddRaw['typoherbariumPhoto']; }, $photosToAddRaw);
    
    /* --- PREPARE THE OBSERVATION --- */

    // Observation Id
    assert( ! isset($_GET['obsId']) || $_GET['obsId'] == NULL );
    
    // Prepare a fresh Observation.
    $obs = iHerbarium\TypoherbariumObservation::createFresh();

    // Get it's Geolocation from Photos.
    assert( ! $obs->geolocation->isKnown() );
    $obs->geolocation = $this->getGeolocFromPhotos($photosToAdd);

    // Display
    if($uid) {
      $obsDisplay = 
	$this->viewObservationCreate($obs, $addFilesToken, $photosToAddRaw);
    } else {
      $obsDisplay = 
	$this->viewObservationCreateNoUser($obs, $addFilesToken, $photosToAddRaw);
    }
    
    return $obsDisplay;
    
  }


  private function actionEdit($uid, iHerbarium\TypoherbariumObservation $obs) {
    
    assert($obs);

    // Display
    $obsDisplay = 
      $this->viewObservationEdit($obs);
    
    return $obsDisplay;

  }

  private function actionShow($uid, iHerbarium\TypoherbariumObservation $obs = NULL) {

    if($obs) {
    
      // Display
      $obsDisplay = 
	$this->viewObservationShow($obs);

    } else {

      // STUB

      // Display
      $obsDisplay = 
	$this->viewObservationsShowList();

    }
    
    return $obsDisplay;

  }


  private function actionSave($uid, $obs) {
    
    // Connect to database.
    $localTypoherbarium = iHerbarium\LocalTypoherbariumDB::get();

    // Files to add.
    $addFiles = False;
    if(isset($_GET['addFiles']) && $_GET['addFiles'] == True) {
      $addFiles = True;
    
      // Add Files Token
      $addFilesToken = NULL;
      if( isset($_POST['addFilesToken']) ) {
	$addFilesToken = $_POST['addFilesToken'];
      }

      //$content .= "<h2>ADD FILES TOKEN : $addFilesToken</h2>";

      $photosToAddRaw = $this->getPhotosToAdd($addFilesToken);
      $photosToAdd = array_map(function($photoToAddRaw) { return $photoToAddRaw['typoherbariumPhoto']; }, $photosToAddRaw);
      assert(count($photosToAddRaw) == count($photosToAdd));
    }
    
    /* --- PREPARE THE OBSERVATION --- */

    // If there is no Observation given.
    if(! $obs) {
      // Prepare a fresh Observation.
      $obs = iHerbarium\TypoherbariumObservation::createFresh();
    } 
    
    // Update it's Geolocation from Photos.
    if( (! $obs->geolocation->isKnown() ) && $addFiles ) {
      $obs->geolocation = $this->getGeolocFromPhotos($photosToAdd);
    }

    /* --- NEW VALUES PASSED BY POST --- */

    // Commentary
    if(isset($_POST['obsCommentary'])) {
      $commentary = $_POST['obsCommentary'];
      $obs->commentary  = $commentary;    
    }
    
     if(isset($_POST['obsAddress'])) {
      $commentary = $_POST['obsAddress'];
      $obs->address  = $commentary;    
    }
    //$content .= "<h2>commentary : $commentary</h2>";
    
    // PlantSize
    if(isset($_POST['obsPlantSize'])) {
      $plantSize = $_POST['obsPlantSize'];
      $obs->plantSize  = $plantSize;    
    }
    //$content .= "<h2>plantSize : $plantSize</h2>";

    // Geoloc
    if(isset($_POST['obsGeolocLatitude']) && isset($_POST['obsGeolocLongitude'])) {
      $latitude = $_POST['obsGeolocLatitude'];
      $longitude = $_POST['obsGeolocLongitude'];
      $geoloc = iHerbarium\TypoherbariumGeolocation::fromLatitudeAndLongitude($latitude, $longitude);
      $obs->geoloc = $geoloc;
    }
    //$content .= "<h2>geoloc :" . ($geoloc ? $geoloc->__toString() : "NULL") . "</h2>";

    // Privacy
    if(isset($_POST['obsPrivacy'])) {
      $privacy = $_POST['obsPrivacy'];
      $obs->privacy = $privacy;    
    }

    // Kind
    if(isset($_POST['obsKind'])) {
      $kind = $_POST['obsKind'];
      $obs->kind = $kind;    
    }

    /* --- SAVE OBSERVATION --- */
      
    // User.
    if($uid != NULL) {
      // User logged in.

    } else {
      // User not logged in.
      
      // NoUserEmail
      $noUserEmail = NULL;
      if(isset($_POST['noUserEmail'])) {
	$noUserEmail = $_POST['noUserEmail'];
      }
      
      if($noUserEmail) {
	// But he has filled in his e-mail.
	  
	// Username is his e-mail
	$username = $noUserEmail;

	// Does he already exist?
	$uid = $localTypoherbarium->getUserUid($username);
	  
	if($uid) {
	  // If he exists we have UID.
	} else {
	  // He doesn't exist - we have to create him an account.
	    
	  // Generate a cool password.
	  //$password = "oompaloompas";
	  $password = substr(md5($username), 0, 6);

	  // Two-letter language.
	  $lang = $GLOBALS['TSFE']->config['config']['language'];

	  // Create the account.
	  $localTypoherbarium->createUser($username, $password, "fr", $name);
	  $uid = $localTypoherbarium->getUserUid($username);

	  // Send an e-mail.

	  // Prepare a message.
	  $msg = new iHerbarium\YouHaveBeenRegisteredMessage();
	  $msg->to       = $username;
	  $msg->lang     = $lang;
	  $msg->username = $username;
	  $msg->password = $password;
	    
	  // Prepare the message consumer.
	  $expertMailSender = new iHerbarium\ExpertMailSender();
	  $expertMailSender->mailFormFactory = new iHerbarium\MailFormFactory();
	  $expertMailSender->templateFactory = new iHerbarium\LocalDBContentTemplateFactory();

	  $expertMailSender->consumeProtocolMessage($msg);
	}
	  
	// Now either way we have an uid - already existing or just created.

      } else {
	// He didn't fill his e-mail.
	$uid = $this->anonymousUid;
      }

    }
    
    // Save Observation.
    $obs = $localTypoherbarium->saveObservation($obs, $uid);

    if($addFiles) {

      // Save uploaded Photos.
      foreach($photosToAdd as $photoToAdd) {
	$savedPhoto = $localTypoherbarium->addPhotoToObservation($photoToAdd, $obs->id, $uid);
	$obs->addPhoto($savedPhoto);
      }
      
      //$content .= $obs->__toString();
      
      // Delete temporary uploaded files (and thumbnails) and temporary upload directories.
      $this->deletePhotosToAdd($addFilesToken);
      
    }

    // Just in case...
    $addFiles = False;
    $addFilesToken = NULL;
    
    // Notify the Determination Protocol
    $p = iHerbarium\DeterminationProtocol::getProtocol("Standard");
    $reObs = $localTypoherbarium->loadObservation($obs->id);
    $p->addedObservation($reObs);
    
    // Display

    // nextAction
    $nextAction = NULL;
    if(isset($_GET['nextAction']) && $_GET['nextAction'])
      $nextAction = $_GET['nextAction'];

    $obsDisplay = $this->viewObservationSave($obs, $nextAction);
    
    return $obsDisplay;
  }

  private function actionDelete($uid, $obs) {
    
    // Connect to database.
    $localTypoherbarium = iHerbarium\LocalTypoherbariumDB::get();

    // Delete Observation if uid is ok.
    if($obs->uid == $uid)
      $localTypoherbarium->deleteObservation($obs->id);

    // nextAction
    $nextAction = NULL;
    if(isset($_GET['nextAction']) && $_GET['nextAction'])
      $nextAction = $_GET['nextAction'];

    $obsDisplay = $this->viewObservationDelete($obs, $nextAction);
    
    return $obsDisplay;
  }


  /**
   * The main method of the PlugIn
   *
   * @param	string		$content: The PlugIn content
   * @param	array		$conf: The PlugIn configuration
   * @return	The content that is displayed on the website
   * 
   */
	
  /* AJOUTER UNE OBSERVATION */
  /* Ce programme permet de rajouter une observation par l'utilisateur et de remplir les différentes tables de la base typoherbarium.
   * Ce programme fait appel au fichier programme.php qui contient les différentes fonctions permettant d'ajouter une observation.
   */
  function main($content,$conf)	{
    $this->conf=$conf;
    $this->pi_setPiVarDefaults();
    $this->pi_loadLL();
    $this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
    
    // jQuery.
    $content .= file_get_contents('typo3conf/ext/iherbarium_observations/pi4/scripts.html');    
    
    // Edit and/or Save Observation 

    //$content .= "POST: <pre>" . var_export($_POST, True) . "</pre>";

    // Connect to database.
    $localTypoherbarium = iHerbarium\LocalTypoherbariumDB::get();
    
    // User
    $uid = $GLOBALS['TSFE']->fe_user->user['uid'];
    //$content .= "<h1>UID : " . var_export($uid, True) . "</h2>";

    // REST: Observation
    if(isset($_GET['obsId']) && $_GET['obsId'] && $_GET['obsId'] > 0) {
      // There is an Observation resource.
      $obsId = $_GET['obsId'];

      // Load the Observation from the Database.
      $obs = $localTypoherbarium->loadObservation($obsId);
    } else {
      // There is no Observation resource.
      $obs = NULL;
    }

    // Retrieve the Action.
    if(isset($_GET["obsAction"]) && $_GET["obsAction"]) {
      $action = $_GET["obsAction"];
    } else {
      $action = "addFiles";
    }
    
    // Run the Action (it returns the Display).
    $obsDisplay = "";

    switch($action) {
    case "addFiles":
      $obsDisplay = $this->actionAddFiles($uid, $obs); break;

    case "create":
      $obsDisplay = $this->actionCreate($uid, $obs); break;
    
    case "edit":
      $obsDisplay = $this->actionEdit($uid, $obs); break;

    case "show":
      $obsDisplay = $this->actionShow($uid, $obs); break;

    case "save":
      $obsDisplay = $this->actionSave($uid, $obs); break;

    case "delete":
      $obsDisplay = $this->actionDelete($uid, $obs); break;
      
    default :
      die("Wrong action!");
    }

    $content .= $obsDisplay;

    return $this->pi_wrapInBaseClass($content);

  }   

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_observations/pi4/class.tx_iherbariumobservations_pi4.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_observations/pi4/class.tx_iherbariumobservations_pi4.php']);
}

?>
