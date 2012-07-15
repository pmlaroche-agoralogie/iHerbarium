<?php

########################################################################
# Extension Manager/Repository config file for ext: "sr_language_menu"
#
# Auto generated 12-03-2009 12:08
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Language Selection',
	'description' => 'A plugin to display a list of languages to select from. Clicking on a language links to the corresponding version of the page.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.5.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Stanislas Rolland',
	'author_email' => 'typo3(arobas)sjbr.ca',
	'author_company' => 'SJBR',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '4.1.0-0.0.0',
			'typo3' => '4.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:135:{s:9:"ChangeLog";s:4:"e22a";s:12:"ext_icon.gif";s:4:"ba72";s:17:"ext_localconf.php";s:4:"20ea";s:14:"ext_tables.php";s:4:"6016";s:14:"ext_tables.sql";s:4:"7ab6";s:28:"ext_typoscript_constants.txt";s:4:"97b0";s:24:"ext_typoscript_setup.txt";s:4:"eae3";s:13:"locallang.xml";s:4:"16f9";s:16:"locallang_db.xml";s:4:"ffab";s:14:"doc/manual.sxw";s:4:"abc3";s:12:"flags/ar.gif";s:4:"6c41";s:12:"flags/ar.png";s:4:"4353";s:14:"flags/ar_d.gif";s:4:"e2e8";s:12:"flags/bg.gif";s:4:"c92b";s:12:"flags/bg.png";s:4:"f72a";s:14:"flags/bg_d.gif";s:4:"19f6";s:12:"flags/bs.gif";s:4:"ec2d";s:12:"flags/bs.png";s:4:"1b95";s:14:"flags/bs_d.gif";s:4:"9ff9";s:12:"flags/ca.gif";s:4:"4289";s:12:"flags/ca.png";s:4:"aacb";s:14:"flags/ca_d.gif";s:4:"3df6";s:12:"flags/cs.gif";s:4:"65bb";s:12:"flags/cs.png";s:4:"2ea1";s:14:"flags/cs_d.gif";s:4:"1d21";s:12:"flags/da.gif";s:4:"9e8d";s:12:"flags/da.png";s:4:"7392";s:14:"flags/da_d.gif";s:4:"e122";s:12:"flags/de.gif";s:4:"20fe";s:12:"flags/de.png";s:4:"1f9a";s:14:"flags/de_d.gif";s:4:"ef9e";s:12:"flags/el.gif";s:4:"d24c";s:12:"flags/el.png";s:4:"da37";s:14:"flags/el_d.gif";s:4:"df40";s:12:"flags/en.gif";s:4:"566c";s:12:"flags/en.png";s:4:"8435";s:14:"flags/en_d.gif";s:4:"b292";s:12:"flags/es.gif";s:4:"6682";s:12:"flags/es.png";s:4:"039c";s:15:"flags/es_MX.gif";s:4:"8126";s:15:"flags/es_MX.png";s:4:"14ff";s:17:"flags/es_MX_d.gif";s:4:"5fab";s:14:"flags/es_d.gif";s:4:"2b10";s:12:"flags/et.gif";s:4:"e00b";s:12:"flags/et.png";s:4:"fdcb";s:14:"flags/et_d.gif";s:4:"07f6";s:12:"flags/fi.gif";s:4:"9429";s:12:"flags/fi.png";s:4:"c3f8";s:14:"flags/fi_d.gif";s:4:"9437";s:12:"flags/fr.gif";s:4:"ba69";s:12:"flags/fr.png";s:4:"f8a7";s:15:"flags/fr_CA.gif";s:4:"4095";s:15:"flags/fr_CA.png";s:4:"36b0";s:17:"flags/fr_CA_d.gif";s:4:"a192";s:14:"flags/fr_d.gif";s:4:"4fc7";s:12:"flags/he.gif";s:4:"bf84";s:12:"flags/he.png";s:4:"09e7";s:14:"flags/he_d.gif";s:4:"56d6";s:12:"flags/hr.gif";s:4:"d25e";s:12:"flags/hr.png";s:4:"2931";s:14:"flags/hr_d.gif";s:4:"f49d";s:12:"flags/hu.gif";s:4:"4f53";s:12:"flags/hu.png";s:4:"49a1";s:14:"flags/hu_d.gif";s:4:"d6cf";s:12:"flags/is.gif";s:4:"5eef";s:12:"flags/is.png";s:4:"9fb9";s:14:"flags/is_d.gif";s:4:"fbb4";s:12:"flags/it.gif";s:4:"14fe";s:12:"flags/it.png";s:4:"5f88";s:14:"flags/it_d.gif";s:4:"a16b";s:12:"flags/ja.gif";s:4:"d56a";s:12:"flags/ja.png";s:4:"25d5";s:14:"flags/ja_d.gif";s:4:"c837";s:12:"flags/kl.gif";s:4:"95b7";s:12:"flags/kl.png";s:4:"550a";s:14:"flags/kl_d.gif";s:4:"3f50";s:12:"flags/ko.gif";s:4:"7a30";s:12:"flags/ko.png";s:4:"58e9";s:14:"flags/ko_d.gif";s:4:"7d45";s:12:"flags/lt.gif";s:4:"1fc0";s:12:"flags/lt.png";s:4:"a586";s:14:"flags/lt_d.gif";s:4:"a3ab";s:12:"flags/lv.gif";s:4:"abbe";s:12:"flags/lv.png";s:4:"e62a";s:14:"flags/lv_d.gif";s:4:"6c14";s:12:"flags/nl.gif";s:4:"1804";s:12:"flags/nl.png";s:4:"b63c";s:14:"flags/nl_d.gif";s:4:"032a";s:12:"flags/no.gif";s:4:"62a1";s:12:"flags/no.png";s:4:"dee5";s:14:"flags/no_d.gif";s:4:"e87a";s:12:"flags/pl.gif";s:4:"ac93";s:12:"flags/pl.png";s:4:"2f00";s:14:"flags/pl_d.gif";s:4:"d567";s:12:"flags/pt.gif";s:4:"d53f";s:12:"flags/pt.png";s:4:"dbf5";s:15:"flags/pt_BR.gif";s:4:"73ee";s:15:"flags/pt_BR.png";s:4:"3274";s:17:"flags/pt_BR_d.gif";s:4:"e324";s:14:"flags/pt_d.gif";s:4:"e568";s:12:"flags/ro.gif";s:4:"bbbf";s:12:"flags/ro.png";s:4:"cb1f";s:14:"flags/ro_d.gif";s:4:"43dc";s:12:"flags/ru.gif";s:4:"26bd";s:12:"flags/ru.png";s:4:"7e6a";s:14:"flags/ru_d.gif";s:4:"a919";s:12:"flags/sk.gif";s:4:"a78e";s:12:"flags/sk.png";s:4:"507f";s:14:"flags/sk_d.gif";s:4:"b423";s:12:"flags/sl.gif";s:4:"410d";s:12:"flags/sl.png";s:4:"7626";s:14:"flags/sl_d.gif";s:4:"7dee";s:12:"flags/sv.gif";s:4:"d7b8";s:12:"flags/sv.png";s:4:"a26b";s:14:"flags/sv_d.gif";s:4:"7603";s:12:"flags/th.gif";s:4:"3c7b";s:12:"flags/th.png";s:4:"9546";s:14:"flags/th_d.gif";s:4:"4b65";s:12:"flags/tr.gif";s:4:"726e";s:12:"flags/tr.png";s:4:"0400";s:14:"flags/tr_d.gif";s:4:"0604";s:12:"flags/uk.gif";s:4:"254e";s:12:"flags/uk.png";s:4:"6f04";s:14:"flags/uk_d.gif";s:4:"0302";s:12:"flags/vi.gif";s:4:"6e7d";s:12:"flags/vi.png";s:4:"05db";s:14:"flags/vi_d.gif";s:4:"c948";s:12:"flags/zh.gif";s:4:"ac69";s:12:"flags/zh.png";s:4:"d1e0";s:14:"flags/zh_d.gif";s:4:"885e";s:14:"pi1/ce_wiz.gif";s:4:"ba72";s:35:"pi1/class.tx_srlanguagemenu_pi1.php";s:4:"9160";s:43:"pi1/class.tx_srlanguagemenu_pi1_wizicon.php";s:4:"7d4e";s:17:"pi1/locallang.xml";s:4:"3ad1";s:39:"pi1/tx_srlanguagemenu_pi1_template.tmpl";s:4:"f377";}',
);

?>