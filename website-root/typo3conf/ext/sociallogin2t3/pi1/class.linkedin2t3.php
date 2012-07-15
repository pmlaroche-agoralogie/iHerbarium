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
require_once(t3lib_extmgm::extPath('sociallogin2t3', 'lib/oauth/linkedin.php'));

/**
 * 
 * @author	Ron Schoellmann <ron@netsinn.de>, Joost van Berckel <joost@contentonline.nl>
 * @package	TYPO3
 * @subpackage	tx_sociallogin2t3
 */
class Linkedin2t3 extends Sociallogin2t3_Base
{

    var $serviceProvider = "linkedin";
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
        if ($user->id)
        {
            foreach ($this->fields4Fetch as $key => $value)
            {
                $this->serviceValues[$key] = (string) $user->{$value};
            }
            $this->fe_usersValues = array_combine($this->fe_usersFields, $this->serviceValues);
            $this->fe_usersValues['tx_sociallogin2t3_id'] = (string) $user->id;
            $this->serviceUserId = (string) $user->id;
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

        //doesn't work with other logins
        //session_start();

        $linkedin = new LinkedIn($this->conf['consumerKey'],
                        $this->conf['consumerSecret'],
                        Sociallogin2t3_Base::getCurrentUrl()
                        . '?service=linkedin' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : ''));

        $linkedin->debug = false;

//        if (!isset($_GET['oauth_token']))
//        {
//            $linkedin->getRequestToken();
//
//            $_SESSION['linkedinRequestToken'] = $linkedin->request_token;
//
//            $loginButton = '<a href="' . $linkedin->generateAuthorizeUrl() . '">'
//                    . '<img src="/' . t3lib_extmgm::siteRelPath('sociallogin2t3') . 'res/linkedin-login-button.png" alt="sign in with linkedin" />'
//                    . '</a>';
//        }

        if ($this->isLoggedOutFromElse())
        {
            //echo "isLoggedOutFromElse";
            setcookie("linkedin_oauth_token", '', time() - 100);
            setcookie("linkedin_oauth_token_secret", '', time() - 100);
            setcookie('service', '', time() - 100);
            unset($_COOKIE['linkedin_oauth_token']);
            unset($_COOKIE['linkedin_oauth_token_secret']);
            unset($_COOKIE['service']);
        }

        if ($this->isLoggingOutFromHere())
        {
            //echo "isLoggingOutFromHere";
            setcookie("linkedin_oauth_token", '', time() - 100);
            setcookie("linkedin_oauth_token_secret", '', time() - 100);
            setcookie('service', '', time() - 100);
            setcookie('linkedin_request_token', '', time() - 100);
            setcookie('linkedin_request_token_secret', '', time() - 100);
            unset($_COOKIE['linkedin_oauth_token']);
            unset($_COOKIE['linkedin_oauth_token_secret']);
            unset($_COOKIE['service']);
            unset($_COOKIE['linkedin_request_token']);
            unset($_COOKIE['linkedin_request_token_secret']);

            $this->logoutUser();

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
                }
                else
                {
                    //$linkedin->request_token = $_SESSION['linkedinRequestToken'];
                    $linkedin->request_token = new Renamed_OAuthConsumer(
                                    $_COOKIE['linkedin_request_token'],
                                    $_COOKIE['linkedin_request_token_secret'],
                                    Sociallogin2t3_Base::getCurrentUrl()
                                    . '?service=linkedin' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : ''));

                    $linkedin->getAccessToken($_GET['oauth_verifier']);
                    setcookie('linkedin_oauth_token', $linkedin->request_token->key);
                    setcookie('linkedin_oauth_token_secret', $linkedin->request_token->secret);
                    setcookie('service', $_GET['service']);
                }

                //$user = $linkedin->getProfile("~:(id,first-name,last-name,headline,picture-url)");
                $user = $linkedin->getProfile("~:(id,first-name,last-name)");
                $user = simplexml_load_string($user);

            }
            elseif (isset($_GET['denied']))
            {
                // user denied access
                //$this->content[] = 'You must sign in first';
            }
            else
            {
                // user not logged in
            }
        }

        //process button:
        if ($this->isLoggingIn && $this->isLoggedInFromElse())
        {
            //if ($this->conf['showLogoutLink'] == 1) $this->content[] = '<a href="' . Sociallogin2t3_Base::getCurrentUrl() . '?logintype=logout">Logout</a>';
        }
        else
        {
            if (!isset($_GET['oauth_token']))
            {
                $linkedin->getRequestToken();
                //$_SESSION['linkedinRequestToken'] = $linkedin->request_token;
                setcookie('linkedin_request_token', $linkedin->request_token->key);
                setcookie('linkedin_request_token_secret', $linkedin->request_token->secret);
                $buttonPath = strlen($this->conf['customButton']) > 1 ? $this->conf['customButton'] : '/' . t3lib_extmgm::siteRelPath('sociallogin2t3') . 'res/linkedin-login-button.png';
                $loginButton = '<a href="' . $linkedin->generateAuthorizeUrl() . '">'
                        . '<img src="' . $buttonPath . '" alt="sign in with linkedin" />'
                        . '</a>';
                $this->content[] = '<div class="linkedinButton">' . $loginButton . '</div>';
            }
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
            throw new Exception('Ext sociallogin2t3: LinkedIn constants "fields4Fetch" & "fe_usersFields" need to have the same number of elements!');
        }

        if (!isset($this->conf['consumerKey']) || $this->conf['consumerKey'] == '')
        {
            throw new Exception('Ext sociallogin2t3: LinkedIn consumer key is not set in constants');
        }

        if (!isset($this->conf['consumerSecret']) || $this->conf['consumerSecret'] == '')
        {
            throw new Exception('Ext sociallogin2t3: LinkedIn consumer secret is not set in constants');
        }
    }

}

?>