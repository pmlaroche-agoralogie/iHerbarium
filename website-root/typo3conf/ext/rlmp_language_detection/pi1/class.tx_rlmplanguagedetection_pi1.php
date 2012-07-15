<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2003-2006 robert lemke medienprojekte (rl@robertlemke.de)
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
 * Plugin 'Language Detection' for the 'rlmp_language_detection' extension.
 *
 * @author	robert lemke medienprojekte <rl@robertlemke.de>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_rlmplanguagedetection_pi1 extends tslib_pibase {
	var $prefixId = 'tx_rlmplanguagedetection_pi1';						// Same as class name
	var $scriptRelPath = 'pi1/class.tx_rlmplanguagedetection_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'rlmp_language_detection';							// The extension key.
	var $conf = array();
	
	/**
	 * The main function recognizes the browser's preferred languages and 
	 * reloads the page accordingly.
	 * 
	 * @param	string		$content: HTML content
	 * @param	array		$conf: The mandatory configuration array
	 * @return	void		
	 */
	function main($content,$conf)	{
		global $TSFE;		
		$this->conf = $conf;


		if (t3lib_div::GPvar ('L') !== NULL) return;		
       // $lang = $GLOBALS['TSFE']->fe_user->getKey('ses', 'lang');
       // $L = $lang['lang_id'];
       // if ($L !== NULL)
          /* LANG vas stored in session, skip autodetection */
         // return;
		
			// Break ouf if the last page visited was also on our site:
		$referer = t3lib_div::getIndpEnv('HTTP_REFERER');
		if (strlen($referer) && stristr ($referer, t3lib_div::getIndpEnv('TYPO3_SITE_URL'))) return;
		
		$acceptedLanguagesArr = $this->getAcceptedLanguages();
		$availableLanguagesArr = $this->conf['useOneTreeMethod'] ? $this->getSysLanguages() : $this->getMultipleTreeLanguages();
		$preferredLanguageOrPageUid = FALSE;
		while (count ($acceptedLanguagesArr) > 0) {
			$currentLanguage = array_shift ($acceptedLanguagesArr);
			if (isset($availableLanguagesArr[$currentLanguage])) {
				$preferredLanguageOrPageUid = $availableLanguagesArr[$currentLanguage];
				break;
			}
		}

		if ($preferredLanguageOrPageUid !== FALSE) {
			if ($this->conf['useOneTreeMethod']) {
				$page = $TSFE->page;
			} else {
				$sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
				$sys_page->init(0);
				$page = $sys_page->getPage ($preferredLanguageOrPageUid);
			}
			$linkData = $TSFE->tmpl->linkData($page,'',0,'',array(),'&L='.$preferredLanguageOrPageUid);
			$locationURL = $this->conf['dontAddSchemeToURL'] ? $linkData['totalURL'] : t3lib_div::locationHeaderUrl($linkData['totalURL']);
			header('Location: '.$locationURL);
		}
	
	}

	/**
	 * Returns the preferred languages ("accepted languages") from the visitor's
	 * browser settings.
	 * 
	 * The accepted languages are described in RFC 2616.
	 * It's a list of language codes (e.g. 'en' for english), separated by
	 * comma (,). Each language may have a quality-value (e.g. 'q=0.7') which
	 * defines a priority. If no q-value is given, '1' is assumed. The q-value
	 * is separated from the language code by a semicolon (;) (e.g. 'de;q=0.7')
	 * 
	 * @return	array	An array containing the accepted languages; key and value = iso code, sorted by quality
	 */
	function getAcceptedLanguages () {
		$languagesArr = array ();		
		$rawAcceptedLanguagesArr = t3lib_div::trimExplode (',',t3lib_div::getIndpEnv('HTTP_ACCEPT_LANGUAGE'),1);

		foreach ($rawAcceptedLanguagesArr as $languageAndQualityStr) {
			list ($languageCode, $quality) = t3lib_div::trimExplode (';',$languageAndQualityStr);
			$acceptedLanguagesArr[$languageCode] = $quality ? (float)substr ($quality,2) : (float)1;
		}

			// Now sort the accepted languages by their quality and create an array containing only the language codes in the correct order.
		if (is_array ($acceptedLanguagesArr)) {
			arsort ($acceptedLanguagesArr);
			$languageCodesArr = array_keys ($acceptedLanguagesArr);
			if (is_array($languageCodesArr)) {
				foreach ($languageCodesArr as $languageCode) {
					$languagesArr[substr ($languageCode,0,2)] = substr ($languageCode,0,2);
				}
			}
		}
		return $languagesArr;
	}

	/**
	 * Returns an array of sys_language records containing the ISO code as the key and the record's uid as the value
	 * 
	 * @return	array	sys_language records: ISO code => uid of sys_language record
	 * @access	private
	 */
	function getSysLanguages () {
		global $TYPO3_DB;

		if (strlen($this->conf['defaultLang'])) $availableLanguages [trim(strtolower($this->conf['defaultLang']))] = 0;
	
			// Two options: prior TYPO3 3.6.0 the title of the sys_language entry must be one of the two-letter iso codes in order
			// to detect the language. But if the static_languages is installed and the sys_language record points to the correct
			// language, we can use that information instead.

		$res = $TYPO3_DB->exec_SELECTquery (
			'lg_iso_2',
			'static_languages',
			'1'
		);
		if ($res) {
				// Table and field exist so create query for the new approach:
			$res = $TYPO3_DB->exec_SELECTquery (
				'sys_language.uid, static_languages.lg_iso_2 as isocode',
				'sys_language LEFT JOIN static_languages ON sys_language.static_lang_isocode = static_languages.uid',
				'1' . $this->cObj->enableFields ('sys_language') . $this->cObj->enableFields ('static_languages')
			);
		} else {
			$res = $TYPO3_DB->exec_SELECTquery (
				'sys_language.uid, sys_language.title as isocode',
				'sys_language',
				'1' . $this->cObj->enableFields ('sys_language')
			);
		}
		while ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
			$availableLanguages [trim(strtolower($row['isocode']))] = $row['uid'];
		}
		return $availableLanguages;
	}

	/**
	 * Returns an array of available languages defined in the TypoScript configuration for this plugin.
	 * Acts as an alternative for getSysLanguages ()
	 * 
	 * @return	array	available languages: ISO code => Page ID of languages' root page
	 * @access	private
	 */
	function getMultipleTreeLanguages () {
		foreach ($this->conf['multipleTreesRootPages.'] as $isoCode=>$uid) {
			$availableLanguages [trim(strtolower($isoCode))] = intval($uid);
		}
		return $availableLanguages;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rlmp_language_detection/pi1/class.tx_rlmplanguagedetection_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rlmp_language_detection/pi1/class.tx_rlmplanguagedetection_pi1.php']);
}

?>
