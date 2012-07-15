<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2003 Robert Lemke (rl@robertlemke.de)
	*      Maintainer since summer 2006: André Steiling <steiling@pilotprojekt.com>
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


	/**
	 * Main class for the plugin.
	 *
	 * @author Robert Lemke <rl@robertlemke.de>
	 * @author André Steiling <steiling@elemente.ms>
	 */
	class tx_rlmpflashdetection_pi1 extends tslib_pibase {
		var $prefixId		= 'tx_rlmpflashdetection_pi1';
		// Same as class name
		var $scriptRelPath	= 'pi1/class.tx_rlmpflashdetection_pi1.php'; // Path to this script relative to the extension dir.
		var $extKey			= 'rlmp_flashdetection'; // The extension key.


		/**
		 * Initialises the flashdection plugin: The JavaScript detection routines are added to the header
		 * of the HTML document and the HTML-code for embedding the Flash movie is returned.
		 *
		 * @param	[string]		$content: The current HTML content
		 * @param	[array]			$conf: The extension's configuration array
		 * @return	[string]		HTML code with the Flash movie embedded
		 */
		function main($content, $conf) {
			$this->conf = $conf;
			$this->pi_setPiVarDefaults();
			$this->pi_loadLL();

			// Normally we want to get the record of the flashmovie which is selected in the insert plugin content element.
			// But you may define an uid in your template which overrides this selection.
			$uid = $this->cObj->data['tx_rlmpflashdetection_flashmovie']?$this->cObj->data['tx_rlmpflashdetection_flashmovie']:$conf['conf.']['overrideUID'];

			// Read configuration from flash record
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_rlmpflashdetection_flashmovie', 'uid = '.intval($uid).$this->cObj->enableFields('tx_rlmpflashdetection_flashmovie'));
			$recordConf['conf.'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

			// Load TCA to check if file referneces are used ...
			t3lib_div::loadTCA('tx_rlmpflashdetection_flashmovie');
			$config = $GLOBALS['TCA']['tx_rlmpflashdetection_flashmovie'];
			
			// Use file references to files or prepend the filenames with upload path
			if ($config['columns']['flashmovie']['config']['uploadfolder']) {
				$recordFolder = $config['columns']['flashmovie']['config']['uploadfolder'].'/';
			} else {
				$recordFolder = '';
			}
			
			// Flash file
			if ($recordConf['conf.']['flashmovie'])	{
				$file = $recordFolder.$recordConf['conf.']['flashmovie'];
				// Bugfix for absolut paths 
				if (strstr($file, 'fileadmin/')) {
					list(,$file) = explode('fileadmin/', $file);
					$file = 'fileadmin/'.$file;
				}
				$this->basePath = dirname($file).'/';
				$recordConf['conf.']['flashmovie'] = $file;
			}
			
			// XML file
			if ($recordConf['conf.']['xmlfile'])	{
				$file = $recordFolder.$recordConf['conf.']['xmlfile'];
				// Bugfix for absolut paths 
				if (strstr($file, 'fileadmin/')) {
					list(,$file) = explode('fileadmin/', $file);
					$file = 'fileadmin/'.$file;
				}
				$recordConf['conf.']['xmlfile'] = $file;
			}
			
			// Alternative image
			if ($recordConf['conf.']['alternatepic'])	{
				$file = $recordFolder.$recordConf['conf.']['alternatepic'];
				// Bugfix for absolut paths 
				if (strstr($file, 'fileadmin/')) {
					list(,$file) = explode('fileadmin/', $file);
					$file = 'fileadmin/'.$file;
				}
				$recordConf['conf.']['alternatepic'] = $file;
			}

			// If configuration via TypoScript is provided, merge the arrays
			if (is_array($conf)) {
				$movieConf = t3lib_div::array_merge_recursive_overrule($recordConf, $conf); 
				// Adding stdWrap to xmlfile option
				$movieConf['conf.']['xmlfile'] = $this->cObj->stdWrap($movieConf['conf.']['xmlfile'],$movieConf['conf.']['xmlfile.']);
			}

			// Get HTML code which embeds the selected movie record
			if (is_array($movieConf['conf.'])) { $content = $this->getFlashHTMLCode($movieConf); }

			// return
			return $this->pi_wrapInBaseClass($content);
		}


		/**
		 * Create the HTML output. A JavaScript detection routine is included which will either
		 * output HTML code for showing the flash movie or an alternative picture.
		 *
		 * @param	[array]			$movieConf: The configuration
		 * @return	[string]		HTML output
		 */
		function getFlashHTMLCode($conf) {
			// Required version
			$conf['conf.']['requiresflashversion'] = t3lib_div::intInRange($conf['conf.']['requiresflashversion'], 2, 1000);

			// Inform the user witch version is required
			$reqVersion = str_replace('###VERSION###', $conf['conf.']['requiresflashversion'], $this->pi_getLL('no_flash_text'));

			// Create alernative image or showing only the reqVersion information form above
			if ($conf['conf.']['alternatepic']) {
				$image['file']				= $conf['conf.']['alternatepic'];
				$image['altText']			= $conf['conf.']['alternatetext']?$conf['conf.']['alternatetext']:$reqVersion;
				$image['titleText']			= $conf['conf.']['alternatetext']?$conf['conf.']['alternatetext']:$reqVersion;
				$image['file.']['width']	= $conf['conf.']['width']?$conf['conf.']['width']:'400';
				$image['file.']['height']	= $conf['conf.']['height']?$conf['conf.']['height']:'300';
				$alternateImage = $this->cObj->IMAGE($image);
				if ($conf['conf.']['alternatelink']) {
					$alternateImage	= $this->cObj->typoLink($alternateImage, array ('parameter' => $conf['conf.']['alternatelink']));
				}
			} else $alternateImage	= $reqVersion;

			// Create HTML / JavaScript code for the additional parameters
			$additionalParamsCode = '';
			if ($conf['conf.']['additionalparams'] != '') {
				$tmpArr = t3lib_div::trimExplode(chr(10), $conf['conf.']['additionalparams'], 1);
				while(list($key, $val) = each($tmpArr)) {
					list ($name, $value)	 = t3lib_div::trimExplode (',',$val);
					$additionalParamsCode	.= '"'.htmlspecialchars($name).'", "'.htmlspecialchars($value).'", '.chr(10);
				}
			}

			// Include Adobe Flash Player Version Detection
			$GLOBALS['TSFE']->additionalHeaderData ['tx_rlmpflashdetection'] = '<script type="text/JavaScript" src="'.t3lib_extMgm::siteRelPath("rlmp_flashdetection").'res/AC_OETags.js"></script>';

			// Create the flash stuff
			$flash = '
				<script type="text/javascript">
					/*<![CDATA[*/
				<!--
					var hasRightVersion = DetectFlashVer('.$conf['conf.']['requiresflashversion'].', 0, 0);
					if (hasRightVersion && "'.htmlspecialchars(preg_replace('/\.swf$/','',$conf['conf.']['flashmovie'])).'"!="") {  // if we\'ve detected an acceptable version and if a Flash movie was defined
						AC_FL_RunContent(
							"movie", "'.htmlspecialchars(preg_replace('/\.swf$/','',$conf['conf.']['flashmovie'])).'",
							"width", "'. ($conf['conf.']['width']?htmlspecialchars($conf['conf.']['width']):'400').'",
							"height", "'.($conf['conf.']['height']?htmlspecialchars($conf['conf.']['height']):'300').'",
							"loop", "'.($conf['conf.']['flashloop']?'true':'false').'",
							"quality", "'.($conf['conf.']['quality']?'low':'high').'",
							"menu", "'.($conf['conf.']['displaymenu']?'true':'false').'",
							"base", "'.t3lib_div::getIndpEnv('TYPO3_REQUEST_DIR').$this->basePath.'",
							'.($conf['conf.']['xmlfile']?(' "flashvars", "'.htmlspecialchars($conf['general.']['xmlFileFlashParamName'].'='.$conf['conf.']['xmlfile']).'",'):'').'
							'.$additionalParamsCode.'
							"allowScriptAccess", "'.htmlspecialchars($conf['general.']['allowScriptAccess']).'",
							"type", "application/x-shockwave-flash",
							"codebase", "'.htmlspecialchars($conf['general.']['codeBase']).'",
							"pluginspage", "'.htmlspecialchars($conf['general.']['pluginsPage']).'"
						);
					} else {  // flash is to old or we can\'t detect the plugin
						var alternateContent = \''.str_replace(array('</',"'"), array('<\/',"\\'"), $alternateImage).'\';
						document.write(alternateContent);  // insert non-flash content
					}
				// -->
					/*]]>*/
				</script>
				<noscript>'.$alternateImage.'</noscript>
			';
			// return
			return $flash;
		}


	} // class


	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rlmp_flashdetection/pi1/class.tx_rlmpflashdetection_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rlmp_flashdetection/pi1/class.tx_rlmpflashdetection_pi1.php']);
	}
?>