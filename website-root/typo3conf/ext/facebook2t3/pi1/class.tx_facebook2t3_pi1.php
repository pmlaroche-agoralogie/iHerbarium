<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Ron Schoellmann <ron@netsinn.de>
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
require_once(t3lib_extmgm::extPath('facebook2t3', 'lib/facebook.php'));

/**
 * Plugin 'Facebook Connect to Typo3' for the 'facebook2t3' extension.
 *
 * @author	Ron Schoellmann <ron@netsinn.de>
 * @package	TYPO3
 * @subpackage	tx_facebook2t3
 */
class tx_facebook2t3_pi1 extends tslib_pibase
{

    var $prefixId = 'tx_facebook2t3_pi1';  // Same as class name
    var $scriptRelPath = 'pi1/class.tx_facebook2t3_pi1.php'; // Path to this script relative to the extension dir.
    var $extKey = 'facebook2t3'; // The extension key.
    var $pi_checkCHash = true;
    var $tableName = 'fe_users';
    var $facebookFields4Perms = array();
    var $facebookFields4Fetch = array();
    var $facebookValues = array();
    var $fe_usersFields = array();
    var $fe_usersValues = array();

    /**
     * The main method of the PlugIn
     *
     * Partly based on Facebook PHP API
     * @param	string		$content: The PlugIn content
     * @param	array		$conf: The PlugIn configuration
     * @return	The content that is displayed on the website
     */
    function main($content, $conf)
    {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();

        global $TYPO3_DB;

        $this->pi_USER_INT_obj = 1; //no cHash params

        $this->facebookFields4Perms = explode(',', $this->conf['facebookFields4Perms']);
        foreach ($this->facebookFields4Perms as $key => $value)
        {
            $this->facebookFields4Perms[$key] = trim($value);
        }

        $this->facebookFields4Fetch = !isset($this->conf['facebookFields4Fetch']) || $this->conf['facebookFields4Fetch'] == '' ? $this->facebookFields4Perms : explode(',', $this->conf['facebookFields4Fetch']);
        foreach ($this->facebookFields4Fetch as $key => $value)
        {
            $this->facebookFields4Fetch[$key] = trim($value);
        }

        $this->fe_usersFields = explode(',', $this->conf['fe_usersFields']);
        foreach ($this->fe_usersFields as $key => $value)
        {
            $this->fe_usersFields[$key] = trim($value);
        }


        $this->checkPrerequisites();

        $publicFields = array('id', 'name', 'first_name', 'last_name', 'link', 'gender');
        // private fields need to be authorized separately
        $privateFields = array_diff($this->facebookFields4Perms, $publicFields);
        $privateFields = array_unique($privateFields);
        $privateFields = implode(',', $privateFields);

        $content = array();

        // Create application instance
        $facebook = new Facebook(array(
                    'appId' => $this->conf['appId'],
                    'secret' => $this->conf['secret'],
                    'cookie' => true,
                ));

        // We may or may not have this data based on a $_GET or $_COOKIE based session.
        //
        // If we get a session here, it means we found a correctly signed session using
        // the Application Secret only Facebook and the Application know. We dont know
        // if it is still valid until we make an API call using the session. A session
        // can become invalid if it has already expired (should not be getting the
        // session back in this case) or if the user logged out of Facebook.
        $session = $facebook->getSession();

        $me = null;

        // Session based API call.
        if ($session)
        {
            try
            {
                $uid = $facebook->getUser();
                $me = $facebook->api('/me');
            } catch (FacebookApiException $e)
            {
                error_log($e);
            }
        }

        // login or logout url will be needed depending on current user state
        if ($me)
        {
            $next = $this->getCurrentUrl() . '?logintype=logout';
            $logoutUrl = $facebook->getLogoutUrl(array('next' => $next));
        }
        else
        {
            $loginUrl = $facebook->getLoginUrl();

            // not sure, if all of these lines are needed:
            $fe_user = $GLOBALS['TSFE']->fe_user; /* @var $fe_user tslib_feUserAuth */
            $fe_user->logoff();
            unset($fe_user->user);
            $GLOBALS["TSFE"]->loginUser = 0;
            setcookie("fe_typo_user", "", time() - 3600, "/", $_SERVER["HTTP_HOST"]);
        }

        // JS SDK, for more info, look here: http://github.com/facebook/connect-js
        $appId = $facebook->getAppId();
        $jsonSession = json_encode($session);
        $facebookLanguage = $this->conf['facebookLanguage'] ? $this->conf['facebookLanguage'] : 'en_US';

        $content[] = <<<FACEBOOKJSSDK
            <div id="fb-root"></div>
            <script>
                window.fbAsyncInit = function() {
                    FB.init({
                        appId   : '$appId',
                        session : $jsonSession, // don't refetch the session when PHP already has it
                        status  : true, // check login status
                        cookie  : true, // enable cookies to allow the server to access the session
                        xfbml   : true // parse XFBML
                    });

                    // whenever the user logs in, we refresh the page
                    FB.Event.subscribe('auth.login', function() {
                        window.location.reload();
                    });
                };

                (function() {
                    var e = document.createElement('script');
                    e.type = 'text/javascript';
                    e.src = document.location.protocol + '//connect.facebook.net/$facebookLanguage/all.js';
                    e.async = true;
                    document.getElementById('fb-root').appendChild(e);
                }());
            </script>
FACEBOOKJSSDK;

        if ($me) $content[] = '
            <a class="facebookLogout" href="' . $logoutUrl . '">
              <img src="http://static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif">
            </a>
        ';

        else $content[] = '
            <div class="facebookLogin">
              <fb:login-button perms="' . $privateFields . '"></fb:login-button>
            </div>
        ';

        // Fill value arrays:
        if ($me)
        {
            foreach ($this->facebookFields4Fetch as $key => $value)
            {
                $this->facebookValues[$key] = $me[$value];
            }
            $this->fe_usersValues = array_combine($this->fe_usersFields, $this->facebookValues);
            $this->fe_usersValues['tx_facebook2t3_id'] = $me['id'];
            $this->facebookUserId = $me['id'];
            if ($this->facebookUserId && $this->facebookUserId != '') $this->storeUser();
        }

        // for debugging:
//        foreach ($this->fe_usersValues as $key => $value)
//        {
//            $content[] = $key . ': ' . $value . '<br/>';
//        }

        return $this->pi_wrapInBaseClass(implode('', $content));
    }

