<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// FE plugins
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_dropdownsitemap_pi1.php',
    '_pi1',
    'list_type',
    1
);
?>
