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
		
		$currentobs = desamorcer($_GET['numero_observation']);
		$urlqrencode = "http://www.iherbarium.org/observation/data/".$currentobs;
		$urlgoogle =  urlencode($urlqrencode);
		$content='<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2"> <tbody> <tr> <td style="width: 100px;">
			<img src=http://chart.apis.google.com/chart?chs=380x380&cht=qr&chld=H&chl='.$urlgoogle.' width=100><br>	
			</td> <td> 
		';
	/*Affichage des informations concernant l'observation */
		bd_connect();
		$sql="select date_depot,idobs,commentaires,longitude,latitude from iherba_observations where idobs=".$currentobs." ";
	    $result = mysql_query($sql)or die ();
	    if(!($lobervation = mysql_fetch_assoc($result))) return ; 
		$date_depot = $lobervation["date_depot"];
		$dateaffichage = str_replace("-","/",$date_depot) ;
		$content.= $this->pi_getLL('text_before_obs_num', '', 1)." ".$currentobs."<br/>\n";
		$content.= $this->pi_getLL('text_before_date', '', 1)." ".$dateaffichage."<br/>\n";
		$content.= $this->pi_getLL('text_before_localisation', '', 1)." ".$lobervation["latitude"]." , ".$lobervation["longitude"]."<br/>\n";
		if($lobervation["commentaires"] !=""){
			$content.=$this->pi_getLL('text_before_comment', '', 1)." ".$lobervation["commentaires"]."<br/>\n";
		}

		//display last given name
		$champscomment = 'email_comment';
		$sql_determination.="select iherba_determination.id , nom_commun,nom_scientifique,date, famille,genre ,id_cases, iherba_determination_cases.$champscomment ,iherba_certitude_level.value as certitude_level, iherba_certitude_level.comment as certitude_comment,";
		$sql_determination.=" iherba_determination.comment,iherba_precision_level.value as precision_level,iherba_precision_level.$champscomment as precisioncomment from iherba_determination,iherba_determination_cases,iherba_certitude_level, iherba_precision_level ";
		$sql_determination.="where  iherba_determination_cases.language = 'fr' and iherba_determination_cases.id_cases = iherba_determination.comment_case AND iherba_determination.precision_level = iherba_precision_level.value ";
		$sql_determination.= " AND iherba_determination.certitude_level = iherba_certitude_level.value AND iherba_determination.id_obs=$currentobs ";
		$sql_determination.= " order by creation_timestamp desc";
		$sql_determination .= " limit 1";
		
		 // echo "<!-- sql $sql_determination -->";
		
		$result_determination = mysql_query($sql_determination) or die ($sql_determination);
		$num_rows = mysql_num_rows($result_determination); //nombre de lignes rÈsultats
			
		if ($row_determination = mysql_fetch_assoc($result_determination)) {
		    $nom_commun=$row_determination["nom_commun"];
		    $nom_scientifique=$row_determination["nom_scientifique"];
		    $date=$row_determination["date"];
		    $content.= $this->pi_getLL('text_before_determination', '', 1) .$date.") : ".$nom_commun. " ".$nom_scientifique."<br>";
		  }
  
		//display pictures
		$sql="select nom_photo_final from iherba_photos where id_obs=".$currentobs;
		$result = mysql_query($sql)or die ();
		/* On affiche les photos que l'utilisateur nous a déjà fait parvenir */
		$repertoire = "/medias/big/";
	    	while ($row = mysql_fetch_assoc($result)) {
			$image="$repertoire/".$row["nom_photo_final"];
			$content.='<img src="'.$image.'" border=0 height="65"  /></blank>&nbsp;';
			}
	
	
		
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbaqr/pi1/class.tx_iherbaqr_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbaqr/pi1/class.tx_iherbaqr_pi1.php']);
}

?>
