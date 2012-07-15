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
require_once(t3lib_extmgm::extPath('sociallogin2t3', 'lib/twitter-async/EpiCurl.php'));
require_once(t3lib_extmgm::extPath('sociallogin2t3', 'lib/twitter-async/EpiOAuth.php'));
require_once(t3lib_extmgm::extPath('sociallogin2t3', 'lib/twitter-async/EpiTwitter.php'));

/**
 *
 * @author	Ron Schoellmann <ron@netsinn.de>, Joost van Berckel <joost@contentonline.nl>
 * @package	TYPO3
 * @subpackage	tx_sociallogin2t3
 */
class Twitter2t3 extends Sociallogin2t3_Base
{

    var $serviceProvider = "twitter";
    var $debug = false;

    function __construct($conf)
    {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();

        if ($this->debug)
        {
            echo '<pre>$conf:';
            print_r($conf);
            echo '</pre><hr/>';
        }

        $this->fields4Fetch = explode(',', $this->conf['fields4Fetch']);
        foreach ($this->fields4Fetch as $key => $value)
        {
            $this->fields4Fetch[$key] = trim($value);
        }

        $this->fe_usersFields = explode(',', $this->conf['fe_usersFields']);
        foreach ($this->fe_usersFields as $key => $value)
        {
            $this->fe_usersFields[$key] = trim($value);
        }

        $this->checkServicePrerequisites();

        $user = $this->doOauthRequest();

        // Fill value arrays:
        if ($user)
        {
            foreach ($this->fields4Fetch as $key => $value)
            {
                $this->serviceValues[$key] = $user[$value];
            }
            $this->fe_usersValues = array_combine($this->fe_usersFields, $this->serviceValues);
            $this->fe_usersValues['tx_sociallogin2t3_id'] = $user['id'];
            $this->serviceUserId = $user['id'];
            if ($this->serviceUserId && $this->serviceUserId != '') $this->storeUser();
        }
    }

    /**
     * OAuth stuff
     *
     * @return array user info
     */
    function doOauthRequest()
    {
        $user = false;
        $loginButton = '';

        $oauth = new EpiTwitter($this->conf['consumerKey'], $this->conf['consumerSecret']);

        $buttonPath = strlen($this->conf['customButton']) > 1 ? $this->conf['customButton'] : '/' . t3lib_extmgm::siteRelPath('sociallogin2t3') . 'res/twitter-login-button.png';

        $loginButton = '<a href="' . $oauth->getAuthorizeUrl(null, array('oauth_callback' => $this->getCurrentUrl()
                    . '?service=twitter' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : ''))) . '">'
                . '<img src="' . $buttonPath . '" alt="sign in with twitter" />'
                . '</a>';

        if ($this->isLoggedOutFromElse())
        {
            //echo "isLoggedOutFromElse";
            setcookie("twitter_oauth_token", '', time() - 100);
            setcookie("twitter_oauth_token_secret", '', time() - 100);
            setcookie('service', '', time() - 100);
            unset($_COOKIE['twitter_oauth_token']);
            unset($_COOKIE['twitter_oauth_token_secret']);
            unset($_COOKIE['service']);
        }

        if ($this->isLoggingOutFromHere())
        {
            //echo "isLoggingOutFromHere";
            setcookie("twitter_oauth_token", '', time() - 100);
            setcookie("twitter_oauth_token_secret", '', time() - 100);

            setcookie('service', '', time() - 100);
            unset($_COOKIE['twitter_oauth_token']);
            unset($_COOKIE['twitter_oauth_token_secret']);
            unset($_COOKIE['service']);

            $this->logoutUser();

            //$this->content[] = $loginButton;
        }
        else
        {

            if ($this->isLoggingIn())
            {
                //echo "isLoggingIn";
                // user accepted access

                if ($this->isLoggedInFromElse())
                {
                    //echo "isLoggedInFromElse";
                    // user switched pages and came back or got here directly, stilled logged in
                    $oauth->setToken($_COOKIE['twitter_oauth_token'], $_COOKIE['twitter_oauth_token_secret']);
                }
                else
                {
                    // user comes from twitter
                    $oauth->setToken($_GET['oauth_token']);
                    $token = $oauth->getAccessToken(array('oauth_verifier' => $_GET['oauth_verifier']));
                    setcookie('twitter_oauth_token', $token->oauth_token);
                    setcookie('twitter_oauth_token_secret', $token->oauth_token_secret);
                    setcookie('service', $_GET['service']);
                    $oauth->setToken($token->oauth_token, $token->oauth_token_secret);
                }

                $user = $oauth->get_accountVerify_credentials();

                if ($this->debug)
                {
                    echo '<pre>$user:';
                    print_r($user);
                    echo '</pre><hr/>';
                }

                //if ($this->conf['showLogoutLink'] == 1 )$this->content[] = '<a href="' . $this->getCurrentUrl() . '?logintype=logout">Logout</a>';
            }
            elseif (isset($_GET['denied']))
            {
                // user denied access
                //$this->content[] = 'You must sign in first';
            }
            else
            {
                // user not logged in
                //if(!$this->isLoggedInFromElse()) $this->content[] = $loginButton;
            }
        }

        //process button:
        if ($this->isLoggingIn && $this->isLoggedInFromElse())
        {
            //if ($this->conf['showLogoutLink'] == 1) $this->content[] = '<a href="' . $this->getCurrentUrl() . '?logintype=logout">Logout</a>';
        }
        else
        {
            $this->content[] = '<div class="twitterButton">' . $loginButton . '</div>';
        }

        return $user;
    }

    /**
     * Check prerequisites and exit with statement if not met
     */
    function checkServicePrerequisites()
    {
        $countFf4F = count($this->fields4Fetch);
        $countF_uF = count($this->fe_usersFields);

        if ($countFf4F != $countF_uF)
        {
            throw new Exception('Ext sociallogin2t3: Twitter constants "fields4Fetch" & "fe_usersFields" need to have the same number of elements!');
        }

        if (!isset($this->conf['consumerKey']) || $this->conf['consumerKey'] == '')
        {
            throw new Exception('Ext sociallogin2t3: Twitter consumer key is not set in constants');
        }

        if (!isset($this->conf['consumerSecret']) || $this->conf['consumerSecret'] == '')
        {
            throw new Exception('Ext sociallogin2t3: Twitter consumer secret is not set in constants');
        }
    }

}

?>