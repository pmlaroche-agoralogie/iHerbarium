<?php

$tx_realurl_config = array(
    'init' => array(
        'enableCHashCache' => false,
        'appendMissingSlash' => 'ifNotFile',
        'enableUrlDecodeCache' => true,
        'enableUrlDecodeCache' => true,
        'emptyUrlReturnValue' => '/',
    ),
    'preVars' => array(
    ),
    'pagePath' => array(
        'type' => 'user',
        'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
        'spaceCharacter' => '-',
        'languageGetVar' => 'L',
        'expireDays' => 0,
        'rootpage_id' => 3,
        'disablePathCache' => 1,
        'firstHitPathCache' => 0,
        'autoUpdatePathCache' => 1,
        'dontResolveShortcuts' => 0,
    ),
    // Fixed post variables
    'fixedPostVars' => array(),
    // Post variables
    'postVarSets' => array
        (
        '_DEFAULT' => array
            (
            /*             * *********************************************************
             * tt_news
             * ******************************************************** */
            'archive' => array
                (
                array
                    (
                    'GETvar' => 'tx_ttnews[year]'
                ),
                array
                    (
                    'GETvar' => 'tx_ttnews[month]',
                    'valueMap' => array                        (
                        'january' => '01',
                        'february' => '02',
                        'march' => '03',
                        'april' => '04',
                        'may' => '05',
                        'june' => '06',
                        'july' => '07',
                        'august' => '08',
                        'september' => '09',
                        'october' => '10',
                        'november' => '11',
                        'december' => '12'
                    )
                )
            ),
            'browse' => array
                (
                array
                    (
                    'GETvar' => 'tx_ttnews[pointer]'
                )
            ),
            'select_category' => array
                (
                array
                    (
                    'GETvar' => 'tx_ttnews[cat]'
                )
            ),
            'article' => array
                (
                array
                    (
                    'GETvar' => 'tx_ttnews[tt_news]',
                    'lookUpTable' => array
                        (
                        'table' => 'tt_news',
                        'id_field' => 'uid',
                        'alias_field' => 'title',
                        'addWhereClause' => ' AND NOT deleted',
                        'useUniqueCache' => 1,
                        'useUniqueCache_conf' => array
                            (
                            'strtolower' => 1,
                            'spaceCharacter' => '-'
                        )
                    )
                ),
                array(
                    'GETvar' => 'tx_ttnews[swords]'
                )
            ),
            /*             * *********************************************************
             * cal
             * ******************************************************** */
            'cal' => array(
                array(
                    'GETvar' => 'tx_cal_controller[view]'
                ),
                array(
                    'GETvar' => 'tx_cal_controller[type]'
                ),
                array(
                    'GETvar' => 'tx_cal_controller[uid]',
                    'lookUpTable' => array(
                        'table' => 'tx_cal_event',
                        'id_field' => 'uid',
                        'alias_field' => 'title',
                        'addWhereClause' => ' AND deleted !=1',
                        'useUniqueCache' => 1,
                        'useUniqueCache_conf' => array(
                            'strtolower' => 1,
                            'spaceCharacter' => '-',
                        ),
                    ),
                ),
                array(
                    'GETvar' => 'tx_cal_controller[lastview]'
                ),
                array(
                    'GETvar' => 'tx_cal_controller[year]'
                ),
                array(
                    'GETvar' => 'tx_cal_controller[month]'
                ),
                array(
                    'GETvar' => 'tx_cal_controller[day]'
                ),
                array(
                    'GETvar' => 'tx_cal_controller[category]',
                    'lookUpTable' => array(
                        'table' => 'tx_cal_category',
                        'id_field' => 'uid',
                        'alias_field' => 'title',
                        'addWhereClause' => ' AND deleted !=1',
                        'useUniqueCache' => 1,
                        'useUniqueCache_conf' => array(
                            'strtolower' => 1,
                            'spaceCharacter' => '-',
                        ),
                    ),
                ),
            ) // cal [end]
        // defin here more extension
        )
    ),
    // File names
    'fileName' => array(
        'index' => array(
            '_DEFAULT' => array(
                'keyValues' => array(),
            ),
            'index.html' => array(
                'keyValues' => array(),
            ),
        ),
    ),
);

$TYPO3_CONF_VARS['EXTCONF']['realurl'] = array(
    'www.iherbarium.fr' => $tx_realurl_config,
 'localname.iherbarium.fr' => $tx_realurl_config,
$_SERVER['HTTP_HOST'] => $tx_realurl_config,
    'www.iherbarium.org' => $tx_realurl_config
);

// Plugin
$TYPO3_CONF_VARS['EXTCONF']['realurl']['localname.iherbarium.fr']['postVarSets']['_DEFAULT']['zone'] = array(array('GETvar' => 'tx_iherbariumobservations_pi1[zone]'));
// Plugin Observation
$TYPO3_CONF_VARS['EXTCONF']['realurl'][$_SERVER['HTTP_HOST']]['postVarSets']['_DEFAULT']['data'] = array(array('GETvar' => 'tx_iherbariumobservations_pi3[detail]'));
//$TYPO3_CONF_VARS['EXTCONF']['realurl']['www.iherbarium.fr']['postVarSets']['_DEFAULT']['fiche'] = array(array('GETvar' => 'tx_iherbariumobservations_pi3[detail]'));

unset($tx_realurl_config);

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DOMAINS'] = array(
    'encode' => array(
        array(
            'GETvar' => 'L',
            'value' => '0',
            'useConfiguration' => 'www.iherbarium.org',
            'urlPrepend' => ''
        ),
        
        array(
            'GETvar' => 'L',
            'value' => $_GET['L'] ,
            'useConfiguration' => $_SERVER['HTTP_HOST'],
            'urlPrepend' => ''
        ),
    ),
    'decode' => array(
        'www.iherbarium.org' => array(
            'GETvars' => array(
                'L' => '0',
            ),
            'useConfiguration' => 'www.iherbarium.org'
        ),
        $_SERVER['HTTP_HOST'] => array(
            'GETvars' => array(
                'L' => $_GET['L'] ,
            ),
            'useConfiguration' =>  $_SERVER['HTTP_HOST']
        )
    )
);
?>
