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
 * TCA helper for extension dropdown_sitemap
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     2.0
 */

class tx_dropdownsitemap_tca
{
    // Database object
    protected static $_db   = NULL;
    
    // Lang object
    protected static $_lang = NULL;
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        // Checks for the database object
        if( !is_object( self::$_db ) ) {
            
            // Gets a reference to the database object
            self::$_db = $GLOBALS[ 'TYPO3_DB' ];
        }
        
        
        // Checks for the database object
        if( !is_object( self::$_lang ) ) {
            
            // Gets a reference to the database object
            self::$_lang = $GLOBALS[ 'LANG' ];
        }
    }
    
    /**
     * Fills the parameters array with the page fields
     * 
     * @param   array   &$params    The parameters of the form
     * @param   object  &$pObj      A reference to the parent object
     * @return  NULL
     */
    public function getPageFields( array &$params, &$pObj )
    {
        // Get fields
        $fields = self::$_db->admin_get_fields( 'pages' );
        
        // Sorts the fields array
        ksort( $fields );
        
        // Process each field
        foreach( $fields as $key => $value ) {
            
            // Lang label
            $langLabel = self::$_lang->sL( 'LLL:EXT:cms/locallang_tca.php:pages.' . $key );
            
            // Option label
            $label     = ( $langLabel ) ? $langLabel . ' [' . $key . ']': '[' . $key . ']';
            
            // Adds the item
            $params[ 'items' ][] = array(
                $label,
                $key
            );
        }
    }
}

/**
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dropdown_sitemap/class.tx_dropdownsitemap_tca.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dropdown_sitemap/class.tx_dropdownsitemap_tca.php']);
}
