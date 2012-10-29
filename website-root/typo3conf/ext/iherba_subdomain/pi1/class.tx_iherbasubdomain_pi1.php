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
 * Plugin 'create-subdom' for the 'iherba_subdomain' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbasubdomain
 */
class tx_iherbasubdomain_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_iherbasubdomain_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_iherbasubdomain_pi1.php';	// Path to this script relative to the extension dir.
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

		$content='
			<form action="'.$this->pi_getPageLink(80).'" method="POST">
			<input type="hidden" name="typaction" value="add_area">
			<table>
				<tr>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_shortname')).'</td>
				<td><input type="text" name="shortname" value=""></td>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_shortname_example')).'</td>
				</tr>
				
				<tr>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_latitude')).'</td>
				<td><input type="text" name="latitude" value=""></td>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_latitude_example')).'</td>
				</tr>
				
				<tr>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_longitude')).'</td>
				<td><input type="text" name="longitude" value=""></td>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_longitude_example')).'</td>
				</tr>
				
				<tr>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_radius')).'</td>
				<td><input type="text" name="radius" value=""></td>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_radius_example')).'</td>
				</tr>
				
				<tr>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_longdesc')).'</td>
				<td><input type="text" name="longdesc" value=""></td>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_longdesc_example')).'</td>
				</tr>
				
				<tr>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_emailcreator')).'</td>
				<td><input type="text" name="emailcreator" value=""></td>
				<td>'.htmlspecialchars($this->pi_getLL('label_form_emailcreator_example')).'</td>
				</tr>
				
				<tr><td>
				<input type="submit" name="'.$this->prefixId.'[submit_button]" value="'.htmlspecialchars($this->pi_getLL('submit_button_label')).'">
				</td>
				</tr>
			</table>
			</form>
			<br />
			
		';
	
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherba_subdomain/pi1/class.tx_iherbasubdomain_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherba_subdomain/pi1/class.tx_iherbasubdomain_pi1.php']);
}

?>