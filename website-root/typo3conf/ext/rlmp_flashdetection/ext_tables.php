<?php
	if (!defined ('TYPO3_MODE')) die ('Access denied.');

	$tempColumns = Array (
		'tx_rlmpflashdetection_flashmovie' => Array (
		'exclude' => 1,
			'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tt_content.tx_rlmpflashdetection_flashmovie',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_rlmpflashdetection_flashmovie',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	);

	t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns, 1);
	$TCA['tx_rlmpflashdetection_flashmovie'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie',
			'label' => 'description',
			'tstamp' => 'tstamp',
			'crdate' => 'crdate',
			'cruser_id' => 'cruser_id',
			'default_sortby' => 'ORDER BY description',
			'thumbnail' => 'flashmovie',
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_rlmpflashdetection_flashmovie.gif",
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'description, requiresflashversion, width, height, quality, displaymenu, flashloop, alternatepic, alternatelink, alternatetext, flashmovie, xmlfile, additionalparams',
		)
	);

	t3lib_div::loadTCA('tt_content');
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'pages,layout,select_key';
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'tx_rlmpflashdetection_flashmovie;;;;1-1-1';

	t3lib_extMgm::allowTableOnStandardPages('tx_rlmpflashdetection_flashmovie');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:rlmp_flashdetection/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'), 'list_type');

	if (TYPO3_MODE == 'BE') $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_rlmpflashdetection_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_rlmpflashdetection_pi1_wizicon.php';
?>