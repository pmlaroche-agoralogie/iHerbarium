<?php
	if (!defined ('TYPO3_MODE')) die ('Access denied.');

	$extConfig		= unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rlmp_flashdetection']);
	$uploadfolder	= $extConfig['useReference']==1?'':'uploads/tx_rlmpflashdetection';

	$TCA['tx_rlmpflashdetection_flashmovie'] = Array (
		'ctrl' => $TCA['tx_rlmpflashdetection_flashmovie']['ctrl'],
		'interface' => Array (
			'showRecordFieldList' => 'description,requiresflashversion,width,height,quality,displaymenu,flashloop,alternatepic,alternatelink,alternatetext,flashmovie,xmlfile,additionalparams'
		),
		'feInterface' => $TCA['tx_rlmpflashdetection_flashmovie']['feInterface'],
		'columns' => Array (
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.description',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'requiresflashversion' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.requiresflashversion',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'int',
				)
			),
			'width' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.width',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					/*'eval' => 'int',*/
				)
			),
			'height' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.height',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					/*'eval' => 'int',*/
				)
			),
			'quality' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.quality',
				'config' => Array (
					'type' => 'radio',
					'items' => Array (
						Array('LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.quality.I.0', '0'),
						Array('LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.quality.I.1', '1'),
					),
				)
			),
			'displaymenu' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.displaymenu',
				'config' => Array (
					'type' => 'check',
				)
			),
			'flashloop' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.flashloop',
				'config' => Array (
					'type' => 'check',
				)
			),
			'alternatepic' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.alternatepic',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
					'max_size' => 1024,
					'uploadfolder' => $uploadfolder,
					'show_thumbs' => 0,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'alternatelink' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.alternatelink',
				'config' => Array (
					'type' => 'input',
					'max' => '255',
					'eval' => 'trim',
					'wizards' => Array(
						'_PADDING' => 2,
						'link' => Array(
							'type' => 'popup',
							'title' => 'Link',
							'icon' => 'link_popup.gif',
							'script' => 'browse_links.php?mode=wizard',
							'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
						)
					)
				)
			),
			'alternatetext' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.alternatetext',
				'config' => Array (
					'type' => 'input',
					'max' => '255',
				)
			),
			'flashmovie' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.flashmovie',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'swf',
					'uploadfolder' => $uploadfolder,
					'show_thumbs' => 0,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'xmlfile' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.xmlfile',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'xml',
					'max_size' => 1024,
					'uploadfolder' => $uploadfolder,
					'show_thumbs' => 0,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'additionalparams' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:rlmp_flashdetection/locallang_db.php:tx_rlmpflashdetection_flashmovie.additionalparams',
				'config' => Array (
					'type' => 'text',
					'wrap' => 'OFF',
					'cols' => '30',
					'rows' => '6',
				)
			),
		),
		'types' => Array (
			'0' => Array('showitem' => 'flashmovie;;;;1-1-1, description;;;;2-2-2, requiresflashversion, width, height, quality, displaymenu, flashloop, additionalparams;;;;3-3-3, xmlfile, alternatepic;;;;4-4-4, alternatelink, alternatetext')
		),
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>