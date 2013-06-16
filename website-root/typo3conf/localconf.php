<?php
$TYPO3_CONF_VARS['FE']['lifetime'] = 1000000; // allow for "stay connected" option in felogin
$TYPO3_CONF_VARS['SYS']['useCachingFramework'] = '0';

$TYPO3_CONF_VARS['SYS']['sitename'] = 'herbarium';

	// Default password is "xxxxxx" :
$TYPO3_CONF_VARS['BE']['installToolPassword'] = '_SHELL_REPLACED_INSTALL_PWD';
$TYPO3_CONF_VARS['SYS']['setDBinit'] = 'SET NAMES utf8;';
$TYPO3_CONF_VARS['BE']['forceCharset'] = 'utf-8';

// For backend charset
 $TYPO3_CONF_VARS['BE']['forceCharset'] = 'utf-8';
 $TYPO3_CONF_VARS['SYS']['setDBinit'] = 'SET NAMES utf8;'; 
 
 // For GIFBUILDER support
 // Set it to 'iconv' or 'mbstring'
 $TYPO3_CONF_VARS['SYS']['t3lib_cs_convMethod'] = 'mbstring';
 // For 'iconv' support you need at least PHP 5.
 $TYPO3_CONF_VARS['SYS']['t3lib_cs_utils'] = 'mbstring';

$TYPO3_CONF_VARS['EXT']['extList'] = 'tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,css_styled_content,t3skin';

$typo_db_extTableDef_script = 'extTables.php';

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!