    /**
     * Check prerequisites and exit with statement if not met
     */
    function checkPrerequisites()
    {
        if (!function_exists('curl_init'))
        {
            throw new Exception('Ext facebook2t3: libcurl package for PHP is needed.');
        }
        if (!function_exists('json_decode'))
        {
            throw new Exception('Ext facebook2t3: JSON PHP extension is needed.');
        }

        $countFf4P = count($this->facebookFields4Perms);
        $countFf4F = count($this->facebookFields4Fetch);
        $countF_uF = count($this->fe_usersFields);
        if ($countFf4P != $countFf4F || $countFf4F != $countF_uF)
        {
            throw new Exception('Ext facebook2t3: constants "facebookFields4Perms", "facebookFields4Fetch" & "fe_usersFields" need to have the same number of elements!');
        }

        if (!isset($this->conf['appId']) || $this->conf['appId'] == '')
        {
            throw new Exception('Ext facebook2t3: Facebook app id is not set in constants');
        }
        if (!isset($this->conf['secret']) || $this->conf['secret'] == '')
        {
            throw new Exception('Ext facebook2t3: Facebook secret is not set in constants');
        }
    }

    /**
     * Amends fe_usersValues and inserts or updates its values to table fe_users
     * @global <type> $TYPO3_DB
     */
    function storeUser()
    {
        global $TYPO3_DB;

        // some additional values:
        $this->fe_usersValues['pid'] = $this->conf['usersPid'];

        // if there is no username defined in constants, set email as username (if existent), else Facebook id
        if (!isset($this->fe_usersValues['username'])) $this->fe_usersValues['username'] = $this->fe_usersValues['mail'] ? $this->fe_usersValues['mail'] : $this->facebookUserId;
        $this->fe_usersValues['tstamp'] = time();
        $this->fe_usersValues['usergroup'] = $this->conf['userGroup'];

        $where = 'tx_facebook2t3_id=' . $TYPO3_DB->quoteStr($this->fe_usersValues['tx_facebook2t3_id'], $this->tableName);

        $result = $TYPO3_DB->exec_SELECTquery('uid', $this->tableName, $where, '', '', '');
        if ($TYPO3_DB->sql_num_rows($result) > 0) $TYPO3_DB->exec_UPDATEquery($this->tableName, $where, $this->fe_usersValues);
        else
        {
            $this->fe_usersValues['password'] = md5($this->fe_usersValues['name'] . time());
            $this->fe_usersValues['crdate'] = time();
            $TYPO3_DB->exec_INSERTquery($this->tableName, $this->fe_usersValues);
        }

        $this->loginUser();
    }

    /**
     * Logs user in after authorized via Facebook
     * Based on fbconnect by Søren Thing Andersen.
     *
     * @global  $TYPO3_DB
     */
    function loginUser()
    {
        global $TYPO3_DB;

        $where = 'tx_facebook2t3_id=' . $TYPO3_DB->quoteStr($this->fe_usersValues['tx_facebook2t3_id'], $this->tableName);
        $result = $TYPO3_DB->exec_SELECTquery('*', $this->tableName, $where, '', '', '');
        if ($result && ($aUser = $TYPO3_DB->sql_fetch_assoc($result)))
        {
            $fe_user = $GLOBALS['TSFE']->fe_user; /* @var $fe_user tslib_feUserAuth */
            unset($fe_user->user);
            $fe_user->createUserSession($aUser);
            $fe_user->loginSessionStarted = TRUE;
            $fe_user->user = $fe_user->fetchUserSession();
            $GLOBALS["TSFE"]->loginUser = 1;
        }
    }

    /**
     * Returns the current URL
     * Based on Facebook PHP API
     *
     * @return String the current URL
     */
    protected function getCurrentUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUrl);

        // use port if non default
        $port =
                isset($parts['port']) &&
                (($protocol === 'http://' && $parts['port'] !== 80) ||
                ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';

        return $protocol . $parts['host'] . $port . $parts['path'];
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/facebook2t3/pi1/class.tx_facebook2t3_pi1.php'])
{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/facebook2t3/pi1/class.tx_facebook2t3_pi1.php']);
}
?>