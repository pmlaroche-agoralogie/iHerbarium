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
 * Plugin 'Drop-Down sitemap' for the 'dropdown_sitemap' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     3.0.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *   52:    class tx_dropdownsitemap_pi1 extends tslib_pibase
 *   86:    public function __construct
 *  104:    protected function _setConfig
 *  149:    protected function _buildMenuConfArray
 *  356:    protected function _buildImageTSConfig( $expanded = false )
 *  421:    protected function _buildJSCode
 *  614:    public function main( $content, array $conf )
 * 
 *          TOTAL FUNCTIONS: 6
 */

// TYPO3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_dropdownsitemap_pi1 extends tslib_pibase
{
    // TypoScript configuration array
    protected $_conf           = array();
    
    // Instance of the Developer API
    protected $_api            = NULL;
    
    // Plugin flexform data
    protected $_piFlexForm     = '';
    
    // New line character
    protected $_NL             = '';
    
    // Tabulation character
    protected $_TAB            = '';
    
    // Same as class name
    public $prefixId           = 'tx_dropdownsitemap_pi1';
    
    // Path to this script relative to the extension dir
    public $scriptRelPath      = 'pi1/class.tx_dropdownsitemap_pi1.php';
    
    // The extension key
    public $extKey             = 'dropdown_sitemap';
    
    // Version of the Developer API required
    public $apimacmade_version = 4.5;
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        // Calls the parent constructor
        parent::__construct();
        
        // Sets the new line character
        $this->_NL  = chr( 10 );
        
        // Sets the tabulation character
        $this->_TAB = chr( 9 );
    }
    
    /**
     * Set configuration array.
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     * 
     * @return  NULL
     */
    protected function _setConfig()
    {
        // Mapping array for PI flexform
        $flex2conf = array(
            'startingPoint'    => 'sDEF:pages',
            'excludeList'      => 'sPAGES:exclude_pages',
            'excludeDoktypes'  => 'sPAGES:exclude_doktypes',
            'includeNotInMenu' => 'sPAGES:include_not_in_menu',
            'showSpacers'      => 'sPAGES:show_spacers',
            'expAllLink'       => 'sOPTIONS:expall',
            'showLevels'       => 'sOPTIONS:show_levels',
            'expandLevels'     => 'sOPTIONS:expand_levels',
            'linkText'         => 'sOPTIONS:link_text',
            'descriptionField' => 'sOPTIONS:description_field',
            'linkTarget'       => 'sADVANCED:link_target',
            'list.'            => array(
                'tag'  => 'sADVANCED:list_tag',
                'type' => 'sADVANCED:list_type'
            ),
            'effects.'   => array(
                'engine' => 'sEFFECTS:engine',
                'appear' => 'sEFFECTS:appear',
                'fade'   => 'sEFFECTS:fade'
            )
        );
        
        // Override TS setup with flexform
        $this->_conf = $this->_api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->_conf,
            $this->_piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->_api->debug( $this->_conf, 'Drop-Down Site Map: configuration array' );
    }
    
    /**
     * Create MENU object configuration
     * 
     * This function creates the configuration array of the sitemap,
     * which will be used by the start() method of the tslib_tmenu class.
     * 
     * @return  array   The configuration array of the menu
     */
    protected function _buildMenuConfArray()
    {
        // PID list storage
        $pidList = array();
        
        // Menu configuration array
        $mconf   = array();
        
        // Exclude pages
        $mconf[ 'excludeUidList' ]   =& $this->_conf[ 'excludeList' ];
        
        // Exclude page types
        $mconf[ 'excludeDoktypes' ]  =& $this->_conf[ 'excludeDoktypes' ];
        
        // Include not in menu
        $mconf[ 'includeNotInMenu' ] =& $this->_conf[ 'includeNotInMenu' ];
        
        // Include not in menu
        $mconf[ 'insertData' ]       =  '1';
        
        // Creating menu items configuration
        for( $i = 1; $i < ( $this->_conf[ 'showLevels' ] + 1 ); $i++ ) {
            
            // Create image TS Config
            $imgTSConfig                                      = ( $i <= $this->_conf[ 'expandLevels' ] ) ? $this->_buildImageTSConfig( true ) : $this->_buildImageTSConfig();
            
            // Checks for the effects engine
            if( isset( $this->_conf[ 'effects.' ][ 'engine' ] )
                && $this->_conf[ 'effects.' ][ 'engine' ] == 'scriptaculous'
            ) {
                
                // No CSS class
                $className = '';
                
                // CSS styles for expand/collapse
                $styles    = ( $i - 1 <= $this->_conf[ 'expandLevels' ] ) ? ' style="display: block;"' : ' style="display: none;"';
                
            } elseif( isset( $this->_conf[ 'effects.' ][ 'engine' ] )
                && $this->_conf[ 'effects.' ][ 'engine' ] == 'mootools'
            ) {
                
                // No CSS class
                $className = '';
                
                // CSS styles for expand/collapse
                $styles    = ( $i - 1 <= $this->_conf[ 'expandLevels' ] ) ? ' style="display: block;"' : ' style="display: none;"';
                
            } else {
                
                // CSS class name for expand/collapse
                $className = ( $i <= $this->_conf[ 'expandLevels' ] )     ? ' class="open"'            : ' class="closed"';
                
                // No CSS styles
                $styles    = '';
            }
            
            // TMENU object
            $mconf[ $i ]                                      = 'TMENU';
            
            // Wrap in an HTML list element
            $mconf[ $i . '.' ][ 'wrap' ]                      = '<'
                                                              . $this->_conf[ 'list.' ][ 'tag' ]
                                                              . ' type="'
                                                              . $this->_conf[ 'list.' ][ 'type' ]
                                                              . '"'
                                                              . $styles
                                                              . '>|</'
                                                              . $this->_conf[ 'list.' ][ 'tag' ]
                                                              . '>';
            
            // Expand all property
            $mconf[ $i . '.' ][ 'expAll' ]                    = '1';
            
            // Target for the links
            $mconf[ $i . '.' ][ 'target' ]                    = $this->_conf[ 'linkTarget' ];
            
            // NO state configuration
            $mconf[ $i . '.' ][ 'NO.' ]                       = array();
            
            // Enable UID field substitution
            $mconf[ $i . '.' ][ 'NO.' ][ 'subst_elementUid' ] = '1';
            
            // End wrap
            $mconf[ $i . '.' ][ 'NO.' ][ 'wrapItemAndSub' ]   = '|</div></li>';
            
            // Start wrap
            $mconf[ $i . '.' ][ 'NO.' ][ 'allWrap' ]          = '<li class="closed"><div class="level_'
                                                              . $i
                                                              . '">'
                                                              . $this->cObj->IMAGE( $imgTSConfig[ 'NO' ] )
                                                              . '<span class="no">|</span>';
            
            // Checks for a specific link text
            if( $this->_conf[ 'linkText' ] ) {
                
                // Forces the link text
                $mconf[ $i . '.' ][ 'NO.' ][ 'stdWrap.' ][ 'field' ] = $this->_conf[ 'linkText' ];
            }
            
            // Check if A tag title must be added
            if( $this->_conf[ 'titleFields' ] ) {
                
                // Add fields for A tag
                $mconf[ $i . '.' ][ 'NO.' ][ 'ATagTitle.' ][ 'field' ] = $this->_conf[ 'titleFields' ];
            }
            
            // Check if a description must be added
            if( $this->_conf[ 'descriptionField' ] && $this->_conf[ 'descriptionField' ] != 'none' ) {
                
                // Add description
                $mconf[ $i . '.' ][ 'NO.' ][ 'after.' ][ 'dataWrap' ] = '|<span class="description">&nbsp;{field:'
                                                                      . $this->_conf[ 'descriptionField' ]
                                                                      . '}</span>';
            }
            
            // Only check for subpages if sublevels must be shown
            if( $i < $this->_conf[ 'showLevels' ] ) {
                
                // IFSUB state configuration
                $mconf[ $i . '.' ][ 'IFSUB.' ]                       = array();
                
                // Enable UID field substitution
                $mconf[ $i . '.' ][ 'IFSUB.' ][ 'subst_elementUid' ] = '1';
                
                // End wrap
                $mconf[ $i . '.' ][ 'IFSUB.' ][ 'wrapItemAndSub' ]   = '|</div></li>';
                
                // Start wrap
                $mconf[ $i . '.' ][ 'IFSUB.' ][ 'allWrap' ]          = '<li id="'
                                                                     . $this->prefixId
                                                                     . '_{elementUid}"'
                                                                     . $className
                                                                     . '><div class="level_'
                                                                     . $i
                                                                     . '"><a href="javascript:'
                                                                     . $this->prefixId
                                                                     . '_swapClasses({elementUid});" title="'
                                                                     . $this->pi_getLL( 'title-ifsub' )
                                                                     . '">'
                                                                     . $this->cObj->IMAGE( $imgTSConfig[ 'IFSUB' ] )
                                                                     . '</a><span class="ifsub">|</span>';
                
                // IFSUB state activation
                $mconf[ $i . '.' ][ 'IFSUB' ]                        = '1';
                
                // Checks for a specific link text
                if( $this->_conf[ 'linkText' ] ) {
                    
                    // Forces the link text
                    $mconf[ $i . '.' ][ 'IFSUB.' ][ 'stdWrap.' ][ 'field' ] = $this->_conf[ 'linkText' ];
                }
                
                // Check if A tag title must be added
                if( $this->_conf[ 'titleFields' ] ) {
                    
                    // Add fields for A tag
                    $mconf[ $i . '.' ][ 'IFSUB.' ][ 'ATagTitle.' ][ 'field' ] = $this->_conf[ 'titleFields' ];
                }
                
                // Check if a description must be added
                if( $this->_conf[ 'descriptionField' ] && $this->_conf[ 'descriptionField' ] != 'none' ) {
                    
                    // Add description
                    $mconf[ $i . '.' ][ 'IFSUB.' ][ 'after.' ][ 'dataWrap' ] = '|<span class="description">&nbsp;{field:'
                                                                             . $this->_conf[ 'descriptionField' ]
                                                                             . '}</span>';
                }
            }
            
            // Configuration for spacers
            if( $this->_conf[ 'showSpacers' ] ) {
                
                // Activate spacers
                $mconf[ $i . '.' ][ 'SPC' ]                      = '1';
                
                // End wrap
                $mconf[ $i . '.' ][ 'SPC.' ][ 'wrapItemAndSub' ] = '|</div></li>';
                
                // Start wrap
                $mconf[ $i . '.' ][ 'SPC.' ][ 'allWrap' ]        = '<li class="closed"><div class="level_'
                                                                 . $i
                                                                 . '">'
                                                                 . $this->cObj->IMAGE( $imgTSConfig[ 'SPC' ] )
                                                                 . '<span class="spc">|</span>';
                
                // Checks for a specific link text
                if( $this->_conf[ 'linkText' ] ) {
                    
                    // Forces the link text
                    $mconf[ $i . '.' ][ 'SPC.' ][ 'stdWrap.' ][ 'field' ] = $this->_conf[ 'linkText' ];
                }
            }
        }
        
        // Return configuration array
        return $mconf;
    }
    
    /**
     * Create IMAGE object configuration
     * 
     * This function creates the configuration array for the expand and
     * collapse pictures. Used by the IMAGE method of the cObj class.
     * 
     * @param   boolean     $expanded   True if the menu should be expanded
     * @return  array       The configuration arrays for the pictures.
     */
    protected function _buildImageTSConfig( $expanded = false )
    {
        // Image TS Config array for NO state
        $imgTSConfigNo                        = array();
        
        // File reference
        $imgTSConfigNo[ 'file' ]              = $this->_conf[ 'picture.' ][ 'page' ];
        
        // File ressource array
        $imgTSConfigNo[ 'file.' ]             = array();
        
        // Width
        $imgTSConfigNo[ 'file.' ][ 'width' ]  = $this->_conf[ 'picture.' ][ 'width' ];
        
        // Height
        $imgTSConfigNo[ 'file.' ][ 'height' ] = $this->_conf[ 'picture.' ][ 'height' ];
        
        // HTML tag parameters
        $imgTSConfigNo[ 'params' ]            = $this->_conf[ 'picture.' ][ 'params' ];
        
        // Image TS Config array for SPC state
        $imgTSConfigSpc                       = $imgTSConfigNo;
        
        // File reference
        $imgTSConfigSpc[ 'file' ]             = $this->_conf[ 'picture.' ][ 'spacer' ];
        
        // Image TS Config array for IFSUB state
        $imgTSConfigSub                       = $imgTSConfigNo;
        
        // File reference
        $imgTSConfigSub[ 'file' ]             = ( $expanded ) ? $this->_conf[ 'picture.' ][ 'collapse' ] : $this->_conf[ 'picture.' ][ 'expand' ];
        
        // HTML tag parameters
        $imgTSConfigSub[ 'params' ]           = $this->_conf[ 'picture.' ][ 'params' ]
                                              . ' id="pic_{elementUid}"';
        
        // Final array
        $imgTSConfig = array(
            'NO'    => $imgTSConfigNo,
            'IFSUB' => $imgTSConfigSub,
            'SPC'   => $imgTSConfigSpc
        );
        
        // Add alt texts
        $imgTSConfig[ 'NO' ][ 'altText' ]    = $this->pi_getLL( 'alt-no' );
        $imgTSConfig[ 'IFSUB' ][ 'altText' ] = $this->pi_getLL( 'alt-ifsub' );
        $imgTSConfig[ 'SPC' ][ 'altText' ]   = $this->pi_getLL( 'alt-spc' );
        
        // Add title texts
        $imgTSConfig[ 'NO' ][ 'titleText' ]    = $this->pi_getLL( 'title-no' );
        $imgTSConfig[ 'IFSUB' ][ 'titleText' ] = $this->pi_getLL( 'title-ifsub' );
        $imgTSConfig[ 'SPC' ][ 'titleText' ]   = $this->pi_getLL( 'title-spc' );
        
        // Return array
        return $imgTSConfig;
    }
    
    /**
     * Adds JavaScript Code.
     * 
     * This function adds the javascript code used to switch between
     * CSS classes and to expand/collapse all sections.
     * 
     * @return      Void.
     */
    protected function _buildJSCode() 
    {
        // Storage
        $jsCode      = array();
        
        // Plus image URL
        $plusImgURL  = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName(
                $this->_conf[ 'picture.' ][ 'expand' ]
            )
        );
        
        // Minus image URL
        $minusImgURL = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName(
                $this->_conf[ 'picture.' ][ 'collapse' ]
            )
        );
        
        // Expand all image URL
        $expOn = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName(
                $this->_conf[ 'picture.' ][ 'expOn' ]
            )
        );
        
        // Collapse all image URL
        $expOff = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName(
                $this->_conf[ 'picture.' ][ 'expOff' ]
            )
        );
        
        // Checks for Scriptaculous
        if( isset( $this->_conf[ 'effects.' ][ 'engine' ] )
            && $this->_conf[ 'effects.' ][ 'engine' ] == 'scriptaculous'
        ) {
            
            // Effects
            $appear = $this->_conf[ 'effects.' ][ 'appear' ];
            $fade   = $this->_conf[ 'effects.' ][ 'fade' ];
            
            // Function for swapping element class
            $jsCode[] = 'function ' . $this->prefixId . '_swapClasses( element )';
            $jsCode[] = '{';
            $jsCode[] = '    var listItem    = $( "' . $this->prefixId . '_" + element );';
            $jsCode[] = '    var descendants = listItem.firstDescendant().immediateDescendants();';
            $jsCode[] = '    var list        = descendants[ descendants.length - 1 ];';
            $jsCode[] = '    var picture     = "pic_" + element;';
            $jsCode[] = '    if( list.getStyle( "display" ) == "none" ) {';
            $jsCode[] = '        Effect.' . $appear . '( list );';
            $jsCode[] = '        document.getElementById( picture ).src = "' . $minusImgURL . '";';
            $jsCode[] = '    } else {';
            $jsCode[] = '        Effect.' . $fade . '( list );';
            $jsCode[] = '        document.getElementById( picture ).src = "' . $plusImgURL . '";';
            $jsCode[] = '    }';
            $jsCode[] = '}';
            
            // Function for expanding/collapsing all elements
            $jsCode[] = 'var ' . $this->prefixId . '_expanded = 0;';
            $jsCode[] = 'function ' . $this->prefixId . '_expAll()';
            $jsCode[] = '{';
            $jsCode[] = '    if( document.getElementsByTagName ) {';
            $jsCode[] = '        var style     = ( ' . $this->prefixId . '_expanded ) ? "none" : "block";';
            $jsCode[] = '        var listItems = document.getElementsByTagName( "li" );';
            $jsCode[] = '        for( i = 0; i < listItems.length; i++ ) {';
            $jsCode[] = '            if( listItems[ i ].id.indexOf( "' . $this->prefixId . '" ) != -1 ) {';
            $jsCode[] = '                var listItem    = $( listItems[ i ].id );';
            $jsCode[] = '                var descendants = listItems[ i ].firstDescendant().immediateDescendants();';
            $jsCode[] = '                var list        = descendants[ descendants.length - 1 ];';
            $jsCode[] = '                var picture     = "pic_" + listItems[ i ].id.replace( "' . $this->prefixId . '_", "" );';
            $jsCode[] = '                if( ' . $this->prefixId . '_expanded && list.getStyle( "display" ) == "block" ) {';
            $jsCode[] = '                    Effect.' . $fade . '( list );';
            $jsCode[] = '                    document.getElementById( picture ).src = "' . $plusImgURL . '";';
            $jsCode[] = '                } else if( list.getStyle( "display" ) == "none" ) {';
            $jsCode[] = '                    Effect.' . $appear . '( list );';
            $jsCode[] = '                    document.getElementById( picture ).src = "' . $minusImgURL . '";';
            $jsCode[] = '                }';
            $jsCode[] = '            }';
            $jsCode[] = '        }';
            $jsCode[] = '        document.getElementById( "' . $this->prefixId . '_expImg" ).src = ( ' . $this->prefixId . '_expanded == 1 ) ? "' . $expOn . '" : "' . $expOff . '"';
            $jsCode[] = '        ' . $this->prefixId . '_expanded                                = ( ' . $this->prefixId . '_expanded == 1 ) ? 0 : 1;';
            $jsCode[] = '    }';
            $jsCode[] = '}';
            
        } elseif( isset( $this->_conf[ 'effects.' ][ 'engine' ] )
            && $this->_conf[ 'effects.' ][ 'engine' ] == 'mootools'
        ) {
            
            // Function for swapping element class
            $jsCode[] = 'function ' . $this->prefixId . '_swapClasses( element )';
            $jsCode[] = '{';
            $jsCode[] = '    var list = $E( "' . $this->_conf[ 'list.' ][ 'tag' ] . '", "' . $this->prefixId . '_" + element );';
            $jsCode[] = '    var picture     = "pic_" + element;';
            $jsCode[] = '    if( list.getStyle( "display" ) == "block" ) {';
            $jsCode[] = '        var width = list.getStyle( "width" );';
            $jsCode[] = '        var fx = new Fx.Elements( list, { onComplete : function () { list.setStyle( "width", width ); list.setStyle( "display", "none" ); list.setStyle( "opacity", 1 ); } } );';
            $jsCode[] = '        fx.start( { 0 : { opacity : [ 1, 0 ], width : [ width, 0 ] } } );';
            $jsCode[] = '        document.getElementById( picture ).src = "' . $plusImgURL . '";';
            $jsCode[] = '    } else {';
            $jsCode[] = '        list.setStyle( "opacity", 0 );';
            $jsCode[] = '        list.setStyle( "display", "block" );';
            $jsCode[] = '        var width = list.getStyle( "width" );';
            $jsCode[] = '        list.setStyle( "width", 0 );';
            $jsCode[] = '        var fx = new Fx.Elements( list );';
            $jsCode[] = '        fx.start( { 0 : { opacity : [ 0, 1 ], width : [ 0, width ] } } );';
            $jsCode[] = '        document.getElementById( picture ).src = "' . $minusImgURL . '";';
            $jsCode[] = '    }';
            $jsCode[] = '}';
            
            // Function for expanding/collapsing all elements
            $jsCode[] = 'var ' . $this->prefixId . '_expanded = 0;';
            $jsCode[] = 'function ' . $this->prefixId . '_expAll()';
            $jsCode[] = '{';
            $jsCode[] = '    if( document.getElementsByTagName ) {';
            $jsCode[] = '        var listItems = document.getElementsByTagName( "li" );';
            $jsCode[] = '        for( i = 0; i < listItems.length; i++ ) {';
            $jsCode[] = '            if( listItems[ i ].id.indexOf( "' . $this->prefixId . '" ) != -1 ) {';
            $jsCode[] = '                var list    = $E( "' . $this->_conf[ 'list.' ][ 'tag' ] . '", listItems[ i ].id );';
            $jsCode[] = '                var picture = "pic_" + listItems[ i ].id.replace( "' . $this->prefixId . '_", "" );';
            $jsCode[] = '                if( ' . $this->prefixId . '_expanded && list.getStyle( "display" ) == "block" ) {';
            $jsCode[] = '                    list.setStyle( "display", "none" );';
            $jsCode[] = '                    document.getElementById( picture ).src = "' . $plusImgURL . '";';
            $jsCode[] = '                } else if( list.getStyle( "display" ) == "none" ) {';
            $jsCode[] = '                    list.setStyle( "display", "block" );';
            $jsCode[] = '                    document.getElementById( picture ).src = "' . $minusImgURL . '";';
            $jsCode[] = '                }';
            $jsCode[] = '            }';
            $jsCode[] = '        }';
            $jsCode[] = '        document.getElementById( "' . $this->prefixId . '_expImg" ).src = ( ' . $this->prefixId . '_expanded == 1 ) ? "' . $expOn . '" : "' . $expOff . '"';
            $jsCode[] = '        ' . $this->prefixId . '_expanded                                = ( ' . $this->prefixId . '_expanded == 1 ) ? 0 : 1;';
            $jsCode[] = '    }';
            $jsCode[] = '}';
            
        } else {
            
            // Function for swapping element class
            $jsCode[] = 'function ' . $this->prefixId . '_swapClasses( element )';
            $jsCode[] = '{';
            $jsCode[] = '    if( document.getElementById ) {';
            $jsCode[] = '        var liClass                                  = "' . $this->prefixId . '_" + element;';
            $jsCode[] = '        var picture                                  = "pic_" + element;';
            $jsCode[] = '        document.getElementById( liClass ).className = ( document.getElementById( liClass ).className == "open" ) ? "closed" : "open";';
            $jsCode[] = '        document.getElementById( picture ).src       = ( document.getElementById( liClass ).className == "open" ) ? "' . $minusImgURL . '" : "' . $plusImgURL . '";';
            $jsCode[] = '    }';
            $jsCode[] = '}';
            
            // Function for expanding/collapsing all elements
            $jsCode[] = 'var ' . $this->prefixId . '_expanded = 0;';
            $jsCode[] = 'function ' . $this->prefixId . '_expAll()';
            $jsCode[] = '{';
            $jsCode[] = '    if( document.getElementsByTagName ) {';
            $jsCode[] = '        var listItems = document.getElementsByTagName( "li" );';
            $jsCode[] = '        for( i = 0; i < listItems.length; i++ ) {';
            $jsCode[] = '            if( listItems[ i ].id.indexOf( "' . $this->prefixId . '" ) != -1 ) {';
            $jsCode[] = '                listItems[ i ].className               = ( ' . $this->prefixId . '_expanded ) ? "closed" : "open";';
            $jsCode[] = '                var picture                            = "pic_" + listItems[ i ].id.replace( "' . $this->prefixId . '_", "" );';
            $jsCode[] = '                listItems[ i ].className               = ( ' . $this->prefixId . '_expanded ) ? "closed" : "open"';
            $jsCode[] = '                document.getElementById( picture ).src = ( ' . $this->prefixId . '_expanded ) ? "' . $plusImgURL . '" : "' . $minusImgURL . '";';
            $jsCode[] = '            }';
            $jsCode[] = '        }';
            $jsCode[] = '        document.getElementById( "' . $this->prefixId . '_expImg" ).src = ( ' . $this->prefixId . '_expanded == 1 ) ? "' . $expOn . '" : "' . $expOff . '"';
            $jsCode[] = '        ' . $this->prefixId . '_expanded                                = ( ' . $this->prefixId . '_expanded == 1 ) ? 0 : 1;';
            $jsCode[] = '    }';
            $jsCode[] = '}';
        }
        
        // Adds JS code
        $GLOBALS[ 'TSFE' ]->setJS(
            $this->prefixId,
            implode( $this->_NL, $jsCode )
        );
    }
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin "tx_dropdownsitemap_pi1", and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param   string  $content    The content object
     * @param   array   $conf       The TS setup
     * @return  string  The content of the plugin.
     * @see     buildJSCode
     */
    public function main( $content, array $conf )
    {
        // New instance of the macmade.net API
        $this->_api  = new tx_apimacmade( $this );
        
        // Placing TS conf array in a class variable
        $this->_conf =& $conf;
        
        // Load LOCAL_LANG values
        $this->pi_loadLL();
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->_piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Checks the configuration array
        if( !is_array( $this->_conf ) || count( $this->_conf ) <= 2 ) {
            
            // Static template not included
            return $this->_api->fe_makeStyledContent(
                'strong',
                'error',
                $this->pi_getLL( 'error' )
            );
        }
        
        // Set final configuration (TS or FF)
        $this->_setConfig();
        
        // Checks for Scriptaculous
        if( isset( $this->_conf[ 'effects.' ][ 'engine' ] )
            && $this->_conf[ 'effects.' ][ 'engine' ] == 'scriptaculous'
        ) {
            
            // Includes Scriptaculous
            $this->_api->fe_includeScriptaculousJs();
        }
        
        // Checks for Mootools
        if( isset( $this->_conf[ 'effects.' ][ 'engine' ] )
            && $this->_conf[ 'effects.' ][ 'engine' ] == 'mootools'
        ) {
            
            // Includes Scriptaculous
            $this->_api->fe_includeMootoolsJs();
        }
        
        // Create the menu configuration array
        $mconf = $this->_buildMenuConfArray();
        
        // Add JavaScrip Code
        $this->_buildJSCode();
        
        // New instance of the tslib_tmenu class
        $menu              = t3lib_div::makeInstance( 'tslib_tmenu' );
        
        // Set some internal vars
        $menu->parent_cObj = $this->cObj;
        
        // Use starting point field
        // Thanks a lot to Steven Bagshaw for that piece of code
        $startingPoint     = $this->_conf[ 'startingPoint' ];
        
        // Class constructor
        $menu->start(
            $GLOBALS[ 'TSFE' ]->tmpl,
            $GLOBALS[ 'TSFE' ]->sys_page,
            $startingPoint,
            $mconf,
            1
        );
        
        // Make the menu
        $menu->makeMenu();
        
        // Storage
        $content = array();
        
        // Display the expAll link
        if( $this->_conf[ 'expAllLink' ] == 1 ) {
            
            // Picture TS configuration array
            $imgTSConfig                        = array();
            
            // File reference
            $imgTSConfig[ 'file' ]              = $this->_conf[ 'picture.' ][ 'expOn' ];
            
            // File ressource array
            $imgTSConfig[ 'file.' ]             = array();
            
            // Width
            $imgTSConfig[ 'file.' ][ 'width' ]  = $this->_conf[ 'picture.' ][ 'width' ];
            
            // Height
            $imgTSConfig[ 'file.' ][ 'height' ] = $this->_conf[ 'picture.' ][ 'height' ];
            
            // HTML tag parameters
            $imgTSConfig[ 'params' ]            = $this->_conf[ 'picture.' ][ 'params' ]
                                                . ' id="'
                                                . $this->prefixId
                                                . '_expImg"';
            
            // Adds the alt and title text
            $imgTSConfig[ 'altText' ]           = $this->pi_getLL( 'expall' );
            $imgTSConfig[ 'titleText' ]         = $this->pi_getLL( 'expall' );
            
            $content[] = '<div class="expAll"><a href="javascript:'
                       . $this->prefixId
                       . '_expAll();" title="'
                       . $this->pi_getLL( 'expall' )
                       . '">'
                       . $this->cObj->IMAGE( $imgTSConfig )
                       . $this->pi_getLL( 'expall' )
                       . '</a></div>';
        }
        
        // Write the full menu with sub-items
        $content[] = $menu->writeMenu();
        
        // Return the full menu
        return $this->pi_wrapInBaseClass( implode( $this->_NL, $content ) );
    }
}

/**
 * XCLASS inclusion
 */
if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dropdown_sitemap/pi1/class.tx_dropdownsitemap_pi1.php"]) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dropdown_sitemap/pi1/class.tx_dropdownsitemap_pi1.php"]);
}
