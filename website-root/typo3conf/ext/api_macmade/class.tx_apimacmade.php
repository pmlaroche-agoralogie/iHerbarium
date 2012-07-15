<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2004 macmade.net
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is 
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Contains the Developer API (api_macmade).
 * 
 * The goal of this API is to provide to the Typo3 developers community
 * some useful functions, to help in the process of extension development.
 * 
 * It includes functions, for frontend, backend, databases and miscellaneous
 * development.
 * 
 * It's not here to replace any of the existing TYPO3 core class or
 * function. It just try to complete them by providing a quick way to
 * develop extensions.
 * 
 * Please take a look at the manual for a complete description of this API.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     4.6
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - INTERNAL
 *              function tx_apimacmade( &$pObj )
 *              function versionError( $version = false )
 *              function errorMsg( $method, $message, $line )
 *              function getPhp5Class( $section, $args = array() )
 *              function &newInstance( $className, $args = array() )
 * 
 * SECTION:     2 - FE
 *              function fe_mergeTSconfFlex( $mapArray, $tsArray, $flexRes )
 *              function fe_initTemplate( $templateFile )
 *              function fe_renderTemplate( $templateMarkers, $templateSection )
 *              function fe_makeStyledContent( $element, $className, $content = false, $piClass = 1, $htmlSpecialChars = false, $startTagOnly = false, $params = array() )
 *              function fe_setInternalVars( $results_at_a_time = false, $maxPages = false, $searchFieldList = false, $orderByList = false )
 *              function fe_buildSwapClassesJSCode( $class1, $class2 )
 *              function fe_makeSwapClassesJSLink( $elementId, $content = false, $htmlSpecialChars = false, $startTagOnly = false, $params = array() )
 *              function fe_createImageObjects( $imgRefs, $conf, $imgPath = false )
 *              function fe_linkTP( $str, $urlParameters = array(), $cache = 0, $altPageId = 0, $conf = array() )
 *              function fe_linkTP_keepPIvars( $str, $overrulePIvars = array(), $cache = 0, $clearAnyway = 0, $altPageId=0 )
 *              function fe_linkTP_keepPIvars_url( $overrulePIvars = array(), $cache = 0, $clearAnyway = 0, $altPageId = 0 )
 *              function fe_linkTP_unsetPIvars( $str, $overrulePIvars = array(), $unsetPIvars = array(), $cache = 0, $clearAnyway = 0, $altPageId = 0 )
 *              function fe_linkTP_unsetPIvars_url( $overrulePIvars = array(), $unsetPIvars = array(), $cache = 0, $clearAnyway = 0, $altPageId = 0 )
 *              function fe_typoLinkParams( $params, $keepPiVars = false )
 *              function fe_initFeAdmin( $conf, $table, $pid, $feAdminConf, $create = 1, $edit = 0, $delete = 0, $infomail = 0, $fe_userOwnSelf = 0, $fe_userEditSelf = 0, $debug = 0, $defaultCmd = 'create', $confKey = 'fe_adminLib' )
 *              function fe_createInput( $type, $name, $feAdminConf, $feAdminSection, $number = 1, $params = array(), $defaultValue = 0, $defaultChecked = 0, $keepSentValues = 1, $langPrefix = 'pi_feadmin_', $headerSeparation = ':<br />' )
 *              function fe_createTextArea( $name, $feAdminConf, $feAdminSection, $params = array(), $defaultValue = 0, $keepSentValues = 1, $langPrefix = 'pi_feadmin_', $headerSeparation = ':<br />' )
 *              function fe_createSelect( $name, $feAdminConf, $feAdminSection, $options, $htmlspecialchars = 1, $params = array(), $keepSentValues = 1, $langPrefix = 'pi_feadmin_', $headerSeparation = ':<br />' )
 *              function fe_createSelectFromTable( $name, $feAdminConf, $feAdminSection, $table, $pidList, $labelField, $valueField = 'uid', $htmlspecialchars = 1, $addWhere = '', $groupBy = '', $orderBy = '', $limit = '', $params = array(), $keepSentValues = 1, $langPrefix = 'pi_feadmin_', $headerSeparation = ':<br />' )
 *              function fe_buildFormElementHeader( $name, $langPrefix, $headerSeparation, $requiredFieldList = false, $evalValues = array() )
 *              function fe_buildLoginBox( $pid, $inputSize = '30', $method = 'post', $target = '_self', $wrap = false, $layout = false, $langPrefix = 'pi_loginbox_', $permaLogin = false )
 *              function fe_buildSearchBox( $method = 'post', $nocache = true, $sword = 'sword', $pointer = 'pointer' )
 *              function fe_buildBrowseBox( $pointer = 'pointer', $count = 'res_count', $maxResults = 'results_at_a_time', $maxPages = 'maxPages' )
 *              function fe_includePrototypeJs
 *              function fe_includeMootoolsJs
 *              function fe_includeScriptaculousJs
 *              function fe_includeLightBoxJs( $includeCss = true )
 *              function fe_includeUfo
 *              function fe_includeSwfObject
 *              function fe_includeWebToolKitJs( $file )
 * 
 * SECTION:     3 - BE
 *              function be_buildRecordIcons( $actions, $table, $uid )
 *              function be_buildPageTreeSelect( $name, $treeStartingPoint = 0, $size = '1', $multiple = false, $pageIcons = 1 )
 *              function be_getSelectStyleRecordIcon( $table, $rec, $backPath )
 *              function be_initCSM
 *              function be_getRecordCSMIcon( $table, $rec, $backPath, $align = 'top' )
 *              function be_includePrototypeJs
 *              function be_includeMootoolsJs
 *              function be_includeScriptaculousJs
 *              function be_includeLightBoxJs( $includeCss = true )
 *              function be_includeUfo
 *              function be_includeSwfObject
 *              function fe_includeWebToolKitJs( $file )
 * 
 * SECTION:     4 - DB
 *              function db_table2text( $table, $fieldList = '*', $addWhere = '', $groupBy = '', $orderBy = '', $limit = '', $sepField = chr(9), $sepRow = chr( 10  )
 *              function db_table2xml( $table, $fieldList = '*', $whereClause = '', $groupBy = '', $orderBy = '', $limit = '', $uppercase = 1, $xmlDeclaration = 1, $xmlVersion = '1.0', $xmlEncoding = 'iso-8859-1', $directOut = 0, $ns = '', $nsPrefix = 'ns' )
 * 
 * SECTION:     5 - DIV
 *              function div_utf8ToIso( $content )
 *              function div_getAge( $tstamp, $currentTime = false, $ageType = false )
 *              function div_writeTagParams( $params )
 *              function div_checkVarType( $vars, $type = 'array' )
 *              function div_cleanArray( $input, $keys, $inverse = 0 )
 *              function div_baseURL( $url, $http = 1, $trailingSlash = 1 )
 *              function div_vCardCreate( $user, $version = '3.0', $charset = false )
 *              function div_vCardFileParse( $file )
 *              function div_str2list( $string, $sep = ', ', $htmlspecialchars = 1, $listType = 'ul', $listParams = array(), $itemsParams = array() )
 *              function div_array2list( $array, $htmlspecialchars = 1, $listType = 'ul', $listParams = array(), $itemsParams = array() )
 *              function div_output( $out, $cType, $fName, $cDisp = 'attachment', $charset = 'utf-8' )
 *              function div_xml2array( $data, $keepAttribs = 1, $caseFolding = 0, $skipWhite = 0, $prefix = false, $numeric = 'n', $index = 'index', $type = 'type', $base64 = 'base64', $php5defCharset = 'iso-8859-1' )
 *              function div_array2xml( $input, $xmlRoot = 'phpArray', $prefix = '', $numeric = 'item', $numericAsAttribute = 'index', $addArrayAttribute = 'type', $xmlDeclaration = 1, $encoding = 'iso-8859-1', $version = '1.0', $standalone = 'yes', $doctype = false, $newLine = 10, $indent = 9, $level = 0 )
 *              function div_crop( $str, $chars, $endString = '...', $crop2space = 1, $stripTags = 1 )
 *              function div_week2date( $day, $week, $year )
 *              function div_numberInRange( $number, $min, $max, $int = false )
 *              function div_rgb2hsl( $R, $G, $B, $round = 1 )
 *              function div_hsl2rgb( $H, $S, $L, $round = 1 )
 *              function div_rgb2hsv( $R, $G, $B, $round = 1 )
 *              function div_hsv2rgb( $H, $S, $V, $round = 1 )
 *              function div_hsl2hsv( $H, $S, $L, $round = 1 )
 *              function div_hsv2hsl( $H, $S, $V, $round = 1 )
 *              function div_createHexColor( $v1, $v2, $v3, $method = 'RGB', $uppercase = 1 )
 *              function div_modifyHexColor( $color, $v1, $v2, $v3, $methid = 'RGB', $uppercase = 1 )
 *              function div_formatXHTML( $xhtml, $uppercase = 0, $newLine = 10, $indent = 9, $level = 0 )
 *              function div_convertLineBreaks( $text, $stripNull = 1 )
 *              function div_checkArrayKeys( $array, $keys, $allowEmpty = false, $checkType )
 *              function div_rmdir( $path, $relative = 0, $cleaned = false )
 *              function div_isType( $var, $type )
 * 
 * SECTION:     6 - DEBUG
 *              function viewArray( $array, $indent = 0 )
 *              function debug( $variable, $header = 'DEBUG' )
 * 
 *              TOTAL FUNCTIONS: 80
 */

class tx_apimacmade
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables.
     * 
     * Internal class variable. At the moment, only $version is set.
     * This variable is used for version checking of the API.
     ***************************************************************/
    
    // Version of the API
    var $version         = 4.6;
    
    // Parent object (if applicable)
    var $pObj            = NULL;
    
    // Prototype JS file was included
    var $prototype       = false;
    
    // Mootools JS file was included
    var $mootools        = false;
    
    // Scriptaculous file was included
    var $scriptaculous   = false;
    
    // Lightbox file was included
    var $lightbox        = false;
    
    // UFO file was included
    var $ufo             = false;
    
    // SWFObject file was included
    var $swfObject       = false;
    
    // WebToolkit JS files include state
    var $webToolKit      = array(
        'base64' => false,
        'crc32'  => false,
        'md5'    => false,
        'sha1'   => false,
        'sha256' => false,
        'url'    => false,
        'utf8'   => false
    );
    
    // Template object for frontend functions
    var $templateContent = NULL;
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - INTERNAL
     *
     * Functions for the initialization of the class.
     * 
     * Those functions are the core of this API. Except the class
     * constructor, they are all reserved for internal use.
     ***************************************************************/
    
    /**
     * Class constructor.
     * 
     * This function can't be called by t3lib_div::makeInstance, since it
     * needs the parent object to be passed as an argument. Sorry about that.
     * When you call the constructor, do not forget to set the
     * $apimacmade_version class variable, as it will be used for version
     * checking of the API.
     * 
     * @param       object      $pObj       The parent object
     * @return      NULL
     * @see         versionError
     */
    function tx_apimacmade( &$pObj )
    {
        // Check the API version required by the caller
        if( isset( $pObj->apimacmade_version ) && $pObj->apimacmade_version <= $this->version ) {
            
            // Store parent object as a class variable
            $this->pObj =& $pObj;
            
        } elseif( isset( $pObj->apimacmade_version ) ) {
            
            // The current version of the API is too old
            print $this->versionError( $pObj->apimacmade_version );
            exit();
            
        } else {
            
            // The current version of the API is undefined
            print $this->versionError();
            exit();
        }
    }
    
    /**
     * Produce a version error message.
     * 
     * This function produce the error message if the version of the API is
     * too old.
     * 
     * @param       string      $version    The required version of the API
     * @return      string      An HTML page with the error message
     */
    function versionError( $version = false )
    {
        // HTML code storage
        $htmlCode = '';
        
        // Required version
        $reqVersion = ( $version ) ? $version : 'undefined';
        
        // Error message
        $htmlCode = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 '
                  . 'Transitional//EN">'
                  . chr( 10 )
                  . '<html lang="en">'
                  . chr( 10 )
                  . '<head>'
                  . chr( 10 )
                  . '<meta http-equiv="content-type" content="text/html; '
                  . 'charset=iso-8859-1">'
                  . chr( 10 )
                  . '<title>Error</title>'
                  . chr( 10 )
                  . '</head>'
                  . chr( 10 )
                  . '<body>'
                  . chr( 10 )
                  . '<div align="center" style="color: #666666; '
                  . 'font-size: 12px; font-family: Verdana,Arial,Helvetica; '
                  . 'padding: 5px; border: solid 1px #660000; '
                  . 'background: #E7CECE; margin: 10px">'
                  . chr( 10 )
                  . '<p><h1>TYPO3 Developer API error:</strong></h1>'
                  . '<p>The version of Typo3 Developer API you are trying to '
                  . 'use is too old. Please update to the required version.</p>'
                  . chr( 10 )
                  . '<hr align="center" noshade size="1" width="50%">'
                  . chr( 10 )
                  . '<p><strong>Loaded version: '
                  . $this->version
                  . '<br>Required version: '
                  . $version
                  . '</strong></p>'
                  . chr( 10 )
                  . '</div>'
                  . chr( 10 )
                  . '</body>'
                  . chr( 10 )
                  . '</html>';
        
        // Return error message
        return $htmlCode;
    }
    
    /**
     * Procude an error message.
     * 
     * This function is used to produce an error message, if another function
     * report a problem, often caused by bad argument types.
     * 
     * @param       string      $method     The name of the method
     * @param       string      $message    The error message to display
     * @param       int         $line       The line number
     * @return      boolean
     */
    function errorMsg( $method, $message, $line )
    {
        // HTML code storage
        $htmlCode = '';
        
        // Error message
        $htmlCode = '<div align="center" style="color: #666666; '
                  . 'font-size: 12px; '
                  . 'font-family: Verdana,Arial,Helvetica; padding: 5px; '
                  . 'border: solid 1px #660000; background: #E7CECE; '
                  . 'margin: 10px">'
                  . chr( 10 )
                  . '<p style="margin-bottom: 10px;"><strong>TYPO3 Developer API error:</strong></p>'
                  . chr( 10 )
                  . '<p style="margin-bottom: 10px;">The method <strong>'
                  . $method
                  . '</strong> reported the following error on line <strong>'
                  . $line
                  . '</strong>:</p>'
                  . chr( 10 )
                  . '<p><strong>'
                  . $message
                  . '</strong></p>'
                  . chr( 10 )
                  . '</div>';
        
        // Return error message
        print $htmlCode;
        return true;
    }
    
    /**
     * Gets a PHP 5 class
     *
     * This function is used to load an PHP5 class contained in this API.
     * 
     * @param   string  $name   The name of the component to load
     * @param   array   $args   An array with the arguments to pass to the constructor (5 maximum)
     * @return  object  An instance of the requested class
     */
    function getPhp5Class( $name, $args = array() )
    {
        // Checks the PHP version
        if( ( double )PHP_VERSION < 5 ) {
            
            // Error message
            tx_apimacmade::errorMsg( __METHOD__, 'PHP5 is required in order to use this method.', __LINE__ );
        }
        
        // File to load
        $file = t3lib_extMgm::extPath( 'api_macmade' )
              . 'php5/class.tx_apimacmade_'
              . $name
              . '.php';
        
        // Checks if the file exists
        if( @file_exists( $file ) ) {
            
            // Includes the file
            require_once( $file );
            
            // Gets the class name
            $className = 'tx_apimacmade_' . $name;
            
            // Checks for constructor arguments
            switch( count( $args ) ) {
                
                case 0:
                    
                    // Return an instance of the class
                    return new $className();
                
                case 1:
                    
                    // Return an instance of the class
                    return new $className( $args[ 0 ] );
                
                case 2:
                    
                    // Return an instance of the class
                    return new $className( $args[ 0 ], $args[ 1 ] );
                
                case 3:
                    
                    // Return an instance of the class
                    return new $className( $args[ 0 ], $args[ 1 ], $args[ 2 ] );
                
                case 4:
                    
                    // Return an instance of the class
                    return new $className( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] );
                
                case 5:
                    
                    // Return an instance of the class
                    return new $className( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ] );
                
                default:
                    
                    // More than 5 constructor arguments - Bad idea... ; )
                    tx_apimacmade::errorMsg( __METHOD__, 'A maximum of 5 arguments can be passed to the class constructor.', __LINE__ );
                    return false;
            }
        }
        
        // The file does not exist
        tx_apimacmade::errorMsg( __METHOD__, 'Requested class file \'' . $file . '\' does not exists', __LINE__ );
    }
    
    /**
     * Returns a new instance of a class
     * 
     * This method does basically the same stuff as the makeInstance method
     * from t3lib_div, except the fact that this one can take arguments that
     * will be passed to the class constructor.
     * 
     * @param   string  $className  The name of the class
     * @param   array   $args       An array with the arguments to pass to the constructor (5 maximum)
     * @return  object  An instance of the requested class
     */
    function &newInstance( $className, $args = array() )
    {
        if( !class_exists( $className ) ) {
            
            $prefix = substr( $className, 0, 6 );
            
            if( $prefix === 't3lib_' ) {
                
                t3lib_div::requireOnce( PATH_t3lib . 'class.' . strtolower( $className ) . '.php' );
            }
            
            if( $prefix === 'tslib_' ) {
                
                t3lib_div::requireOnce( PATH_tslib . 'class.' . strtolower( $className ) . '.php' );
            }
        }
        
        if( class_exists( 'ux_' . $className ) ) {
            
            return tx_apimacmade::newInstance( 'ux_' . $className, $args );
        }
        
        if( !is_array( $args ) ) {
            
            return new $className;
        }
        
        // Checks for constructor arguments
        switch( count( $args ) ) {
            
            case 0:
                
                // Return an instance of the class
                return new $className();
            
            case 1:
                
                // Return an instance of the class
                return new $className( $args[ 0 ] );
            
            case 2:
                
                // Return an instance of the class
                return new $className( $args[ 0 ], $args[ 1 ] );
            
            case 3:
                
                // Return an instance of the class
                return new $className( $args[ 0 ], $args[ 1 ], $args[ 2 ] );
            
            case 4:
                
                // Return an instance of the class
                return new $className( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] );
            
            case 5:
                
                // Return an instance of the class
                return new $className( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ] );
            
            default:
                
                // More than 5 constructor arguments - Bad idea... ; )
                return new $className;
        }
    }
    
    
    
    
    
    /***************************************************************
     * SECTION 2 - FE
     *
     * Functions for frontend development.
     * 
     * All of those functions are only available in a frontend context.
     * They also all need the API class to be instantiated, as they will
     * use the internal variable $pObj.
     * 
     * Do not try to use them out of a frontend context, and without
     * the API class instantiated.
     ***************************************************************/
    
    /**
     * Merge plugin TS configuration with flexform configuration.
     * 
     * This function merge the plugin TS configuration array with the
     * flexform configuration (priority is given to flexform). Everything
     * is done automatically with a mapping array containing the path of the
     * TS elements to replace, and the path of the flexform fields in the XML.
     * 
     * @param       array       $mapArray           The mapping array with informations about values to replace
     * @param       array       $tsArray            The initial TS configuration array
     * @param       string      $flexRes            The flexform object (usually $this->pObj->cObj->data[ 'pi_flexform' ])
     * @return      array       The merged configuration array
     */
    function fe_mergeTSconfFlex( $mapArray, $tsArray, $flexRes )
    {
        // Checks the argument
        if( !is_array( $mapArray ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $mapArray must be an array.', __LINE__ );
            return false;
        }
        
        // Checks the argument
        if( !is_array( $tsArray ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $tsArray must be an array.', __LINE__ );
            return false;
        }
            
        // Temporary config array
        $tempConfig = $tsArray;
        
        // Process each entry of the mapping array
        foreach( $mapArray as $key => $value ) {
            
            // Check if current TS object has sub objects
            if( is_array( $value ) ) {
                
                // Item has sub objects - Process the array
                $tempConfig[ $key ] = $this->fe_mergeTSconfFlex(
                    $value,
                    $tsArray[ $key ],
                    $flexRes
                );
                
            } else {
                
                // No sub objects - Get informations about the flexform value to get
                $flexInfo = explode( ':', $value );
                
                // Try to get the requested flexform value
                $flexValue = ( string )$this->pObj->pi_getFFvalue(
                    $flexRes,
                    $flexInfo[ 1 ],
                    $flexInfo[ 0 ]
                );
                
                // Check for an existing value, or a zero value
                if( !empty( $flexValue ) || $flexValue == '0' ) {
                    
                    // Override TS setup
                    $tempConfig[ $key ] = $flexValue;
                }
            }
        }
        
        // Return configuration array
        return $tempConfig;
    }
    
    /**
     * Loads a template file.
     * 
     * This function reads a template file and store it as a
     * C-Object in the API class.
     * 
     * @param       string      $templateFile       The template file to load
     * @return      boolean
     */
    function fe_initTemplate( $templateFile )
    {
        // Load and store the template file
        $this->templateContent = $this->pObj->cObj->fileResource( $templateFile );
        return true;
    }
    
    /**
     * Template rendering.
     * 
     * This function analyzes the template C-Object, previously set by
     * $this->fe_initTemplate and substitute the specified section with
     * the specified subsections.
     * 
     * @param       array       $templateMarkers    The markers array
     * @param       array       $templateSection    The section to substitute
     * @return      string      The processed template section
     */
    function fe_renderTemplate( $templateMarkers, $templateSection )
    {
        // Check if the template is loaded
        if( !$this->templateContent ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The template object does not exists. Please call the fe_initTemplate method before using this method.', __LINE__ );
            return false;
        }
        
        // Check argument
        if( !is_array( $templateMarkers ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $templateMarkers must be an array.', __LINE__ );
            return false;
        }
            
        // Get template subparts
        $subpart = $this->pObj->cObj->getSubpart(
            $this->templateContent,
            $templateSection
        );
        
        // Return substituted section
        return $this->pObj->cObj->substituteMarkerArrayCached(
            $subpart,
            array(),
            $templateMarkers,
            array()
        );
    }
    
    /**
     * Returns the content with CSS.
     * 
     * This function is used to output the requested content
     * wrapped in an HTML element, containing a CSS class.
     * 
     * @param       string      $element            The HTML element to produce
     * @param       string      $className          The CSS class name to link
     * @param       mixed       $content            The content to wrap
     * @param       boolean     $piClass            Prepends class name with plugin name (using pi_classParam)
     * @param       boolean     $htmlSpecialChars   Pass the content through htmlspecialchars()
     * @param       boolean     $startTagOnly       Generate only the starting tag (without content!)
     * @param       array       $params             The parameters of the HTML element as key/value pairs
     * @return      string      The CSS styled content
     * @see         div_cleanArray
     * @see         div_writeTagParams
     */
    function fe_makeStyledContent( $element, $className, $content = false, $piClass = 1, $htmlSpecialChars = false, $startTagOnly = false, $params = array() )
    {
        // Check arguments
        if( !is_array( $params ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $params must be an array.', __LINE__ );
            return false;
        }
    
        // Check if the content must be passed through htmlspecialchars()
        if( $content && $htmlSpecialChars ) {
            
            $content = htmlspecialchars( $content );
        }
        
        // Clean parameters array to remove class if present
        $params = $this->div_cleanArray( $params, 'class' );
        
        // CSS class attribute
        if( $piClass ) {
            
            // Prepend class name with plugin name
            $classAttrib = $this->pObj->pi_classParam( $className );
            
        } else {
            
            // Use class name as is
            $classAttrib = 'class="' . $className . '"';
        }
        
        // Tag parameters
        $tagParams = ( count( $params ) ) ? ' ' . $this->div_writeTagParams( $params ) : '';
        
        // Create start tag
        $startTag = $element . ' ' . $classAttrib . $tagParams;
        
        // Check rendering method
        if( $startTagOnly ) {
            
            // Return only the starting tag
            return '<' . $startTag . '>';
            
        }
        
        // Create complete element
        $styledContent = ( $content !== false ) ? '<' . $startTag . '>' . $content . '</' . $element . '>' : '<' . $startTag . ' />';
        
        // Return content
        return $styledContent;
    }
    
    /**
     * Sets internals variables.
     * 
     * This function is used to set the internal variables array
     * ($this->pObj->internal) needed to execute a MySQL query.
     * 
     * @param       mixed       $results_at_a_time  The maximum number of records to display in a list view
     * @param       mixed       $maxPages           The maximum number of pages to display in the browsebox
     * @param       mixed       $searchFieldList    The fields available for searching
     * @param       mixed       $orderByList        The fields available to use as ORDER BY parameter
     * @return      boolean
     */
    function fe_setInternalVars( $results_at_a_time = false, $maxPages = false, $searchFieldList = false, $orderByList = false )
    {
        // Check arguments
        if( !is_array( $this->pObj->internal ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The $internal property of the parent object must be an array.', __LINE__ );
            return false;
        }
        
        // MySQL ORDER BY parameter
        list(
            $this->pObj->internal[ 'orderBy' ],
            $this->pObj->internal[ 'descFlag' ]
        )                                            = explode( ':', $this->pObj->piVars[ 'sort' ] );
        
        // Number of results to show in a listing
        $this->pObj->internal[ 'results_at_a_time' ] = t3lib_div::intInRange( $results_at_a_time, 0 );
        
        // Maximum number of pages
        $this->pObj->internal[ 'maxPages' ]          = t3lib_div::intInRange( $maxPages, 0 );
        
        // Search fields
        $this->pObj->internal[ 'searchFieldList' ]   = $searchFieldList;
        
        // MySQL ORDER BY list
        $this->pObj->internal[ 'orderByList' ]       = $orderByList;
        
        return true;
    }
    
    /**
     * Adds swapClasses JavaScript Code.
     * 
     * This function adds the javascript code used to switch between
     * CSS classes.
     * 
     * @param       string      $class1             The first class
     * @param       string      $class2             The second class
     * @param       boolean     $changeIcon         If this is set, the JS code will also change the source of an icon. This variable must be an array, with "plus" and "minus" as keys, containing the path for the icons.
     * @param       string      $iconSuffix         The suffix for the icon ID
     * @return      boolean
     */
    function fe_buildSwapClassesJSCode( $class1, $class2, $changeIcon = false, $iconSuffix = '_pic' )
    {
        // Storage
        $jsCode = '';
        
        // Function for swapping element class
        $jsCode = 'function '
                . $this->pObj->prefixId
                . '_swapClasses( element )'
                . chr( 10 )
                . '{'
                . chr( 10 )
                . '	if( document.getElementById ) {'
                . chr( 10 )
                . '		document.getElementById( element ).className = '
                . '( document.getElementById( element ).className == "'
                . $class1
                . '") ? "'
                . $class2
                . '" : "'
                . $class1
                . '";';
        
        // Check if an icon must be changed
        if( is_array( $changeIcon )
             && array_key_exists( 'minus', $changeIcon )
             && array_key_exists( 'plus', $changeIcon ) ) {
            
            // Plus icon
            $plus  = $GLOBALS[ 'TSFE' ]->tmpl->getFileName( $changeIcon[ 'plus' ] );
            
            // Minus icon
            $minus = $GLOBALS[ 'TSFE' ]->tmpl->getFileName( $changeIcon[ 'minus' ] );
            
            // Add icon change
            $jsCode .= chr( 10 )
                    .  '		icon = element + "'
                    . $iconSuffix
                    . '";'
                    .chr( 10 )
                    .  '		document.getElementById( icon ).src = '
                    . '( document.getElementById( element ).className == "'
                    . $class1
                    . '") ? "'
                    . $minus
                    . '" : "'
                    . $plus
                    . '";';
        }
        
        // End JS code
        $jsCode .= '	}'
                .  '}';
        
        // Adds JS code
        $GLOBALS[ 'TSFE' ]->setJS(
            $this->pObj->prefixId,
            $jsCode
        );
        
        return true;
    }
    
    /**
     * Returns a swapClasses link.
     * 
     * This function is used to output the requested content
     * wrapped in an HTML link element calling the swapClasses JS function.
     * 
     * @param       string      $elementId          The ID of the HTML element to change
     * @param       mixed       $content            The content to wrap
     * @param       boolean     $htmlSpecialChars   Pass the content through htmlspecialchars()
     * @param       boolean     $startTagOnly       Generate only the starting tag (without content!)
     * @param       array       $params             The attributes of the HTML element as key/value pairs
     * @return      string      The swap classes link
     * @see         div_cleanArray
     * @see         div_writeTagParams
     */
    function fe_makeSwapClassesJSLink( $elementId, $content = false, $htmlSpecialChars = false, $startTagOnly = false, $params = array() )
    {
        // Check arguments
        if( !is_array( $params ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $params must be an array.', __LINE__ );
            return false;
        }
        
        // Check if the content must be passed through htmlspecialchars()
        if( $content && $htmlSpecialChars ) {
            
            $content = htmlspecialchars( $content );
        }
        
        // Clean parameters array to remove href if present
        $params = $this->div_cleanArray( $params, 'class' );
        
        // Create start tag
        $startTag = '<a href="javascript:'
                  . $this->pObj->prefixId
                  . '_swapClasses(\''
                  . $elementId
                  . '\');" '
                  . $this->div_writeTagParams( $params )
                  . '>';
        
        if( $startTagOnly ) {
            
            // Return only the starting tag
            return $startTag;
            
        }
        
        // Create complete element
        $fullLink = $startTag . $content . '</a>';
        
        // Return content
        return $fullLink;
    }
    
    /**
     * Create IMAGE cObjects.
     * 
     * This function creates an IMAGE cObject for each given filename.
     * This function is particularly useful with image references stored
     * in a database field.
     * 
     * @param       string      $imgRefs            A comma list of picture names
     * @param       array       $conf               The TS setup for the images
     * @param       mixed       $imgPath            The path of the images (will be prepended to each picture name)
     * @return      string      An IMAGE cObject for each picture
     */
    function fe_createImageObjects( $imgRefs, $conf, $imgPath = false )
    {
        // Check arguments
        if( !is_array( $conf ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $conf must be an array.', __LINE__ );
            return false;
        }
    
        // Storage
        $content = array();
        
        // Pictures to get
        $imgObjects = explode( ',', $imgRefs );
        
        // Process each picture
        foreach( $imgObjects as $file ) {
            
            // Full path
            $fullPath              = $imgPath . $file;
            
            // Set IMG TS config
            $imgTSConfig           = $conf;
            
            // Add file reference
            $imgTSConfig[ 'file' ] = $fullPath;
            
            // Add picture
            $content[]             = $this->pObj->cObj->IMAGE( $imgTSConfig );
        }
        
        // Return content
        return implode( chr( 10 ), $content );
    }
    
    /**
     * Link a string to some page.
     * 
     * This function links a string to a page (the active one by default). It's
     * the same function as tslib_pibase::pi_linkTP(), except that a
     * configuration array for the typolink can be passed directly as argument,
     * and that it will always returns a correct cHash.
     *
     * @param       string      $str                The content string to wrap in <a> tags
     * @param       array       $urlParameters      Array with URL parameters as key/value pairs. They will be "imploded" and added to the list of parameters defined in the plugins TypoScript property "parent.addParams" plus $this->pi_moreParams
     * @param       boolean     $cache              If $cache is set (0/1), the page is asked to be cached by a &cHash value (unless the current plugin using this class is a USER_INT). Otherwise the no_cache-parameter will be a part of the link
     * @param       int         $altPageId          Alternative page ID for the link (by default this function links to the SAME page!)
     * @param       array       $conf               An optionnal array for the typolink configuration
     * @return      string      The input string wrapped in <a> tags
     */
    function fe_linkTP( $str, $urlParameters = array(), $cache = 0, $altPageId = 0, $conf = array() )
    {
        // Cache hash (this should always be true!)
        $conf[ 'useCacheHash' ]     = 1;
        
        // No cache
        $conf[ 'no_cache' ]         = ( $this->pObj->pi_USER_INT_obj ) ? 0 : !$cache;
        
        // Alternative page ID
        $conf[ 'parameter' ]        = ( $altPageId ) ? $altPageId : ( ( $this->pObj->pi_tmpPageId ) ? $this->pObj->pi_tmpPageId : $GLOBALS[ 'TSFE' ]->id );
        
        // Additionnal URL parameters
        $conf[ 'additionalParams' ] = $this->pObj->conf[ 'parent.' ][ 'addParams' ]
                                    . t3lib_div::implodeArrayForUrl( '', $urlParameters, '' ,1 )
                                    . $this->pObj->pi_moreParams;
        
        // Create link
        return $this->pObj->cObj->typoLink( $str, $conf );
    }
    
    /**
     * Link a string to some page.
     * 
     * This method is the same as method fe_linkTP_keepPIvars of tslib_piBase,
     * except the fact that it will always return a correct cHash.
     * 
     * @param       string      $str                The content string to wrap in <a> tags
     * @param       array       $overrulePIvars     Array of values to override or add in the current piVars
     * @param       boolean     $cache              Ask the page to be cached by a &cHash value
     * @param       boolean     $clearAnyway        Do not preserve current piVars
     * @param       int         $altPageId          Alternative page ID for the link
     * @return      string      The input string wrapped in <a> tags
     * @see         fe_linkTP
     */
    function fe_linkTP_keepPIvars( $str, $overrulePIvars = array(), $cache = 0, $clearAnyway = 0, $altPageId=0 )
    {
        // Checks argument
        if( !is_array( $overrulePIvars ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $overrulePIvars must be an array.', __LINE__ );
            return false;
        }
        
        // Checks arguments
        if( is_array( $this->pObj->piVars )
            && !$clearAnyway
        ) {
            
            // Gets a copy of the plugin variables
            $piVars = $this->pObj->piVars;
            
            // Unset the DATA variable
            unset( $piVars[ 'DATA' ] );
            
            // Merge the plugin variables with the passed variables
            $overrulePIvars = t3lib_div::array_merge_recursive_overrule( $piVars, $overrulePIvars );
            
            // Checsk cache settings
            if ( isset( $this->pObj->pi_autoCacheEn ) && $this->pObj->pi_autoCacheEn ) {
                
                // Cache value
                $cache = $this->pObj->pi_autoCache( $overrulePIvars );
            }
        }
    
        // Creates the link
        $res = $this->fe_linkTP(
            $str,
            array(
                $this->pObj->prefixId => $overrulePIvars
            ),
            $cache,
            $altPageId
        );
            
        // Returns the link
        return $res;
    }
    
    /**
     * Returns an URL to some page.
     * 
     * This method is the same as method pi_fe_linkTP_keepPIvars_url of
     * tslib_piBase, except the fact that it will always return a correct cHash.
     * 
     * @param       array       $overrulePIvars     Array of values to override or add in the current piVars
     * @param       boolean     $cache              Ask the page to be cached by a &cHash value
     * @param       boolean     $clearAnyway        Do not preserve current piVars
     * @param       int         $altPageId          Alternative page ID for the link
     * @return      string      The complete URL
     * @see         fe_linkTP_keepPIvars
     */
    function fe_linkTP_keepPIvars_url( $overrulePIvars = array(), $cache = 0, $clearAnyway = 0, $altPageId = 0 )
    {
        // Creates the the link
        $this->fe_linkTP_keepPIvars(
            '|',
            $overrulePIvars,
            $cache,
            $clearAnyway,
            $altPageId
        );
        
        // Returns the link
        return $this->pObj->cObj->lastTypoLinkUrl;
    }
    
    /**
     * Link a string to some page.
     * 
     * This function links a string to a page (the active one by default), while keeping current piVars.
     * Additionnal piVars can be added or overlaid in the overrulePIvars array. All piVars found in the
     * unsetPIvars array won't be preserved.
     * 
     * @param       string      $str                The content string to wrap in <a> tags
     * @param       array       $overrulePIvars     Array of values to override or add in the current piVars
     * @param       array       $unsetPIvars        Array of values not to include in the current piVars
     * @param       boolean     $cache      Ask the page to be cached by a &cHash value
     * @param       boolean     $clearAnyway        Do not preserve current piVars
     * @param       int         $altPageId          Alternative page ID for the link
     * @return      string      The input string wrapped in <a> tags
     * @see         fe_linkTP_keepPIvars
     */
    function fe_linkTP_unsetPIvars( $str, $overrulePIvars = array(), $unsetPIvars = array(), $cache = 0, $clearAnyway = 0, $altPageId = 0 )
    {
        // Check arguments
        if( !is_array( $overrulePIvars ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $overrulePIvars argument be an array.', __LINE__ );
            return false;
        }
        
        // Check argument
        if( !is_array( $unsetPIvars ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $unsetPIvars argument be an array.', __LINE__ );
            return false;
        }
        
        // Process unsetPIvars array
        foreach( $unsetPIvars as $piVar ) {
            
            // Set piVar to false
            $overrulePIvars[ $piVar ] = false;
        }
        
        // Returns link
        return $this->fe_linkTP_keepPIvars(
            $str,
            $overrulePIvars,
            $cache,
            $clearAnyway,
            $altPageId
        );
    }
    
    /**
     * Returns an URL to some page.
     * 
     * This function returns the URL to a page (the active one by default), while keeping current piVars.
     * Additionnal piVars can be added or overlaid in the overrulePIvars array. All piVars found in the
     * unsetPIvars array won't be preserved. Same as fe_linkTP_unsetPIvars, but it returns only the URL.
     * 
     * @param       array       $overrulePIvars     Array of values to override or add in the current piVars
     * @param       array       $unsetPIvars        Array of values not to include in the current piVars
     * @param       boolean     $cache              Ask the page to be cached by a &cHash value
     * @param       boolean     $clearAnyway        Do not preserve current piVars
     * @param       int         $altPageId          Alternative page ID for the link
     * @return      string      The complete URL
     * @see         fe_linkTP_keepPIvars_url
     */
    function fe_linkTP_unsetPIvars_url( $overrulePIvars = array(), $unsetPIvars = array(), $cache = 0, $clearAnyway = 0, $altPageId = 0 )
    {
        // Check arguments
        if( !is_array( $overrulePIvars ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $overrulePIvars argument be an array.', __LINE__ );
            return false;
        }
        
        // Check arguments
        if( !is_array( $unsetPIvars ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $unsetPIvars argument be an array.', __LINE__ );
            return false;
        }
            
        // Process unsetPIvars array
        foreach( $unsetPIvars as $piVar ) {
            
            // Set piVar to false
            $overrulePIvars[ $piVar ] = false;
        }
        
        // Returns link
        return $this->fe_linkTP_keepPIvars_url(
            $overrulePIvars,
            $cache,
            $clearAnyway,
            $altPageId
        );
    }
    
    /**
     * Builds parameters for a typoLink
     * 
     * This method is used to build the 'additionalParams' TypoScript option.
     * Each parameter of the array will be formed as a piVar (prefixed with
     * pObj->prefixId).
     * 
     * @param       array       $params             An associative array with the URL parameters
     * @param       boolean     $keepPiVars         If this is true, the piVars will be kept
     * @return      string      The additional parameters ready for a typoLink
     */
    function fe_typoLinkParams( $params, $keepPiVars = false )
    {
        if( !is_array( $params ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $params argument be an array.', __LINE__ );
            return false;
        }
        
        // Storage
        $additionalParams = array();
        
        // Checks the extension prefix
        if( isset( $this->pObj->prefixId ) ) {
            
            // Checks if the piVars must be kept
            if( $keepPiVars && Isset( $this->pObj->piVars ) && is_array( $this->pObj->piVars ) ) {
                
                // Process piVars
                foreach( $this->pObj->piVars as $key => $value ) {
                    
                    // Adds the parameter
                    $additionalParams[ $key ] .= '&'
                                              .  $this->pObj->prefixId
                                              .  '['
                                              .  $key
                                              .  ']'
                                              .  '='
                                              . $value;
                }
            }
            
            // Process parameters
            foreach( $params as $key => $value ) {
                
                // Adds the parameter
                $additionalParams[ $key ] .= '&'
                                          .  $this->pObj->prefixId
                                          .  '['
                                          .  $key
                                          .  ']'
                                          .  '='
                                          . $value;
            }
        }
        
        // Returns the parameters
        return implode( '', $additionalParams );
    }
    
    /**
     * Init the FE-Admin script for frontend input.
     * 
     * This function adds all the configuration necessary to use fe_adminLib to
     * the plugin configuration array.
     * 
     * @param       array       $conf               The plugin configuration array
     * @param       string      $table              The table to use
     * @param       int         $pid                The pid for the records
     * @param       array       $feAdminConf        The configuration array for fe_adminLib subparts
     * @param       boolean     $create             Create capabilities (Boolean)
     * @param       boolean     $edit               Edit capabilities (Boolean)
     * @param       boolean     $delete             Delete capabilities (Boolean)
     * @param       boolean     $infomail           Infomail capabilities (Boolean)
     * @param       boolean     $fe_userOwnSelf     FE-Users own themselves (Boolean)
     * @param       boolean     $fe_userEditSelf    FE-Users can edit themselves (Boolean)
     * @param       boolean     $debug              Output debug informations (Boolean)
     * @param       string      $defaultCmd         The default command to use if none is found
     * @param       string      $confKey            The key to use for fe_adminLib in the plugin configuration array
     * @return      array       The complete plugin configuration array with a valid fe_adminLib configuration
     */
    function fe_initFeAdmin( $conf, $table, $pid, $feAdminConf, $create = 1, $edit = 0, $delete = 0, $infomail = 0, $fe_userOwnSelf = 0, $fe_userEditSelf = 0, $debug = 0, $defaultCmd = 'create', $confKey = 'fe_adminLib' )
    {
        // Check arguments
        if( !is_array( $conf ) ) {
        
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $conf must be an array.', __LINE__ );
            return false;
        }
        
        // Check arguments
        if( !is_array( $feAdminConf) ) {
        
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $feAdminConf must be an array.', __LINE__ );
            return false;
        }
        
        // Create feAdmin configuration array
        $conf[ $confKey]        = 'USER_INT';
        $conf[ $confKey . '.' ] = array(
            
            // User func
            'userFunc'        => 'user_feAdmin->init',
            
            // Include library
            'includeLibs'     => 'media/scripts/fe_adminLib.inc',
            
            // Database table to use
            'table'           => $table,
            
            // PID for storage
            'pid'             => $pid,
            
            // Default command
            'defaultCmd'      => $defaultCmd,
            
            // Output debug infos
            'debug'           => $debug,
            
            // Deletion capabilities
            'delete'          => $delete,
            
            // Infomail setup
            'infomail'        => $infomail,
            
            // Creation capabilities
            'create'          => $create,
            
            // Edition capabilities
            'edit'            => $edit,
            
            // Own capabilities
            'fe_userOwnSelf'  => $fe_userOwnSelf,
            'fe_userEditSelf' => $fe_userEditSelf
        );
        
        // Create configuration
        if( $create ) {
            
            // Add sub-configuration
            $conf[ $confKey . '.' ][ 'create.' ]   = $feAdminConf[ 'create.' ];
        }
        
        // Edit configuration
        if( $edit ) {
            
            // Add sub-configuration
            $conf[ $confKey . '.' ][ 'edit.' ]     = $feAdminConf[ 'edit.' ];
        }
        
        // Delete configuration
        if( $delete ) {
            
            // Add sub-configuration
            $conf[ $confKey . '.' ][ 'delete.' ]   = $feAdminConf[ 'delete.' ];
        }
        
        // Infomail configuration
        if( $infomail ) {
            
            // Add sub-configuration
            $conf[ $confKey . '.' ][ 'infomail.' ] = $feAdminConf[ 'infomail.' ];
        }
        
        // Return configuration array
        return $conf;
    }
    
    /**
     * Creates an input.
     * 
     * This function creates an HTML input tag, ready for a usage with fe_adminLib.
     * 
     * @param       string      $type               The type of the input
     * @param       string      $name               The name of the input (field)
     * @param       array       $feAdminConf        The fe_adminLib configuration array
     * @param       string      $feAdminSection     The fe_adminLib section (usually create or edit) - Used to check for required fields
     * @param       int         $number             The number of input to create
     * @param       array       $params             The input tag parameters (depending of context) as an array with key/value pairs
     * @param       mixed       $defaultValue       The default value for the input. Can be an array for multiple inputs, or 'unix' for checkboxes/radios with unix-perms like values (eg. 1 -2 - 4 - 8, etc.), or 'increment' for incrementing values (eg. 0 - 1 -2 - 3, etc.)
     * @param       mixed       $defaultChecked     For checkboxes or radios, if the input must be checked by default. Can be a comma list for multiple checkboxes
     * @param       boolean     $keepSentValues     Keep element value if the form is redrawn (will override default value or default checked)
     * @param       string      $langPrefix         The prefix for the plugin locallangfile. Used to fetch the title of the input and the warning message, if applicable
     * @param       string      $headerSeparation   The separation between the title and the input
     * @return      string      A complete form element, with title, warning, and the input itself
     * @see         div_checkVarType
     * @see         div_cleanArray
     * @see         fe_buildFormElementHeader
     * @see         div_writeTagParams
     */
    function fe_createInput( $type, $name, $feAdminConf, $feAdminSection, $number = 1, $params = array(), $defaultValue = 0, $defaultChecked = 0, $keepSentValues = 1, $langPrefix = 'pi_feadmin_', $headerSeparation = ':<br />' )
    {
        // Check arguments
        if( !is_array( $feAdminConf ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $feAdminConf must be an array.', __LINE__ );
            return false;
        }
        
        // Check arguments
        if( !is_array( $params) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $params must be an array.', __LINE__ );
            return false;
        }
            
        // HTML code storage
        $htmlCode    = array();
        
        // Create FE-Admin name for all types except submit
        $feAdminName = ( $type != 'submit' ) ? 'FE[' . $feAdminConf[ 'table' ] . '][' . $name . ']' : $name;
        
        // Update name for multiple inputs or file inputs
        if( $number > 1 ) {
            
            $feAdminName .= '[]';
        }
        
        // Clean parameters array to remove name and type if present
        $params = $this->div_cleanArray( $params, 'type,name' );
        
        // Check if input is not a submit
        if( $type != 'submit' ) {
            
            // Add form element header
            $htmlCode[] = $this->fe_buildFormElementHeader(
                $name,
                $langPrefix,
                $headerSeparation,
                $feAdminConf[ $feAdminSection . '.' ][ 'required' ],
                $feAdminConf[ $feAdminSection . '.' ][ 'evalValues.' ]
            );
        }
        
        // Recover sent variables
        $feAdmin_vars = t3lib_div::_GP( 'FE' );
        
        // Check for valid variables
        if( is_array( $feAdmin_vars ) ) {
            
            // Set value
            $fieldValue = $feAdmin_vars[ $feAdminConf[ 'table' ] ][ $name ];
        }
        
        // Process input(s)
        for( $i = 0; $i < $number; $i++ ) {
            
            // Check value type
            if( is_array( $defaultValue ) ) {
                
                // Custom values
                $currentValue = $defaultValue[ $i ];
                
            } elseif( $defaultValue && $defaultValue == 'unix' ) {
                
                // Unix like values
                $currentValue = pow(2,$i);
                
            } elseif( $defaultValue && $defaultValue == 'increment' ) {
                
                // Increment values
                $currentValue = $i;
                
            } elseif( $keepSentValues && $fieldValue ) {
                
                // Recover sent variable
                $currentValue = $fieldValue;
                
            } elseif( $defaultValue ) {
                
                // Normal value
                $currentValue = $defaultValue;
                
            }
            
            // Set value parameter
            if( $currentValue ) {
                
                $params[ 'value' ] = $currentValue;
            }
            
            // Special processing for checkboxes
            if( $type == 'radio' || $type == 'checkbox' ) {
                
                // Check for value
                if( $keepSentValues && $fieldValue ) {
                    
                    // Get checked values
                    $checked = ( is_array( $fieldValue ) ) ? $fieldValue : explode( ',', $fieldValue );
                    
                    // Check if input must be checked or not
                    if( in_array( $currentValue, $checked ) ) {
                        
                        // Checked by default
                        $params[ 'checked' ] = 'checked';
                        
                    } else {
                        
                        // Not checked by default
                        unset( $params[ 'checked' ] );
                    }
                    
                } else {
                    
                    // Get default checked values
                    $checked = explode( ',', $defaultChecked );
                    
                    // Check if input must be checked by default or not
                    if( in_array( $i + 1, $checked ) ) {
                        
                        // Checked by default
                        $params[ 'checked' ] = 'checked';
                        
                    } else {
                        
                        // Not checked by default
                        unset( $params[ 'checked' ] );
                    }
                }
            }
            
            // Create input
            $htmlCode[] = '<input type="'
                        . $type
                        . '" name="'
                        . $feAdminName
                        . '" '
                        . $this->div_writeTagParams( $params )
                        . '>';
        }
        
        // Return HTML code
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * Creates a text area.
     * 
     * This function creates an HTML <textarea> tag for use with the
     * fe_adminLib script.
     * 
     * @param       string      $name               The name of the textarea (field)
     * @param       array       $feAdminConf        The fe_adminLib configuration array
     * @param       string      $feAdminSection     The fe_adminLib section (usually create or edit) - Used to check for required fields
     * @param       array       $params             The textarea tag parameters as an array with key/value pairs
     * @param       mixed       $defaultValue       The default value for the textarea
     * @param       boolean     $keepSentValues     Keep element value if the form is redrawn (will override default value)
     * @param       string      $langPrefix     The prefix for the plugin locallangfile. Used to fetch the title of the textarea and the warning message, if applicable
     * @param       string      $headerSeparation   The separation between the title and the textarea
     * @return      string      A complete textarea zone, with title and warning.
     * @see         div_cleanArray
     * @see         buildFormElementHeader
     * @see         div_writeTagParams
     */
    function fe_createTextArea( $name, $feAdminConf, $feAdminSection, $params = array(), $defaultValue = 0, $keepSentValues = 1, $langPrefix = 'pi_feadmin_', $headerSeparation = ':<br />' )
    {
        // Check arguments
        if( !is_array( $feAdminConf ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $feAdminConf must be an array.', __LINE__ );
            return false;
        }
        
        // Check arguments
        if( !is_array( $params) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $params must be an array.', __LINE__ );
            return false;
        }
            
        // Check for value
        if( $keepSentValues ) {
            
            // Recover sent variables
            $feAdmin_vars = t3lib_div::_GP( 'FE' );
            
            // Check for valid variables
            if( is_array( $feAdmin_vars ) ) {
                
                // Set value
                $value = $feAdmin_vars[ $feAdminConf[ 'table' ] ][ $name ];
            }
            
        } elseif( $defaultValue ) {
            
            // Use default value
            $value = $defaultValue;
        }
        
        // HTML code storage
        $htmlCode    = array();
        
        // Create FE-Admin name for all types except submit
        $feAdminName = 'FE[' . $feAdminConf[ 'table' ] . '][' . $name . ']';
        
        // Clean parameters array to remove name and value if present
        $params      = $this->div_cleanArray( $params, 'name,value' );
        
        // Add form element header
        $htmlCode[]  = $this->fe_buildFormElementHeader(
            $name,
            $langPrefix,
            $headerSeparation,
            $feAdminConf[ $feAdminSection . '.' ][ 'required' ],
            $feAdminConf[ $feAdminSection . '.' ][ 'evalValues.' ]
        );
        
        // Create textarea
        $htmlCode[]  = '<textarea name="'
                     . $feAdminName
                     . '" '
                     . $this->div_writeTagParams( $params )
                     . '>'
                     . $value
                     . '</textarea>';
        
        // Return HTML code
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * Creates a select.
     * 
     * This function creates an HTML select tag for use with the
     * fe_adminLib script.
     * 
     * The $option parameter can be an array or a number. If it's an
     * array, the options will get the keys as values, and the values
     * as label. If it's a number, it will create x options (x representing
     * that number). The values will be incremented from zero, and the labels
     * taken from the locallang file, according to the standard Typo3 syntax
     * (eg.: lang_prefix_fieldname.I.value).
     * 
     * @param       string      $name               The name of the select (field)
     * @param       array       $feAdminConf        The fe_adminLib configuration array
     * @param       string      $feAdminSection     The fe_adminLib section (usually create or edit) - Used to check for required fields
     * @param       mixed       $options            The options to create.
     * @param       boolean     $htmlspecialchars   Pass the option labels through htmlspecialchars()
     * @param       array       $params             The select tag parameters as an array with key/value pairs
     * @param       boolean     $keepSentValues     Keep element value if the form is redrawn
     * @param       string      $langPrefix         The prefix for the plugin locallangfile. Used to fetch the title of the select and the warning message, if applicable
     * @param       string      $headerSeparation   The separation between the title and the select
     * @return      string      A complete select, with title and warning.
     * @see         div_cleanArray
     * @see         buildFormElementHeader
     * @see         div_writeTagParams
     */
    function fe_createSelect( $name, $feAdminConf, $feAdminSection, $options, $htmlspecialchars = 1, $params = array(), $keepSentValues = 1, $langPrefix = 'pi_feadmin_', $headerSeparation = ':<br />' )
    {
        // Check arguments
        if( !is_array( $feAdminConf ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $feAdminConf must be an array.', __LINE__ );
            return false;
        }
        
        // Check arguments
        if( !is_array( $params) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $params must be an array.', __LINE__ );
            return false;
        }
            
        // HTML code storage
        $htmlCode    = array();
        
        // Create FE-Admin name for all types except submit
        $feAdminName = 'FE[' . $feAdminConf[ 'table' ] . '][' . $name . ']';
        
        // Clean parameters array to remove name if present
        $params      = $this->div_cleanArray( $params, 'name' );
        
        // Add form element header
        $htmlCode[]  = $this->fe_buildFormElementHeader(
            $name,
            $langPrefix,
            $headerSeparation,
            $feAdminConf[ $feAdminSection . '.' ][ 'required' ],
            $feAdminConf[ $feAdminSection . '.' ][ 'evalValues.' ]
        );
        
        // Start select
        $htmlCode[]  = '<select name="'
                     . $feAdminName
                     . '" '
                     . $this->div_writeTagParams( $params )
                     . '>';
        
        // Check for value
        if( $keepSentValues ) {
            
            // Recover sent variables
            $feAdmin_vars = t3lib_div::_GP( 'FE' );
            
            // Check for valid variables
            if( is_array( $feAdmin_vars ) ) {
                
                // Set value
                $sentValue = $feAdmin_vars[ $feAdminConf[ 'table' ] ][ $name ];
            }
        }
        
        // Check options
        if( is_array( $options ) ) {
            
            // Create options from array
            foreach( $options as $key => $value ) {
                
                // Check if the option must be selected
                if( $sentValue && $key == $sentValue ) {
                    
                    // Selected
                    $selected = ' selected';
                    
                } else {
                    
                    // Not selected
                    $selected = '';
                }
                
                // Pass the value through htmlspecialchars() if required
                $value = ( $htmlspecialchars ) ? htmlspecialchars( $value ) : $value;
                
                // Add option
                $htmlCode[] = '<option value="'
                            . $key
                            . '"'
                            . $selected
                            . '>'
                            . $value
                            . '</option>';
                
            }
            
        } else {
            
            // Create options from number
            for( $i = 0; $i < $options; $i++ ) {
                
                // Check if the option must be selected
                if( $sentValue && $i == $sentValue ) {
                    
                    // Selected
                    $selected = ' selected';
                    
                } else {
                    
                    // Not selected
                    $selected = '';
                }
                
                // Pass the value through htmlspecialchars() if required
                $value = ( $htmlspecialchars ) ? htmlspecialchars( $value ) : $value;
                
                // Add option
                $htmlCode[] = '<option value="'
                            . $i
                            . '"'
                            . $selected
                            . '>'
                            . $this->pObj->pi_getLL( $langPrefix . $name . '.I.' . $i, '[item' . $i . ']' )
                            . '</option>';
            }
        }
        
        // End select
        $htmlCode[] = '</select>';
        
        // Return HTML code
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * Creates a select from a table.
     * 
     * This function creates an HTML select tag for use with the
     * fe_adminLib script. Values and labels are taken from an
     * external table.
     * 
     * @param       string      $name               The name of the select (field)
     * @param       array       $feAdminConf        The fe_adminLib configuration array
     * @param       string      $feAdminSection     The fe_adminLib section (usually create or edit) - Used to check for required fields
     * @param       string      $table              The table containing the records (must be a valid Typo3 table)
     * @param       mixed       $pidList            The pages from where to select the records (as a comma list)
     * @param       string      $labelField         The field in the database to use as option label
     * @param       string      $valueField         The field in the database to use as option value (usually UID)
     * @param       boolean     $htmlspecialchars   Pass the option labels through htmlspecialchars()
     * @param       string      $addWhere           Optional additional WHERE clauses put in the end of the query. DO NOT PUT IN GROUP BY, ORDER BY or LIMIT!
     * @param       string      $groupBy            Optional GROUP BY field(s), if none, supply blank string.
     * @param       string      $orderBy            Optional ORDER BY field(s), if none, supply blank string.
     * @param       string      $limit              Optional LIMIT value ([begin,]max), if none, supply blank string.
     * @param       array       $params             The select tag parameters as an array with key/value pairs
     * @param       boolean     $keepSentValues     Keep element value if the form is redrawn
     * @param       string      $langPrefix         The prefix for the plugin locallang file. Used to fetch the title of the select and the warning message, if applicable
     * @param       string      $headerSeparation   The separation between the title and the select
     * @return      string      A complete select, with title and warning.
     * @see         div_cleanArray
     * @see         buildFormElementHeader
     * @see         div_writeTagParams
     */
    function fe_createSelectFromTable( $name, $feAdminConf, $feAdminSection, $table, $pidList, $labelField, $valueField = 'uid', $htmlspecialchars = 1, $addWhere = '', $groupBy = '', $orderBy = '', $limit = '', $params = array(), $keepSentValues = 1, $langPrefix = 'pi_feadmin_', $headerSeparation = ':<br />' )
    {
        // Check arguments
        if( !is_array( $feAdminConf ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $feAdminConf must be an array.', __LINE__ );
            return false;
        }
        
        // Check arguments
        if( !is_array( $params) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $params must be an array.', __LINE__ );
            return false;
        }
            
        // HTML code storage
        $htmlCode    = array();
        
        // Create FE-Admin name for all types except submit
        $feAdminName = 'FE[' . $feAdminConf[ 'table' ] . '][' . $name . ']';
        
        // Clean parameters array to remove name if present
        $params      = $this->div_cleanArray( $params, 'name' );
        
        // Add form element header
        $htmlCode[]  = $this->fe_buildFormElementHeader(
            $name,
            $langPrefix,
            $headerSeparation,
            $feAdminConf[ $feAdminSection . '.' ][ 'required' ],
            $feAdminConf[ $feAdminSection . '.' ][ 'evalValues.' ]
        );
        
        // Start select
        $htmlCode[]  = '<select name="'
                     . $feAdminName
                     . '" '
                     . $this->div_writeTagParams( $params )
                     . '>';
        
        // Check for value
        if( $keepSentValues ) {
            
            // Recover sent variables
            $feAdmin_vars = t3lib_div::_GP( 'FE' );
            
            // Check for valid variables
            if( is_array( $feAdmin_vars ) ) {
                
                // Set value
                $sentValue = $feAdmin_vars[ $feAdminConf[ 'table' ] ][ $name ];
            }
        }
        
        // Get an array with all PID to select records from
        $pages          = explode( ',', $pidList );
        
        // Get main PID
        $mainPid        = array_shift( $pages );
        
        // MySQL WHERE clause storage
        $whereClause    = array();
        
        // Add enableFields
        $whereClause[]  = $this->pObj->cObj->enableFields( $table );
        
        // Process each remaining PID
        foreach( $pages as $pid ) {
            
            $addWhere[] = 'OR pid=' . $pid;
        }
        
        // Add user defined clause
        $whereClause[]  = $addWhere;
        
        // Get items from table
        $options        = $this->pObj->pi_getCategoryTableContents(
            $table,
            $mainPid,
            implode( ' ', $whereClause ),
            $groupBy,
            $orderBy
        );
        
        // Check options
        if( is_array( $options ) ) {
            
            // Create options from rows
            foreach( $options as $row ) {
                
                // Check if the option must be selected
                if( $sentValue && $row[ $valueField ] == $sentValue ) {
                    
                    // Selected
                    $selected = ' selected';
                    
                } else {
                    
                    // Not selected
                    $selected = '';
                }
                
                // Pass the value through htmlspecialchars() if required
                $value = ( $htmlspecialchars ) ? htmlspecialchars( $row[ $labelField ] ) : $row[ $labelField ];
                
                // Add option
                $htmlCode[] = '<option value="'
                            . $row[ $valueField ]
                            . '"'
                            . $selected
                            . '>'
                            . $value
                            . '</option>';
                
            }
        }
        
        // End select
        $htmlCode[] = '</select>';
        
        // Return HTML code
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * Returns a form element header.
     * 
     * This function creates the header of a form element for use with
     * the fe_adminLib script. It also checks if the field is required in
     * the plugin configuration array, and adds warning markers.
     * 
     * @param       string      $name               The name of the field
     * @param       string      $langPrefix         The prefix to use to get the field title in the locallang file
     * @param       string      $headerSeparation   The separation to use between the header and the form element
     * @param       string      $requiredFieldList  A comma list of the required fields of the feAdmin section
     * @param       array       $evalValues         The evalValues array from the feAdmin configuration array
     * @return      string      The header zone
     */
    function fe_buildFormElementHeader( $name, $langPrefix, $headerSeparation, $requiredFieldList = false, $evalValues = array() )
    {
        // Get an array with all required fields
        $requiredFields = explode( ',', $requiredFieldList );
        
        // Check if field must be evaluated
        $eval           = ( array_key_exists( $name, $evalValues ) ) ? true : false;
        
        // Check if field is required
        $required       = ( in_array( $name, $requiredFields ) ) ? ' <strong>*</strong>' : false;
        
        // Header storage
        $header         = array();
        
        // Add field title
        $header[]       = $this->pObj->pi_getLL( $langPrefix . $name,'[' . $name . ']') . $required . $headerSeparation;
        
        // Add special markers
        if( $eval ) {
            
            // Field must be evaluated
            $header[] = '<!--###SUB_REQUIRED_FIELD_'
                      . $name
                      . '###-->';
            $header[] = '<br /><strong>'
                      . $this->pObj->pi_getLL( $langPrefix . $name . '_evalerror', 'Error with the data type')
                      . '</strong><br />';
            $header[] = '<!--###SUB_REQUIRED_FIELD_'
                      . $name
                      . '###-->';
            
        } elseif( $required ) {
            
            // Field is required
            $header[] = '<!--###SUB_REQUIRED_FIELD_'
                      . $name
                      . '###-->';
            $header[] = '<br /><strong>'
                      . $this->pObj->pi_getLL( $langPrefix . $name . '_required', 'Error: the field is required')
                      . '</strong><br />';
            $header[] = '<!--###SUB_REQUIRED_FIELD_'
                      . $name
                      . '###-->';
        }
        
        // Return header
        return implode( chr( 10 ), $header );
    }
    
    /**
     * Build a login box.
     * 
     * This function constructs a standard Typo3 login box. All the setup is
     * done by the function. You only have to specify the PID of the sysfolder
     * where you store your website users records, and it will handle everything.
     * If the user is already logged, it display a logout form.
     * 
     * @param       int         $pid                The PID of the sysfolder containing the frontend users allowed to login
     * @param       int         $inputSize          The size of the inputs to generate
     * @param       string      $method             The method of the form object used for sending variables
     * @param       string      $target             The target of the form object
     * @param       mixed       $wrap               Wrap the whole object
     * @param       mixed       $layout             The layout of the form object
     * @param       string      $langPrefix         The prefix to use to get the field title in the locallang file
     * @param       boolean     $permaLogin         Show permalogin option (needs extension 'core_permalogin')
     * @param       boolean     $asTsArray          Returns the TS array, not the HTML code
     * @return      string      A login box
     * @see         fe_makeStyledContent
     */
    function fe_buildLoginBox( $pid, $inputSize = '30', $method = 'post', $target = '_self', $wrap = false, $layout = false, $langPrefix = 'pi_loginbox_', $permaLogin = false, $asTsArray = false )
    {
        // Get locallang values
        $labels = array(
            'username'   => $this->pObj->pi_getLL( $langPrefix . 'username', 'Username' ) . ':',
            'password'   => $this->pObj->pi_getLL( $langPrefix . 'password', 'Password' ) . ':',
            'login'      => $this->pObj->pi_getLL( $langPrefix . 'login', 'Click here to log-in' ),
            'logout'     => $this->pObj->pi_getLL( $langPrefix . 'logout', 'Click here to log-out' ),
            'permalogin' => $this->pObj->pi_getLL( $langPrefix . 'permalogin', 'Remember me' ),
            'yes'        => $this->pObj->pi_getLL( $langPrefix . 'yes', 'Yes' ),
            'no'         => $this->pObj->pi_getLL( $langPrefix . 'no', 'No' )
        );
        
        // Set default layout
        if ( !$layout ) {
            
            $layout = '<tr>'
                    . $this->fe_makeStyledContent(
                        'td',
                        'labelCell',
                        '###LABEL###',
                        1,
                        false,
                        false,
                        array(
                            'width'  => '25%',
                            'align'  => 'left',
                            'valign' => 'middle'
                        )
                    )
                    . $this->fe_makeStyledContent(
                        'td',
                        'fieldCell',
                        '###FIELD###',
                        1,
                        false,
                        false,
                        array(
                            'width'  => '75%',
                            'align'  => 'left',
                            'valign' => 'top'
                        )
                    )
                    . '</tr>';
        }
        
        // Set default wrap
        if ( !$wrap ) {
            
            $wrap = $this->fe_makeStyledContent(
                'table',
                'loginTable',
                '|',
                1,
                false,
                false,
                array(
                    'border'      => '0',
                    'width'       => '100%',
                    'align'       => 'center',
                    'cellspacing' => '0',
                    'cellpading'  => '0'
                )
            );
        }
        
        // Configuration array
        $conf = array(
            'layout'              => $layout,               // Layout
            'stdWrap.'            => array(                 // StdWrap
                'wrap'            => $wrap                  // Wrap
            ),
            'method'              => $method,               // Form method
            'target'              => $target,               // Form target
            'locationData'        => 0,                     // Location data
            'hiddenFields.'       => array(                 // Hidden fields
                'pid'             => 'TEXT',                // PID
                'pid.'            => array(
                    'value'       => $pid,                  // Value
                    'override.'   => array(                 // Override
                        'field'   => 'pages',
                        'listNum' => 1
                    )
                )
            ),
            'params.'             => array(                 // Element parameters
                'input'           => 'class="input"',
                'password'        => 'class="password"',
                'submit'          => 'class="submit"'
            )
        );
        
        // Check if user is logged
        if ( $GLOBALS[ 'TSFE' ]->loginUser ) {
            
            // Display username
            $username = $this->fe_makeStyledContent(
                'span',
                'loggedUser',
                '<!--###USERNAME###-->'
            );
            
            // Logged - Show status
            $conf['dataArray.'] = array(                                        // Data array
                '10.'       => array(                                           // Username
                    'label' => $labels[ 'username' ] . '&nbsp;' . $username,    // Label
                    'type'  => 'submit=submit',                                 // Type
                    'value' => $labels[ 'logout' ]                              // Value
                ),
                '20.'       => array(                                           // Login type
                    'type'  => 'logintype=hidden',                              // Type
                    'value' => 'logout'                                         // Value
                )
            );
            
        } else {
            
            // Not logged - Show login box
            $conf['dataArray.'] = array(                        // Data array
                '10.'       => array(                           // Username
                    'label' => $labels[ 'username' ],           // Label
                    'type'  => '*user=input,' . $inputSize      // Type
                ),
                '20.'       => array(                           // Password
                    'label' => $labels[ 'password' ],           // Label
                    'type'  => '*pass=password,' . $inputSize   // Type
                ),
                '40.'       => array(                           // Login type
                    'type'  => 'logintype=hidden',              // Type
                    'value' => 'login'                          // Value
                ),
                '50.'       => array(                           // Submit
                    'type'  => 'submit=submit',                 // Type
                    'value' => $labels[ 'login' ]               // Value
                )
            );
            
            // Check permalogin
            if ( $permaLogin && t3lib_extMgm::isLoaded( 'core_permalogin' ) ) {
                
                // Get permalogin configuration
                $permaConf = unserialize(
                    $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'core_permalogin' ]
                );
                
                // Check permalogin type
                switch( $permaConf[ 'permalogin' ] ) {
                    
                    // Enabled
                    case '1':
                    
                        // Add checkbox field
                        $conf[ 'dataArray.' ][ '30.' ] = array(
                            'label'         => $labels[ 'permalogin' ], // Label
                            'type'          => 'permalogin=radio',      // Type
                            'valueArray.'   => array(                   // Values
                                
                                // Enable
                                '10.'       => array(
                                    'label' => '*' . $labels[ 'yes' ],  // Label
                                    'value' => 1                        // Value
                                ),
                                
                                // Disable
                                '20.'       => array(
                                    'label' => $labels[ 'no' ],         // Label
                                    'value' => 0                        // Value
                                )
                            )
                        );
                    break;
                    
                    // Disabled
                    case '0':
                    
                        // Add checkbox field
                        $conf[ 'dataArray.' ][ '30.' ] = array(
                            'label'         => $labels[ 'permalogin' ], // Label
                            'type'          => 'permalogin=radio',      // Type
                            'valueArray.'   => array(                   // Values
                                
                                // Enable
                                '10.'       => array(
                                    'label' => $labels[ 'yes' ],        // Label
                                    'value' => 1                        // Value
                                ),
                                
                                // Disable
                                '20.'       => array(
                                    'label' => '*' . $labels[ 'no' ],   // Label
                                    'value' => 0                        // Value
                                )
                            )
                        );
                    break;
                }
            }
        }
        
        // Form action
        $formAction = $this->pObj->cObj->typoLink_URL(
            array(
                'parameter'    => $GLOBALS[ 'TSFE' ]->id,
                'useCacheHash' => 1
            )
        );
        
        // Checks the return mode
        if( $asTsArray ) {
            
            return array(
                'ts'     => $conf,
                'action' => $formAction
            );
            
        } else {
            
            // Builds the login box
            $form = $this->pObj->cObj->FORM( $conf );
            
            // Fixes the form action and returns the full form
            return preg_replace(
                '/form action="[^"]+"/',
                'form action="' . $formAction . '"',
                $form
            );
        }
    }
    
    /**
     * Build a search box.
     * 
     * This function constructs a standard Typo3 search box for use in
     * plugins. The result is basically the same as tslib_piBase::pi_list_searchBox,
     * but the output is valid XHTML, without tables.
     * 
     * @param       string      $method             The method for the search form (get or post)
     * @param       boolean     $nocache            Add a no cache flag
     * @param       string      $sword              The sword variable in piVars
     * @param       string      $pointer            The pointer variable in piVars
     * @return      string      A search box
     * @see         fe_makeStyledContent
     */
    function fe_buildSearchBox( $method = 'post', $nocache = true, $sword = 'sword', $pointer = 'pointer' )
    {
        // Sword
        $swordValue = ( array_key_exists( $sword, $this->pObj->piVars ) ) ? htmlspecialchars( $this->pObj->piVars[ $sword ] ) : '';
        
        // Storage
        $htmlCode   = array();
        
        // Header
        $htmlCode[] = $this->fe_makeStyledContent(
            'h2',
            'searchbox-header',
            $this->pObj->pi_getLL( 'searchbox.header', 'Search' )
        );
        
        // Start form
        $htmlCode[] = $this->fe_makeStyledContent(
            'form',
            'searchform',
            false,
            1,
            0,
            1,
            array(
                'action'  => htmlspecialchars( t3lib_div::getIndpEnv( 'REQUEST_URI' ) ),
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                'method'  => $method
            )
        );
        
        // Search word
        $htmlCode[] = $this->fe_makeStyledContent(
            'input',
            $sword,
            false,
            1,
            0,
            0,
            array(
                'type'  => 'text',
                'name'  => $this->pObj->prefixId . '[' . $sword . ']',
                'value' => $swordValue
            )
        );
        
        // Submit
        $htmlCode[] = $this->fe_makeStyledContent(
            'input',
            'submit',
            false,
            1,
            0,
            0,
            array(
                'type'  => 'submit',
                'value' => $this->pObj->pi_getLL( 'searchbox.submit', 'Submit' )
            )
        );
        
        // Cache option
        if( $nocache ) {
            
            // No cache
            $htmlCode[] = '<input name="no_cache" type="hidden" value="1" />';
        }
        
        // Pointer
        $htmlCode[] = '<input name="'
                    . $this->pObj->prefixId
                    . '['
                    . $pointer
                    . ']'
                    . '" type="hidden" value="" />';
        
        // End form
        $htmlCode[] = '</form>';
        
        // Return code
        return  $this->fe_makeStyledContent(
            'div',
            'searchbox',
            implode( chr( 10 ), $htmlCode )
        );
    }
    
    /**
     * Build a browse box.
     * 
     * This function constructs a standard Typo3 browse box for use in
     * plugins. The result is basically the same as tslib_piBase::pi_list_browseresults,
     * but the output is valid XHTML, without tables.
     * 
     * @param       string      $pointer            The pointer variable in piVars
     * @param       string      $count              The SQL count ressource in PI internal variables
     * @param       string      $maxResults         The max results number in PI internal variables
     * @param       string      $maxPages           The max pages number in PI internal variables
     * @return      string      A browse box
     * @see         fe_makeStyledContent
     */
    function fe_buildBrowseBox( $pointer = 'pointer', $count = 'res_count', $maxResults = 'results_at_a_time', $maxPages = 'maxPages' )
    {
        // Base variables
        $PI_pointer    = ( array_key_exists( $pointer, $this->pObj->piVars ) ) ? intval( $this->pObj->piVars[ $pointer ] ) : 0;
        $PI_count      = ( array_key_exists( $count, $this->pObj->internal ) ) ? intval( $this->pObj->internal[ $count ] ) : 0;
        $PI_maxResults = t3lib_div::intInRange( $this->pObj->internal[ $maxResults ], 1, 1000 );
        $PI_totalPages = ceil( $PI_count / $PI_maxResults );
        $PI_maxPages   = t3lib_div::intInRange( $this->pObj->internal[ $maxPages ], 1, 100 );
        
        // Storage
        $htmlCode      = array();
        
        // Results numbers
        $rN1           = ( $PI_pointer * $PI_maxResults ) + 1;
        $rN2           = ( ( $PI_pointer * $PI_maxResults ) + $PI_maxResults > $PI_count ) ? $PI_count : ( $PI_pointer * $PI_maxResults ) + $PI_maxResults;
        
        // Results number
        $htmlCode[]    = $this->fe_makeStyledContent(
            'div',
            'results',
            sprintf(
                $this->pObj->pi_getLL( 'browsebox.results' ),
                $rN1,
                $rN2,
                $PI_count
            )
        );
        
        // Check for multiple pages
        if( $PI_totalPages > 1 ) {
            
            // Number of pages to show
            $showPages  = ( $PI_totalPages < $PI_maxPages ) ? $PI_totalPages : $PI_maxPages;
            
            // Start list
            $htmlCode[] = $this->fe_makeStyledContent(
                'ul',
                'pages',
                false,
                1,
                0,
                1
            );
            
            // Check pointer
            if( $PI_pointer > 0 ) {
                
                // Previous link
                $prevLink   = $this->pObj->pi_linkTP_keepPIvars(
                    $this->pObj->pi_getLL( 'browsebox.previous' ),
                    array(
                        $pointer => $PI_pointer - 1
                    )
                );
                
                // Add next link
                $htmlCode[] = $this->fe_makeStyledContent(
                    'li',
                    'previous',
                    $prevLink
                );
            }
            
            // Build pages links
            for( $i = 0; $i < $showPages; $i++ ) {
                
                // CSS class
                $class      = ( $i == $PI_pointer ) ? 'cur' : 'page';
                
                // Page link
                $pageLink   = $this->pObj->pi_linkTP_keepPIvars(
                    sprintf(
                        $this->pObj->pi_getLL( 'browsebox.page' ),
                        $i + 1
                    ),
                    array(
                        $pointer => $i
                    )
                );
                
                // Add list item
                $htmlCode[] = $this->fe_makeStyledContent(
                    'li',
                    $class,
                    $pageLink
                );
            }
            
            // Check pointer
            if( $PI_pointer < $PI_totalPages - 1 ) {
                
                // Next link
                $nextLink   = $this->pObj->pi_linkTP_keepPIvars(
                    $this->pObj->pi_getLL( 'browsebox.next' ),
                    array(
                        $pointer => $PI_pointer + 1
                    )
                );
                
                // Add next link
                $htmlCode[] = $this->fe_makeStyledContent(
                    'li',
                    'next',
                    $nextLink
                );
            }
            
            // End list
            $htmlCode[] = '</ul>';
        }
        
        // Return code
        return  $this->fe_makeStyledContent(
            'div',
            'browsebox',
            implode( chr( 10 ), $htmlCode )
        );
    }
    
    /**
     * Includes the Prototype framework
     * 
     * This function includes the Prototype JavaScript framework by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a frontend context.
     * 
     * @return      boolean
     */
    function fe_includePrototypeJs()
    {
        // Checks if the script must be loaded from the API
        if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasPrototype' ] )
            && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasPrototype' ] == 1
        ) {
            
            // Script should not be loaded from the API
            $this->prototype = true;
        }
        
        // Check if script has already been included
        if( !$this->prototype ) {
            
            // Add script
            $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_apimacmade_prototype' ] = '<script src="'
                                                                                  . t3lib_extMgm::siteRelPath( 'api_macmade' )
                                                                                  . 'res/js/prototype/prototype.js'
                                                                                  . '" type="text/javascript"></script>';
            
            // Set included flag
            $this->prototype = true;
        }
        
        return true;
    }
    
    /**
     * Includes the Mootools framework
     * 
     * This function includes the Mootools JavaScript framework by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a frontend context.
     * 
     * @return      boolean
     */
    function fe_includeMootoolsJs()
    {
        // Checks if the script must be loaded from the API
        if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasMootools' ] )
            && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasMootools' ] == 1
        ) {
            
            // Script should not be loaded from the API
            $this->mootools = true;
        }
        
        // Check if script has already been included
        if( !$this->mootools ) {
            
            // Add script
            $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_apimacmade_mootools' ] = '<script src="'
                                                                                 . t3lib_extMgm::siteRelPath( 'api_macmade' )
                                                                                 . 'res/js/mootools/mootools.js'
                                                                                 . '" type="text/javascript"></script>';
            
            // Set included flag
            $this->mootools = true;
        }
        
        return true;
    }
    
    /**
     * Includes the Scriptaculous framework
     * 
     * This function includes the Scriptaculous JavaScript framework by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a frontend context.
     * 
     * @return      boolean
     * @see         fe_includePrototypeJs
     */
    function fe_includeScriptaculousJs()
    {
        // Checks if the script must be loaded from the API
        if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasScriptaculous' ] )
            && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasScriptaculous' ] == 1
        ) {
            
            // Script should not be loaded from the API
            $this->scriptaculous = true;
        }
        
        // Include prototype
        $this->fe_includePrototypeJs();
        
        // Check if script has already been included
        if( !$this->scriptaculous ) {
            
            // Add script
            $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_apimacmade_scriptaculous' ] = '<script src="'
                                                                                      . t3lib_extMgm::siteRelPath( 'api_macmade' )
                                                                                      . 'res/js/scriptaculous/src/scriptaculous.js'
                                                                                      . '" type="text/javascript"></script>';
            
            // Set included flag
            $this->scriptaculous = true;
        }
        
        return true;
    }
    
    /**
     * Includes the lightbox script
     * 
     * This function includes the lightbox JavaScript by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a frontend context.
     * 
     * @param       boolean     $includeCss     If set, the default lightbox CSS styles will be included
     * @return      boolean
     * @see         fe_includeScriptaculousJs
     */
    function fe_includeLightBoxJs( $includeCss = true )
    {
        // Checks if the script must be loaded from the API
        if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasLightBox' ] )
            && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasLightBox' ] == 1
        ) {
            
            // Script should not be loaded from the API
            $this->lightbox = true;
        }
        
        // Include Scriptaculous
        $this->fe_includeScriptaculousJs();
        
        // Check if script has already been included
        if( !$this->lightbox ) {
            
            // Extension relative path
            $extPath = t3lib_extMgm::siteRelPath( 'api_macmade' );
            
            // Add script
            $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_apimacmade_lightbox' ] = '<script src="'
                                                                                 . $extPath
                                                                                 . 'res/js/lightbox/js/lightbox.js'
                                                                                 . '" type="text/javascript"></script>'
                                                                                 . chr( 10 )
                                                                                 . '<script type="text/javascript" charset="utf-8">'
                                                                                 . chr( 10 )
                                                                                 . '// <![CDATA['
                                                                                 . chr( 10 )
                                                                                 . 'var fileLoadingImage = "' . $extPath . 'res/js/lightbox/images/loading.gif";'
                                                                                 . chr( 10 )
                                                                                 . 'var fileBottomNavCloseImage = "' . $extPath . 'res/js/lightbox/images/closelabel.gif";'
                                                                                 . chr( 10 )
                                                                                 . '// ]]>'
                                                                                 . chr( 10 )
                                                                                 . '</script>';
            
            // Checks if the CSS styles must be included
            if( $includeCss ) {
                
                // Adds the CSS styles
                $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_apimacmade_lightbox_css' ] = '<link rel="stylesheet" href="'
                                                                                         . $extPath
                                                                                         . 'res/js/lightbox/css/lightbox.css'
                                                                                         . '" type="text/css" media="screen" charset="utf-8" />'
                                                                                         . chr( 10 )
                                                                                         . '<style type="text/css" media="screen">'
                                                                                         . chr( 10 )
                                                                                         . '/* <![CDATA[ */'
                                                                                         . chr( 10 )
                                                                                         . '#prevLink:hover, #prevLink:visited:hover { background: url( "' . $extPath . 'res/js/lightbox/images/prevlabel.gif" ) left 15% no-repeat; }'
                                                                                         . chr( 10 )
                                                                                         . '#nextLink:hover, #nextLink:visited:hover { background: url( "' . $extPath . 'res/js/lightbox/images/nextlabel.gif" ) right 15% no-repeat; }'
                                                                                         . chr( 10 )
                                                                                         . '/* ]]> */'
                                                                                         . chr( 10 )
                                                                                         . '</style>';
            }
            
            // Set included flag
            $this->lightbox = true;
        }
        
        return true;
    }
    
    /**
     * Includes the UFO script
     * 
     * This function includes the UFO JavaScript script by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a frontend context.
     * 
     * @return      boolean
     */
    function fe_includeUfo()
    {
        // Checks if the script must be loaded from the API
        if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasUfo' ] )
            && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasUfo' ] == 1
        ) {
            
            // Script should not be loaded from the API
            $this->ufo = true;
        }
        
        // Check if script has already been included
        if( !$this->ufo ) {
            
            // Add script
            $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_apimacmade_ufo' ] = '<script src="'
                                                                            . t3lib_extMgm::siteRelPath( 'api_macmade' )
                                                                            . 'res/js/ufo/ufo.js'
                                                                            . '" type="text/javascript"></script>';
            
            // Set included flag
            $this->ufo = true;
        }
        
        return true;
    }
    
    /**
     * Includes the SWFObject script
     * 
     * This function includes the SWFObject JavaScript script by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a frontend context.
     * 
     * @return      boolean
     */
    function fe_includeSwfObject()
    {
        // Checks if the script must be loaded from the API
        if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasSwfObject' ] )
            && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasSwfObject' ] == 1
        ) {
            
            // Script should not be loaded from the API
            $this->swfObject = true;
        }
        
        // Check if script has already been included
        if( !$this->swfObject ) {
            
            // Add script
            $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_apimacmade_swfObject' ] = '<script src="'
                                                                                  . t3lib_extMgm::siteRelPath( 'api_macmade' )
                                                                                  . 'res/js/swfobject1-5/swfobject.js'
                                                                                  . '" type="text/javascript"></script>';
            
            // Set included flag
            $this->swfObject = true;
        }
        
        return true;
    }
    
    /**
     * Includes a script from WebToolKit
     * 
     * This function includes a JavaScript script from WebToolKit by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a frontend context. Available scripts are base64, crc32, md5, sha1,
     * sha256, url and utf8.
     * 
     * @param       string      $file       The name of the script to include
     * @return      boolean
     */
    function fe_includeWebToolKitJs( $file )
    {
        // Checks for a valid name
        if( !isset( $this->webToolKit[ $file ] ) ) {
            
            // The requested file is not available
            return false;
        }
        
        // Checks if the script must be loaded from the API
        if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasWebToolkit.' ][ $file ] )
            && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_apimacmade_pi1.' ][ 'hasWebToolkit.' ][ $file ] == 1
        ) {
            
            // Script should not be loaded from the API
            $this->webToolKit[ $file ] = true;
        }
        
        // Check if script has already been included
        if( !$this->webToolKit[ $file ] ) {
            
            // Add script
            $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_apimacmade_webToolKit_' . $file ] = '<script src="'
                                                                                            . t3lib_extMgm::siteRelPath( 'api_macmade' )
                                                                                            . 'res/js/webtoolkit/webtoolkit.'
                                                                                            . $file
                                                                                            . '.js'
                                                                                            . '" type="text/javascript"></script>';
            
            // Set included flag
            $this->webToolKit[ $file ] = true;
        }
        
        return true;
    }
    
    
    
    
    
    /***************************************************************
     * SECTION 3 - BE
     *
     * Functions for backend development.
     * 
     * All of those functions are only available in a backend context.
     * They also all need the API class to be instantiated, as they will
     * use the internal variable $pObj.
     * 
     * Do not try to use them out of a backend context, and without
     * the API class instantiated.
     ***************************************************************/
    
    /**
     * Build action icons.
     * 
     * This function creates the icon(s) associated with an action to
     * do on a record. Compatible with Typo3 skinning functions.
     * 
     * @param       string      $actions            The action(s) to produce, as a comma list (can be 'show', 'edit' or 'delete' at the moment).
     * @param       string      $table              The table of the record
     * @param       int         $uid                The record uid
     * @return      string      The record icons
     */
    function be_buildRecordIcons( $actions, $table, $uid )
    {
        // Storage
        $htmlCode = array();
        
        // Get actions
        $actionsArray = explode( ',', $actions );
        
        // Link parameters
        $linkParams = array(
            'edit'   => '&edit[' . $table . '][' . $uid . ']=edit',
            'delete' => '&cmd[' . $table . ']['. $uid .'][delete]=1'
        );
        
        // Icons & links definitions
        $icons = array(
            'show'     => array(
                'file' => 'gfx/zoom2.gif',
                'link' => '<a href="#" onclick="top.launchView(\''
                       .  $table
                       .  '\', \''
                       .  $uid
                       .  '\'); return false;">'
            ),
            'edit'     => array(
                'file' => 'gfx/edit2.gif',
                'link' => '<a href="#" onclick="javascript:'
                       .  htmlspecialchars( t3lib_BEfunc::editOnClick( $linkParams[ 'edit' ], $GLOBALS[ 'BACK_PATH' ] ) )
                       .  '">'
            ),
            'delete'   => array(
                'file' => 'gfx/garbage.gif',
                'link' => '<a href="'
                       .  htmlspecialchars( $GLOBALS[ 'SOBE' ]->doc->issueCommand( $linkParams[ 'delete' ] ) )
                       .  '" onclick="'
                       .  htmlspecialchars( 'return confirm(\''
                       .  $GLOBALS[ 'LANG' ]->sL( 'LLL:EXT:lang/locallang_alt_doc.xml:deleteWarning' )
                       .  '\');' )
                       .  '">'
            )
        );
        
        // Build icons
        foreach( $actionsArray as $icon ) {
            
            // Check for a valid action
            if( array_key_exists( $icon, $icons ) ) {
            
                // Build link and <img> tags
                $htmlCode[] =  $icons[ $icon ][ 'link' ]
                            . '<img '
                            . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], $icons[ $icon ][ 'file' ], '' )
                            . ' alt="" hspace="0" vspace="0" border="0" align="middle"></a>';
            }
        }
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * Build a select menu representing the page tree
     * 
     * This function creates a select menu containing the page tree from a
     * specified page ID.
     * 
     * @param       string      $name               The name of the select
     * @param       int         $treeStartingPoint  The starting point for the page tree
     * @param       int         $size               The size of the select
     * @param       boolean     $multiple           Allow multiple selection
     * @param       boolean     $pageIcons          Render page icons in option tags
     * @return      string      A select menu with the page tree
     */
    function be_buildPageTreeSelect( $name, $treeStartingPoint = 0, $size = '1', $multiple = false, $pageIcons = 1 )
    {
        // New tree object
        $tree = t3lib_div::makeInstance( 't3lib_pageTree' );
        $tree->init( 'AND ' . $GLOBALS[ 'BE_USER' ]->getPagePermsClause( 1 ) );
        
        // Get tree
        $tree->getTree( $treeStartingPoint );
        
        // Storage
        $htmlCode      = array();
        
        // Multiple selection
        $multipleParam = ( $multiple ) ? ' multiple' : '';
        
        // Start select
        $htmlCode[]    = '<select name="'
                       . $name
                       . '" size="'
                       . $size
                       . '"'
                       . $multiple
                       . '>';
        
        // Depth of the first page
        $depth         = $tree->tree[ 0 ][ 'invertedDepth' ];
        
        // Process pages
        foreach( $tree->tree as $page ) {
            
            // Compute indentation
            $level  = $depth - $page[ 'invertedDepth' ];
            
            // Indentation
            $indent = '';
            
            // Create indent characters
            for( $i = 0; $i < $level; $i++ ) {
                
                // Indentation signs
                $indentChars = ( $i == $level - 1 ) ? '-->&nbsp;' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
                
                // Add indentation
                $indent     .= '|' . $indentChars;
            }
            
            // Create page icons?
            if( $pageIcons ) {
                
                // Style parameter
                $styleParam = ' style="'
                            . tx_apimacmade::be_getSelectStyleRecordIcon( 'pages', $page[ 'row' ], $GLOBALS[ 'BACK_PATH' ] ) 
                            . '"';
            }
            
            // Option
            $htmlCode[] = '<option value="'
                        . $page[ 'row' ][ 'uid' ]
                        . '"'
                        . $styleParam
                        . '>'
                        . $indent
                        . $page[ 'row' ][ 'title' ]
                        . '</option>';
        }
        
        // End select
        $htmlCode[] = '</select>';
        
        // Add content
        return implode( chr( 10 ),$htmlCode );
    }
    
    /**
     * Build CSS styles for select menu items
     * 
     * This function creates the CSS styles to correctly display a record
     * icon inside an option tag of a select menu.
     * 
     * @param       string      $table              The table
     * @param       array       $rec                The record row (array)
     * @param       string      $backPath           The back path to typo3 (usually $GLOBALS[ 'BACK_PATH' ])
     * @return      string      CSS styles ready to be included in a style parameter
     */
    function be_getSelectStyleRecordIcon( $table, $rec, $backPath )
    {
        // Get record icon path
        $iconPath = t3lib_iconWorks::getIcon( $table, $rec );
        
        // Get skin icon
        $skinIcon = t3lib_iconWorks::skinImg( $backPath, $iconPath, '', 1 );
        
        // Width & Height
        $infos    = @getimagesize( $skinIcon );
        
        // Top padding
        $padTop   = t3lib_div::intInRange( ( $infos[ 1 ] - 12) / 2, 0 );
        
        // Height
        $height   = t3lib_div::intInRange( ( $infos[ 1 ] + 2 ) - $padTop, 0 );
        
        // Style parameter
        $style    = 'background: url('
                  . $skinIcon
                  . '); background-repeat: no-repeat; padding-top: '
                  . $padTop
                  . '; padding-left: '
                  . ( $infos[ 0 ] + 1 )
                  . '; height: '
                  . $height
                  . ';';
        
        // Return style
        return $style;
    }
    
    /**
     * Init CSM
     * 
     * This function initialize the Contenxt Sensitive Menu (CSM), in order
     * to use it in a backend module.
     * 
     * @return      Boolean
     */
    function be_initCSM()
    {
        
        // Get CSM menu code
        $CMparts                           = $this->pObj->doc->getContextMenuCode();
        
        // Add parameters to BODY tag
        $this->pObj->doc->bodyTagAdditions = $CMparts[ 1 ];
        
        // Add JavaScript code
        $this->pObj->doc->JScode          .= $CMparts[ 0 ];
        
        // Add POST code
        $this->pObj->doc->postCode        .= $CMparts[ 2 ];
        
        return true;
    }
    
    /**
     * Get CSM menu for a record
     * 
     * This function creates an icon of the requested record with a
     * Context Sensitive Menu (CSM).
     * 
     * @param       string      $table              The table of the record
     * @param       array       $rec                The record's row
     * @param       string      $backPath           The back path to typo3 (usually $GLOBALS['BACK_PATH']).
     * @param       string      $align              The align parameter of the IMG tag
     * @return      string      The icon with CSM menu
     */
    function be_getRecordCSMIcon( $table, $rec, $backPath, $align='top' )
    {
        // Get record icon
        $icon = t3lib_iconWorks::getIconImage(
            $table,
            $rec,
            $backPath
        );
        
        // Return icon with CSM
        return $this->pObj->doc->wrapClickMenuOnIcon(
            $icon,
            $table,
            $rec[ 'uid' ],
            1
        );
    }
    
    /**
     * Includes the Prototype framework
     * 
     * This function includes the Prototype JavaScript framework by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a backend context.
     * 
     * @return      boolean
     */
    function be_includePrototypeJs()
    {
        // Check if script has already been included
        if( !$this->prototype ) {
            
            // Add script
            $this->pObj->doc->JScode .= chr( 10 )
                                     .  '<script src="'
                                     .  $GLOBALS[ 'BACK_PATH' ]
                                     .  t3lib_extMgm::extRelPath( 'api_macmade' )
                                     .  'res/js/prototype/prototype.js'
                                     .  '" type="text/javascript" charset="utf-8"></script>';
            
            // Set included flag
            $this->prototype = true;
        }
        
        return true;
    }
    
    /**
     * Includes the Prototype framework
     * 
     * This function includes the Prototype JavaScript framework by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a backend context.
     * 
     * @return      boolean
     */
    function be_includeMootoolsJs()
    {
        // Check if script has already been included
        if( !$this->mootools ) {
            
            // Add script
            $this->pObj->doc->JScode .= chr( 10 )
                                     .  '<script src="'
                                     .  $GLOBALS[ 'BACK_PATH' ]
                                     .  t3lib_extMgm::extRelPath( 'api_macmade' )
                                     .  'res/js/mootools/mootools.js'
                                     .  '" type="text/javascript" charset="utf-8"></script>';
            
            // Set included flag
            $this->mootools = true;
        }
        
        return true;
    }
    
    /**
     * Includes the Scriptaculous framework
     * 
     * This function includes the Scriptaculous JavaScript framework by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a backend context.
     * 
     * @return      boolean
     * @see         be_includePrototypeJs
     */
    function be_includeScriptaculousJs()
    {
        // Include prototype
        $this->be_includePrototypeJs();
        
        // Check if script has already been included
        if( !$this->scriptaculous ) {
            
            // Add script
            $this->pObj->doc->JScode .= chr( 10 )
                                     .  '<script src="'
                                     .  $GLOBALS[ 'BACK_PATH' ]
                                     .  t3lib_extMgm::extRelPath( 'api_macmade' )
                                     .  'res/js/scriptaculous/src/scriptaculous.js'
                                     .  '" type="text/javascript" charset="utf-8"></script>';
            
            // Set included flag
            $this->scriptaculous = true;
        }
        
        return true;
    }
    
    /**
     * Includes the lightbox script
     * 
     * This function includes the lightbox JavaScript by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a backend context.
     * 
     * @param       boolean     $includeCss     If set, the default lightbox CSS styles will be included
     * @return      boolean
     * @see         be_includeScriptaculousJs
     */
    function be_includeLightBoxJs( $includeCss = true )
    {
        // Include Scriptaculous
        $this->be_includeScriptaculousJs();
        
        // Check if script has already been included
        if( !$this->lightbox ) {
            
            // Extension relative path
            $extPath = $GLOBALS[ 'BACK_PATH' ] . t3lib_extMgm::extRelPath( 'api_macmade' );
            
            // Add script
            $this->pObj->doc->JScode .= '<script src="'
                                     .  $extPath
                                     .  'res/js/lightbox/js/lightbox.js'
                                     .  '" type="text/javascript"></script>'
                                     .  chr( 10 )
                                     .  '<script type="text/javascript" charset="utf-8">'
                                     .  chr( 10 )
                                     .  '// <![CDATA['
                                     .  chr( 10 )
                                     .  'var fileLoadingImage = "' . $extPath . 'res/js/lightbox/images/loading.gif";'
                                     .  chr( 10 )
                                     .  'var fileBottomNavCloseImage = "' . $extPath . 'res/js/lightbox/images/closelabel.gif";'
                                     .  chr( 10 )
                                     .  '// ]]>'
                                     .  chr( 10 )
                                     .  '</script>';
            
            // Checks if the CSS styles must be included
            if( $includeCss ) {
                
                // Adds the CSS styles
                $this->pObj->doc->JScode .= '<link rel="stylesheet" href="'
                                         .  $extPath
                                         .  'res/js/lightbox/css/lightbox.css'
                                         .  '" type="text/css" media="screen" charset="utf-8" />'
                                         .  chr( 10 )
                                         .  '<style type="text/css" media="screen">'
                                         .  chr( 10 )
                                         .  '/* <![CDATA[ */'
                                         .  chr( 10 )
                                         .  '#prevLink:hover, #prevLink:visited:hover { background: url( "' . $extPath . 'res/js/lightbox/images/prevlabel.gif" ) left 15% no-repeat; }'
                                         .  chr( 10 )
                                         .  '#nextLink:hover, #nextLink:visited:hover { background: url( "' . $extPath . 'res/js/lightbox/images/nextlabel.gif" ) right 15% no-repeat; }'
                                         .  chr( 10 )
                                         .  '/* ]]> */'
                                         .  chr( 10 )
                                         .  '</style>';
            }
            
            // Set included flag
            $this->lightbox = true;
        }
        
        return true;
    }
    
    /**
     * Includes the UFO script
     * 
     * This function includes the UFO JavaScript script by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a backend context.
     * 
     * @return      boolean
     */
    function be_includeUfo()
    {   
        // Check if script has already been included
        if( !$this->ufo ) {
            
            // Add script
            $this->pObj->doc->JScode .= chr( 10 )
                                     .  '<script src="'
                                     .  $GLOBALS[ 'BACK_PATH' ]
                                     .  t3lib_extMgm::extRelPath( 'api_macmade' )
                                     .  'res/js/ufo/ufo.js'
                                     .  '" type="text/javascript" charset="utf-8"></script>';
            
            // Set included flag
            $this->ufo = true;
        }
        
        return true;
    }
    
    /**
     * Includes the SWFObject script
     * 
     * This function includes the SWFObject JavaScript script by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a backend context.
     * 
     * @return      boolean
     */
    function be_includeSwfObject()
    {   
        // Check if script has already been included
        if( !$this->swfObject ) {
            
            // Add script
            $this->pObj->doc->JScode .= chr( 10 )
                                     .  '<script src="'
                                     .  $GLOBALS[ 'BACK_PATH' ]
                                     .  t3lib_extMgm::extRelPath( 'api_macmade' )
                                     .  'res/js/swfobject1-5/swfobject.js'
                                     .  '" type="text/javascript" charset="utf-8"></script>';
            
            // Set included flag
            $this->swfObject = true;
        }
        
        return true;
    }
    
    /**
     * Includes a script from WebToolKit
     * 
     * This function includes a JavaScript script from WebToolKit by adding a
     * script tag to the TYPO3 page headers. This method can only be used in
     * a backend context. Available scripts are base64, crc32, md5, sha1,
     * sha256, url and utf8.
     * 
     * @param       string      $file       The name of the script to include
     * @return      boolean
     */
    function be_includeWebToolKitJs( $file )
    {
        // Checks for a valid name
        if( !isset( $this->webToolKit[ $file ] ) ) {
            
            // The requested file is not available
            return false;
        }
        
        // Check if script has already been included
        if( !$this->webToolKit[ $file ] ) {
            
            // Add script
            $this->pObj->doc->JScode .= chr( 10 )
                                     .  '<script src="'
                                     .  $GLOBALS[ 'BACK_PATH' ]
                                     .  t3lib_extMgm::extRelPath( 'api_macmade' )
                                     .  'res/js/webtoolkit/webtoolkit.'
                                     .  $file
                                     .  '.js'
                                     .  '" type="text/javascript" charset="utf-8"></script>';
            
            // Set included flag
            $this->webToolKit[ $file ] = true;
        }
        
        return true;
    }
    
    
    
    
    
    /***************************************************************
     * SECTION 4 - DB
     *
     * Functions related to databases queries.
     * 
     * Those functions are available in any context, but they will need
     * the AI class to be instantiated most of the time.
     ***************************************************************/
    
    /**
     * Export a database table to text format.
     * 
     * This function returns a database table contents as a formatted text
     * variable. By default, it generates a tabulated text file.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $table              The table to export
     * @param       string      $fieldList          The list of fields to select from the table
     * @param       string      $whereClause        Optional WHERE clauses put in the end of the query, if none, supply blank string
     * @param       string      $groupBy            Optional GROUP BY field(s), if none, supply blank string
     * @param       string      $orderBy            Optional ORDER BY field(s), if none, supply blank string.
     * @param       string      $limit              Optional LIMIT value ([begin,]max), if none, supply blank string
     * @param       int         $sepField           The ASCII separator character for each field
     * @param       int         $sepRow             The ASCII separator character for each row
     * @param       boolean     $directOut          Output the generated file directly as a text file
     * @param       string      $directOutCharset   The charset for the output file
     * @return      string      The formatted database table contents
     */
    function db_table2text( $table, $fieldList = '*', $whereClause = '', $groupBy = '', $orderBy = '', $limit = '', $sepField = 9, $sepRow = 10, $directOut = 0, $directOutCharset = 'iso-8859-1' )
    {
        // Output storage
        $output = array();
        
        // MySQL ressource
        $res    = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            $fieldList,
            $table,
            $whereClause,
            $groupBy,
            $orderBy,
            $limit
        );
        
        // Process each row
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
            
            // Process fields
            $output[] = implode( chr( $sepField ), $row );
        }
        
        // Prepare content
        $content = implode( chr( $sepRow ), $output );
        
        // Check output method
        if( $directOut ) {
            
            // Output content
            tx_apimacmade::div_output(
                $content,
                'text/plain',
                't3db_' . $table . '.txt',
                'attachment',
                $directOutCharset
            );
            
        } else {
            
            // Return content
            return $content;
        }
    }
    
    /**
     * Export a database table to XML format.
     * 
     * This function returns a database table contents as an XML object.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $table              The table to export
     * @param       string      $fieldList          The list of fields to select from the table
     * @param       string      $whereClause        Optional WHERE clauses put in the end of the query, if none, supply blank string
     * @param       string      $groupBy            Optional GROUP BY field(s), if none, supply blank string
     * @param       string      $orderBy            Optional ORDER BY field(s), if none, supply blank string
     * @param       string      $limit              Optional LIMIT value ([begin,]max), if none, supply blank string
     * @param       boolean     $uppercase          Use uppercase for XML tags
     * @param       boolean     $xmlDeclaration     Insert XML declaration
     * @param       string      $xmlVersion         The XML version
     * @param       string      $xmlEncoding        The XML encoding
     * @param       boolean     $directOut          Output the generated file directly as an XML file
     * @param       mixed       $ns                 The XML namespace
     * @param       string      $nsPrefix           The prefix used in the XML to link elements to the namespace
     * @return      string      The database table contents as XML nodes
     * @see         div_output
     */
    function db_table2xml( $table, $fieldList = '*', $whereClause = '', $groupBy = '', $orderBy = '', $limit = '', $uppercase = 0, $xmlDeclaration = 1, $xmlVersion = '1.0', $xmlEncoding = 'iso-8859-1', $directOut = 0, $ns = false, $nsPrefix = 'ns' )
    {
        // Output storage
        $output = array();
        
        // Check if an XML declaration must be inserted
        if( $xmlDeclaration ) {
            
            // Add XML declaration
            $output[] = '<?xml version="'
                      . $xmlVersion
                      . '" encoding="'
                      . $xmlEncoding
                      . '"?'
                      . '>';
        }
        
        // Check if a namespace must be inserted
        if( $ns ) {
            
            // Add namespace and update NS prefix
            $ns        = ' xmlns:' . $nsPrefix . '="' . $ns . '"';
            $nsPrefix .= ':';
            
        } else {
            
            // Remove NS prefix
            $nsPrefix = '';
        }
        
        // Add comments
        $output[] = '<!--';
        $output[] = '- Typo3 XML Export';
        $output[] = '- ';
        $output[] = '- Generation Time: ' . date( 'r', time() );
        $output[] = '- ';
        $output[] = '- Host: ' . TYPO3_db_host;
        $output[] = '- Database: ' . TYPO3_db;
        $output[] = '- Table: ' . $table;
        $output[] = '-->';
        
        // Prepare generic tag names
        $tags = array(
            'db'    => $nsPrefix . ( ( $uppercase ) ? strtoupper( TYPO3_db ) : strtolower( TYPO3_db ) ),
            'table' => $nsPrefix . ( ( $uppercase ) ? strtoupper( $table )   : strtolower( $table ) )
        );
        
        // Begin database node
        $output[] = '<' . $tags[ 'db' ] . $ns . '>';
        
        // MySQL ressource
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            $fieldList,
            $table,
            $whereClause,
            $groupBy,
            $orderBy,
            $limit
        );
        
        // Begin table node
        $output[] = chr( 9 )
                  . '<'
                  . $tags[ 'table' ]
                  . '>';
        
        // Row counter
        $rowCount = 0;
        
        // Process each row
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
            
            // Increase row count
            $rowCount++;
            
            // Begin row node
            $output[] = chr( 9 )
                      . '<'
                      . $nsPrefix
                      . 'record'
                      . ' n="'
                      . $rowCount
                      . '">';
            
            // Process fields
            foreach( $row as $key => $value ) {
                
                // Procude field name
                $name = $nsPrefix . ( ($uppercase ) ? strtoupper( $key ) : strtolower( $key ) );
                
                // Check if value must be protected
                if( strstr( $value, '&' ) || strstr( $value, '<' ) ) {
                    
                    $value = '<![CDATA[' . $value . ']]>';
                }
                
                // Begin table node
                $output[] = chr( 9 )
                          . chr( 9 )
                          . chr( 9 )
                          . '<'
                          . $name
                          . '>'
                          . $value
                          . '</'
                          . $name
                          . '>';
            }
            
            // End table node
            $output[] = chr( 9 )
                      . '</'
                      . $nsPrefix
                      . 'record>';
        }
        
        // End table node
        $output[] = chr( 9 )
                  . '</'
                  . $tags[ 'table' ]
                  . '>';
        
        // End database node
        $output[] = '</'
                  . $tags[ 'db' ]
                  . '>';
        
        // Prepare content
        $content = implode( chr( 10 ), $output );
        
        // Check output method
        if( $directOut ) {
            
            // Output content
            tx_apimacmade::div_output(
                $content,
                'text/xml',
                't3db_' . $table . '.xml',
                'attachment',
                $xmlEncoding
            );
            
        } else {
            
            // Return content
            return $content;
        }
    }
    
    
    
    
    
    /***************************************************************
     * SECTION 5 - DIV
     *
     * General purposes functions for miscellaneous development.
     * 
     * Those functions are available in any context. You also don't
     * need the instantiate the API class to use them, as they don't
     * need a parent object.
     ***************************************************************/
    
    /**
     * String format conversion.
     * 
     * This function converts a string with ISO-8859-1 characters encoded with UTF-8
     * to single-byte ISO-8859-1.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $content            The content to convert
     * @return      string      An ISO-8859-1 string.
     */
    function div_utf8ToIso( $content )
    {
        // Convert content
        $isoContent = utf8_decode( $content );
        
        // Return converted content
        return $isoContent;
    }
    
    /**
     * Returns an age.
     * 
     * This function returns an age, calculated from a timestamp. By default, the function
     * takes the current time as reference, but another timestamp can be specified. The function
     * also returns by default the age in days, but it can also returns it in seconds, minutes or
     * hours.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       int         $tstamp             The base timestamp
     * @param       int         $currentTime        The time from which to calculate the age (timestamp). Will use current time if none supplied
     * @param       mixed       $ageType            The type of age to return (seconds, minutes, hours, or days)
     * @return      int         An age, as a numeric value
     */
    function div_getAge( $tstamp, $currentTime = false, $ageType = false )
    {
        // Process age types
        switch( $ageType ) {
            
            // Seconds
            case 'seconds':
                $division = 1;
            break;
            
            // Minutes
            case 'minutes':
                $division = 60;
            break;
            
            // Hours
            case 'hours':
                $division = 3600;
            break;
            
            // Default - Days
            default:
                $division = 86400;
            break;
        }
        
        // Get current time, if none specified
        if( !$currentTime ) {
            $currentTime = time();
        }
        
        // Get differences between the two timestamps
        $diff = $currentTime - $tstamp;
        
        // Return age
        return ceil( $diff / $division );
    }
    
    /**
     * Write tag parameters.
     * 
     * This function write, from an array, every given key, with it's value, as an
     * HTML tag parameter.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       array       $params             An array with the tag parameters as key/value pairs
     * @return      string      A string with all tag parameters formatted as HTML
     */
    function div_writeTagParams( $params )
    {
        // Check arguments
        if( !is_array( $params ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $params must be an array.', __LINE__ );
            return false;
        }
    
        // Parameters storages
        $tagParams = array();
        
        // Process each parameter
        foreach( $params as $key => $value ) {
            
            // Store parameter
            $tagParams[] = $key . '="' . $value . '"';
        }
        
        // Return parameters
        return implode( ' ', $tagParams );
    }
    
    /**
     * Check for valid variables.
     * 
     * This function checks for the type of multiples variables, passed in an array.
     * It's used to check with one function call multiple variable which should be
     * of the same type.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       array       $vars               An array with the variables to check
     * @param       string      $type               The type of the variables to check for
     * @return      boolean     True if every variable in the input array correspond to the given type
     * @see         div_isType
     */
    function div_checkVarType( $vars, $type = 'array' )
    {
        // Check for a valid input array
        if( !is_array( $vars ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $vars must be an array.', __LINE__ );
            return false;
        }
        
        // Result
        $result = false;
        
        // Check each variable
        foreach( $vars as $object ) {
            
            // Check if each object is valid
            if( tx_apimacmade::div_isType( $object, $type ) ) {
                
                // Valid object
                $result = true;
                
            } else {
                
                // Invalid object - Exit and return false
                $result = false;
                break;
            }
        }
        
        // Return result
        return $result;
    }
    
    /**
     * Clean an array
     * 
     * This function process an array and removes all the keys given as second parameter.
     * It can also keep only the given keys if the $inverse parameter is set.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       array       $input              The array to process
     * @param       string      $keys               A comma list of the keys to remove or keep in the input array
     * @param       boolean     $inverse            If reverse is set, the function will keep only the keys supplied in the array. Otherwise, it will remove them.
     * @return      array       A cleaned array
     */
    function div_cleanArray( $input, $keys, $inverse = 0 )
    {
        // Check arguments
        if( is_array( $input ) ) {
            
            // Get an array whith all the keys to unset in the input array
            $arrayKeys = explode( ',', $keys );
            
            // Check how to handle keys (keep or delete)
            if( $inverse ) {
                
                // Keep only specified keys
                foreach( $input as $key => $value ) {
                    
                    // Unset keys not in list
                    if( !in_array( $key, $arrayKeys ) ) {
                        
                        unset( $input[ $key ] );
                    }
                }
                
            } else {
                
                // Remove specified keys
                foreach( $arrayKeys as $key ) {
                    
                    // Unset item
                    unset( $input[ $key ] );
                }
            }
            
            // Return cleaned up array
            return $input;
            
        }
        
        // Error
        tx_apimacmade::errorMsg( __METHOD__, 'The argument $vars must be an array.', __LINE__ );
        return false;
    }
    
    /**
     * Returns a base URL.
     * 
     * This function is used to get only the host part of an URL.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $url                The URL to process
     * @param       boolean     $http               If set, add http:// before the base URL
     * @param       boolean     $trailingSlash      If set, add a slash after the base URL
     * @return      string      A base URL
     */
    function div_baseURL( $url, $http = 1, $trailingSlash = 1 )
    {
        // Check the link and keep only the base URL
        if( strstr( $url, 'http://' ) ) {
            
            // URL with http://
            $baseURL = ereg_replace( 'http://([^\/]+).*', '\1', $url );
            
        } else {
            
            // URL without http://
            $baseURL = ereg_replace( '([^\/]+).*', '\1', $url );
        }
        
        // Add http:// if required
        if( $http ) {
            
            $baseURL = 'http://' . $baseURL;
        }
        
        // Add trailing slash if required
        if( $trailingSlash ) {
            
            $baseURL .= '/';
        }
        
        // Return base URL
        return $baseURL;
    }
    
    /**
     * Create a vCard.
     * 
     * This function produces a vCard (.vcf format) from an array.
     * 
     * Here's an example input array:
     * 
     * array(
     *      'firstname'       => string     // First name
     *      'name'            => string     // Last name
     *      'username'        => string     // Nick name
     *      'company'         => string     // Company
     *      'department'      => string     // Department
     *      'title'           => string     // Job title
     *      'www'             => string     // Home page
     *      'note'            => string     // Notes
     *      'birthday'        => tstamp     // Birtday
     *      'email            => array(
     *          array(
     *              'mail'    => string     // Email address
     *              'type'    => string     // Type (WORK - HOME - Other)
     *          )
     *      )
     *      'phone            => array(
     *          array(
     *              'number'  => string     // Number
     *              'type'    => string     // Type (WORK - HOME - CELL - MAIN - HOMEFAX - WORKFAX - Other)
     *          )
     *      )
     *      'messenger        => array(
     *          array(
     *              'name'    => string     // Account name
     *              'service' => string     // Service (AIM - JABBER - MSN - YAHOO - ICQ)
     *              'type'    => string     // Type (WORK - HOME - Other)
     *          )
     *      )
     *      address           => array(
     *          array(
     *              'street'  => string     // Street
     *              'city'    => string     // City
     *              'state'   => string     // State
     *              'zip'     => string     // ZIP code
     *              'country' => string     // Country
     *              'type'    => string     // Type (WORK - HOME - Other)
     *          )
     *      )
     *      'image'           => string     // Picture reference
     *      'iscompany'       => boolean    // Define as company
     *  )
     * 
     * The image must be a valid path to an image. It will be
     * read and encoded in base64 chunks.
     * 
     * To create a pack of vCards, just call this function the
     * number of time necessary, and concatenate the results.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       array       $user               The user array
     * @param       string      $version            The vCard version
     * @param       mixed       $charset            An optionnal charset to use for the properties
     * @return      string      The vCard content, ready for output
     */
    function div_vCardCreate( $user, $version = '3.0', $charset = false )
    {
        if( !is_array( $user ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $user must be an array.', __LINE__ );
            return false;
        }
    
        // vCard fields storage
        $vCard = array();
        
        // Check charset
        if( $charset ) {
            
            // Create charset property
            $charset = ';CHARSET=' . $charset;
        }
        
        // Items number counter
        $items = 1;
        
        // Begin vCard
        $vCard[] = 'BEGIN:VCARD';
        $vCard[] = 'VERSION:' . $version;
        
        // Process fixed fields
        $vCard[] = 'N'
                 . $charset
                 . ':'
                 . $user[ 'name' ]
                 . ';'
                 . $user[ 'firstname' ]
                 . ';;;';
        $vCard[] = 'NICKNAME'
                 . $charset
                 . ':'
                 . $user[ 'username' ];
        $vCard[] = 'ORG'
                 . $charset
                 . ':'
                 . $user[ 'company' ]
                 . ';'
                 . $user[ 'department' ];
        $vCard[] = 'TITLE'
                 . $charset
                 . ':'
                 . $user[ 'title' ];
        $vCard[] = 'URL:'
                 . $user[ 'www' ];
        $vCard[] = 'BDAY;value=date:'
                 . date( 'Y-m-d', $user[ 'birthday' ] );
        $vCard[] = 'NOTE'
                 . $charset
                 . ':'
                 . $user[ 'note' ];
        
        // Create email fields
        if( is_array( $user[ 'email' ] ) && count( $user[ 'email' ] ) ) {
            
            // Process each email
            foreach( $user[ 'email' ] as $email ) {
                
                // Check valid array
                if( is_array( $email ) ) {
                    
                    // Check type
                    if( $email[ 'type' ] == 'WORK' || $email[ 'type' ] == 'HOME' ) {
                        
                        // Add standard email
                        $vCard[] = 'EMAIL;type=INTERNET;type='
                                 . $email[ 'type' ] 
                                 . ':' 
                                 . $email[ 'mail' ];
                        
                    } else {
                        
                        // Add custom email
                        $vCard[] = 'item'
                                 . $items
                                 . '.EMAIL;type=INTERNET:'
                                 . $email[ 'mail' ];
                        $vCard[] = 'item'
                                 . $items
                                 . '.X-ABLabel:_$!<Other>!$_';
                        
                        // Increase item counter
                        $item++;
                    }
                }
            }
        }
        
        // Create messenger fields
        if( is_array( $user[ 'messenger' ] ) && count( $user[ 'messenger' ] ) ) {
            
            // Process each account
            foreach( $user[ 'messenger' ] as $messenger ) {
                
                // Check valid array
                if( is_array( $messenger ) ) {
                    
                    // Check type
                    if( $messenger[ 'type' ] == 'WORK' || $messenger[ 'type' ] == 'HOME' ) {
                        
                        // Add standard messenger
                        $vCard[] = 'X-'
                                 . $messenger[ 'service' ]
                                 . ';type='
                                 . $messenger[ 'type' ]
                                 . ':'
                                 . $messenger[ 'name' ];
                        
                    } else {
                        
                        // Add custom messenger
                        $vCard[] = 'item'
                                 . $items
                                 . '.X-'
                                 . $messenger[ 'service' ]
                                 . ':'
                                 . $messenger[ 'mail' ];
                        $vCard[] = 'item'
                                 . $items 
                                 . '.X-ABLabel:_$!<Other>!$_';
                        
                        // Increase item counter
                        $item++;
                    }
                }
            }
        }
        
        // Create address fields
        if( is_array( $user[ 'address' ] ) && count( $user[ 'address' ] ) ) {
            
            // Process each address
            foreach( $user[ 'address' ] as $address ) {
                
                // Check valid array
                if( is_array( $address ) ) {
                    
                    // Check type
                    if( $address[ 'type' ] == 'WORK' || $address[ 'type' ] == 'HOME' ) {
                        
                        // Add standard address
                        $vCard[] = 'item'
                                 . $items
                                 . '.ADR'
                                 . $charset
                                 . ';type='
                                 . $address[ 'type' ]
                                 . ':;;'
                                 . $address[ 'street' ]
                                 . ';'
                                 . $address[ 'city' ]
                                 . ';'
                                 . $address[ 'state' ]
                                 . ';'
                                 . $address[ 'zip' ]
                                 . ';'
                                 . $address[ 'country' ];
                        
                    } else {
                        
                        // Add custom address
                        $vCard[] = 'item'
                                 . $items
                                 . '.ADR'
                                 . $charset
                                 . ';type=HOME:;;'
                                 . $address[ 'street' ]
                                 . ';'
                                 . $address[ 'city' ]
                                 . ';'
                                 . $address[ 'state' ]
                                 . ';'
                                 . $address[ 'zip' ]
                                 . ';'
                                 . $address[ 'country' ];
                        $vCard[] = 'item'
                                 . $items
                                 . '.X-ABLabel:_$!<Other>!$_';
                    }
                    
                    // Fixed fields
                    $vCard[] = 'item'
                             . $items
                             . '.X-ABADR:us';
                    
                    // Increase item counter
                    $item++;
                }
            }
        }
        
        // Create phone fields
        if( is_array( $user[ 'phone' ] ) && count( $user[ 'phone' ] ) ) {
            
            // Process each phone number
            foreach( $user[ 'phone' ] as $phone ) {
                
                // Check valid array
                if( is_array( $phone ) ) {
                    
                    // Check type
                    if( $phone[ 'type' ] == 'WORK' ||
                        $phone[ 'type' ] == 'HOME' ||
                        $phone[ 'type' ] == 'CELL' ||
                        $phone[ 'type' ] == 'MAIN' ||
                        $phone[ 'type' ] == 'PAGER') {
                        
                        // Add standard number
                        $vCard[] = 'TEL;type='
                                 . $phone['type']
                                 . ':'
                                 . $phone['number'];
                        
                    } elseif( $phone[ 'type' ] == 'WORKFAX' || $phone[ 'type' ] == 'HOMEFAX' ) {
                        
                        // Add fax number
                        $vCard[] = 'TEL;type='
                                 . substr( $phone[ 'type' ], 0, 4 )
                                 . ';type=FAX:'
                                 . $phone[ 'number' ];
                        
                    } else {
                        
                        // Add custom number
                        $vCard[] = 'item'
                                 . $items
                                 . '.TEL:'
                                 . $phone[ 'number' ];
                        $vCard[] = 'item'
                                 . $items
                                 . '.X-ABLabel:_$!<Other>!$_';
                        
                        // Increase item counter
                        $item++;
                    }
                }
            }
        }
        
        // Check for a picture
        if( !empty( $user[ 'image' ] ) ) {
            
            // Picture path
            $picturePath = $user[ 'image' ];
            
            // Open file ressource
            if( $handle = @fopen( $user[ 'image' ],  'r' ) ) {
                
                // Read image
                $pictureContent =  fread( $handle, filesize( $picturePath ) );
                
                // Close file ressource
                fclose( $handle );
            
                // Add picture to vCard
                $vCard[] = 'PHOTO;BASE64:';
                $vCard[] = '  ' . chunk_split( base64_encode( $pictureContent ), 76, "\r\n  " );
            }
        }
        
        // Check if vCard is a company
        if( $user[ 'iscompany' ] ) {
            
            // FN as company
            $vCard[] = 'FN:' . $user[ 'company' ];
            
            // Add company flag
            $vCard[] = 'X-ABShowAs:COMPANY';
            
        } else {
            
            // FN as user
            $vCard[] = 'FN'
                     . $charset
                     . ':'
                     . $user[ 'firstname' ]
                     . ' '
                     . $user[ 'name' ];
        }
        
        // End vCard
        $vCard[] = 'END:VCARD';
        
        // Return vCard
        return implode( chr( 10 ), $vCard );
    }
    
    /**
     * Parse avCard (or a pack of vCards).
     * 
     * This function produis used to parse a vCard, or a pack of vCard, and to produce
     * a multi-dimensionnal array with all the vCard informations. The array produced
     * is exactly the same kind of array that must be passed to the div_vCardCreate
     * function of this API. So the two functions are compatible.
     * 
     * If a vCard has an embed base65 encoded image, it's decoded, and a temporary
     * image file is created in typo3temp/. The file reference is then included in the
     * output array.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $file               The path to the file to process
     * @return      array       An array containing all the vCard(s) informations
     */
    function div_vCardFileParse( $file )
    {
        // Try to open file in read mode
        if( $fp = @fopen( $file, 'r' ) ) {
            
            // Storage
            $vCard    = array();
            
            // Start flags
            $start    = 0;
            $imgStart = 0;
            
            // Get file content with converted line breaks
            $content  = tx_apimacmade::div_convertLineBreaks(
                fread( $fp, filesize( $file ) )
            );
            
            // Fix a bug with MacOS X AddressBook
            if( substr( $content, 1, 1 ) == chr( 255 ) ) {
                
                // Strip first two characters
                $content = substr( $content, 2, strlen( $content ) );
            }
            
            // Get each line
            $lines = explode( chr( 10 ), $content );
            
            // Process each line
            foreach( $lines as $ln ) {
                
                // Check if an image is beeing processed
                if( $imgStart ) {
                    
                    if( substr( $ln, 0, 2 ) == '  ' ) {
                        
                        // Add current image line (binary)
                        $imgLines[] = trim( $ln );
                        
                        // Next line
                        continue;
                        
                    } else {
                        
                        // Temporary image name
                        $imageName = t3lib_div::tempnam( 'vcardimg_' );
                        
                        // Try to create the image file
                        if( $pic = @fopen( $imageName, 'wb' ) ) {
                            
                            // Image binary code
                            $binary = base64_decode( implode( '', $imgLines ) );
                            
                            // Write image binary code
                            fwrite( $pic, $binary, strlen( $binary ) );
                            
                            // Add image reference
                            $tmp[ 'image' ] = $imageName;
                            
                            // Close file
                            fclose( $pic );
                        }
                        
                        // Reset image flag
                        $imgStart = 0;
                    }
                }
                
                // Trim line
                $ln = trim( $ln );
                
                // Process only lines with vCard instructions
                if( $col = strpos( $ln, ':' ) ) {
                    
                    // vCard instruction
                    $key = strtoupper( substr( $ln, 0, $col ) );
                    
                    // vCard content
                    $val = substr( $ln, $col + 1, strlen( $ln ) );
                    
                    // Begin vCard
                    if( $key == 'BEGIN' && strtoupper( $val ) == 'VCARD' ) {
                        
                        // vCard has started
                        $start = 1;
                        
                        // Create temp array for the current vCard
                        $tmp   = array();
                        
                    } elseif( $start && $key == 'END' && strtoupper( $val ) == 'VCARD') {
                        
                        // vCard has ended
                        $start   = 0;
                        
                        // Memorize informations
                        $vCard[] = $tmp;
                        
                        // Delete temporary array
                        unset( $tmp );
                        
                    } elseif( $start ) {
                        
                        // Check for a custom item
                        if( $custom = strpos( $key, '.' ) ) {
                            
                            // Erase item number
                            $key = substr( $key, $custom + 1, strlen( $key ) );
                        }
                        
                        // Get instruction supbarts
                        $key_parts = explode( ';', $key );
                        
                        // Get value supbarts
                        $val_parts = explode( ';', $val );
                        
                        // Check instruction type
                        switch( $key_parts[ 0 ] ) {
                            
                            // Name
                            case 'N';
                                
                                // Add last name
                                $tmp[ 'name' ]      = $val_parts[ 0 ];
                                
                                // Add last name
                                $tmp[ 'firstname' ] = $val_parts[ 1 ];
                            break;
                            
                            // Nickname
                            case 'NICKNAME';
                                
                                // Add nickname
                                $tmp[ 'username' ] = $val_parts[ 0 ];
                            break;
                            
                            // Organisation
                            case 'ORG';
                                
                                // Add company
                                $tmp[ 'company' ]    = $val_parts[ 0 ];
                                
                                // Add department
                                $tmp[ 'department' ] = $val_parts[ 1 ];
                            break;
                            
                            // Job title
                            case 'TITLE';
                                
                                // Add title
                                $tmp[ 'title' ] = $val_parts[ 0 ];
                            break;
                            
                            // Home page
                            case 'URL';
                                
                                // Add URL
                                $tmp[ 'www' ] = $val_parts[ 0 ];
                            break;
                            
                            // Notes
                            case 'NOTE';
                                
                                // Add notes
                                $tmp[ 'note' ] = implode( ';', $val_parts );
                            break;
                            
                            // Birthday
                            case 'BDAY':
                                
                                // Check format
                                if( in_array( 'VALUE=DATE', $key_parts ) ) {
                                    
                                    // Add date
                                    $tmp[ 'birthday' ] = strtotime( $val_parts[ 0 ] );
                                }
                            break;
                            
                            // Render as company
                            case 'X-ABSHOWAS':
                                
                                // Add company flag
                                $tmp[ 'iscompany' ] = ( strtoupper( $val_parts[ 0 ] ) == 'COMPANY') ? 1 : '';
                            break;
                            
                            // Image
                            case 'PHOTO':
                                
                                // Check format
                                if( in_array( 'BASE64', $key_parts ) ) {
                                    
                                    // Set image flag
                                    $imgStart = 1;
                                    
                                    // Image storage
                                    $imgLines = array();
                                }
                            break;
                            
                            // Email
                            case 'EMAIL':
                                
                                // Check if sub array exists
                                if( !is_array( $tmp[ 'email' ] ) ) {
                                    
                                    // Create array
                                    $tmp[ 'email' ] = array();
                                }
                                
                                // Create sub array
                                $tmp[ 'email' ][] = array();
                                
                                // Current email key
                                $emailKey         = count( $tmp[ 'email' ] ) - 1;
                                
                                // Add email value
                                $tmp[ 'email' ][ $emailKey ][ 'mail' ] = $val_parts[ 0 ];
                                
                                // Check email type
                                if( in_array( 'TYPE=WORK', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'email' ][ $emailKey ][ 'type' ] = 'WORK';
                                    
                                } elseif( in_array( 'TYPE=HOME', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'email' ][ $emailKey ][ 'type' ] = 'HOME';
                                    
                                } else {
                                    
                                    // Add type
                                    $tmp[ 'email' ][ $emailKey ][ 'type' ] = 'Other';
                                }
                            break;
                            
                            // Address
                            case 'ADR':
                                
                                // Check if sub array exists
                                if( !is_array( $tmp[ 'address' ] ) ) {
                                    
                                    // Create array
                                    $tmp[ 'address' ] = array();
                                }
                                
                                // Create sub array
                                $tmp[ 'address' ][] = array();
                                
                                // Current email key
                                $addressKey         = count( $tmp[ 'address' ] ) - 1;
                                
                                // Add values
                                $tmp[ 'address' ][ $addressKey ][ 'street' ]  = $val_parts[ 2 ];
                                $tmp[ 'address' ][ $addressKey ][ 'city' ]    = $val_parts[ 3 ];
                                $tmp[ 'address' ][ $addressKey ][ 'state' ]   = $val_parts[ 4 ];
                                $tmp[ 'address' ][ $addressKey ][ 'zip' ]     = $val_parts[ 5 ];
                                $tmp[ 'address' ][ $addressKey ][ 'country' ] = $val_parts[ 6 ];
                                
                                // Check email type
                                if( in_array( 'TYPE=WORK', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'address' ][ $addressKey ][ 'type' ] = 'WORK';
                                    
                                } elseif( in_array( 'TYPE=HOME', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'address' ][ $addressKey ][ 'type' ] = 'HOME';
                                    
                                } else {
                                    
                                    // Add type
                                    $tmp[ 'address' ][ $addressKey ][ 'type' ] = 'Other';
                                }
                            break;
                            
                            // Phone
                            case 'TEL':
                                
                                // Check if sub array exists
                                if( !is_array( $tmp[ 'phone' ] ) ) {
                                    
                                    // Create array
                                    $tmp[ 'phone' ] = array();
                                }
                                
                                // Create sub array
                                $tmp[ 'phone' ][] = array();
                                
                                // Current email key
                                $phoneKey         = count( $tmp[ 'phone' ] ) - 1;
                                
                                // Add email value
                                $tmp[ 'phone' ][ $phoneKey ][ 'number' ] = $val_parts[ 0 ];
                                
                                // Check email type
                                if( in_array( 'TYPE=WORK', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'phone' ][ $phoneKey ][ 'type' ] = 'WORK';
                                    
                                } elseif( in_array( 'TYPE=HOME', $key_parts ) && in_array( 'TYPE=FAX', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'phone' ][ $phoneKey ][ 'type' ] = 'HOMEFAX';
                                    
                                } elseif( in_array( 'TYPE=WORK', $key_parts ) && in_array( 'TYPE=FAX', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'phone' ][ $phoneKey ][ 'type' ] = 'WORKFAX';
                                    
                                } elseif( in_array( 'TYPE=HOME', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'phone' ][ $phoneKey ][ 'type' ] = 'HOME';
                                    
                                } elseif( in_array( 'TYPE=CELL', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'phone' ][ $phoneKey ][ 'type' ] = 'CELL';
                                    
                                } elseif( in_array( 'TYPE=MAIN', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'phone' ][ $phoneKey ][ 'type' ] = 'MAIN';
                                    
                                } elseif( in_array( 'TYPE=PAGER', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'phone' ][ $phoneKey ][ 'type' ] = 'PAGER';
                                    
                                } else {
                                    
                                    // Add type
                                    $tmp[ 'phone' ][ $phoneKey ][ 'type' ] = 'Other';
                                }
                            break;
                            
                            // Instant messengers
                            case 'X-AIM':
                            case 'X-JABBER':
                            case 'X-MSN':
                            case 'X-YAHOO':
                            case 'X-ICQ':
                                
                                // Check if sub array exists
                                if( !is_array( $tmp[ 'messenger' ] ) ) {
                                    
                                    // Create array
                                    $tmp[ 'messenger' ] = array();
                                }
                                
                                // Create sub array
                                $tmp[ 'messenger' ][] = array();
                                
                                // Current email key
                                $messengerKey         = count( $tmp[ 'messenger' ] ) - 1;
                                
                                // Add values
                                $tmp[ 'messenger' ][ $messengerKey ][ 'name' ]    = $val_parts[ 0 ];
                                $tmp[ 'messenger' ][ $messengerKey ][ 'service' ] = str_replace( 'X-', '', $key_parts[ 0 ] );
                                
                                // Check email type
                                if( in_array( 'TYPE=WORK', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'messenger' ][ $messengerKey ][ 'type' ] = 'WORK';
                                    
                                } elseif( in_array( 'TYPE=HOME', $key_parts ) ) {
                                    
                                    // Add type
                                    $tmp[ 'messenger' ][ $messengerKey ][ 'type' ] = 'HOME';
                                    
                                } else {
                                    
                                    // Add type
                                    $tmp[ 'messenger' ][ $messengerKey ][ 'type' ] = 'Other';
                                }
                            break;
                        }
                    }
                }
            }
            
            // Close file
            fclose( $fp );
            
            // Return vCard array
            return $vCard;
        }
    }
    
    /**
     * Create an HTML list from a string.
     * 
     * This function produces an HTML list element (UL or OL) from
     * a string, using a separator to get each list item.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $string             The string to convert
     * @param       string      $sep                The separator used to get list items
     * @param       boolean     $htmlspecialchars   Pass the item through htmlspecialchars()
     * @param       string      $listType           The type of list to produce (UL, OL)
     * @param       array       $listParams         The parameters of the list tag as key/value pairs
     * @param       array       $itemsParams        The parameters of the list items tag as key/value pairs
     * @return      string      An HTML list
     * @see         div_writeTagParams
     */
    function div_str2list( $string, $sep = ',', $htmlspecialchars = 1, $listType = 'ul', $listParams = array(), $itemsParams = array() )
    {
        // Storage
        $listCode   = array();
        
        // Array of field values
        $listItems  = explode( $sep, $string );
        
        // Start list
        $listCode[] = '<'
                    . $listType
                    . ' '
                    . tx_apimacmade::div_writeTagParams( $listParams )
                    . '>';
        
        // Process each value
        foreach( $listItems as $val ) {
            
            // Pass the item through htmlspecialchars() if required
            $item       = ( $htmlspecialchars ) ? htmlspecialchars( $val ) : $val;
            
            // Write list item
            $listCode[] = '<li '
                        . tx_apimacmade::div_writeTagParams( $itemsParams )
                        . '>'
                        . $item
                        . '</li>';
        }
        
        // End list
        $listCode[] = '</'
                    . $listType
                    . '>';
        
        // Return list
        return implode( chr( 10 ), $listCode );
    }
    
    /**
     * Create an HTML list from an array.
     * 
     * This function produces an HTML list element (UL or OL) from
     * an array.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       array       $inputArray         The array to convert
     * @param       boolean     $htmlspecialchars   Pass the item through htmlspecialchars()
     * @param       string      $listType           The type of list to produce (UL, OL)
     * @param       array       $listParams         The parameters of the list tag as key/value pairs
     * @param       array       $itemsParams        The parameters of the list items tag as key/value pairs
     * @return      string      An HTML list
     * @see         div_writeTagParams
     */
    function div_array2list( $inputArray, $htmlspecialchars = 1, $listType = 'ul', $listParams = array(), $itemsParams = array() )
    {
        // Check arguments
        if( !is_array( $inputArray ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $inputArray must be an array.', __LINE__ );
            return false;
        }
    
        // Storage
        $listCode = array();
        
        // Start list
        $listCode[] = '<'
                    . $listType
                    . ' '
                    . tx_apimacmade::div_writeTagParams( $listParams )
                    . '>';
        
        // Process each value
        foreach( $inputArray as $val ) {
            
            // Pass the item through htmlspecialchars() if required
            $item       = ( $htmlspecialchars ) ? htmlspecialchars( $val ) : $val;
            
            // Write list item
            $listCode[] = '<li '
                        . tx_apimacmade::div_writeTagParams( $itemsParams )
                        . '>'
                        . $item
                        . '</li>';
        }
        
        // End list
        $listCode[] = '</'
                    . $listType
                    . '>';
        
        // Return list
        return implode( chr( 10 ), $listCode );
    }
    
    /**
     * Output some content.
     * 
     * This function is used to output content with a specified type, as attachment for example.
     * You can get a list of all the available type at the following address:
     * 
     * http://www.openmobilealliance.org/tech/omna/omna-wsp-content-type.htm
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $out                The content to output
     * @param       string      $cType              The content type
     * @param       string      $fName              The name of the file to output
     * @param       string      $cDisp              The content disposition
     * @param       string      $charset            The charset to use for the output
     * @return      NULL
     */
    function div_output( $out, $cType, $fName, $cDisp = 'attachment', $charset = 'utf-8' )
    {
        // Add content type header
        header(
            'Content-Type: '
          . $cType
          . '; charset="'
          . $charset
          . '"'
        );
        
        // Add content disposition header
        header(
            'Content-Disposition: '
          . $cDisp
          . '; filename="'
          . $fName
          . '"'
        );
        
        // Write content
        print $out;
        
        // Abort the script
        exit();
    }
    
    /**
     * Convert XML data to an array.
     * 
     * This function is used to convert an XML data to a multi-dimensionnal array,
     * representing the structure of the data.
     * 
     * This function is based on the Typo3 array2xml function, in t3lib_div. It basically
     * does the same, but has a few more options, like the inclusion of the xml tags arguments
     * in the output array. This function also has support for same multiple tag names
     * inside the same XML element, which is not the case with the core Typo3 function. In that
     * specific case, the array keys are suffixed with '-N', where N is a numeric value.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $data               The XML data to process
     * @param       boolean     $keepAttribs        If set, also includes the tag attributes in the array (with key 'xml-attribs')
     * @param       mixed       $caseFolding        XML parser option: case management
     * @param       mixed       $skipWhite          XML parser option: white space management
     * @param       string      $prefix             A tag prefix to remove
     * @param       string      $numeric            Keep only the numeric value for a tag prefixed with this argument (default is 'n')
     * @param       string      $index              Set the tag name to an alternate value found in the tag arguments (default is 'index')
     * @param       string      $type               Force the tag value to a special type, found in the tag arguments (default is 'type')
     * @param       string      $base64             Decode the tag value from base64 if the specified tag argument is present (default is 'base64')
     * @param       string      $php5defCharset     The default charset to use with PHP5
     * @return      An array with the XML structure, or an XML error message if the data is not valid
     */
    function div_xml2array( $data, $keepAttribs = 1, $caseFolding = 0, $skipWhite = 0, $prefix = false, $numeric = 'n', $index = 'index', $type = 'type', $base64 = 'base64', $php5defCharset = 'iso-8859-1' )
    {
        // Storage
        $xml        = array();
        $xmlValues  = array();
        $xmlIndex   = array();
        $stack      = array( array() );
        
        // Counter
        $stackCount = 0;
        
        // New XML parser
        $parser     = xml_parser_create();
        
        // Case management option
        xml_parser_set_option(
            $parser,
            XML_OPTION_CASE_FOLDING,
            $caseFolding
        );
        
        // White space management option
        xml_parser_set_option(
            $parser,
            XML_OPTION_SKIP_WHITE,
            $skipWhite
        );
        
        // Support for PHP5 charset detection
        if( ( double ) phpversion() >= 5 ) {
            
            // Find the encoding parameter in the XML declaration
            ereg(
                '^[[:space:]]*<\?xml[^>]*encoding[[:space:]]*=[[:space:]]*"([^"]*)"',
                substr( $data, 0, 200 ),
                $result
            );
            
            // Check result
            if( $result[ 1 ] ) {
                
                // Charset found in the XML declaration
                $charset = $result[ 1 ];
            
            } elseif( $TYPO3_CONF_VARS[ 'BE' ][ 'forceCharset' ] ) {
                
                // Force charset to Typo3 configuration if defined
                $charset = $TYPO3_CONF_VARS[ 'BE' ][ 'forceCharset' ];
                
            } else {
                
                // Default charset
                $charset = $php5defCharset;
            }
            
            // Charset management option
            xml_parser_set_option(
                $parser,
                XML_OPTION_TARGET_ENCODING,
                $charset
            );
        }
        
        // Parse XML structure
        xml_parse_into_struct(
            $parser,
            $data,
            $xmlValues,
            $xmlIndex
        );
        
        // Error in XML
        if( xml_get_error_code( $parser ) ) {
            
            // Error
            $error = 'XML error: '
                   . xml_error_string( xml_get_error_code( $parser ) )
                   . ' at line '
                   . xml_get_current_line_number( $parser );
            
            // Free XML parser
            xml_parser_free( $parser );
            
            // Return error
            return $error;
            
        } else {
            
            // Free XML parser
            xml_parser_free( $parser );
            
            // Counter for multiple same keys
            $sameKeyCount = array();
            
            // Process each value
            foreach( $xmlValues as $key => $val ) {
                
                // Get the tag name (without prefix if specified)
                $tagName = ( $prefix && substr( $val[ 'tag' ], 0, strlen( $prefix ) ) == $prefix ) ? substr( $val[ 'tag' ], strlen( $prefix ) ) : $val[ 'tag' ];
                
                // Support for numeric tags (<nXXX>)
                $numTag  = ( substr( $tagName, 0, 1 ) == $numeric ) ? substr( $tagName, 1 ) : false;
                
                // Check if tag is a real numeric value
                if( $numTag && !strcmp( intval( $numTag ), $numTag ) ) {
                    
                    // Store only numeric value
                    $tagName = intval( $numTag );
                }
                
                // Support for alternative value
                if( strlen( $val[ 'attributes' ][ $index ] ) ) {
                    
                    // Store alternate value
                    $tagName = $val[ 'attributes' ][ $index ];
                }
                
                // Check if array key already exists
                if( array_key_exists( $tagName, $xml ) ) {
                    
                    // Check if the current level has already a key counter
                    if( !isset( $sameKeyCount[ $val[ 'level' ] ] ) ) {
                        
                        // Create array
                        $sameKeyCount[ $val[ 'level' ] ] = 0;
                    }
                    
                    // Increase key counter
                    $sameKeyCount[ $val[ 'level' ] ]++;
                    
                    // Change tag name to avoid overwriting existing values
                    $tagName = $tagName . '-' . $sameKeyCount[ $val[ 'level' ] ];
                }
                
                // Check tag type
                switch( $val[ 'type' ] ) {
                    
                    // Open tag
                    case 'open':
                        
                        // Storage
                        $xml[ $tagName ]        = array();
                        
                        // Memorize content
                        $stack[ $stackCount++ ] = $xml;
                        
                        // Reset main storage
                        $xml                    = array();
                        
                        // Support for tag attributes
                        if( $keepAttribs && $val[ 'attributes' ] ) {
                            
                            // Store attributes
                            $xml[ 'xml-attribs' ] = $val[ 'attributes' ];
                        }
                    break;
                    
                    // Close tag
                    case 'close':
                        
                        // Memorize array
                        $tempXML = $xml;
                        
                        // Decrease the stack counter
                        $xml     = $stack[ --$stackCount ];
                        
                        // Go to the end of the array
                        end( $xml );
                        
                        // Add temp array
                        $xml[ key( $xml ) ] = $tempXML;
                        
                        // Unset temp array
                        unset( $tempXML );
                        
                        // Unset key counters for the child level
                        unset( $sameKeyCount[ $val[ 'level' ] + 1 ] );
                        
                    break;
                    
                    // Complete tag
                    case 'complete':
                        
                        // Check for base64
                        if( $val[ 'attributes' ][ 'base64' ] ) {
                            
                            // Decode value
                            $xml[ $tagName ] = base64_decode( $val[ 'value' ] );
                        
                        } else {
                            
                            // Add value (force string)
                            $xml[ $tagName ] = ( string )$val[ 'value' ];
                            
                            // Support for value types
                            switch( ( string )$val[ 'attributes' ][ $type ] ) {
                                
                                // Integer
                                case 'integer':
                                    
                                    // Force variable type
                                    $xml[ $tagName ] = ( integer )$xml[ $tagName ];
                                    
                                break;
                                
                                // Double
                                case 'double':
                                    
                                    $xml[ $tagName ] = ( double )$xml[ $tagName ];
                                
                                break;
                                
                                // Boolean
                                case 'boolean':
                                    
                                    // Force type
                                    $xml[ $tagName ] = ( bool )$xml[ $tagName ];
                                
                                break;
                                
                                // Array
                                case 'array':
                                    
                                    // Create an empty array
                                    $xml[ $tagName ] = array();
                                
                                break;
                            }
                        }
                        
                        // Support for tag attributes
                        if( $keepAttribs && $val[ 'attributes' ] ) {
                            
                            // Memorize tag value
                            $tempTagValue                     = $xml[ $tagName ];
                            
                            // New array with value
                            $xml[ $tagName ]                  = array(
                                'xml-value' => $tempTagValue
                            );
                            
                            // Store attributes
                            $xml[ $tagName ][ 'xml-attribs' ] = $val[ 'attributes' ];
                            
                            // Unset memorized value
                            unset( $tempTagValue );
                        }
                        
                    break;
                }
            }
            
            // Return the array of the XML root element
            return $xml;
        }
    }
    
    /**
     * Convert an array to XML.
     * 
     * This function is used to convert a multi-dimensionnal array to XML code. This is the
     * reverse function of div_xml2array().
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       array       $input              The array to process
     * @param       string      $xmlRoot            The name of the XML root element
     * @param       string      $prefix             A prefix for the tag names
     * @param       string      $numeric            The prefix for numeric keys
     * @param       string      $numericAsAttribute Use an XML attribute to store numeric keys
     * @param       string      $addArrayAttribute  Add an XML attribute if the array value is an array
     * @param       boolean     $xmlDeclaration     Include XML declaration
     * @param       string      $encoding           XML declaration parameter: encoding
     * @param       string      $version            XML declaration parameter: version
     * @param       string      $standalone         XML declaration parameter: standalone
     * @param       boolean     $doctype            The URL of the doctype
     * @param       int         $newLine            The new line character to use (through chr())
     * @param       int         $indent             The indentation character to use (through chr())
     * @param       int         $level              The level processed. Don't touch this, as it's used for the correct code indentation
     * @return      string      An XML string
     */
    function div_array2xml( $input, $xmlRoot = 'phpArray', $prefix = '', $numeric = 'item', $numericAsAttribute = 'index', $addArrayAttribute = 'type', $xmlDeclaration = 1, $encoding = 'iso-8859-1', $version = '1.0', $standalone = 'yes', $doctype = false, $newLine = 10, $indent = 9, $level = 0 )
    {
        // Key for XML attributes
        $attribs   = 'xml-attribs';
        
        // Key for single value stored in a sub-array
        $valueOnly = 'xml-value';
        
        // Check arguments
        if( !is_array( $input ) ) {
            
            // Error
            tx_apimacmade::errorMsg( __METHOD__, 'The argument $input must be an array.', __LINE__ );
            return false;
        }
        
        // Storage
        $xml = array();
        
        // Include XML declaration?
        if( $xmlDeclaration && $level == 0 ) {
            
            // XML declaration
            $xml[] = '<?xml version="'
                   . $version
                   . '" encoding="'
                   . $encoding
                   . '" standalone="'
                   . $standalone
                   . '"?'
                   . '>';
        }
        
        // Doctype?
        if( $doctype && $level == 0 ) {
            
            // XML declaration
            $xml[] = '<!DOCTYPE '
                   . $xmlRoot
                   . ' SYSTEM "'
                   . $doctype
                   . '" >';
        }
        
        // Start root element
        if( $level == 0 ) {
            
            // Storage
            $rootAttribs = array('');
            
            // Root element attributes?
            if( is_array( $input[ $attribs ] ) ) {
                
                // Process attributes
                foreach( $input[ $attribs ] as $attrName => $attrValue ) {
                    
                    // Add attribute
                    $rootAttribs[] = $attrName
                                   . '="'
                                   . $attrValue
                                   . '"';
                }
            }
            
            // Write tag
            $xml[] = '<'
                   . $prefix
                   . $xmlRoot
                   . implode( ' ', $rootAttribs )
                   . '>';
        }
        
        // Code indentation
        $codeIndents;
        
        // Adds necessary indentations
        for( $i = 0; $i < ( $level + 1 ); $i++ ) {
            
            // Add indentation character
            $codeIndents .= chr( $indent );
        }
        
        // Process array elements
        foreach( $input as $tagName => $tagValue ) {
            
            // Don't process arguments array
            if( ( string )$tagName != $attribs ) {
                
                // Check if tag name begins with xml
                if( strtolower( substr( $tagName, 0, 3 ) ) == 'xml' ) {
                    
                    // Remove XML prefix
                    $tagName = substr( $tagName, 3 );
                }
                
                // Check if tag name begins with a number
                if( is_numeric( substr( $tagName, 0, 1 ) ) ) {
                    
                    // Check how to handle numeric tags (attribute or prefix)
                    if( $numericAsAttribute ) {
                        
                        // Numeric attribute
                        $numKey  = ( string )$tagName;
                        
                        // Tag name
                        $tagName = $numeric;
                        
                        // Add flag to add numeric attribute
                        $isNum   = 1;
                        
                    } else {
                        
                        // Add prefix
                        $tagName = $numeric . $tagName;
                    }
                } else {
                    
                    // Reset variables
                    $numKey = false;
                    $isNum  = false;
                }
                
                // Check if tag name contains a space
                if( strstr( $tagName, ' ' ) ) {
                    
                    // Replace space
                    $tagName = str_replace( ' ', '_', $tagName );
                }
                
                // Check if tag name contains an equal sign
                if( strstr( $tagName, '=' ) ) {
                    
                    // Replace space
                    $tagName = str_replace( '=', '_', $tagName );
                }
                
                // Remove numeric suffic in tag names if present
                $tagName = ereg_replace( '-[0-9]+', '', $tagName );
                
                // Check for numeric attribute
                if( $isNum ) {
                    
                    // Set attribute
                    $fixedAttribs = ' '
                                  . $numericAsAttribute
                                  . '="'
                                  . $numKey
                                  . '"';
                    
                } else {
                    
                    // Reset fixed attributes
                    $fixedAttribs = false;
                }
                
                // Check for sub arrays
                if( is_array( $tagValue ) ) {
                    
                    // Storage
                    $elAttribs = array('');
                    
                    // Add array attribute?
                    if( $addArrayAttribute ) {
                        
                        // Add attribute
                        $elAttribs[] = $addArrayAttribute . '="array"';
                    }
                    
                    // Element attributes?
                    if( is_array( $tagValue[ $attribs ] ) ) {
                        
                        // Process attributes
                        foreach( $tagValue[ $attribs ] as $attrName => $attrValue ) {
                            
                            // Add attribute
                            $elAttribs[] = $attrName
                                         . '="'
                                         . $attrValue
                                         . '"';
                        }
                        
                        // Remove attributes array for further processing
                        unset( $tagValue[ $attribs ] );
                    }
                    
                    // Check for a value
                    if( count( $tagValue ) ) {
                        
                        // Check for a single value stored in a sub array
                        if( array_key_exists( 'xml-value', $tagValue ) ) {
                            
                            // Add single value
                            $elValue = $tagValue[ 'xml-value' ];
                            
                        } else {
                        
                            // Process sub arrays
                            $elValue = chr( $newLine )
                                     . tx_apimacmade::div_array2xml(
                                            $tagValue,
                                            $xmlRoot,
                                            $prefix,
                                            $numeric,
                                            $numericAsAttribute,
                                            $addArrayAttribute,
                                            $xmlDeclaration,
                                            $encoding,
                                            $version,
                                            $standalone,
                                            $doctype,
                                            $newLine,
                                            $indent,
                                            $level + 1
                                       )
                                     . chr( $newLine )
                                     . $codeIndents;
                        }
                        
                        // Write tag
                        $xml[] = $codeIndents
                               . '<'
                               . $prefix
                               . $tagName
                               . $fixedAttribs
                               . implode( ' ', $elAttribs )
                               . '>'
                               . $elValue
                               . '</'
                               . $prefix
                               . $tagName
                               . '>';
                    
                    } else {
                        
                        // Empty tag
                        $xml[] = $codeIndents
                               . '<'
                               . $prefix
                               . $tagName
                               . $fixedAttribs
                               . implode( ' ', $elAttribs )
                               . ' />';
                    }
                    
                } else {
                    
                    // Check for a value
                    if( $tagValue ) {
                        
                        // Check if value must be protected
                        if( strstr( $tagValue, '&' ) || strstr( $tagValue, '<' ) ) {
                            
                            // Protect with CDATA
                            $elValue = '<![CDATA['
                                     . $tagValue
                                     . ']]>';
                            
                        } else {
                            
                            // Don't protect
                            $elValue = $tagValue;
                        }
                        
                        // Write tag
                        $xml[] = $codeIndents
                               . '<'
                               . $prefix
                               . $tagName
                               . $fixedAttribs
                               . '>'
                               . $elValue
                               . '</'
                               . $prefix
                               . $tagName
                               . '>';
                        
                    } else {
                        
                        // Empty tag
                        $xml[] = $codeIndents
                               . '<'
                               . $prefix
                               . $fixedAttribs
                               . $tagName
                               . ' />';
                    }
                }
            }
        }
        
        // End root element
        if( $level == 0 ) {
            
            $xml[] = '</'
                   . $prefix
                   . $xmlRoot
                   . '>';
        }
        
        // Return XML code
        return implode( chr( $newLine ), $xml );
    }
    
    /**
     * Crops a string.
     * 
     * This function is used to crop a string to a specified number of characters. By default,
     * it crops the string after an entire word, and not in the middle of a word. It also
     * strips by default all HTML tags before cropping, to avoid display problems.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $str                The string to crop
     * @param       int         $chars              Number of characters
     * @param       string      $endString          The string to add after the cropped string
     * @param       boolean     $crop2space         Don't crop in a middle of a word
     * @param       boolean     $stripTags          Remove all HTML tags from the string
     * @return      string      The cropped string
     */
    function div_crop( $str, $chars, $endString = '...', $crop2space = 1, $stripTags = 1 )
    {
        // Remove HTML tags?
        if( $stripTags ) {
            
            // Remove all tags
            $str = strip_tags( $str );
        }
        
        // Check string length
        if( strlen( $str ) < $chars ) {
            
            // Return string
            return $str;
            
        } else {
            
            // Substring
            $str = substr( $str, 0, $chars );
            
            // Crop only after a word?
            if( $crop2space && strstr( $str, ' ' ) ) {
                
                // Last space
                $cropPos = strrpos( $str, ' ' );
                
                // Crop string
                $str     = substr( $str, 0, $cropPos );
            }
            
            // Return string
            return $str . $endString;
        }
    }
    
    /**
     * Returns a timestamp.
     * 
     * This function returns a timestamp for a given year (XXXX), week number, and
     * day number (0 is sunday, 6 is saturday).
     * 
     * Thanx to Nicolas Miroz (nmiroz@free.fr) for the informations about
     * date computing.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param		$day				The day number
     * @param		$week				The week number
     * @param		$year				The year
     * @return		A timestamp
     */
    function div_week2date( $day, $week, $year )
    {
        // First january of the year
        $firstDay    = mktime( 0, 0, 0, 1, 1, $year );
        
        // Get the day for the first january
        $firstDayNum = date( 'w', $firstDay );
        
        // Compute first monday of the year and the week number for that day
        switch( $firstDayNum ) {
        
            // Sunday
            case 0:
                
                // Monday is 02.01 | Week is 1
                $monday  = mktime( 0, 0, 0, 01, 02, $year );
                $weekNum = 1;
            break;
            
            // Monday
            case 1:
                
                // Monday is 01.01 | Week is 1
                $monday  = mktime( 0, 0, 0, 01, 01, $year );
                $weekNum = 1;
            break;
            
            // Tuesday
            case 2:
                
                // Monday is 07.01 | Week is 2
                $monday  = mktime( 0, 0, 0, 01, 07, $year );
                $weekNum = 2;
            break;
            
            // Wednesday
            case 3:
                
                // Monday is 06.01 | Week is 2
                $monday  = mktime( 0, 0, 0, 01, 06, $year );
                $weekNum = 2;
            break;
            
            // Thursday
            case 4:
                
                // Monday is 05.01 | Week is 2
                $monday  = mktime( 0, 0, 0, 01, 05, $year );
                $weekNum = 2;
            break;
            
            // Friday
            case 5:
                
                // Monday is 04.01 | Week is 1
                $monday  = mktime( 0, 0, 0, 01, 04, $year );
                $weekNum = 1;
            break;
            
            // Saturday
            case 6:
                
                // Monday is 03.01 | Week is 1
                $monday  = mktime( 0, 0, 0, 01, 03, $year );
                $weekNum = 1;
            break;
        }
        
        // Compute the difference in days from the monday to the requested day
        $dayDiff = ( $day == 0 ) ? 6 : ( $day - 1 );
        
        // Number of day to the requested date
        $numDay = ( ( $week - ( $weekNum - 1 ) - 1 ) * 7 ) + $dayDiff + date( 'd', $monday );
        
        // Create and return the timestamp for the requested date
        return mktime( 0, 0, 0, 01, $numDay, $year );
    }
    
    /**
     * Ensure a number is in a specified range
     * 
     * This function forces the specified number into the boundaries of a minimum and maximum
     * number.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       number      $number             The number to check
     * @param       number      $min                The minimum value
     * @param       number      $max                The maximum value
     * @param       boolean     $int                Evaluate number as an integer
     * @return      number      A number in the specified range
     */
    function div_numberInRange( $number, $min, $max, $int = false )
    {
        // Convert number to an integer if required
        if( $int ) {
            $number = intval( $number );
        }
        
        // Check number
        if( $number > $max ) {
            
            // Number bigger than maximum value
            $number = $max;
            
        } elseif( $number < $min ) {
            
            // Number smaller than minimal value
            $number = $min;
        }
        
        // Return number
        return $number;
    }
    
    /**
     * Converts RGB color values into HSL
     * 
     * This function takes RGB (Red-Green-Blue) color values and converts them
     * into HSL (Hue-Saturation-Luminance) color values. RGB values are from 0 to
     * 255, and HSL values are returned in an array with associative keys. Note that
     * the returned hue value is an angle (0-360), and the saturation and luminance
     * are percentage (0-100).
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       int         $R                  The red value (0-255)
     * @param       int         $G                  The green value (0-255)
     * @param       int         $B                  The blue value (0-255)
     * @param       boolean     $round              Round final values
     * @return      array       An array with HSL color values
     * @see         div_numberInRange
     */
    function div_rgb2hsl( $R, $G, $B, $round = 1 )
    {
        // Check correct values
        $R = tx_apimacmade::div_numberInRange( $R, 0, 255 );
        $G = tx_apimacmade::div_numberInRange( $G, 0, 255 );
        $B = tx_apimacmade::div_numberInRange( $B, 0, 255 );
        
        // HSL colors storage
        $colors = array(
            'H' => 0,		// Hue
            'S' => 0,		// Saturation
            'L' => 0,		// Luminance
        );
        
        // Converts RGB values (0-1)
        $R = ( $R / 255 );
        $G = ( $G / 255 );
        $B = ( $B / 255 );
        
        // Find the maximum and minimum RGB values
        $max = max( $R, $G, $B );
        $min = min( $R, $G, $B );
        
        // RGB delta
        $delta = $max - $min;
        
        // Compute luminance
        $colors[ 'L' ] = ( $max + $min ) / 2;
        
        // Check for chromatic data
        if( $delta == 0 ) {
            
            // No chromatic data
            $colors[ 'H' ] = 0;
            $colors[ 'S' ] = 0;
            
        } else {
            
            // Check luminance
            if( $colors[ 'L' ] < 0.5 ) {
                
                // Compute saturation
                $colors[ 'S' ] = $delta / ( $max + $min );
                
            } else {
                
                // Compute saturation
                $colors[ 'S' ] = $delta / ( 2 - $max - $min );
            }
            
            // RGB deltas
            $R_delta = ( ( ( $max - $R ) / 6 ) + ( $delta / 2 ) ) / $delta;
            $G_delta = ( ( ( $max - $G ) / 6 ) + ( $delta / 2 ) ) / $delta;
            $B_delta = ( ( ( $max - $B ) / 6 ) + ( $delta / 2 ) ) / $delta;
            
            // Check RGB max value
            if( $R == $max ) {
                
                // Compute hue
                $colors[ 'H' ] = $B_delta - $G_delta;
                
            } elseif( $G == $max ) {
                
                // Compute hue
                $colors[ 'H' ] = ( 1 / 3 ) + $R_delta - $B_delta;
                
            } elseif( $B == $max ) {
                
                // Compute hue
                $colors[ 'H' ] = ( 2 / 3 ) + $G_delta - $R_delta;
            }
            
            // Check hue
            if( $colors[ 'H' ] < 0 ) {
                
                // Increase hue
                $colors[ 'H' ] += 1;
                
            } elseif( $colors[ 'H' ] > 1 ) {
                
                // Decrease hue
                $colors[ 'H' ] -= 1;
            }
        }
        
        // Convert HSL values
        $colors[ 'H' ] = $colors[ 'H' ] * 360;		// Angle
        $colors[ 'S' ] = $colors[ 'S' ] * 100;		// Percentage
        $colors[ 'L' ] = $colors[ 'L' ] * 100;		// Percentage
        
        // Round values?
        if( $round ) {
            
            // Process each value
            foreach( $colors as $key => $value ) {
                
                $colors[ $key ] = round( $value );
            }
        }
        
        // Return HSL values
        return $colors;
    }
    
    /**
     * Converts HSL color values into RGB
     * 
     * This function takes HSL (Hue-Saturation-Luminance) color values and converts them
     * into RGB (Red-Green-Blue) color values. This is the reverse function of div_rgb2hsl().
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       int         $H                  The hue value (0-360)
     * @param       int         $S                  The saturation value (0-100)
     * @param       int         $L                  The luminance value (0-100)
     * @param       boolean     $round              Round final values
     * @return		array       An array with RGB color values
     * @see			div_numberInRange
     */
    function div_hsl2rgb( $H, $S, $L, $round = 1 )
    {
        // Check correct values
        $H = tx_apimacmade::div_numberInRange( $H, 0, 360 );
        $S = tx_apimacmade::div_numberInRange( $S, 0, 100 );
        $L = tx_apimacmade::div_numberInRange( $L, 0, 100 );
        
        // RGB colors storage
        $colors = array(
            'R' => 0,		// Red
            'G' => 0,		// Green
            'B' => 0,		// Blue
        );
        
        // Converts HSL values (0-1)
        $H = ( $H / 360 );
        $S = ( $S / 100 );
        $L = ( $L / 100 );
        
        // Check saturation
        if( $S == 0 ) {
            
            // No saturation
            $colors[ 'R' ] = $L * 255;
            $colors[ 'G' ] = $L * 255;
            $colors[ 'B' ] = $L * 255;
        
        } else {
            
            // Check luminance
            if( $L < 0.5 ) {
                
                // Computing variable #2
                $c2 = $L * ( 1 + $S );
                
            } else {
                
                // Computing variable #2
                $c2 = ( $L + $S ) - ( $S * $L );
            }
            
            // Computing variable #1
            $c1 = 2 * $L - $c2;
            
            // Process each RGB color
            foreach( $colors as $key => $value ) {
                
                // Create hue variable for specific RGB values
                switch( $key ) {
                    
                    // Red
                    case 'R':
                        $vH = $H + ( 1 / 3 );
                    break;
                    
                    // Green
                    case 'G':
                        $vH = $H;
                    break;
                    
                    // Blue
                    case 'B':
                        $vH = $H - ( 1 / 3 );
                    break;
                }
                
                // Adjust hue variable
                if( $vH < 0 ) {
                    
                    // Increase hue
                    $vH += 1;
                    
                } elseif( $vH > 1 ) {
                    
                    // Decrease hue
                    $vH -= 1;
                }
                
                // Check hue
                if( ( 6 * $vH ) < 1 ) {
                    
                    // Create color value
                    $colors[ $key ] = $c1 + ( $c2 - $c1 ) * 6 * $vH;
                    
                } elseif( ( 2 * $vH ) < 1 ) {
                    
                    // Create color value
                    $colors[ $key ] = $c2;
                    
                } elseif( ( 3 * $vH ) < 2 ) {
                    
                    // Create color value
                    $colors[ $key ] = $c1 + ( $c2 - $c1 ) * ( ( 2 / 3 ) - $vH ) * 6;
                    
                } else {
                    
                    // Create color value
                    $colors[ $key ] = $c1;
                }
            }
            
            // Convert RBG colors
            $colors[ 'R' ] = $colors[ 'R' ] * 255;
            $colors[ 'G' ] = $colors[ 'G' ] * 255;
            $colors[ 'B' ] = $colors[ 'B' ] * 255;
        }
        
        // Round values?
        if( $round ) {
            
            // Process each value
            foreach( $colors as $key => $value ) {
                
                $colors[ $key ] = round( $value );
            }
        }
        
        // Return RGB values
        return $colors;
    }
    
    /**
     * Converts RGB color values into HSV
     * 
     * This function takes RGB (Red-Green-Blue) color values and converts them
     * into HSV (Hue-Saturation-Value) color values. RGB values are from 0 to
     * 255, and HSV values are returned in an array with associative keys. Note that
     * the returned hue value is an angle (0-360), and the saturation and value
     * are percentage (0-100).
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       int         $R                  The red value (0-255)
     * @param       int         $G                  The green value (0-255)
     * @param       int         $B                  The blue value (0-255)
     * @param       boolean     $round              Round final values
     * @return      array       An array with HSV color values
     * @see         div_numberInRange
     */
    function div_rgb2hsv( $R, $G, $B, $round = 1 )
    {
        // Check correct values
        $R = tx_apimacmade::div_numberInRange( $R, 0, 255 );
        $G = tx_apimacmade::div_numberInRange( $G, 0, 255 );
        $B = tx_apimacmade::div_numberInRange( $B, 0, 255 );
        
        // HSV colors storage
        $colors = array(
            'H' => 0,		// Hue
            'S' => 0,		// Saturation
            'V' => 0,		// Luminance
        );
        
        // Converts RGB values (0-1)
        $R = ( $R / 255 );
        $G = ( $G / 255 );
        $B = ( $B / 255 );
        
        // Find the maximum and minimum RGB values
        $max = max( $R, $G, $B );
        $min = min( $R, $G, $B );
        
        // RGB delta
        $delta = $max - $min;
        
        // Compute value
        $colors[ 'V' ] = $max;
        
        // Check for chromatic data
        if( $delta == 0 ) {
            
            // No chromatic data
            $colors[ 'H' ] = 0;
            $colors[ 'S' ] = 0;
            
        } else {
            
            // Compute saturation
            $colors[ 'S' ] = $delta / $max;
            
            // RGB deltas
            $R_delta = ( ( ( $max - $R ) / 6 ) + ( $delta / 2 ) ) / $delta;
            $G_delta = ( ( ( $max - $G ) / 6 ) + ( $delta / 2 ) ) / $delta;
            $B_delta = ( ( ( $max - $B ) / 6 ) + ( $delta / 2 ) ) / $delta;
            
            // Check RGB max value
            if( $R == $max ) {
                
                // Compute hue
                $colors[ 'H' ] = $B_delta - $G_delta;
                
            } elseif( $G == $max ) {
                
                // Compute hue
                $colors[ 'H' ] = ( 1 / 3 ) + $R_delta - $B_delta;
                
            } elseif( $B == $max ) {
                
                // Compute hue
                $colors[ 'H' ] = ( 2 / 3 ) + $G_delta - $R_delta;
            }
            
            // Check hue
            if( $colors[ 'H' ] < 0 ) {
                
                // Increase hue
                $colors[ 'H' ] += 1;
                
            } elseif( $colors[ 'H' ] > 1 ) {
                
                // Decrease hue
                $colors[ 'H' ] -= 1;
            }
        }
        
        // Convert HSL values
        $colors[ 'H' ] = $colors[ 'H' ] * 360;		// Angle
        $colors[ 'S' ] = $colors[ 'S' ] * 100;		// Percentage
        $colors[ 'V' ] = $colors[ 'V' ] * 100;		// Percentage
        
        // Round values?
        if( $round ) {
            
            // Process each value
            foreach( $colors as $key => $value ) {
                
                $colors[ $key ] = round( $value );
            }
        }
        
        // Return HSV values
        return $colors;
    }
    
    /**
     * Converts HSV color values into RGB
     * 
     * This function takes HSV (Hue-Saturation-Value) color values and converts them
     * into RGB (Red-Green-Blue) color values. This is the reverse function of div_rgb2hsv().
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       int         $H                  The hue value (0-360)
     * @param       int         $S                  The saturation value (0-100)
     * @param       int         $V                  The value value (0-100)
     * @param       boolean     $round              Round final values
     * @return      array       An array with RGB color values
     * @see			div_numberInRange
     */
    function div_hsv2rgb( $H, $S, $V, $round = 1 )
    {
        // Check correct values
        $H = tx_apimacmade::div_numberInRange( $H, 0, 360 );
        $S = tx_apimacmade::div_numberInRange( $S, 0, 100 );
        $V = tx_apimacmade::div_numberInRange( $V, 0, 100 );
        
        // RGB colors storage
        $colors = array(
            'R' => 0,		// Red
            'G' => 0,		// Green
            'B' => 0,		// Blue
        );
        
        // Converts HSV values (0-1)
        $H = ( $H / 360 );
        $S = ( $S / 100 );
        $V = ( $V / 100 );
        
        // Check saturation
        if( $S == 0 ) {
            
            // No saturation
            $colors[ 'R' ] = $V * 255;
            $colors[ 'G' ] = $V * 255;
            $colors[ 'B' ] = $V * 255;
        
        } else {
            
            // Hue variables
            $vH = $H * 6;
            $iH = intval( $vH );
            
            // Computing variables
            $c1 = $V * ( 1 - $S );
            $c2 = $V * ( 1 - $S * ( $vH - $iH ) );
            $c3 = $V * ( 1 - $S * ( 1 - ( $vH - $iH ) ) );
            
            // Check hue integer value
            if( $iH == 0 ) {
                
                // Create RGB values
                $vR = $V;
                $vG = $c3;
                $vB = $c1;
                
            } elseif( $iH == 1 ) {
                
                // Create RGB values
                $vR = $c2;
                $vG = $V;
                $vB = $c1;
                
            } elseif( $iH == 2 ) {
                
                // Create RGB values
                $vR = $c1;
                $vG = $V;
                $vB = $c3;
                
            } elseif( $iH == 3 ) {
                
                // Create RGB values
                $vR = $c1;
                $vG = $c2;
                $vB = $V;
                
            } elseif( $iH == 4 ) {
                
                // Create RGB values
                $vR = $c3;
                $vG = $c1;
                $vB = $V;
                
            } else {
                
                // Create RGB values
                $vR = $V;
                $vG = $c1;
                $vB = $c2;
            }
            
            // Create RBG colors
            $colors[ 'R' ] = $vR * 255;
            $colors[ 'G' ] = $vG * 255;
            $colors[ 'B' ] = $vB * 255;
        }
        
        // Round values?
        if( $round ) {
            
            // Process each value
            foreach( $colors as $key => $value ) {
                
                $colors[ $key ] = round( $value );
            }
        }
        
        // Return RGB values
        return $colors;
    }
    
    /**
     * Converts HSL color values into HSV
     * 
     * This function takes HSL (Hue-Saturation-Luminance) color values and converts them
     * into HSV (Hue-Saturation-Value) color values.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       int         $H                  The hue value (0-360)
     * @param       int         $S                  The saturation value (0-100)
     * @param       int         $L                  The luminance value (0-100)
     * @param       boolean     $round              Round final values
     * @return      array       An array with HSV color values
     * @see         div_hsl2rgb
     * @see         div_rgb2hsv
     */
    function div_hsl2hsv( $H, $S, $L, $round = 1 )
    {
        // Convert HSL to RGB
        $rgbColors = tx_apimacmade::div_hsl2rgb(
            $H,
            $S,
            $L,
            $round
        );
        
        // Convert RGB to HSV
        return tx_apimacmade::div_rgb2hsv(
            $rgbColors[ 'R' ],
            $rgbColors[ 'G' ],
            $rgbColors[ 'B' ],
            $round
        );
    }
    
    /**
     * Converts HSV color values into HSL
     * 
     * This function takes HSV (Hue-Saturation-Value) color values and converts them
     * into HSL (Hue-Saturation-Luminance) color values. This is the reverse function of div_hsl2hsv().
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       int         $H                  The hue value (0-360)
     * @param       int         $S                  The saturation value (0-100)
     * @param       int         $V                  The value value (0-100)
     * @param       boolean     $round              Round final values
     * @return      array       An array with HSL color values
     * @see         div_hsv2rgb
     * @see         div_rgb2hsl
     */
    function div_hsv2hsl( $H, $S, $V, $round = 1 )
    {
        // Convert HSV to RGB
        $rgbColors = tx_apimacmade::div_hsv2rgb(
            $H,
            $S,
            $V,
            $round
        );
        
        // Convert RGB to HSL
        return tx_apimacmade::div_rgb2hsl(
            $rgbColors[ 'R' ],
            $rgbColors[ 'G' ],
            $rgbColors[ 'B' ],
            $round
        );
    }
    
    /**
     * Create an hexadecimal color
     * 
     * This function is used to create an hexadecimal color representation from
     * RGB (Red-Green-Blue), HSL (Hue-Saturation-Luminance) or HSV
     * (Hue-Saturation-Value) values.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       int         $v1                 The first value (red or hue, depending of the method)
     * @param       int         $v2                 The second value (green or saturation, depending of the method)
     * @param       int         $v3                 The third value (blue, luminosity or value, depending of the method)
     * @param       string      $method             The method to use for the color creation. Can be 'RGB', 'HSL' or 'HSV'
     * @param       boolean     $uppercase          Return value in uppercase
     * @return      string      The hexadecimal value of the color
     * @see         div_hsl2rgb
     * @see         div_hsv2rgb
     * @see         div_numberInRange
     */
    function div_createHexColor( $v1, $v2, $v3, $method = 'RGB', $uppercase = 1 )
    {
        // Convert method to uppercase
        $method = strtoupper( $method );
        
        // Check color creation method
        if( $method === 'HSL' ) {
            
            // Convert colors
            $colors = tx_apimacmade::div_hsl2rgb( $v1, $v2, $v3 );
            
            // Set converted values
            $v1 = $colors[ 'R' ];
            $v2 = $colors[ 'G' ];
            $v3 = $colors[ 'B' ];
            
        } elseif( $method === 'HSV' ) {
            
            // Convert colors
            $colors = tx_apimacmade::div_hsv2rgb( $v1, $v2, $v3 );
            
            // Set converted values
            $v1 = $colors[ 'R' ];
            $v2 = $colors[ 'G' ];
            $v3 = $colors[ 'B' ];
        }
        
        // Convert each color into hexadecimal
        $R = dechex( tx_apimacmade::div_numberInRange( $v1, 0, 255 ) );
        $G = dechex( tx_apimacmade::div_numberInRange( $v2, 0, 255 ) );
        $B = dechex( tx_apimacmade::div_numberInRange( $v3, 0, 255 ) );
        
        // Complete each color if needed
        $R = ( strlen( $R ) == 1) ? '0' . $R : $R;
        $G = ( strlen( $G ) == 1) ? '0' . $G : $G;
        $B = ( strlen( $B ) == 1) ? '0' . $B : $B;
        
        // Create full hexadecimal color
        $color =  $R . $G . $B;
        
        // Upper or lower case
        $color = ( $uppercase ) ? strtoupper( $color ) : strtolower( $color );
        
        // Return color
        return '#' . $color;
    }
    
    /**
     * Modify an hexadecimal color
     * 
     * This function is used to modify an hexadecimal color representation by adding
     * RGB (Red-Green-Blue), HSL (Hue-Saturation-Luminance) or HSV (Hue-Saturation-Value)
     * values.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $color              The original color (hexadecimal)
     * @param       int         $v1                 The first value (red or hue, depending of the method)
     * @param       int         $v2                 The second value (green or saturation, depending of the method)
     * @param       int         $v3                 The third value (blue, luminosity or value, depending of the method)
     * @param       string      $method             The method to use for the color modification. Can be 'RGB','HSL' or 'HSV'
     * @param       boolean     $uppercase          Return value in uppercase
     * @return      string      The hexadecimal value of the modified color
     * @see         div_rgb2hsl
     * @see         div_rgb2hsv
     * @see         div_createHexColor
     */
    function div_modifyHexColor( $color, $v1, $v2, $v3, $method = 'RGB', $uppercase = 1 )
    {
        // Convert method to uppercase
        $method = strtoupper( $method );
        
        // Erase the # character if present
        $color = ( substr( $color, 0, 1 ) == '#') ? substr( $color, 1 , strlen( $color ) ) : $color;
        
        // Check color length (3 or 6 )
        if( strlen( $color ) == 3 ) {
            
            // Extract RGB values from the hexadecimal color
            $R = hexdec( substr( $color, 0, 1 ) );
            $G = hexdec( substr( $color, 1, 1 ) );
            $B = hexdec( substr( $color, 2, 1 ) );
            
        } elseif(strlen($color) == 6) {
            
            // Extract RGB values from the hexadecimal color
            $R = hexdec( substr( $color, 0, 2 ) );
            $G = hexdec( substr( $color, 2, 2 ) );
            $B = hexdec( substr( $color, 4, 2 ) );
        }
        
        // Check modification method
        if( $method === 'HSL' ) {
            
            // Convert colors
            $colors = tx_apimacmade::div_rgb2hsl( $R, $G, $B );
            
            // Create modified color
            return tx_apimacmade::div_createHexColor(
                $colors[ 'H' ] + $v1,
                $colors[ 'S' ] + $v2,
                $colors[ 'L' ] + $v3,
                'HSL',
                $uppercase
            );
            
        } elseif( $method === 'HSV' ) {
            
            // Convert colors
            $colors = tx_apimacmade::div_rgb2hsv( $R, $G, $B );
            
            // Create modified color
            return tx_apimacmade::div_createHexColor(
                $colors[ 'H' ] + $v1,
                $colors[ 'S' ] + $v2,
                $colors[ 'V' ] + $v3,
                'HSV',
                $uppercase
            );
            
        } else {
            
            // Create modified color
            return tx_apimacmade::div_createHexColor(
                $R + $v1,
                $G + $v2,
                $B + $v3,
                'RGB',
                $uppercase
            );
        }
    }
    
    /**
     * Reformat XHTML code
     * 
     * This function is used to reformat XHTML code, with linebreaks and indentations.
     * The code passed to this function MUST be XHTML compliant in order to be reformatted,
     * as it will be parsed as XML! If this is not the case, the original code is return.
     * Also note that if you have mixed content inside a tag (eg. cData & tags), the cData
     * won't be preserved, as it will be recognized as junk data by the XML parser!
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $html               The original XHTML code
     * @param       boolean     $uppercase          Render XHTML tags in uppercase
     * @param       int         $newLine            The new line character to use (through chr())
     * @param       int         $indent             The indentation character to use (through chr())
     * @param       int         $level              The level processed. Don't touch this, as it's used for the correct code indentation
     * @return      string      The reformatted HTML code
     * @see         div_xml2array
     * @see         div_writeTagParams
     */
    function div_formatXHTML( $xhtml, $uppercase = 0, $newLine = 10, $indent = 9, $level = 0 )
    {
        // Check if the parsing process has already be done
        if( is_array( $xhtml ) ) {
            
            // Code storage
            $code = array();
            
            // Code indentation
            $codeIndents;
            
            // Adds necessary indentations
            for( $i = 0; $i < ( $level ); $i++ ) {
                
                // Add indentation character
                $codeIndents .= chr( $indent );
            }
            
            // Process each XHTML tag
            foreach( $xhtml as $key => $value ) {
                
                // Create tag name
                $tagName = ( $uppercase ) ? strtoupper( $key ) : strtolower( $key );
                
                // Remove numeric suffic in tag names if present
                $tagName = ereg_replace( '-[0-9]+', '', $tagName );
                
                // Check if value is an array
                if( is_array( $value ) ) {
                    
                    // XHTML tag attributes storage
                    $tagAttribs = '';
                    
                    // Check for XHTML attributes
                    if( $value[ 'xml-attribs' ] ) {
                        
                        // Write tag attributes
                        $tagAttribs = ' ' . tx_apimacmade::div_writeTagParams(
                            $value[ 'xml-attribs' ]                            
                        );
                        
                        // Remove attributes from parent array
                        unset( $value[ 'xml-attribs' ] );
                    }
                    
                    // Check for tag type
                    if( !$value ) {
                        
                        // Write empty tag
                        $code[] = $codeIndents
                                . '<'
                                . $tagName
                                . $tagAttribs
                                . ' />';
                        
                    } elseif( $value[ 'xml-value' ] ) {
                        
                        // Write complete XHTML tag
                        $code[] = $codeIndents
                                . '<'
                                . $tagName
                                . $tagAttribs
                                . '>'
                                . $value[ 'xml-value' ]
                                . '</'
                                . $tagName
                                . '>';
                        
                    } else {
                        
                        // Write complete XHTML tag with child tags
                        $code[] = $codeIndents
                                . '<'
                                . $tagName
                                . $tagAttribs
                                . '>';
                        $code[] = tx_apimacmade::div_formatXHTML(
                            $value,
                            $uppercase,
                            $newLine,
                            $indent,
                            $level + 1
                        );
                        $code[] = $codeIndents
                                . '</'
                                . $tagName
                                . '>';
                    }
                } else {
                    
                    // Check for empty tags
                    if( !$value ) {
                        
                        // Write empty tag
                        $code[] = $codeIndents
                                . '<'
                                . $tagName
                                . ' />';
                        
                    } else {
                        
                        // Write complete XHTML tag
                        $code[] = $codeIndents
                                . '<'
                                . $tagName
                                . '>'
                                . $value
                                . '</'
                                . $tagName
                                . '>';
                    }
                }
            }
            
            // Return HTML code
            return implode( chr( $newLine ), $code );
            
        } else {
            
            // Add root element to avoid XML errors
            $data = '<xml-root>'
                  . $xhtml
                  . '</xml-root>';
            
            // Try to convert XHTML code in an array
            $htmlArray = tx_apimacmade::div_xml2array( $data );
            
            // Check for a real array
            if( is_array( $htmlArray ) ) {
                
                // Begin to reformat HTML code
                return tx_apimacmade::div_formatXHTML(
                    $htmlArray[ 'xml-root' ],
                    $uppercase,
                    $newLine,
                    $indent,
                    0
                );
                
            } else {
                
                // Not valid XHTML - Return unformatted code
                return $xhtml;
            }
        }
    }
    
    /**
     * Convert line breaks
     * 
     * This function converts Macintosh & DOS line breaks to standard Unix
     * line breaks. This means replacing CR (u000D / chr(13)) and CR + LF
     * (u000D + u000A / chr(13) + chr( 10 )) by LF (u000A / chr( 10 )). It also
     * replace LF + CR (u000A + u000D / chr( 10 ) + chr(13)) sequences. By default,
     * the function erases all ASCII null characters (u0000 / chr(0)).
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $text               The text to process
     * @param       int         $stripNull          Erase ASCII null characters
     * @return      The text with standard Unix line breaks
     */
    function div_convertLineBreaks( $text, $stripNull = 1 )
    {
        // Strip ASCII null character?
        if( $stripNull ) {
            
            // Erase null
            $text = str_replace( chr( 0 ), '', $text );
        }
        
        // DOS CR + LF (u000D + u000A / chr(13) + chr( 10 ))
        $text = str_replace(
            chr( 13 ) . chr( 10 ),
            chr( 10 ),
            $text
        );
        
        // LF + CR (u000A + u000D / chr( 10 ) + chr(13))
        $text = str_replace(    
            chr( 10 ) . chr( 13 ),
            chr( 10 ),
            $text
        );
        
        // Macintosh CR (u000D / chr(13))
        $text = str_replace(
            chr( 13 ),
            chr( 10 ),
            $text
        );
        
        // Return text
        return $text;
    }
    
    /**
     * Check keys in a multidimensionnal array
     * 
     * This function is used to check for specific keys in a
     * multidimensionnal array.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       array       $array              The input array
     * @param       string      $keys               The keys to check for, as a commalist representing the array hierarchy
     * @param       boolean     $allowEmpty         If this is set, the function returns true even if the final array item has an empty value
     * @param       boolean     $checkType          The variable type to check for in the final array (array,bool,double,finite,float,int,long,nan,null,numeric,object,real,scalar,string)
     * @return      boolean
     * @see         div_isType
     */
    function div_checkArrayKeys( $array, $keys, $allowEmpty = false, $checkType = false )
    {
        // Check variables
        if( is_array( $array ) && count( $array ) ) {
            
            // Create an array with keys
            $check = explode( ',', $keys );
            
            // Check key array
            if( is_array( $check ) && count( $check ) ) {
                
                // Last key
                $lastKey = $check[ count( $check ) - 1 ];
                
                // Process keys
                foreach( $check as $key ) {
                    
                    // Check key in array
                    if( $key == $lastKey && array_key_exists( $key, $array ) ) {
                        
                        // Check type
                        if( !$checkType || tx_apimacmade::div_isType( $array[ $key ], $checkType ) ) {
                            
                            // Allow empty values in last key?
                            if( $allowEmpty ) {
                                
                                // Key exists
                                return true;
                                
                            } elseif( !empty( $array[ $key ] ) ) {
                                
                                // Key exists and has value
                                return true;
                            }
                        }
                    } elseif( array_key_exists( $key, $array ) ) {
                        
                        // Replace array with sub element
                        $array = $array[ $key ];
                        
                    } else {
                        
                        // Key not found
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Remove a directory
     * 
     * This function is used to delete a complete directory. If the permissions
     * are OK, every file (which can be on subdirectories) is deleted. Then,
     * all the subdirectories are deleted. This function is equivalent to a
     * 'rm -rf' command, but in a more elegant way.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       string      $path               The path of the directory (absolute)
     * @param       boolean     $relative           Path is relative to the Typo3 site, otherwise absolute
     * @param       boolean     $cleaned            Internal only! File cleaning has been processed
     * @return      boolean
     */
    function div_rmdir( $path, $relative = 0, $cleaned = false )
    {
        // Check if cleaning has already been done
        if( !$cleaned ) {
            
            // Fix path
            $path = t3lib_div::fixWindowsFilePath( $path );
            
            // Validate path
            if( t3lib_div::validPathStr( $path ) ) {
                
                // Check if path is relative
                if( $relative ) {
                    
                    // Get absolute path
                    $path = t3lib_div::getFileAbsFileName( $path );
                }
                
                // Check if path is absolute and allowed
                if( t3lib_div::isAbsPath( $path ) && t3lib_div::isAllowedAbsPath( $path ) ) {
                    
                    // Check if directory exists and is writeable
                    if( @file_exists( $path ) && @is_writeable( $path ) ) {
                        
                        // Get all sub files
                        $sub = t3lib_div::getAllFilesAndFoldersInPath(
                            array(),
                            $path
                        );
                        
                        // Check for sub files
                        if( is_array( $sub ) && count( $sub ) ) {
                            
                            // Delete each file
                            foreach( $sub as $file ) {
                                
                                // Check if file is writeable
                                if( @is_writeable( $file ) ) {
                                    
                                    // Delete file
                                    unlink( $file );
                                    
                                } else {
                                    
                                    // Error flag
                                    $error = 1;
                                    
                                    // Exit loop
                                    break;
                                }
                            }
                        }
                        
                        // Check for errors
                        if( !isset( $error ) ) {
                            
                            // DIrectory is cleanded, now erase each directory
                            tx_apimacmade::div_rmdir( $path, 0, 1 );
                            
                        } else {
                            
                            // Return false
                            return false;
                        }
                    }
                }
            }
            
        } else {
            
            // Get directories
            $directories = t3lib_div::get_dirs( $path );
            
            // Delete subdirectories if any
            if( is_array( $directories ) && count( $directories ) ) {
                
                // Process sub directories
                foreach( $directories as $subdir ) {
                    
                    // Try to delete subdirectories
                    $subDeleted = tx_apimacmade::div_rmdir(
                        $path . $subdir . '/',
                        0,
                        1
                    );
                }
            }
            
            // Check if directory is writeable
            if( !@is_writeable( $path ) || ( isset( $subDeleted ) && $subDeleted == 0 ) ) {
                
                // Error
                return false;
                
            } else {
                
                // Delete directory
                rmdir( $path );
                
                // Success
                return true;
            }
        }
    }
    
    /**
     * Check a variable type
     * 
     * This function is used to check the type of a variable, without using
     * the PHP gettype() function, which should never be used to check a variable type.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       mixed       $var                The variable to check
     * @param       string      $type               The type to check for(array,bool,double,finite,float,int,long,nan,null,numeric,object,real,scalar,string)
     * @return      Boolean
     */
    function div_isType( $var, $type )
    {
        // Function name
        $funcName = 'is_' . $type;
        
        // Check function
        if( function_exists( $funcName ) ) {
            
            // Check type and return result
            #return call_user_func( $funcName, $var );
            
            // Faster (thanx Stef)
            return $funcName( $var );
        }
    }
    
    
    
    
    
    /***************************************************************
     * SECTION 6 - DEBUG
     *
     * Debug functions.
     * 
     * Those functions are used to output debug informations. They are
     * very similar to the debug functions of the core Typo3 class t3lib_div,
     * except in the fact that they may produce a "smarter" output. So it's
     * just a question of tastes.
     * 
     * Those functions are available in any context. You also don't
     * need the instantiate the API class to use them, as they don't
     * need a parent object.
     ***************************************************************/
    
    /**
     * Display an array as an HTML table.
     * 
     * This function display an HTML table representing the given array.
     * Each item goes in a row, and keys/values in cols. If the array has
     * sub array, they are processed as well.
     * 
     * This function does the same stuff as the original Typo3 viewArray function,
     * in t3lib_div, except that this one produce a table with some nice CSS
     * syling, which should be useful to differentiate variable type at a glance.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       array       $array              The array to display
     * @param       int         $indent             The indentation level (used for indenting the HTML code)
     * @return      string      An HTML representation of the array
     */
    function viewArray( $array, $indent = 0 )
    {
        // Check if input is an array
        if( is_array( $array ) && count( $array ) ) {
            
            // First level indent
            $indent_1 = str_pad( chr( 9 ), $indent, chr( 9 ) );
            
            // Second level indent
            $indent_2 = str_pad( chr( 9 ), $indent + 1, chr( 9 ) );
            
            // Third level indent
            $indent_3 = str_pad( chr( 9 ), $indent + 2, chr( 9 ) );
            
            // Output storage
            $output   = array();
            
            // Start HTML comment
            $output[] = $indent_1 . '<!-- [PHP ARRAY - BEGIN] !-->';
            
            // Start table
            $output[] = $indent_1
                      . '<table style="margin: 5px; background: #FFFFFF; '
                      . 'border: solid 1px #666666; border-collapse: collapse;" '
                      . 'border="0" width="100%" cellspacing="0" cellpadding="5" '
                      . 'align="center">';
            
            // Process array
            foreach( $array as $key => $value ) {
                
                // Start row
                $output[] = $indent_2 . '<tr>';
                
                // Key
                $output[] = $indent_3
                          . '<td style="background: #FAFAFA; '
                          . 'border: dotted 1px #666666;" width="10%" align="left" '
                          . 'valign="top"><span style="font-size: 10px; '
                          . 'color: #666666">'
                          . $key
                          . '</span></td>';
                
                // Get value type
                $valueType = gettype( $value );
                
                // Process value types
                switch( $valueType ) {
                    
                    // Array
                    case 'array':
                        
                        $content = tx_apimacmade::viewArray( $value, $indent + 3 );
                    break;
                    
                    // String
                    case 'string':
                        
                        $content = '<span style="font-family: Helvetica, Arial, Verdana; '
                                 . 'font-size: 10px; color: #990000">'
                                 . nl2br( htmlspecialchars( $value ) )
                                 . '</span>';
                    break;
                    
                    // Default
                    default:
                        
                        $content = '<span style="font-family: Helvetica, Arial, Verdana; '
                                 . 'font-size: 10px; color: #006666">'
                                 . $value
                                 . '</span>';
                    break;
                }
                
                // Empty values
                if( empty( $value ) ) {
                    
                    $content = '<span style="font-family: Helvetica, Arial, Verdana; '
                             . 'font-size: 10px; color: #FF3300">[EMPTY]</span>';
                }
                
                // Value
                $output[] = $indent_3
                          . '<td style="background: #FFFFFF; border: dotted 1px #666666;" '
                          . 'width="90%" align="left" valign="top">'
                          . $content
                          . '</td>';
                
                // End row
                $output[] = $indent_2 . '</tr>';
            }
            
            // End table
            $output[] = $indent_1 . '</table>';
            
            // End HTML comment
            $output[] = $indent_1 . '<!-- [PHP ARRAY - END] !-->';
            
            // Return output
            return implode( chr( 10 ), $output );
        }
    }
    
    /**
     * Writes an PHP variable HTML representation.
     * 
     * This function produces an HTML representation of a PHP variable. If the
     * variable is an array, the function tx_apimacmade::viewArray is called.
     * Otherwise, the object is printed.
     * 
     * This function does the same stuff as the original Typo3 debug function,
     * in t3lib_div, except that this one produce a div with some nice CSS
     * syling, which should be useful to differentiate variable type at a glance.
     * 
     * SPECIAL NOTE: This function can be called without the API class instantiated.
     * 
     * @param       mixed       $variable           The variable to display
     * @param       string      $header             The header of the debug zone
     * @return      boolean
     * @see         viewArray
     */
    function debug( $variable, $header = 'DEBUG' )
    {
        // Get variable type
        $variableType = gettype( $variable );
        
        // Start div
        print '<div style="margin: 5px; border: solid 1px #666666; '
            . 'padding: 10px; background: #FAFAFA">';
        
        // Check if variable is empty
        if( empty( $variable ) ) {
            
            // Empty message
            print '<h1 style="font-family: Helvetica, Arial, Verdana; '
                . 'font-size: 15px; color: #666666">['
                . $header
                . ' - EMPTY]</h1>';
            
        } else {
            
            // Header
            print '<h1 style="font-family: Helvetica, Arial, Verdana; '
                . 'font-size: 15px; color: #666666">['
                . $header
                . ']</h1>';
            
            // Process object types
            switch( $variableType ) {
                
                // Array
                case 'array':
                    
                    // View array
                    print tx_apimacmade::viewArray( $variable );
                    
                break;
                
                // Object
                case 'object':
                    
                    // Start preformatted text
                    print '<pre style="font-size: 10px; color: #990000">';
                    
                    // Write object
                    print_r( $variable );
                    
                    // End preformatted text
                    print '</pre>';
                    
                break;
                
                // String
                case 'string':
                    
                    // Write object
                    print '<span style="font-family: Helvetica, Arial, Verdana; '
                        . 'font-size: 10px; color: #990000">'
                        . nl2br( htmlentities( $variable ) )
                        . '</span>';
                    
                break;
                
                // Default
                default:
                    
                    // Write object
                    print '<span style="font-family: Helvetica, Arial, Verdana; '
                        . 'font-size: 10px; color: #006666">'
                        . $variable
                        . '</span>';
                    
                break;
            }
        }
        
        // End div
        print '</div>';
        return true;
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/api_macmade/class.tx_apimacmade.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/api_macmade/class.tx_apimacmade.php']);
}
