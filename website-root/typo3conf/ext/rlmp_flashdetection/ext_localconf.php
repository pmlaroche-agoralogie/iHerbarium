<?php
	if (!defined ('TYPO3_MODE')) die ('Access denied.');

	t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_rlmpflashdetection_flashmovie=1');

	$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_rlmpflashdetection_flashmovie'][0] = array (
		'fList' => 'description,width,height,flashmovie',
		'icon' => TRUE
	);

	## Extending TypoScript from static template uid=43 to set up userdefined tag:
	t3lib_extMgm::addTypoScript($_EXTKEY, 'editorcfg', "tt_content.CSS_editor.ch.tx_rlmpflashdetection_pi1 = < plugin.tx_rlmpflashdetection_pi1.CSS_editor", 43);
	t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_rlmpflashdetection_pi1.php', "_pi1", 'list_type', 1);
?>