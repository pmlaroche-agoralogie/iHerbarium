<?php
# TYPO3 CVS ID: $Id: ext_localconf.php 944 2003-12-29 16:50:17Z typo3 $
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_newloginbox_pi1.php','_pi1','list_type',0);

  // Extending TypoScript from static template uid=43
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
    tt_content.CSS_editor.ch.tx_newloginbox_pi3 = < plugin.tx_newloginbox_pi3.CSS_editor
',43);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi3/class.tx_newloginbox_pi3.php','_pi3','list_type',0);


t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
    tt_content.shortcut.20.0.conf.fe_users = < plugin.'.t3lib_extMgm::getCN($_EXTKEY).'_pi3
    tt_content.shortcut.20.0.conf.fe_users.CMD = singleView
',43);

?>