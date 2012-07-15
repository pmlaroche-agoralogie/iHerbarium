<?php
	/***************************************************************
	 * Copyright notice
	 * 
	 * (c) 2005 macmade.net
	 * All rights reserved
	 * 
	 * This script is part of the TYPO3 project. The TYPO3 project is 
	 * free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 * 
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html.
	 * 
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * This copyright notice MUST APPEAR in all copies of the script!
	***************************************************************/
	
	/** 
	 * Class/Function which manipulates the item-array for field dir_pictures
	 * of table tx_slideshow_slidwshows.
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      59:		function main(&$params,&$pObj)
	 * 
	 *				TOTAL FUNCTIONS: 1
	 */
	
    
	
	class tx_flvvideogalleryhandleFLVFiles {
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Base functions.
		 ***************************************************************/
		
		/**
		 * Adds items to the flv Playlist selector.
		 * 
		 * This function reads all the CSS file in the defined stylesheet
		 * directory, and adds the references to the selector.
		 * 
		 * @param		&$params			The parameters of the form
		 * @param		&$pObj				Reference to the parent object
		 * @return		Nothing
		 */
		function main(&$params,&$pObj) {
			
			 //This is the flexform XML Structure to parse
       //<field index="dir_path">
       //   <value index="vDEF">/uploads/tx_bddbflvvideogallery</value>
       //</field>
	
      //read dir_path value out of flexform xml
      $dir=$params['row']['pi_flexform'];
      
      $dir_path=t3lib_div::xml2array($dir);
      
      if(!is_array($dir_path)) {
      	return '';
      }

       $dir_path=$dir_path['data']['config']['lDEF']['dir_path']['vDEF'];
      
			// Get storage directory
			$readPath = t3lib_div::getFileAbsFileName($dir_path);
						
			if (@is_dir($readPath)) {
				
				// Gets all CSS files
				$flvFiles = t3lib_div::getFilesInDir($readPath,'flv,FLV,mp3,MP3,swf,SWF,mp4,Mp4,MP4',1,1);
				
				foreach($flvFiles as $flv) {
					
					// Reset
					$selectorBoxItem_title = '';
					
					// Adds items
					$selectorBoxItem_title = basename($flv);
					$params['items'][] = array(
						$selectorBoxItem_title,
						$flv
					);
				}
			}
		}
	}
	
	/**
	 * XClass inclusion.
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bddb_flvvideogallery/class.tx_flvvideogalleryhandleflvfiles.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bddb_flvvideogallery/class.tx_flvvideogalleryhandleflvfiles.php']);
	}

?>
