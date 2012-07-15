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
require_once(t3lib_extmgm::extPath('sociallogin2t3', 'lib/facebook.php'));

/**

 * @author	Ron Schoellmann <ron@netsinn.de>, Joost van Berckel <joost@contentonline.nl>
 * @package	TYPO3
 * @subpackage	tx_sociallogin2t3
 */
class Facebook2t3 extends Sociallogin2t3_Base
{

    var $serviceProvider = "facebook";
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

        /*
          $publicFields = array('id', 'name', 'first_name', 'last_name', 'link', 'gender');
          // private fields need to be authorized separately
          $privateFields = array_diff($this->fields4Perms, $publicFields);
          $privateFields = array_unique($privateFields);
          $privateFields = implode(',', $privateFields);
         */

        // Create application instance
        $facebook = new Facebook(array(
                    'appId' => $this->conf['consumerKey'],
                    'secret' => $this->conf['consumerSecret'],
                    'cookie' => true,
                ));

        //needed?, check deletion
        if ($GLOBALS["TSFE"]->loginUser == 0 || (isset($_GET['logintype']) && $_GET['logintype'] == "logout"))
        {
            setcookie('fbs_' . $facebook->getAppId(), '', time() - 100);
            unset($_COOKIE['fbs_' . $facebook->getAppId()]);
        }


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

            $this->logoutUser();
        }

        /*
          // JS SDK, for more info, look here: http://github.com/facebook/connect-js
          $appId = $facebook->getAppId();
          $jsonSession = json_encode($session);
          $facebookLanguage = $this->conf['facebookLanguage'] ? $this->conf['facebookLanguage'] : 'en_US';

          $this->content[] = <<<FACEBOOKJSSDK
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
         */


        //button to show
//        if ($me || ($GLOBALS["TSFE"]->loginUser == 1))
        if ($session || ($GLOBALS["TSFE"]->loginUser == 1))
        {
//            $this->content[] = '
//            <a class="facebookLogout" href="' . $logoutUrl . '">
//              <img src="http://static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif">
//            </a>
//        ';
//            $this->content[] = '<a href="' . $this->getCurrentUrl() . '?logintype=logout">Logout</a>';
        }
        else
        {
            $buttonPath = strlen($this->conf['customButton']) > 1 ? $this->conf['customButton'] : '/' . t3lib_extmgm::siteRelPath('sociallogin2t3') . 'res/facebook-login-button.png';
            $this->content[] = ''
                    . '<div class="facebookButton">'
                    . '<a href="' . $facebook->getLoginUrl() . '">'
                    . '<img src="' . $buttonPath . '" alt="sign in with facebook" />'
                    . '</a>'
                    . '</div>';
//            $this->content[] = '
//            <div class="facebookLogin">
//              <fb:login-button perms="' . $privateFields . '"></fb:login-button>
//            </div>
//        ';
        }


        // Fill value arrays:
        if ($me)
        {
            foreach ($this->fields4Fetch as $key => $value)
            {
                $this->serviceValues[$key] = $me[$value];
            }
            $this->fe_usersValues = array_combine($this->fe_usersFields, $this->serviceValues);
            $this->fe_usersValues['tx_sociallogin2t3_id'] = $me['id'];
            $this->facebookUserId = $me['id'];
            if ($this->facebookUserId && $this->facebookUserId != '') $this->storeUser();
        }

        if ($this->debug)
        {
            foreach ($this->fe_usersValues as $key => $value)
            {
                $this->content[] = $key . ': ' . $value . '<br/>';
            }
        }
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
            throw new Exception('Ext sociallogin2t3: Facebook constants "fields4Fetch" & "fe_usersFields" need to have the same number of elements!');
        }

        if (!isset($this->conf['consumerKey']) || $this->conf['consumerKey'] == '')
        {
            throw new Exception('Ext sociallogin2t3: Facebook app id is not set in constants');
        }
        if (!isset($this->conf['consumerSecret']) || $this->conf['consumerSecret'] == '')
        {
            throw new Exception('Ext sociallogin2t3: Facebook secret is not set in constants');
        }
    }

}

?>