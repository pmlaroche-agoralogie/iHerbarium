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


/**
 * Plugin 'page' for the 'iherbaqr' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbaqr
 */
class tx_iherbaqr_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_iherbaqr_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_iherbaqr_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'iherbaqr';	// The extension key.
	
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
	
		$urlqrecnode = "http://www.iherbarium.org/index.php?id=21&numero_observation=";
		$urlqrecnode .= desamorcer($_GET['numero_observation']);
		$urlgoogle =  urlencode($urlqrecnode);
		$content='<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2"> <tbody> <tr> <td style="width: 100px;">
			<img src=http://chart.apis.google.com/chart?chs=350x350&cht=qr&chld=H&chl='.$urlgoogle.' width=100><br>	
			</td> <td> 
		';
	/*Affichage des informations concernant l'observation */
		bd_connect();
		$sql="select date_depot,idobs,commentaires,longitude,latitude from iherba_observations where idobs=".desamorcer($_GET['numero_observation'])." ";
	    $result = mysql_query($sql)or die ();
	    if(!($lobervation = mysql_fetch_assoc($result))) return ; 
		$date_depot = $lobervation["date_depot"];
		$dateaffichage = str_replace("-","/",$date_depot) ;
		$content.= "Observation : "." ".desamorcer($_GET['numero_observation'])."<br/>\n";//$this->pi_getLL('numeroObservation', '', 1)
		$content.= "Date  : "." ".$dateaffichage."<br/>\n";//$this->pi_getLL('numeroObservation', '', 1)
		if($lobervation["commentaires"] !=""){
			$content.="Commentaire : "." ".$lobervation["commentaires"]."<br/>\n";//$this->pi_getLL('commentairesObservation', '', 1)
		}
		
		$sql="select nom_photo_final from iherba_photos where id_obs=".desamorcer($_GET['numero_observation']);
		$result = mysql_query($sql)or die ();
		/* On affiche les photos que l'utilisateur nous a déjà fait parvenir */
		$repertoire = "/medias/big/";
	    	while ($row = mysql_fetch_assoc($result)) {
			$image="$repertoire/".$row["nom_photo_final"];
			$content.='<img src="'.$image.'" border=0 height="65"  /></blank>&nbsp;';
			}
	
		$content.= "</td> </tr> </tbody> </table> ";	
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbaqr/pi1/class.tx_iherbaqr_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbaqr/pi1/class.tx_iherbaqr_pi1.php']);
}

?>
