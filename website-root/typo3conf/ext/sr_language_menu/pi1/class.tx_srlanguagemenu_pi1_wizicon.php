<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Class that adds the wizard icon.
 * The plugin is defined as a CType
 *
 * @author Stanislas Rolland <typo3(arobas)sjbr.ca>
 */
class tx_srlanguagemenu_pi1_wizicon {
	function proc($wizardItems) {
		global $LANG;

		$LANG->includeLLFile(t3lib_extMgm::extPath('sr_language_menu').'locallang.xml');

		$wizardItems['plugins_tx_srincludepages_pi1'] = array(
			'icon' => t3lib_extMgm::extRelPath('sr_language_menu').'pi1/ce_wiz.gif',
			'title' => $LANG->getLL('pi1_title'),
			'description' => $LANG->getLL('pi1_plus_wiz_description'),
			'params' => '&defVals[tt_content][CType]=sr_language_menu_pi1' );

		return $wizardItems;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_language_menu/pi1/class.tx_srlanguagemenu_pi1_wizicon.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_language_menu/pi1/class.tx_srlanguagemenu_pi1_wizicon.php']);
}

?>