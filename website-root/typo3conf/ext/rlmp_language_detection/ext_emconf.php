<?php

########################################################################
# Extension Manager/Repository config file for ext: "rlmp_language_detection"
#
# Auto generated 10-10-2007 17:43
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Language Detection',
	'description' => 'This plugin detects the visitor\'s preferred language and sets the local configuration for TYPO3\'s language engine accordingly. Both, one-tree and multiple tree concepts, are supported.',
	'category' => 'misc',
	'shy' => 0,
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Robert Lemke',
	'author_email' => 'rl@robertlemke.de',
	'author_company' => 'robert lemke medienprojekte',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.2.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '3.5.0-0.0.0',
			'php' => '3.0.0-0.0.0',
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:6:{s:12:"ext_icon.gif";s:4:"ba72";s:17:"ext_localconf.php";s:4:"3a3e";s:24:"ext_typoscript_setup.txt";s:4:"fdda";s:18:"tx_langsession.php";s:4:"cf48";s:14:"doc/manual.sxw";s:4:"75b6";s:42:"pi1/class.tx_rlmplanguagedetection_pi1.php";s:4:"0a4a";}',
	'suggests' => array(
	),
);

?>