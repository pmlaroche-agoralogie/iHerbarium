<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$tempColumns = array (
	'tx_facebook2t3_id' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:facebook2t3/locallang_db.xml:fe_users.tx_facebook2t3_id',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',	
			'eval' => 'trim',
		)
	),
	'tx_facebook2t3_first_name' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:facebook2t3/locallang_db.xml:fe_users.tx_facebook2t3_first_name',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',	
			'eval' => 'trim',
		)
	),
	'tx_facebook2t3_last_name' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:facebook2t3/locallang_db.xml:fe_users.tx_facebook2t3_last_name',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',	
			'eval' => 'trim',
		)
	),
	'tx_facebook2t3_link' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:facebook2t3/locallang_db.xml:fe_users.tx_facebook2t3_link',		
		'config' => array (
			'type'     => 'input',
			'size'     => '15',
			'max'      => '255',
			'checkbox' => '',
			'eval'     => 'trim',
			'wizards'  => array(
				'_PADDING' => 2,
				'link'     => array(
					'type'         => 'popup',
					'title'        => 'Link',
					'icon'         => 'link_popup.gif',
					'script'       => 'browse_links.php?mode=wizard',
					'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
				)
			)
		)
	),
	'tx_facebook2t3_gender' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:facebook2t3/locallang_db.xml:fe_users.tx_facebook2t3_gender',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',	
			'eval' => 'trim',
		)
	),
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_facebook2t3_id;;;;1-1-1, tx_facebook2t3_first_name, tx_facebook2t3_last_name, tx_facebook2t3_link, tx_facebook2t3_gender');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:facebook2t3/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY,'static/facebook_connect_to_typo3/', 'Facebook Connect to TYPO3');
?>