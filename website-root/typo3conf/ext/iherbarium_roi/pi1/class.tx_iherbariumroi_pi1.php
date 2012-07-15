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

// Kuba ->

$balPath = PATH_tslib . "../../../../boiteauxlettres/";

require_once($balPath . "myPhpLib.php");

require_once($balPath . "debug.php");
require_once($balPath . "config.php");

require_once($balPath . "typoherbariumModel.php");
require_once($balPath . "persistentObject.php");

iHerbarium\Debug::init("iHerbariumROIplugin", false);

// <- Kuba

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_tslib.'../../../../bibliotheque/common_functions.php');


/**
 * Plugin 'visualisation tags observation' for the 'iherbarium_roi' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbariumroi
 */
class tx_iherbariumroi_pi1 extends tslib_pibase {
  var $prefixId      = 'tx_iherbariumroi_pi1';		// Same as class name
  var $scriptRelPath = 'pi1/class.tx_iherbariumroi_pi1.php';	// Path to this script relative to the extension dir.
  var $extKey        = 'iherbarium_roi';	// The extension key.
	
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

    // Kuba ->

    if(isset($_GET["deleteROI"])) {
      $roiId = $_GET["deleteROI"];
      $localTypoherbarium = iHerbarium\LocalTypoherbariumDB::get();
      $roi = $localTypoherbarium->loadROI($roiId);
      if($roi) $localTypoherbarium->deleteROI($roi);
    }
    
    // <- Kuba
    
    $content="";
    $rep_roi=roi_sources;
    $numero_observation=$_GET['numero_observation'];
    //	    $chemin_web = str_replace('\\','/',dirname($_SERVER['REQUEST_URI']));
    //		 if(substr($chemin_web, -1)!="/"){
    //			$chemin_web .= "/";
    //		 }
		
    $repertoire=repertoire_vignettes;
    bd_connect();
    /* On sélectionne toutes les images de l'observation afin de les afficher avec leurs zones d'intérêt déjà définies */
    $sql="select id_obs,nom_photo_final,idphotos from iherba_photos where id_obs=$numero_observation";
    $result = mysql_query($sql)or die ('Erreur SQL !'.$sql.'<br />'.mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
      $nom_photo_final=$row["nom_photo_final"];
      $idphotos=$row["idphotos"];
      $photo="$repertoire/$nom_photo_final";
      $content.='<a href="'.$photo.'" target="_blank"><img src="'.$photo.'" border=2 width="200"  /></blank></a>';

      // Kuba ->

      $content.="<br/><a href=index.php?id=30&numero_observation=".$numero_observation."&identifiant_photo=".$idphotos."&L=".$GLOBALS['TSFE']->sys_language_uid."&action=replace> ".$this->pi_getLL('definirZoneInteret', '', 1)."</a> ";
      $content.="<br/><a href=index.php?id=30&numero_observation=".$numero_observation."&identifiant_photo=".$idphotos."&L=".$GLOBALS['TSFE']->sys_language_uid."&action=add> "    .$this->pi_getLL('ajouterZoneInteret', '', 1)."</a> ";

      // <- Kuba

      $content.="<br/>\n";
      $compteur=0;
      /*Pour chacune des photos on va définir si des zones d'intérets ont déjà été définies.
       * Si c'est le cas, on va afficher chacune des zones d'intérêt déjà définie à partir du répertoire des vignettes*/
      $sql_roi="select id from iherba_roi where id_photo=$idphotos";
      $result_roi = mysql_query($sql_roi)or die ('Erreur SQL !'.$sql_roi.'<br />'.mysql_error());
      while($row=mysql_fetch_assoc($result_roi)){
	if($compteur==0){
	  $content.="<br/>".$this->pi_getLL('zonesDejaDefinies', '', 1);
	}
	$id=$row['id'];
	$langue=$GLOBALS['TSFE']->config[config][language];
	/*On va afficher à côté de chacune des roi déjà définies, le tag qui lui est associé (s'il s'agit d'une fleur, d'une feuille...)*/
	// PL penser a ajuster lien vers table translation
	// if($langue=="fr")
	//{
	$sql_tag="select iherba_tags.tag from iherba_tags,iherba_roi_tag,iherba_roi where iherba_roi.id='$id' 
					   and iherba_roi.id=iherba_roi_tag.id_roi and iherba_roi_tag.id_tag=iherba_tags.id_tag";
	$result_tag = mysql_query($sql_tag)or die ('Erreur SQL !'.$sql_tag.'<br />'.mysql_error());
	while($row_tag=mysql_fetch_assoc($result_tag)){
	  $tag=$row_tag["tag"];
							   
	  // Kuba ->
	  $content .= "<br/>";
	  // <- Kuba
							   
	  $content.=$tag." ";
	}
	//}
			 			   
	           
	$rep_roi=roi_vignettes;
	$montest ="roi_".$id.".jpg";
	$content.="<img src='$rep_roi/$montest' width='70' >";

	// Kuba ->
	$content .= " <a href=index.php?id=29&numero_observation=".$numero_observation."&deleteROI=" . $id . ">" . $this->pi_getLL('supprimerZoneInteret', '', 1) . "</a>";
	// <- Kuba

	$compteur++;
      }
		   
      $content.="<br/><br/>";
    }

		
    return $this->pi_wrapInBaseClass($content);
  }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_roi/pi1/class.tx_iherbariumroi_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_roi/pi1/class.tx_iherbariumroi_pi1.php']);
}
?>
