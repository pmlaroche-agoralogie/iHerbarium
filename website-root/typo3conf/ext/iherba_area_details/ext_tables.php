<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:iherba_area_details/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:iherba_area_details/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:iherba_area_details/locallang_db.xml:tt_content.list_type_pi3',
	$_EXTKEY . '_pi3',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi4']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:iherba_area_details/locallang_db.xml:tt_content.list_type_pi4',
	$_EXTKEY . '_pi4',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi5']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:iherba_area_details/locallang_db.xml:tt_content.list_type_pi5',
	$_EXTKEY . '_pi5',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi6']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:iherba_area_details/locallang_db.xml:tt_content.list_type_pi6',
	$_EXTKEY . '_pi6',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');
?>