$typo_db_username = '_SHELL_REPLACED_USER_PROD';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_password = '_SHELL_REPLACED_PWD_PROD';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_host = 'localhost';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['encryptionKey'] = 'ac10e1b43229eb36502f7dd81fdc473b';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['compat_version'] = '4.3';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['SYS']['exceptionalErrors'] = E_ALL ^ E_NOTICE ^ E_WARNING ^ E_USER_ERROR ^ E_USER_NOTICE ^ E_USER_WARNING ^ E_DEPRECATED;
$TYPO3_CONF_VARS['SYS']['displayErrors'] = 1;
$typo_db = '_SHELL_REPLACED_DATABASE_PROD';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['installToolPassword'] = '7c841fca6f1b9fb0eb6260dc7e913ea6';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']["im"] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_combine_filename'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']["im_path"] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']["im_path_lzw"] = '';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 04-06-07 08:37:47
$TYPO3_CONF_VARS['EXT']['extList'] = 'css_styled_content,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,t3skin,newloginbox,cc_awstats,tt_address,rlmp_language_detection,indexed_search,macina_searchbox,dropdown_sitemap,api_macmade,bddb_flvvideogallery,rlmp_flashdetection,static_info_tables,sr_language_menu,tt_news,kickstarter,iherbarium_observations,about,cshmanual,feedit,opendocs,recycler,t3editor,reports,scheduler,iherbarium_roi,iherba_nommage,div2007,sr_feuser_register,iherbaqr,sociallogin2t3,info,perm,func,filelist,realurl,superuser,iherbarium_groups,iherba_subdomain,direct_mail,felogin,iherba_spacemanage';	// Modified or inserted by TYPO3 Extension Manager. Modified or inserted by TYPO3 Core Update Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['tt_address'] = 'a:2:{s:24:"disableCombinedNameField";s:1:"0";s:21:"backwardsCompatFormat";s:9:"%1$s %3$s";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['direct_mail'] = 'a:17:{s:12:"sendPerCycle";s:3:"150";s:13:"cron_language";s:2:"en";s:12:"useDeferMode";s:1:"0";s:14:"addRecipFields";s:0:"";s:19:"enablePlainTextNews";s:1:"1";s:10:"adminEmail";s:21:"contact@iherbarium.fr";s:7:"cronInt";s:1:"5";s:15:"notificationJob";s:1:"1";s:11:"SmtpEnabled";s:1:"0";s:8:"SmtpHost";s:9:"localhost";s:8:"SmtpPort";s:2:"25";s:8:"SmtpAuth";s:1:"0";s:8:"SmtpUser";s:0:"";s:12:"SmtpPassword";s:0:"";s:14:"SmtpPersistent";s:1:"0";s:12:"encodeHeader";s:1:"1";s:14:"UseHttpToFetch";s:1:"0";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['tt_news'] = 'a:22:{s:13:"useStoragePid";s:1:"1";s:13:"noTabDividers";s:1:"0";s:25:"l10n_mode_prefixLangTitle";s:1:"1";s:22:"l10n_mode_imageExclude";s:1:"1";s:20:"hideNewLocalizations";s:1:"0";s:13:"prependAtCopy";s:1:"1";s:5:"label";s:5:"title";s:9:"label_alt";s:0:"";s:10:"label_alt2";s:0:"";s:15:"label_alt_force";s:1:"0";s:11:"treeOrderBy";s:3:"uid";s:21:"categorySelectedWidth";s:1:"0";s:17:"categoryTreeWidth";s:1:"0";s:18:"categoryTreeHeigth";s:1:"5";s:17:"requireCategories";s:1:"0";s:18:"useInternalCaching";s:1:"1";s:11:"cachingMode";s:6:"normal";s:13:"cacheLifetime";s:1:"0";s:13:"cachingEngine";s:8:"internal";s:24:"writeCachingInfoToDevlog";s:10:"disabled|0";s:23:"writeParseTimesToDevlog";s:1:"0";s:18:"parsetimeThreshold";s:3:"0.1";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['indexed_search'] = 'a:17:{s:8:"pdftools";s:9:"/usr/bin/";s:8:"pdf_mode";s:2:"20";s:5:"unzip";s:9:"/usr/bin/";s:6:"catdoc";s:9:"/usr/bin/";s:6:"xlhtml";s:9:"/usr/bin/";s:7:"ppthtml";s:9:"/usr/bin/";s:5:"unrtf";s:9:"/usr/bin/";s:9:"debugMode";s:1:"0";s:18:"fullTextDataLength";s:1:"0";s:23:"disableFrontendIndexing";s:1:"0";s:6:"minAge";s:2:"24";s:6:"maxAge";s:1:"0";s:16:"maxExternalFiles";s:1:"5";s:26:"useCrawlerForExternalFiles";s:1:"0";s:11:"flagBitMask";s:3:"192";s:16:"ignoreExtensions";s:0:"";s:17:"indexExternalURLs";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['static_info_tables'] = 'a:2:{s:7:"charset";s:5:"utf-8";s:12:"usePatch1822";s:1:"0";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['rtehtmlarea'] = 'a:18:{s:21:"noSpellCheckLanguages";s:23:"ja,km,ko,lo,th,zh,b5,gb";s:15:"AspellDirectory";s:15:"/usr/bin/aspell";s:17:"defaultDictionary";s:2:"en";s:14:"dictionaryList";s:2:"en";s:20:"defaultConfiguration";s:105:"Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)";s:12:"enableImages";s:1:"1";s:20:"enableInlineElements";s:1:"0";s:24:"enableAccessibilityIcons";s:1:"0";s:16:"enableDAMBrowser";s:1:"0";s:18:"enableClickEnlarge";s:1:"0";s:22:"enableMozillaExtension";s:1:"0";s:14:"enableInOpera9";s:1:"0";s:16:"forceCommandMode";s:1:"0";s:15:"enableDebugMode";s:1:"0";s:23:"enableCompressedScripts";s:1:"1";s:20:"mozAllowClipboardURL";s:55:"http://typo3.org/fileadmin/allowclipboardhelper-0.6.xpi";s:18:"plainImageMaxWidth";s:1:"0";s:19:"plainImageMaxHeight";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['realurl'] = 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"0";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}';	// Modified or inserted by TYPO3 Extension Manager. 
// Updated by TYPO3 Extension Manager 21-04-10 16:02:02
// Updated by TYPO3 Install Tool 13-06-10 19:41:53
$TYPO3_CONF_VARS['EXT']['extList_FE'] = 'css_styled_content,install,rtehtmlarea,t3skin,newloginbox,cc_awstats,tt_address,rlmp_language_detection,indexed_search,macina_searchbox,dropdown_sitemap,api_macmade,bddb_flvvideogallery,rlmp_flashdetection,static_info_tables,sr_language_menu,tt_news,kickstarter,iherbarium_observations,feedit,iherbarium_roi,iherba_nommage,div2007,sr_feuser_register,iherbaqr,sociallogin2t3,realurl,superuser,iherbarium_groups,iherba_subdomain,direct_mail,felogin,iherba_spacemanage';	// Modified or inserted by TYPO3 Extension Manager. 
// Updated by TYPO3 Extension Manager 23-06-10 09:33:50
// Updated by TYPO3 Core Update Manager 07-07-10 08:23:06
$TYPO3_CONF_VARS['EXT']['extConf']['sr_feuser_register'] = 'a:6:{s:12:"uploadFolder";s:27:"uploads/tx_srfeuserregister";s:10:"imageTypes";s:30:"png, jpg, jpeg, gif, tif, tiff";s:12:"imageMaxSize";s:3:"500";s:12:"useFlexforms";s:1:"1";s:14:"useMd5Password";s:1:"0";s:12:"usePatch1822";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 20-12-10 17:00:02
// Updated by TYPO3 Extension Manager 12-05-11 10:12:30
$TYPO3_CONF_VARS['BE']['versionNumberInFilename'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['setDBinit'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['forceCharset'] = '';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 15-09-11 09:29:54
// Updated by TYPO3 Extension Manager 15-09-11 09:30:31
$TYPO3_CONF_VARS['INSTALL']['wizardDone']['tx_coreupdates_installsysexts'] = '1';	//  Modified or inserted by TYPO3 Upgrade Wizard.
// Updated by TYPO3 Upgrade Wizard 15-09-11 09:30:32
$TYPO3_CONF_VARS['BE']['disable_exec_function'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['gdlib_png'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 15-09-11 09:31:45
// Updated by TYPO3 Extension Manager 24-10-11 14:32:51

switch (t3lib_div::getIndpEnv('HTTP_HOST')) {
  case 'localhost':
  case 'www.iherbarium.net':
    $_GET['L'] = 1;
    break;
  default:
    $_GET['L'] = 0;
    break;
}

if(strpos(t3lib_div::getIndpEnv('HTTP_HOST'),"iherbarium.fr",0)>0)
  $_GET['L'] = 1;
if(strpos(t3lib_div::getIndpEnv('HTTP_HOST'),"iherbarium.com.br",0)>0)
  $_GET['L'] = 2;
if(strpos(t3lib_div::getIndpEnv('HTTP_HOST'),"iherbarium.de",0)>0)
  $_GET['L'] = 3;
if(strpos(t3lib_div::getIndpEnv('HTTP_HOST'),"iherbarium.it",0)>0)
  $_GET['L'] = 4;
if(strpos(t3lib_div::getIndpEnv('HTTP_HOST'),"iherbarium.es",0)>0)
  $_GET['L'] = 5;
/*$left = substr(t3lib_div::getIndpEnv('HTTP_HOST'),0,strpos(t3lib_div::getIndpEnv('HTTP_HOST'),".",0));
if(($left != "www")&&($left != "localname"))
	{
	$GLOBALS['TSFE']->tmpl->setup['config.']['baseURL']="$left.iherbarium.fr";
	$_SERVER['SERVER_NAME'] = str_replace($left,'localname',$_SERVER['SERVER_NAME']);
	$_SERVER['HTTP_HOST'] = str_replace($left,'localname',$_SERVER['HTTP_HOST']);
	$_SERVER['REQUEST_URI'] .= 'zone/'.$left;
	$_SERVER['REDIRECT_URL'] .= 'zone/'.$left;
	}
*/

require_once(PATH_tslib.'../../../../bibliotheque/common_functions.php');
require_once(PATH_tslib.'../../../../bibliotheque/init_fonctions_sousdomaine.php');

$left = substr(t3lib_div::getIndpEnv('HTTP_HOST'),0,strpos(t3lib_div::getIndpEnv('HTTP_HOST'),".",0));
if($left=="iherbarium")$left ="www";
if( ! exists_sousdomaine($left,$data) && ($left != "www")&& ($left != "wwwtest") && ($left != "test")){
  // if the subdomain doesn't exists
  $sitedebase  = "http://www".substr(t3lib_div::getIndpEnv('HTTP_HOST'),strpos(t3lib_div::getIndpEnv('HTTP_HOST'),".",0))."?id=noarea";
  header("Location: $sitedebase");
  die("");
  }
  else {
    set_sousdomaine($left,$data);
  }
  
test_limitation_parameters();
if(!is_sousdomaine_www())set_view_limitation();

// Updated by TYPO3 Extension Manager 14-05-12 18:21:46
?>