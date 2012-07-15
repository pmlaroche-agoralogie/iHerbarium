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
 * Class for updating the extension from older versions.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     2.0
 */

class ext_update
{
    // Database object
    protected static $_db = NULL;
    
    // Document object
    protected $_doc       = NULL;
    
    // Back path
    protected $_backPath  = '';
    
    // New line character
    protected $_NL        = '';
    
    // Tabulation character
    protected $_TAB       = '';
    
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
        
        // Back path
        $this->_backPath = $GLOBALS[ 'BACK_PATH' ];
        
        // Sets the new line character
        $this->_NL  = chr( 10 );
        
        // Sets the tabulation character
        $this->_TAB = chr( 9 );
    }
    
    /**
     * Select old sitemap records
     * 
     * This function select all the old instance of the dropdown sitemap plugin
     * in the tt_content database.
     * 
     * @return	ressource   The MySQL pointer
     */
    protected function _selectOldRecords()
    {
        // Table to use
        $table       = 'tt_content';
        
        // Where clause to select old sitemap records
        $whereClause = 'CType="menu" AND menu_type="dropdown_sitemap_pi1"';
        
        // Select records
        $res         = self::$_db->exec_SELECTquery( '*', $table, $whereClause );
        
        // Return ressource
        return $res;
    }
    
    /**
     * Check if an update is needed.
     * 
     * This function check if old sitemap records are present in the database. It is
     * used to display the update menu in the TYPO3 extension manager.
     * 
     * @return	boolean
     */
    public function access()
    {
        // Check if records need to be updated
        if( self::$_db->sql_num_rows( $this->_selectOldRecords() ) ) {
            
            // Make the update menu available
            return true;
        }
    }
    
    /**
     * Updates records from the extension
     * 
     * This is the main function for updating the dropdown sitemap extension. It is
     * used to display a list of the old records, and to update them.
     * 
     * @return	string  The update view
     */
    public function main()
    {
        // New instance of the document class
        $this->_doc  = t3lib_div::makeInstance( 'bigDoc' );
        
        // Select records
        $res         = $this->_selectOldRecords();
        
        // Count records
        $recNum      = self::$_db->sql_num_rows( $res );
        
        // Counters
        $colorcount  = 0;
        
        // Storage
        $htmlCode    = array();
        
        // Start form
        $htmlCode[]  = '<form action="'
                     . t3lib_div::linkThisScript()
                     . '" method="post" enctype="'
                     . $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ]
                     . '">';
        
        // Check action
        if ( t3lib_div::_GP( 'update' ) ) {
            
            // Infos
            $htmlCode[] = '<p><img '
                        . t3lib_iconWorks::skinImg( $this->_backPath, 'gfx/icon_note.gif', '' )
                        . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;<strong>Here are the results of the update process.</strong><br />If all the records were successfully updated, you won\'t see this page anymore in the extension manager.</p>';
            
            // Divider
            $htmlCode[] = $this->_doc->divider( 5 );
            
            // Start table
            $htmlCode[] = '<table id="recList" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="'
                        . $this->_doc->bgColor2
                        . '">';
            $htmlCode[] = '<tr>';
            $htmlCode[] = '<td align="left" valign="middle"></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Title</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Page</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Path</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Status</strong></td>';
            $htmlCode[] = '</tr>';
            
            // Process records
            while( $row = self::$_db->sql_fetch_assoc( $res ) ) {
                
                // Change row color
                $colorcount  = ( $colorcount == 1 ) ? 0                     : 1;
                $color       = ( $colorcount == 1 ) ? $this->_doc->bgColor4 : $this->_doc->bgColor5;
                
                // Build row parameters
                $tr_params   = ' bgcolor="' . $color . '"';
                
                // Start row
                $htmlCode[]  = '<tr' . $tr_params . '>';
                
                // Page row
                $page        = t3lib_BEfunc::getRecord( 'pages', $row[ 'pid' ] );
                
                // Where clause to select old sitemap records
                $whereClause = 'uid=' . $row[ 'uid' ];
                
                // Update record
                $update      = array(
                    'CType'     => 'list',
                    'menu_type' => '',
                    'list_type' => 'dropdown_sitemap_pi1',
                );
                
                // Status icon
                $status = ( self::$_db->exec_UPDATEquery( 'tt_content', $whereClause, $update ) ) ? 'gfx/icon_ok2.gif' : 'gfx/icon_warning.gif';
                
                // Fields
                $htmlCode[] = '<td align="left" valign="middle">'
                            . t3lib_iconWorks::getIconImage( 'tt_content', $row, $this->_backPath )
                            . '</td>';
                $htmlCode[] = '<td align="left" valign="middle"><strong>'
                            . $row[ 'header' ]
                            . '</strong> ('
                            . $row[ 'uid' ]
                            . ')</td>';
                $htmlCode[] = '<td align="left" valign="middle">'
                            . $page[ 'title' ]
                            . ' ('
                            . $page[ 'uid' ]
                            . ')</td>';
                $htmlCode[] = '<td align="left" valign="middle">'
                            . t3lib_BEfunc::getRecordPath( $row[ 'pid' ], '', 50 )
                            . '</td>';
                $htmlCode[] = '<td align="left" valign="middle"><img '
                            . t3lib_iconWorks::skinImg( $this->_backPath, $status, '' )
                            . ' alt="" hspace="0" vspace="0" border="0" align="middle"></td>';
                
                // End row
                $htmlCode[] = '</tr>';
            }
            
            // End table
            $htmlCode[] = '</table>';
            
        } else {
            
            // Infos
            $htmlCode[] = '<p><img '
                        . t3lib_iconWorks::skinImg( $this->_backPath, 'gfx/icon_note.gif', '' )
                        . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;<strong>Some of the database records need to be updated in order to use the new version of the extension.</strong><br />Please click the button below to update the records listed here.</p>';
            
            // Spacer
            $htmlCode[] = $this->_doc->spacer( 5 );
            
            // Submit
            $htmlCode[] = '<input name="update" type="submit" value="Update database ('
                        . $recNum
                        . ' record(s) affected)">';
            
            // GET variables
            $CMD = t3lib_div::_GP( 'CMD' );
            $SET = t3lib_div::_GP( 'SET' );
            
            // Hidden inputs to preserve GET variables
            $htmlCode[] = '<input name="id" type="hidden" value="'
                        . t3lib_div::_GP( 'id' )
                        . '">';
            $htmlCode[] = '<input name="CMD[showExt]" type="hidden" value="'
                        . $CMD[ 'showExt' ]
                        . '">';
            $htmlCode[] = '<input name="SET[singleDetails]" type="hidden" value="'
                        . $SET[ 'singleDetails' ]
                        . '">';
            
            // Divider
            $htmlCode[] = $this->_doc->divider( 5 );
            
            // Start table
            $htmlCode[] = '<table id="recList" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="'
                        . $this->_doc->bgColor2
                        . '">';
            $htmlCode[] = '<tr>';
            $htmlCode[] = '<td align="left" valign="middle"></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Title</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Page</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Path</strong></td>';
            $htmlCode[] = '</tr>';
            
            // Show records
            while( $row = self::$_db->sql_fetch_assoc( $res ) ) {
                
                // Change row color
                $colorcount = ( $colorcount == 1 ) ? 0                    : 1;
                $color      = ( $colorcount == 1 ) ? $this->_doc->bgColor4 : $this->_doc->bgColor5;
                
                // Build row parameters
                $tr_params  = ' bgcolor="' . $color . '"';
                
                // Start row
                $htmlCode[] = '<tr' . $tr_params . '>';
                
                // Page row
                $page       = t3lib_BEfunc::getRecord( 'pages', $row[ 'pid' ] );
                
                // Fields
                $htmlCode[] = '<td align="left" valign="middle">'
                            . t3lib_iconWorks::getIconImage( 'tt_content', $row, $this->_backPath )
                            . '</td>';
                $htmlCode[] = '<td align="left" valign="middle"><strong>'
                            . $row[ 'header' ]
                            . '</strong> ('
                            . $row[ 'uid' ]
                            .')</td>';
                $htmlCode[] = '<td align="left" valign="middle">'
                            . $page[ 'title' ]
                            . ' ('
                            . $page[ 'uid' ]
                            . ')</td>';
                $htmlCode[] = '<td align="left" valign="middle">'
                            . t3lib_BEfunc::getRecordPath( $row[ 'pid' ], '', 50 )
                            . '</td>';
                
                // End row
                $htmlCode[] = '</tr>';
            }
            
            // End table
            $htmlCode[] = '</table>';
        }
        
        // End form
        $htmlCode[]  = '</form>';
        
        // Return content
        return implode( $this->_NL, $htmlCode );
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dropdown_sitemap/class.ext_update.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dropdown_sitemap/class.ext_update.php']);
}
