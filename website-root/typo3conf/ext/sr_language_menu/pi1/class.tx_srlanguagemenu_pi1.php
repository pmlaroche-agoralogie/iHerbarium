<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2003 Kasper Skaarhoej (kasper@typo3.com)
*  (c) 2004-2009 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
*  A copy is found in the textfile GPL.txt and important notices to the license 
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Plugin 'Language Selection' for the 'sr_language_menu' extension.
 *
 * @author	Kasper Skaarhoej <kasper@typo3.com>
 * @coauthor	Stanislas Rolland <typo3(arobas)sjbr.ca>
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('static_info_tables').'pi1/class.tx_staticinfotables_pi1.php');

class tx_srlanguagemenu_pi1 extends tslib_pibase {
	var $prefixId = 'tx_srlanguagemenu_pi1';			// Same as class name
	var $scriptRelPath = 'pi1/class.tx_srlanguagemenu_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'sr_language_menu';				// The extension key.
	var $conf = array();
	var $cObj;
	
	var $languagesUids = array();
	var $languagesExternalUrls = array();
	var $forwardParams;
	var $localTemplate;
	
	/**
	 * The constructor returns the language menu
	 * 
	 * @param	string		$content: HTML content
	 * @param	array		$conf: The mandatory configuration array
	 * @return	void		
	 */
	 
