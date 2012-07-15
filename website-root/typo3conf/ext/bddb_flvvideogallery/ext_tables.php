<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Include the class to handle JPG files
	if (TYPO3_MODE=='BE') {
		include_once(t3lib_extMgm::extPath('bddb_flvvideogallery') . 'class.tx_flvvideogalleryhandleflvfiles.php');
	}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(Array('LLL:EXT:bddb_flvvideogallery/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Simple FLV Player");


//load Flexform
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1','FILE:EXT:'.$_EXTKEY.'/flexform.xml');

if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_bddbflvvideogallery_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_bddbflvvideogallery_pi1_wizicon.php';
?>
