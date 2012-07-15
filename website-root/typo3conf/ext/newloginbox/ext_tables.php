<?php
# TYPO3 CVS ID: $Id: ext_tables.php 947 2004-02-02 09:45:39Z typo3 $
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');

	// Lists the fields which 1) should NOT be displayed and 2) those which SHOULD be displayed with the 'Better login-box' plugin
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi3']='pi_flexform';
	// Lists fields to exclude with the 'User list' plugin
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key';

	// Adds the two plugins to the TCA
t3lib_extMgm::addPlugin(Array('LLL:EXT:newloginbox/locallang_db.php:tt_content.list_type1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addPlugin(Array('LLL:EXT:newloginbox/locallang_db.php:tt_content.list_type3', $_EXTKEY.'_pi3'),'list_type');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:newloginbox/flexform_ds.xml');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi3', 'FILE:EXT:newloginbox/flexform_ds_pi3.xml');

	// Adds wizard icon to the content element wizard.
if (TYPO3_MODE=='BE')	{
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_newloginbox_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_newloginbox_pi1_wizicon.php';
	require_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_newloginbox_feusers.php');
}
?>