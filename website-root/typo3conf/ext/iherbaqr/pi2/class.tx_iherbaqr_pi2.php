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
 * Plugin 'codeqr' for the 'iherbaqr' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbaqr
 */
class tx_iherbaqr_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_iherbaqr_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_iherbaqr_pi2.php';	// Path to this script relative to the extension dir.
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
		
		global $control_remove_limitation ;
		//rien ˆ voir avec qr code, mais evite de creer un plugin
		
		if($control_remove_limitation=="")return ;
		
		// case of a limitation
		$content='
			<div id="bloc_contenu"><h1> ##title##</h1>
			<div id="bloc_contenu_texte">
			<p>##explanation##</p>
			##maincontent##<p>
			</p></div></div>
		';
		
		$mytitle = get_string_language_sql("ws_view_limitation_rightbordertitle",language_iso_from_lang_id($this->cObj->data['sys_language_uid']));
		$mylegend = get_string_language_sql("ws_view_limitation_explanation",language_iso_from_lang_id($this->cObj->data['sys_language_uid']));
		$content = str_replace("##title##",$mytitle,$content);
		$content = str_replace("##explanation##",$mylegend,$content);
		$content = str_replace("##maincontent##",$control_remove_limitation,$content);
		
		$paramlien = array(remove  => 1);
		$linksamepage =$this->pi_getPageLink($GLOBALS['TSFE']->id,'',$paramlien);
		$content = str_replace("###samepage###",$linksamepage,$content);
	
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbaqr/pi2/class.tx_iherbaqr_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbaqr/pi2/class.tx_iherbaqr_pi2.php']);
}

?>