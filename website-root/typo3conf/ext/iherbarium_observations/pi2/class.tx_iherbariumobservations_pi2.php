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

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_tslib.'../../../../bibliotheque/common_functions.php');


/**
 * Plugin 'liste d'un utilisateur' for the 'iherbarium_observations' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbariumobservations
 */
class tx_iherbariumobservations_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_iherbariumobservations_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_iherbariumobservations_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'iherbarium_observations';	// The extension key.
	
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
		
		global $control_remove_limitation;
		$content="";
		
		if($GLOBALS['TSFE']->page["uid"]==52)
		{ // page 52 : plus utilisŽ (affiche un calcul direct)
			$numero_observation = desamorcer($_GET['numero_observation']);
			$content.=information_analyse($numero_observation,$GLOBALS['TSFE']->sys_language_uid,"Morphologie:");
			$desc = charge_description($numero_observation);
			$obs_prob = calcule_liste_proche ($desc);
			if(count($obs_prob) >0)
				{
				asort($obs_prob); // vŽrifier que a trie sur la probabilitŽ !
				foreach($obs_prob as $ligne)
					{
					$content .= affiche_une_observation_dans_liste($this,$ligne['numobs'],"public");
					}
				}
			
		}
		else
		{  
			if($GLOBALS['TSFE']->page["uid"]==27)
			       $content.=afficher_carte_observations($this,$GLOBALS['TSFE']->fe_user->user['uid']);  //page "carte des observations"
			else
			       $content.=afficher_carte_observations($this,0);  //page "carte des observations publiques"
			$mylanguage = language_iso_from_lang_id($this->cObj->data['sys_language_uid']);
			if((!is_sousdomaine_www())||($control_remove_limitation!=""))
				$content.= "<br><br>".liste_espece($this,0,$mylanguage);  //inventaire publique
		}
			
	return $this->pi_wrapInBaseClass($content);
   }

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_observations/pi2/class.tx_iherbariumobservations_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_observations/pi2/class.tx_iherbariumobservations_pi2.php']);
}
?>
