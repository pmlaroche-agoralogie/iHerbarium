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

/**
 * @author	Ron Schoellmann <ron@netsinn.de>, Joost van Berckel <joost@contentonline.nl>
 * @package	TYPO3
 * @subpackage	tx_sociallogin2t3
 */
class Sociallogin2t3_Base extends tslib_pibase
{

    var $tableName = 'fe_users';
    var $fields4Fetch = array();
    var $serviceValues = array();
    var $fe_usersFields = array();
    var $fe_usersValues = array();
    var $debug = false;
    var $content = array();


    /*
     * 4 states to be checked:
     *
     * isLoggingIn: logging in via this page
     * isLoggedInFromElse: logged in on other t3 page or via previous request from here
     *
     * isLoggingOutFromHere: logging out via this script
     * isLoggedOutFromElse: logged out on other t3 page (= not logged in)
     */

    function isLoggingIn()
    {
        $requestFromHere = ($_GET['service'] == $this->serviceProvider || $_COOKIE['service'] == $this->serviceProvider) ? true : false;

        $oauth_tokenAvailable = (isset($_GET['oauth_token']) || (isset($_COOKIE[$this->serviceProvider . '_oauth_token']) && isset($_COOKIE[$this->serviceProvider . '_oauth_token_secret']))) ? true : false;

        return ($requestFromHere && $oauth_tokenAvailable) ? true : false;
    }

    function isLoggedInFromElse()
    {
        $loggedIn = ($GLOBALS["TSFE"]->loginUser == 1) ? true : false;
        $cookieSet = (isset($_COOKIE[$this->serviceProvider . '_oauth_token']) || isset($_COOKIE[$this->serviceProvider . '_oauth_token_secret'])) ? true : false;

        return ($loggedIn || $cookieSet) ? true : false;
    }

    function isLoggingOutFromHere()
    {
        // user clicked logout button
        return (isset($_GET['logintype']) && $_GET['logintype'] == "logout") ? true : false;
    }

    function isLoggedOutFromElse()
    {
        return ($GLOBALS["TSFE"]->loginUser == 0) ? true : false;
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
        $this->fe_usersValues['tx_sociallogin2t3_service_provider'] = $this->serviceProvider;

        // if there is no username defined in constants, set email as username (if existent), else service provider id
        if (!isset($this->fe_usersValues['username'])) $this->fe_usersValues['username'] = $this->fe_usersValues['mail'] ? $this->fe_usersValues['mail'] : $this->serviceUserId;
        $this->fe_usersValues['tstamp'] = time();
        $this->fe_usersValues['usergroup'] = $this->conf['userGroup'];

        $where = 'tx_sociallogin2t3_id="' . $TYPO3_DB->quoteStr($this->fe_usersValues['tx_sociallogin2t3_id'], $this->tableName) . '"';

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
     * Logs user in after authorized via OAuth
     * Based on fbconnect by Søren Thing Andersen.
     *
     * @global  $TYPO3_DB
     */
    function loginUser()
    {
        global $TYPO3_DB;

        $where = 'tx_sociallogin2t3_id="' . $TYPO3_DB->quoteStr($this->fe_usersValues['tx_sociallogin2t3_id'], $this->tableName) . '"';
        $result = $TYPO3_DB->exec_SELECTquery('*', $this->tableName, $where, '', '', '');
        if ($result && ($aUser = $TYPO3_DB->sql_fetch_assoc($result)))
        {
            $fe_user = $GLOBALS['TSFE']->fe_user; /* @var $fe_user tslib_feUserAuth */
            unset($fe_user->user);
            $fe_user->createUserSession($aUser);
            $fe_user->loginSessionStarted = TRUE;
            $fe_user->user = $fe_user->fetchUserSession();
            $GLOBALS["TSFE"]->loginUser = 1;

            $this->loginSessionStarted = TRUE;

            //relocate in order to get logged in state for TYPO3
            if (isset($_GET['oauth_token']) || isset($_GET['session'])) header("Location: " . self::getCurrentUrl()
                        . (isset($_GET['id']) ? '?id=' . $_GET['id'] : ''));
        }
    }

    
    function logoutUser()
    {
        // not sure, if all of these lines are needed:
        $fe_user = $GLOBALS['TSFE']->fe_user; /* @var $fe_user tslib_feUserAuth */
        $fe_user->logoff();
        unset($fe_user->user);
        $GLOBALS["TSFE"]->loginUser = 0;
        setcookie("fe_typo_user", "", time() - 3600, "/", $_SERVER["HTTP_HOST"]);
    }

    /**
     * Returns the current URL
     * Based on Facebook PHP API
     *
     * @return String the current URL
     */
    public static function getCurrentUrl()
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

?>