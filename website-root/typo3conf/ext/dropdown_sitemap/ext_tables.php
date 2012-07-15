<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Includes the TCA helper class
require_once( t3lib_extMgm::extPath( $_EXTKEY ) . 'class.tx_dropdownsitemap_tca.php' );

// Load content TCA
t3lib_div::loadTCA( 'tt_content' );

// Plugin options
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi1' ] = 'layout,select_key,pages,recursive';

// Add flexform field to plugin options
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_pi1' ]     = 'pi_flexform';

// Add flexform DataStructure
t3lib_extMgm::addPiFlexFormValue(
    $_EXTKEY . '_pi1',
    'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml'
);

// Add plugin
t3lib_extMgm::addPlugin(
    array(
        'LLL:EXT:dropdown_sitemap/locallang_db.php:tt_content.list_type_pi1',
        $_EXTKEY . '_pi1'
    ),
    'list_type'
);

// Static templates
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/ts/', 'Drop-Down Site Map' );

// Wizard icons
if( TYPO3_MODE == 'BE' ) {
    
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_dropdownsitemap_pi1_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi1/class.tx_dropdownsitemap_pi1_wizicon.php';
}
?>
