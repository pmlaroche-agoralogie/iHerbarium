<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$tempColumns = Array (
	'tx_srlanguagemenu_languages' => Array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:sr_language_menu/locallang_db.xml:tt_content.tx_srlanguagemenu_languages',		
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => 'sys_language',
			'size' => '5',
			'maxitems' => 50,
			'minitems' => 1,
			'show_thumbs' => 1,
		)
	),
	'tx_srlanguagemenu_type' => Array (        
		'exclude' => 0,        
		'label' => 'LLL:EXT:sr_language_menu/locallang_db.xml:tt_content.tx_srlanguagemenu_type',        
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array('LLL:EXT:sr_language_menu/locallang_db.xml:tt_content.tx_srlanguagemenu_type.I.0', '0'),
				Array('LLL:EXT:sr_language_menu/locallang_db.xml:tt_content.tx_srlanguagemenu_type.I.1', '1'),
				Array('LLL:EXT:sr_language_menu/locallang_db.xml:tt_content.tx_srlanguagemenu_type.I.2', '2'),
			),
		),
	),
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

$TCA['tt_content']['types'][$_EXTKEY.'_pi1']['showitem'] = 'CType;;4;button;1-1-1, header;;3;;2-2-2, tx_srlanguagemenu_type;;;;3-3-3,tx_srlanguagemenu_languages';
$TCA['tt_content']['ctrl']['typeicons'][$_EXTKEY.'_pi1'] = t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon.gif';

t3lib_extMgm::addPlugin(Array('LLL:EXT:sr_language_menu/locallang_db.xml:tt_content.CType', $_EXTKEY.'_pi1'),'CType');

if (TYPO3_MODE=='BE')	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_srlanguagemenu_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_srlanguagemenu_pi1_wizicon.php';

?>