<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_rlmplanguagedetection_pi1.php','_pi1','includeLib',0);

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['initFEuser'][] = t3lib_extMgm::extPath($_EXTKEY).'tx_langsession.php:user_langsession';
?>
