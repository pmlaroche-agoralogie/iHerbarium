<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2003-2004 Kasper Skårhøj (kasper@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Class/Function which manipulates the item-array for the FEusers listing
 * 
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 */

 
/**
 * SELECT box processing
 * 
 * @author	Kasper Skårhøj (kasper@typo3.com)
 * @package TYPO3
 * @subpackage tx_newloginbox
 */
class tx_newloginbox_feusers {

	/**
	 * Adding fe_users field list to selector box array
	 * 
	 * @param	array		Parameters, changing "items". Passed by reference.
	 * @param	object		Parent object
	 * @return	void		
	 */
	function main(&$params,&$pObj)	{
		global $TCA;
		
		t3lib_div::loadTCA('fe_users');

		$params['items'] = array();
		if (is_array($TCA['fe_users']['columns']))	{
			foreach($TCA['fe_users']['columns'] as $key => $config)	{
				if ($config['label'] && !t3lib_div::inList('password',$key))	{
					$label = t3lib_div::fixed_lgd(ereg_replace(':$','',$GLOBALS['LANG']->sL($config['label'])),30).' ('.$key.')';
					$params['items'][]=Array($label, $key);
				}
			}
		}
	}

	/**
	 * Adding fe_users field list to selector box array - details view
	 * 
	 * @param	array		Parameters, changing "items". Passed by reference.
	 * @param	object		Parent object
	 * @return	void		
	 */
	function mainDetails(&$params,&$pObj)	{
		global $TCA;
		
		$this->main($params,$pObj);

		if (is_array($params['items']))	{
			array_unshift($params['items'],array('-- divider --','---'));
		}
	}
}


// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/newloginbox/class.tx_newloginbox_feusers.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/newloginbox/class.tx_newloginbox_feusers.php']);
}

?>