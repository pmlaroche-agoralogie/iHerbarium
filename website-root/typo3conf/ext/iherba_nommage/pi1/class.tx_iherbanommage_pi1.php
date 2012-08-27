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
require_once(PATH_tslib.'../../../../bibliotheque/lecture_page_tropicos.php');

/**
 * Plugin 'choix du nom' for the 'iherba_nommage' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbanommage
 */
class tx_iherbanommage_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_iherbanommage_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_iherbanommage_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'iherba_nommage';	// The extension key.
	
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
	
		
		if($_GET['etape']=="")$content = affichage_formulaire($this);	
		if($_GET['etape']=="1")$content = preciser_determination($this);
		if($_GET['etape']=="comment")$content = affichage_formulaire_comment($this);
		if($_GET['etape']=="record_comment")$content = preciser_determination_comment($this);
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherba_nommage/pi1/class.tx_iherbanommage_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherba_nommage/pi1/class.tx_iherbanommage_pi1.php']);
}
?>