	function main($content,$conf)	{
		
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->linkVars = $GLOBALS['TSFE']->linkVars;
		
		$this->staticInfo = t3lib_div::makeInstance('tx_staticinfotables_pi1');
		$this->staticInfo->init();
		
		$useSysLanguageTitle = trim($this->conf['useSysLanguageTitle']) ? trim($this->conf['useSysLanguageTitle']) : 0;
		$useIsoLanguageCountryCode = trim($this->conf['useIsoLanguageCountryCode']) ? trim($this->conf['useIsoLanguageCountryCode']) : 0;
		$useIsoLanguageCountryCode = $useSysLanguageTitle ? 0 : $useIsoLanguageCountryCode;
		$useSelfLanguageTitle = trim($this->conf['useSelfLanguageTitle']) ? trim($this->conf['useSelfLanguageTitle']) : 0;
		$useSelfLanguageTitle = ($useSysLanguageTitle || $useIsoLanguageCountryCode) ? 0 : $useSelfLanguageTitle;
		
			// Check if extension realURL is installed and configured in TS template
		$this->realUrlLoaded = t3lib_extMgm::isLoaded('realurl', 0) && $GLOBALS['TSFE']->config['config']['tx_realurl_enable'];
		$this->rlmp_language_detectionLoaded = t3lib_extMgm::isLoaded('rlmp_language_detection', 0);
		
		$this->localTemplate = new t3lib_TStemplate;
		
		$removeParams = t3lib_div::trimExplode(',', $this->conf['removeParams'], 1);
		$this->forwardParams = $this->local_add_vars($GLOBALS['HTTP_GET_VARS'], $removeParams);
		$this->forwardParams .= $this->local_add_vars($GLOBALS['HTTP_POST_VARS'], $removeParams);
		$GLOBALS['TSFE']->linkVars = $this->remove_vars($GLOBALS['TSFE']->linkVars, $removeParams);
		
		$tableA = 'sys_language';
		$tableB = 'static_languages';
		
		$languagesUidsList = trim($this->cObj->data['tx_srlanguagemenu_languages']) ? trim($this->cObj->data['tx_srlanguagemenu_languages']) : trim($this->conf['languagesUidsList']);
		$languages = array();
		$languagesLabels = array();
			// Set default language
		$defaultLanguageISOCode = trim($this->conf['defaultLanguageISOCode']) ?  strtoupper(trim($this->conf['defaultLanguageISOCode'])) : 'EN';
		$defaultCountryISOCode = trim($this->conf['defaultCountryISOCode']) ?  strtoupper(trim($this->conf['defaultCountryISOCode'])) : '';
		$languages[] = strtolower($defaultLanguageISOCode).($defaultCountryISOCode?'_'.$defaultCountryISOCode:'');
		$this->languagesUids[] = '0';
		if ($useIsoLanguageCountryCode) {
			$languagesLabels[] = strtolower($defaultLanguageISOCode).($defaultCountryISOCode?'-'.strtolower($defaultCountryISOCode):'');
		} else {
			$languagesLabels[] = $this->staticInfo->getStaticInfoName('LANGUAGES', strtoupper($languages['0']),'','',$useSelfLanguageTitle);
			if (!$languagesLabels['0'] && $defaultCountryISOCode) {
				$languagesLabels['0'] = $this->staticInfo->getStaticInfoName('LANGUAGES', strtoupper($defaultLanguageISOCode),'','',$useSelfLanguageTitle);
			}
		}
			// Get the language codes and labels for the languages set in the plugin list
		$selectFields = $tableA . '.uid, ' . $tableA . '.title, ' . $tableB . '.lg_iso_2, ' . $tableB . '.lg_name_en, ' . $tableB . '.lg_country_iso_2';
		$table = $tableA . ' LEFT JOIN ' . $tableB . ' ON ' . $tableA . '.static_lang_isocode=' . $tableB . '.uid';
			// Ignore IN clause if language list is empty. This means that all languages found in the sys_language table will be used
		if (!empty($languagesUidsList)) {
			$whereClause = $tableA . '.uid IN (' . $languagesUidsList . ') ';
		} else {
			$whereClause = '1=1 ';
		}
		$whereClause .= $this->cObj->enableFields ($tableA);
		$whereClause .= $this->cObj->enableFields ($tableB);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($selectFields, $table, $whereClause);
			// If $languagesUidsList is not empty, the languages will be sorted in the order it specifies
		$languagesUidsArray = t3lib_div::trimExplode(',', $languagesUidsList, 1);
		$index = 0;
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if ($row['lg_iso_2'] != $defaultLanguageISOCode || $row['lg_country_iso_2'] != $defaultCountryISOCode) {
				$index++;
				$key = array_search($row['uid'], $languagesUidsArray);
				$key = ($key !== FALSE) ? $key+1 : $index;
				$this->languagesUids[$key] = $row['uid'];
				$languages[$key] = strtolower($row['lg_iso_2']).($row['lg_country_iso_2']?'_'.$row['lg_country_iso_2']:'');
				if ($useIsoLanguageCountryCode) {
					$languagesLabels[$key] =  strtolower($row['lg_iso_2']).($row['lg_country_iso_2']?'-'.strtolower($row['lg_country_iso_2']):'');
				} elseif ($useSysLanguageTitle) {
					$languagesLabels[$key] =  $row['title'];
				} else {
					$languagesLabels[$key] =  $this->staticInfo->getStaticInfoName('LANGUAGES', $row['lg_iso_2'].($row['lg_country_iso_2']?'_'.$row['lg_country_iso_2']:''),'','',$useSelfLanguageTitle);
				}
			} elseif ($useSysLanguageTitle) {
					$languagesLabels['0'] =  $row['title'];
			}
		}
		ksort($languages);
		ksort($this->languagesUids);
		ksort($languagesLabels);
			// Show current language first, if configured
		if ($this->conf['showCurrentFirst']) {
			$key = array_search($GLOBALS['TSFE']->sys_language_uid, $this->languagesUids);
			if ($key) {
				$code = $languages[$key];
				$uid = $this->languagesUids[$key];
				$label = $languagesLabels[$key];
				unset($languages[$key]);
				unset($this->languagesUids[$key]);
				unset($languagesLabels[$key]);
				array_unshift($languages, $code);
				array_unshift($this->languagesUids, $uid);
				array_unshift($languagesLabels, $label);
			}
		}
			// Select all pages_language_overlay records on the current page. Each represents a possibility for a language.
		$langArr = array();
		$table = 'pages_language_overlay';
		$whereClause = 'pid=' . $GLOBALS['TSFE']->id . ' ';
		$whereClause .= $GLOBALS['TSFE']->sys_page->enableFields($table);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT sys_language_uid', $table, $whereClause);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$langArr[$row['sys_language_uid']] = $row['sys_language_uid'];
		}
			// Add configured external url's for missing overlay records.
		if (is_array($this->conf['useExternalUrl.'])) {
			foreach ($languages as $key => $val) {
				if ($this->languagesUids[$key]) {
					if (!$langArr[$this->languagesUids[$key]]) {
						if ($this->conf['useExternalUrl.'][$val]) {
							$this->languagesExternalUrls[$key] = $this->conf['useExternalUrl.'][$val];
							$langArr[$this->languagesUids[$key]] = $this->languagesUids[$key];
						}
					} else {
						if ($this->conf['useExternalUrl.'][$val] && ($this->conf['useExternalUrl.'][$val. '.']['force'] || $this->conf['forceUseOfExternalUrl'])) {
							$this->languagesExternalUrls[$key] = $this->conf['useExternalUrl.'][$val];
						}
					}
				}
			}
		}

		if (!$this->conf['hideIfNoAltLanguages'] || (count($langArr) > 0)) {
				// Get the template
			$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
				// Get the specified layout
			$layout = $this->cObj->data['tx_srlanguagemenu_type'] ? $this->cObj->data['tx_srlanguagemenu_type'] : trim($this->conf['defaultLayout']);
			switch ($layout) {
				case 1:
						// Drop-down layout
					$templateMarker = '###TEMPLATE_1###';
					$template = $this->cObj->getSubpart($this->templateCode, $templateMarker);
					$subpartArray = array();
					
					$selected = '';
					$this->selectorEmpty = true;
						// If 'Hide default translation of page' is set, do not show the default language
					if ($GLOBALS['TSFE']->page['l18n_cfg']&1) {
						unset($languages[0]);
					}
					foreach ($languages as $key => $val) {
						$uri = $this->makeUrl($key, !$this->realUrlLoaded);
						if (!$this->languagesUids[$key] || $langArr[$this->languagesUids[$key]]) {
							$names[$key][(($this->realUrlLoaded && !$this->languagesExternalUrls[$key]) ? $this->getWebsiteDir().$uri : $this->languagesUids[$key])] = $languagesLabels[$key];
							$selected = ($GLOBALS['TSFE']->sys_language_uid == $this->languagesUids[$key]) ? (($this->realUrlLoaded && !$this->languagesExternalUrls[$key]) ? $this->getWebsiteDir().$uri : $this->languagesUids[$key]) : $selected;
						}
					}

					if (!$this->realUrlLoaded) {
						$questionMark = strstr($uri, '?') ? '' : '?';
						$subpartArray['###LANGUAGE_SELECT###'] =  $this->buildLanguageSelector($names, 'L', '', $this->pi_getLL('select_language'), $selected, 'if(' . ($this->rlmp_language_detectionLoaded?'true':'false') . ' || this.options[this.selectedIndex].value != \'0\') { top.location.replace(\'' . htmlspecialchars($uri . $questionMark . '&L=') . '\' + this.options[this.selectedIndex].value ); } else { top.location.replace(\'' . htmlspecialchars($uri) .  '\'); }' );
					} else {
						$subpartArray['###LANGUAGE_SELECT###'] =  $this->buildLanguageSelector($names, 'L', '', $this->pi_getLL('select_language'), $selected, 'top.location.replace(this.options[this.selectedIndex].value );' );
					}
					if (!$this->selectorEmpty) {
						$subpartArray['###LANGUAGE_SELECT###'] = '<form action="" id="sr_language_menu_form">
							<fieldset>
								<legend>' . $this->pi_getLL('form_fieldset_legend') . '</legend>
								<label for="sr_language_menu_select">' . $this->pi_getLL('form_select_label') . '</label>
								' . $subpartArray['###LANGUAGE_SELECT###']. '
							</fieldset>
						</form>';
					} else {
						$subpartArray['###LANGUAGE_SELECT###'] = ' ';
					}

					$content = $this->cObj->substituteMarkerArrayCached($template, $subpartArray, array(), array());
					break;
				case 2:
						// Links layout
					$templateMarker = '###TEMPLATE_2###';
					$template = $this->cObj->getSubpart($this->templateCode, $templateMarker);
					$linksListHeader = $this->conf['links.']['header'] ? $this->pi_getLL('select_language') : '';
					$linksListHeader = ($linksListHeader && $this->conf['links.']['header.']['stdWrap.']) ? $this->cObj->stdWrap($linksListHeader, $this->conf['links.']['header.']['stdWrap.']) : $linksListHeader;
					$template = $this->cObj->substituteMarker($template, '###LINK_SELECT_LANGUAGE###', $linksListHeader);
					$subpartArray = array();
					$subpartArray['###LINK_LIST###'] = '';
					$linkEntrySubpart = $this->cObj->getSubpart($template, '###LINK_ENTRY###');
					$markerArray = array();

					$firstItem = true;
					foreach ($languages as $key => $val) {
						$uri = $this->makeUrl($key);
						$label = $languagesLabels[$key];
						$current = ($GLOBALS['TSFE']->sys_language_uid == $this->languagesUids[$key]);
						$inactive = (($this->languagesUids[$key] && !$langArr[$this->languagesUids[$key]]) || (!$this->languagesUids[$key] && $GLOBALS['TSFE']->page['l18n_cfg']&1));
						if (($current && $this->conf['link.']['CUR.']['doNotLinkIt']) || ($inactive && $this->conf['link.']['INACT.']['doNotLinkIt'])) {
							$linkItem = $label;
						} else {
							$linkItem = '<a href="' . htmlspecialchars($uri) . (trim($this->conf['target']) ? ('" target="' . trim($this->conf['target']) . '"') : '') . '">'.$label.'</a>';
						}
						if ($current) {
							$linkItem = ($this->conf['link.']['CUR.']['stdWrap.']) ? $this->cObj->stdWrap($linkItem, $this->conf['link.']['CUR.']['stdWrap.']) : $linkItem;
						} elseif ($inactive) {
							$linkItem = ($this->conf['link.']['INACT.']['stdWrap.']) ? $this->cObj->stdWrap($linkItem, $this->conf['link.']['INACT.']['stdWrap.']) : $linkItem;
						} else {
							$linkItem = ($this->conf['link.']['NO.']['stdWrap.']) ? $this->cObj->stdWrap($linkItem, $this->conf['link.']['NO.']['stdWrap.']) : $linkItem;
						}
						if (!empty($linkItem) && !$firstItem)  $linkItem = ' ' . ($this->conf['links.']['stdWrap.']['split.']['token']?$this->conf['links.']['stdWrap.']['split.']['token']:'|') . ' ' . $linkItem;
						$firstItem = (!empty($linkItem)) ? false : $firstItem;
						$markerArray['###LINK###'] = $linkItem;
						$subpartArray['###LINK_LIST###'] .= $this->cObj->substituteMarkerArrayCached($linkEntrySubpart, $markerArray, array(), array());
					}
					$subpartArray['###LINK_LIST###'] = ($this->conf['links.']['stdWrap.']) ? $this->cObj->stdWrap($subpartArray['###LINK_LIST###'], $this->conf['links.']['stdWrap.'])  : $subpartArray['###LINK_LIST###'];
					$content = $this->cObj->substituteMarkerArrayCached($template, $markerArray, $subpartArray, array());
					break;
				case 0:
				default:
						// Flags layout
					$templateMarker = '###TEMPLATE_0###';
					$template = $this->cObj->getSubpart($this->templateCode, $templateMarker);
					$subpartArray = array();
					$subpartArray['###FLAG_LIST###'] = '';
					$flagEntrySubpart = $this->cObj->getSubpart($template, '###FLAG_ENTRY###');
					$markerArray = array();

					if (trim($this->conf['englishFlagFile'])) {
						$flagsDir = dirname($GLOBALS['TSFE']->tmpl->getFileName(trim($this->conf['englishFlagFile']))) . '/';
					}
					if (!$flagsDir) {
						$flagsDir = t3lib_extMgm::extPath($this->extKey) . 'flags/';
					}
						// Set each icon. If the language is the current, red arrow is printed to the left. If the language is NOT found, the icon is dimmed.
					$flags = array();
					$firstItem = true;
					foreach ($languages as $key => $val) {
						$uri = $this->makeUrl($key);
						$current = ($GLOBALS['TSFE']->sys_language_uid == $this->languagesUids[$key]);
						$inactive = (($this->languagesUids[$key] && !$langArr[$this->languagesUids[$key]]) || (!$this->languagesUids[$key] && $GLOBALS['TSFE']->page['l18n_cfg']&1));
							// flag item
						if (($current && $this->conf['flag.']['CUR.']['doNotLinkIt']) || ($inactive && $this->conf['flag.']['INACT.']['doNotLinkIt'])) {
							$item = '<img src="' . $flagsDir .($this->conf['alternateFlags.'][$languages[$key]]?$this->conf['alternateFlags.'][$languages[$key]]:$languages[$key]).($inactive?'_d':'') . '.gif" title="'.$languagesLabels[$key].'" alt="'.$languagesLabels[$key].'"'.$this->pi_classParam('flag').' />';
						} else {
							$item = '<a href="' . htmlspecialchars($uri) . (trim($this->conf['target']) ? ('" target="' . trim($this->conf['target']) . '"') : '') . '"><img src="' . $flagsDir .($this->conf['alternateFlags.'][$languages[$key]]?$this->conf['alternateFlags.'][$languages[$key]]:$languages[$key]).($inactive?'_d':'') . '.gif" title="'.$languagesLabels[$key].'" alt="'.$languagesLabels[$key].'"'.$this->pi_classParam('flag').' /></a>';
						}
						if ($current) {
							$item = ($this->conf['flag.']['CUR.']['stdWrap.']) ? $this->cObj->stdWrap($item, $this->conf['flag.']['CUR.']['stdWrap.']) : $item;
						} elseif ($inactive) {
							$item = ($this->conf['flag.']['INACT.']['stdWrap.']) ? $this->cObj->stdWrap($item, $this->conf['flag.']['INACT.']['stdWrap.']) : $item;
						} else {
							$item = ($this->conf['flag.']['NO.']['stdWrap.']) ? $this->cObj->stdWrap($item, $this->conf['flag.']['NO.']['stdWrap.']) : $item;
						}
						if (!empty($item) && !$firstItem)  $item = ' ' . ($this->conf['flags.']['stdWrap.']['split.']['token']?$this->conf['flags.']['stdWrap.']['split.']['token']:'|') . ' ' . $item;
							// link item
						$label = $languagesLabels[$key];
						if (($current && $this->conf['link.']['CUR.']['doNotLinkIt']) || ($inactive && $this->conf['link.']['INACT.']['doNotLinkIt'])) {
							$linkItem = $label;
						} else {
							$linkItem = '<a href="' . htmlspecialchars($uri) . (trim($this->conf['target']) ? ('" target="' . trim($this->conf['target']) . '"') : '') . '">'.$label.'</a>';
						} 
						if ($current) {
							$linkItem = ($this->conf['link.']['CUR.']['stdWrap.']) ? $this->cObj->stdWrap($linkItem, $this->conf['link.']['CUR.']['stdWrap.']) : $linkItem;
						} elseif ($inactive) {
							$linkItem = ($this->conf['link.']['INACT.']['stdWrap.']) ? $this->cObj->stdWrap($linkItem, $this->conf['link.']['INACT.']['stdWrap.']) : $linkItem;
						} else {
							$linkItem = ($this->conf['link.']['NO.']['stdWrap.']) ? $this->cObj->stdWrap($linkItem, $this->conf['link.']['NO.']['stdWrap.']) : $linkItem;
						}
						if (!empty($linkItem) && !$firstItem)  $linkItem = ' ' . ($this->conf['links.']['stdWrap.']['split.']['token']?$this->conf['links.']['stdWrap.']['split.']['token']:'|') . ' ' . $linkItem;
						$firstItem = (!empty($item)) ? false : $firstItem;
						$markerArray['###FLAG###'] = $item;
						$markerArray['###LINK###'] = $this->conf['showLinkWithFlag'] ? $linkItem : '';
						$subpartArray['###FLAG_LIST###'] .= $this->cObj->substituteMarkerArrayCached($flagEntrySubpart, $markerArray, array(), array());
					}
					$subpartArray['###FLAG_LIST###'] = ($this->conf['flags.']['stdWrap.']) ? $this->cObj->stdWrap($subpartArray['###FLAG_LIST###'], $this->conf['flags.']['stdWrap.'])  : $subpartArray['###FLAG_LIST###'];
					$content = $this->cObj->substituteMarkerArrayCached($template, $markerArray, $subpartArray, array());
			}
		}
		$GLOBALS['TSFE']->linkVars = $this->linkVars;
		return $this->pi_wrapInBaseClass($content);
	}

	function local_add_vars2($vars,$path) {
		$res='';
		if (isset($vars) && is_array($vars)) {
			foreach ($vars as $key => $val) {
				if (!is_array($val)) {
					$res .= '&'.$path.'['.rawurlencode($key).']'.'='.rawurlencode($val);
				} else {
					$res .= $this->local_add_vars2($val, $path.'['.rawurlencode($key).']');
				}
			}
		}
		return $res;
	}
	
	function local_add_vars($vars, $varNames) {
		$res='';
		if (isset($vars) && is_array($vars)) {
			foreach ($vars as $key => $val) {
				if (is_array($val)) {
					if (!in_array($key,$varNames)) {
						$res .= $this->local_add_vars2($val, rawurlencode($key));
					}
				} else {
					if (($key != 'id') && ($key != 'type') && ($key != 'L') && !in_array($key,$varNames)) {
						$res .= '&'.rawurlencode($key).'='.rawurlencode($val);
					}
				}
			}
		}
		return $res;
	}
	
	function remove_vars($linkVars, $varNames) {
		$newLinkVars='';
		if (strcmp($linkVars,'')) {
			$p = explode('&',$linkVars);
			foreach($p as $k => $v) {
				if ((string)$v) {
					list($pName) = explode('=',$v,2);
					if(in_array($pName,$varNames)) unset($p[$k]);
				} else unset($p[$k]);
			}
		$newLinkVars = count($p) ? '&'.implode('&',$p) : '';
		}
		return $newLinkVars;
	}
	
	/**
	 * Buils a HTML drop-down selector of languages
	 *
	 * @param	array		An array where the values will be the texts of an <option> tags and keys will be the values of the tags
	 * @param	string		A value for the name attribute of the <select> tag
	 * @param	string		A value for the class attribute of the <select> tag
	 * @param	string		A value for the title attribute of the <select> tag
	 * @param	string		The value of the code of the entry to be pre-selected in the drop-down selector
	 * @param	boolean/string	If set to 1, an onchange attribute will be added to the <select> tag for immediate submit of the changed value; if set to other than 1, overrides the onchange script
	 * @return	string		A set of HTML <select> and <option> tags
	 */
	 
	function buildLanguageSelector($names, $name='L', $class='', $title='', $selected='', $submit=0) {
		$nameAttribute = (trim($name)) ? 'name="' . trim($name) . '" ' : '';
		$classAttribute = (trim($class)) ? 'class="' . trim($class) . '" ' : '';
		$titleAttribute = (trim($title)) ? 'title="' . trim($title) . '" ' : '';
		$onchangeAttribute = '';
		if ($submit) {
			if ($submit == 1) {
				$onchangeAttribute = 'onchange="' . trim($this->conf['onChangeAttribute']) . '" ';
			} else {
				$onchangeAttribute = 'onchange="';
				if ($this->conf['list.']['header']) {
					$onchangeAttribute .= 'if (this.options[this.selectedIndex].value == \'\') return;';
				}
				$onchangeAttribute .= $submit . '" ';
			}
		}
		$selector = '<select size="1" ' . $nameAttribute . $classAttribute . $titleAttribute . $onchangeAttribute . ' id="sr_language_menu_select">' . chr(10);
		$selected = (trim($selected)) ? trim($selected) : '';
		$selected = $selected ? $selected : key($names);
		if (count($names) > 0) {
			$selector .= $this->optionsConstructor($names, $selected);
			$selector .= '</select>' . chr(10);
		} else {
			$selector = '';
		}
		return $selector;
	}
	
	/**
	 * Builds a list of <option> tags
	 *
	 * @param	array		An array where the values will be the texts of an <option> tags and keys will be the values of the tags
	 * @param	string		A pre-selected value: if the value appears as a key, the <option> tag will bear a 'selected' attribute
	 * @return	string		A string of HTML <option> tags
	 */
	function optionsConstructor($names, $selected='') {
		$options = '';
			// Use a header, if configured
		if ($this->conf['list.']['header']) {
			$options .= '<option value ="">'.$this->pi_getLL('select_language').'</option>';
			if ($this->conf['list.']['separator'])	{
				$options .= '<option value ="">'.$this->conf['list.']['separator'].'</option>';
			}
		}
		foreach ($names as $langUid => $langOptions) {
			foreach ($langOptions as $value => $name) {
					// Don't show current language if showCurrent=0
				if ($selected != $value || $this->conf['list.']['showCurrent']) {
					$options  .= '<option value="'.$value.'"'.$this->pi_classParam('option-'.$langUid);
						// Don't pre-select language when using a header
					if (!$this->conf['list.']['header'] && $selected == $value) {
						$options  .= ' selected="selected"';
					}
					$options  .= '>'.$name.'</option>'.chr(10);
					$this->selectorEmpty = false;
				}
			}
		}
		return $options;
	}

	/**
	 * Gets the directory in which the website resides.
	 *
	 * @return    string        Either '/' or e.g. '/myWebsiteDir/'
	 */
	function getWebsiteDir() {
			// Standard is the webroot which is okay for non-realUrl sites in any case as there is a different handling in changing the language via SELECT box
		$websiteDir = '/';
			// For realURL we need the path segment after host and domain as set in config.baseURL 
		if ($this->realUrlLoaded) {
			$baseUrlParts = parse_url($GLOBALS['TSFE']->config['config']['baseURL']);
			$websiteDir = $baseUrlParts['path'];
		}
		return $websiteDir;
	}

	/**
	 * Makes the url
	 *
	 * @param	string	$key: the ordinal number of the language for which an url should be made
	 * @param	boolean	$noLVariable: if set, the url is built without the L variable
	 * @return   	string  the url
	 */
	function makeUrl($key, $noLVariable=0) {
		if ($this->languagesExternalUrls[$key]) {
			return $this->languagesExternalUrls[$key];
		}
		if (strstr($GLOBALS['TSFE']->linkVars, '&L=')) {
			$GLOBALS['TSFE']->linkVars = ereg_replace('&L=[0-9]*' , ($noLVariable ? '' : '&L='.$this->languagesUids[$key]), $GLOBALS['TSFE']->linkVars);
		} else {
			$GLOBALS['TSFE']->linkVars .= $noLVariable ? '' : '&L='.$this->languagesUids[$key];
		}
		if (!$this->rlmp_language_detectionLoaded) {
			$GLOBALS['TSFE']->linkVars = ereg_replace('&L=0' , '', $GLOBALS['TSFE']->linkVars);
		}
		$LD = $this->localTemplate->linkData($GLOBALS['TSFE']->page,'','','','',$this->forwardParams,'0');
		return $LD['totalURL'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_language_menu/pi1/class.tx_srlanguagemenu_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_language_menu/pi1/class.tx_srlanguagemenu_pi1.php']);
}
?>