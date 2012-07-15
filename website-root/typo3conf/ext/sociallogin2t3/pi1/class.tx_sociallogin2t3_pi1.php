<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Ron Schoellmann <ron@netsinn.de>,
 *           Joost van Berckel <joost@contentonline.nl>
 * 
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
 * ************************************************************* */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */
require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extmgm::extPath('sociallogin2t3', 'pi1/class.sociallogin2t3_base.php'));

/**
 * Plugin 'Social Login to TYPO3' for the 'sociallogin2t3' extension.
 *
 * @author	Ron Schoellmann, Joost van Berckel <ron@netsinn.de, joost@contentonline.nl>
 * @package	TYPO3
 * @subpackage	tx_sociallogin2t3
 */
class tx_sociallogin2t3_pi1 extends tslib_pibase
{

    var $prefixId = 'tx_sociallogin2t3_pi1';  // Same as class name
    var $scriptRelPath = 'pi1/class.tx_sociallogin2t3_pi1.php'; // Path to this script relative to the extension dir.
    var $extKey = 'sociallogin2t3'; // The extension key.
    var $pi_checkCHash = true;
    var $content = array();
    var $debug = false;

    /**
     * The main method of the PlugIn
     *
     * @param	string		$content: The PlugIn content
     * @param	array		$conf: The PlugIn configuration
     * @return	The content that is displayed on the website
     */
    function main($content, $conf)
    {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();

        if ($this->debug)
        {
            error_reporting(E_ALL ^ E_NOTICE);
            echo '<pre>$conf:';
            print_r($conf);
            echo '</pre><hr/>';
        }

        $this->pi_USER_INT_obj = 1; //no cHash params

        $this->checkPrerequisites();

        if ($GLOBALS["TSFE"]->loginUser == 0)
        {

            if ($conf['facebook.']['includeIt'] == 1)
            {
                require_once(t3lib_extmgm::extPath('sociallogin2t3', 'pi1/class.facebook2t3.php'));
                $facebook = new Facebook2t3($conf['facebook.']);
                $this->content[] = implode('', $facebook->content);
            }

            if ($conf['hyves.']['includeIt'] == 1)
            {
                require_once(t3lib_extmgm::extPath('sociallogin2t3', 'pi1/class.hyves2t3.php'));
                $hyves = new Hyves2t3($conf['hyves.']);
                $this->content[] = implode('', $hyves->content);
            }

            if ($conf['twitter.']['includeIt'] == 1)
            {
                require_once(t3lib_extmgm::extPath('sociallogin2t3', 'pi1/class.twitter2t3.php'));
                $twitter = new Twitter2t3($conf['twitter.']);
                $this->content[] = implode('', $twitter->content);
            }

            if ($conf['linkedin.']['includeIt'] == 1)
            {
                require_once(t3lib_extmgm::extPath('sociallogin2t3', 'pi1/class.linkedin2t3.php'));
                $linkedin = new Linkedin2t3($conf['linkedin.']);
                $this->content[] = implode('', $linkedin->content);
            }
        }
        else
        {
            if ($this->conf['showLogoutLink'] == 1) $this->content[] = '<a href="' . Sociallogin2t3_Base::getCurrentUrl() . '?logintype=logout">Logout</a>';
        }

        return $this->pi_wrapInBaseClass(implode('', $this->content));
    }

    /**
     * Check prerequisites and exit with statement if not met
     */
    function checkPrerequisites()
    {

        if (!function_exists('curl_init'))
        {
            throw new Exception('Ext sociallogin2t3: libcurl package for PHP is needed.');
        }

        if (!function_exists('json_decode'))
        {
            throw new Exception('Ext sociallogin2t3: JSON PHP extension is needed.');
        }

        if (!function_exists('simplexml_load_string'))
        {
            throw new Exception('Ext sociallogin2t3: SimpleXML extension is needed.');
        }

//        if (!function_exists('getallheaders'))
//        {
//            throw new Exception('Ext sociallogin2t3: Apache extension is needed.');
//        }

        /*
          openssl for RSA-SHA1 support
         */
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sociallogin2t3/pi1/class.tx_sociallogin2t3_pi1.php'])
{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sociallogin2t3/pi1/class.tx_sociallogin2t3_pi1.php']);
}
?>