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


/**
 * Plugin 'manage-subdomain' for the 'iherba_subdomain' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbasubdomain
 */
class tx_iherbasubdomain_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_iherbasubdomain_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_iherbasubdomain_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'iherba_subdomain';	// The extension key.
	
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
	
		if ($_POST['typaction']=="add_area"){
			$creation_area=" INSERT INTO  `typoherbarium`.`iherba_area` (
					`name` ,
					`domain` ,
					`center_lat` ,
					`center_long` ,
					`radius` ,
					`areaname` ,
					`email_creator` 
					)
					VALUES (
					'".desamorcer($_POST['shortname'])."',  '',  '".desamorcer($_POST['latitude'])."',
					'".desamorcer($_POST['longitude'])."',  '".(desamorcer($_POST['radius'])*360/42000000)."',
					'".desamorcer($_POST['longdesc'])."',  '".desamorcer($_POST['emailcreator'])."'
					);";
			mysql_query ($creation_area) or die ('');
		}
		$content='<!-- $creation_area -->';
		$sql_liste_area = "SELECT * FROM  `iherba_area`  ORDER BY  `iherba_area`.`creation_timestamp` DESC LIMIT 0 , 60";
		$result=mysql_query ($sql_liste_area) or die ();
		$nb_lignes_resultats=mysql_num_rows($result);
		if($nb_lignes_resultats>0)$content='<table>';
		while ($donnees = mysql_fetch_array($result)){
		
		
			//$content.='<tr><td><a href=http://'.$donnees['name'].substr(t3lib_div::getIndpEnv('HTTP_HOST'),strpos(t3lib_div::getIndpEnv('HTTP_HOST'),".",0)).'>'.$donnees['name'].'</a></td>';
			$content.='<tr><td><a href=http://www'.substr(t3lib_div::getIndpEnv('HTTP_HOST'),strpos(t3lib_div::getIndpEnv('HTTP_HOST'),".",0))."?area_limitation=areaname:".$donnees['name'].'>'.$donnees['name'].'</a></td>';
			$content.='<td>'.$donnees['areaname'].'</td>';
			
			if($donnees['auto_export_to_indicateur']==1)
				{
					$content.='<td><a href=http://www'.substr(t3lib_div::getIndpEnv('HTTP_HOST'),strpos(t3lib_div::getIndpEnv('HTTP_HOST'),".",0))."?id=indicators&setid=".$donnees['uid_set'].'>'."estimateurs".'</a></td>';
				}
			$content.='</tr>';
		}
		if($nb_lignes_resultats>0)$content.='</table>';
		
	
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherba_subdomain/pi2/class.tx_iherbasubdomain_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherba_subdomain/pi2/class.tx_iherbasubdomain_pi2.php']);
}

?>
