<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Steffen Kamper <steffen@dislabs.de>
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
* Plugin 'Simple FLV Player' for the 'bddb_flvvideogallery' extension.
* Author of the Flash Player : Stephan Funk <s.funke@funkeundfunke.de>
* look here for his flashwork: http://www.funkeundfunke.de
*
* @author Steffen Kamper <steffen@dislabs.de>
*
* * Modifications and new features:
* @author Sebastian Fahrenkrog <sfahrenkrog@binaerdesign>
*/


require_once ( PATH_tslib.'class.tslib_pibase.php' );
require_once ( PATH_t3lib.'class.t3lib_loadmodules.php' );

class tx_bddbflvvideogallery_pi1 extends tslib_pibase
{
    
    var $prefixId = 'tx_bddbflvvideogallery_pi1';

    // Same as class name
    var $scriptRelPath = 'pi1/class.tx_bddbflvvideogallery_pi1.php';

    // Path to this script relative to the extension dir.
    var $extKey = 'bddb_flvvideogallery';

    // The extension key.
    var $pi_checkCHash = TRUE;
    
    var $swfFile;
    var $autoStart;
    var $videoFile;
    var $width;
    var $height;
    var $vID;
    var $divID;
    var $vIndex;
    var $xajaxLoaded;

    function init ( $conf )
    {
        
        if ( !$this -> cObj )
        {
            $this -> cObj = t3lib_div :: makeInstance ( 'tslib_cObj' );
        }

        //-----------------------------------------------------------------
        // Initialize all class and global variables
        //-----------------------------------------------------------------
        $this -> conf = $conf;
        $this -> pi_setPiVarDefaults ( );
        $this -> pi_initPIflexForm ( );
        $this -> pi_loadLL ( );
        
        $GLOBALS["TSFE"] -> set_no_cache ( );

        // set this for debugging, but turn it off when go to beta tes
        //Get Flexform Values
        $piFlexForm = $this -> cObj -> data['pi_flexform'];
        

        if ( isset ( $piFlexForm['data'] ) )
        {
            foreach ( $piFlexForm['data'] as $sheet => $data )
            {
                foreach ( $data as $lang => $value )
                {
                    foreach ( $value as $key => $val )
                    {
                        $ffValue = $this -> pi_getFFvalue ( $piFlexForm, $key, $sheet );
                        $this -> conf[$key] = ($ffValue) ? $ffValue : $this -> conf[$key];
                    }
                }
            }
        }
                

        // Traverse the entire array based on the language...
        // and assign each configuration option to $this->lConf array...
        

        $this -> conf['video-thumb']    = $this -> conf['video-thumb'] ? explode ( ',', $this -> conf['video-thumb'] ) : '';
        $this -> conf['video']          = $this -> conf['video'] ? explode ( ',', $this -> conf['video'] ) : '';
        $this -> conf['videotitle']     = $this -> conf['videotitle'] ? explode ( "\n", $this -> conf['videotitle'] ) : '';
        $this -> conf['videocaption']   = $this -> conf['videocaption'] ? explode ( "\n", $this -> conf['videocaption'] ) : '';
        $this -> conf['video-subtitle'] = $this -> conf['video-subtitle'] ? explode ( ',', $this -> conf['video-subtitle'] ) : '';

        //Ini Values ----------------------------------------------------------------------
        $this -> conf['autostart'] = $this -> conf['autostart'] ? 'true' : 'false';
        if ( intval ( $this -> conf['width'] ) == 0 )
        {
            $this -> conf['width'] = 520;
        }
        if ( intval ( $this -> conf['height'] ) == 0 )
        {
            $this -> conf['height'] = 520;
            
        }
        if ( intval ( $this -> conf['maxcolumn'] ) == 0 )
        {
            $this -> conf['maxcolumn'] = 3;
        }
        if ( intval ( $this -> conf['maxrows'] ) == 0 )
        {
            $this -> conf['maxrows'] = 4;
        }
        
        $this -> conf['FlashVersion'] = $conf['FlashVersion'] ? $conf['FlashVersion'] : 8;
        
        $this -> vID       = time ( );
        $this -> vIndex    = intval ( $this -> piVars['video'] );
        $this -> swfFile   = t3lib_extMgm :: siteRelPath ( $this -> extKey ).'swf/flvplayer.swf';
        $this -> autoStart = $this -> conf['autostart'];

        //show xml playlist
        if ( $this -> conf['showxmlplaylist'] )
        {
            $this -> videoFile = $this -> pi_getPageLink ( $GLOBALS['TSFE'] -> id, '', array ( 'type' => '123' ) );
        } else
        {
            $this -> videoFile = $this -> conf['video'][$this -> vIndex];
        }
        
        $this -> width  = $this -> conf['width'];
        $this -> height = $this -> conf['height'];
        $this -> divID  = $this -> prefixId.'-111';
        
        $this -> conf['ExtUploadPath'] = substr ( $this -> prefixId, 0, strlen ( $this -> prefixId ) - 4 );

        //Use xajax for the pagebrowser
        $this -> xajaxLoaded = intval ( $this -> conf['noAjax'] ) == 1 ? false : t3lib_extMgm :: isLoaded ( 'xajax' );
        
      //  t3lib_div::debug( $this -> xajaxLoaded);

        // Get the hole template
        $this -> conf['templateCode'] = $this -> cObj -> fileResource ( $conf['templateFile'] );
        $this -> conf['template'] = $this -> cObj -> getSubpart ( $this -> conf['templateCode'], '###FLVPLAYER###' );
        

    }

    /**
    * The main method of the PlugIn
    *
    * @param string  $content: The PlugIn content
    * @param array  $conf: The PlugIn configuration
    * @return The  content that is displayed on the website
    */

    function main ( $content, $conf )
    {

        //Ini Extension
        $this -> init ( $conf );

        //render xml playlist
        if ( t3lib_div :: _GP ( 'type' ) == '123' )
        {
            return $this -> xmlPlaylist ( );
        }

         //render ajax views
        if ( $this -> xajaxLoaded )
        {
            $this -> processAjaxRequest ( );
        }
 
         //Get Playlist or hide Playlist
        if ( $this -> conf['showplaylist'] )
        {
            $playlist = '<div id="playlist">'.$this -> videoMenu ( ).'</div>';
            
            if ( $this -> conf['shownoplayer'] )
            {
              return $playlist;
            }
            
        } else
        {
            $playlist = '';
        }
 
        //Get Flash Player
        $player = $this -> videoObject ( );

        //Get Description for Videos
        $caption = $this -> videoCaption ( );

        //Include Ajax Loading Message
        //load only if needed!
        if($this -> xajaxLoaded)
        {
          $ajaxloading = $this -> incLoadMessage ( $this -> conf['AjaxLoadingMsg'] );        
        }

        
        $marker['###LOADING###'] = $ajaxloading;

        //replace all Markers
        $marker['###PLAYLIST###']    = $playlist;
        $marker['###XMLPLAYLIST###'] = '';
        $marker['###PLAYER###']      = $player;
        $marker['###DESC###']        = $caption;
        


        $content = $this -> cObj -> substituteMarkerArrayCached ( $this -> conf['template'], array ( ), $marker );
        

        $content = $this -> cObj -> stdWrap ( $content, $this -> conf['plugin.'] );
        $content = $this -> pi_wrapInBaseClass ( $content );
        
        return $content;
    }

    /**
    * Function to insert Javascript at Ext. Runtime
    *
    * @param string  $script Input the Script Name to insert JS
    * @return
    */

    function incJsFile ( $script )
    {
        
        $js .= '<script src="'.t3lib_extMgm :: siteRelPath ( $this -> extKey ).'js/'.$script.'.js" type="text/javascript"><!-- //--></script>';
        $GLOBALS['TSFE'] -> additionalHeaderData[$this -> extKey.$script] = $js;
    }

    /**
    * Function to insert Javascript at Ext. Runtime
    *
    * @param string  $script Input the Script Name to insert JS
    * @return
    */

    function incJsSnippet ( $script )
    {

        $GLOBALS["TSFE"] -> setJS ( "snippet", $script );
    }

    /**
    * Return the Message to show if an Ajax Request were made. Include Javascript
    *
    * @return none
    */

    function incLoadMessage ( $message )
    {
        
        $ajaxmsg = '<div id="loadingMessage" style="display: none;">'.$message.'</div>';
        
        $ajaxjs = 'xajax.loadingFunction = 
                function(){xajax.$(\'loadingMessage\').style.display=\'block\';};
            function hideLoadingMessage()
            {
                xajax.$(\'loadingMessage\').style.display = \'none\';
            }
            xajax.doneLoadingFunction = hideLoadingMessage;';

        //Include JS in the page header
        $this -> incJsSnippet ( $ajaxjs );
        
        return $ajaxmsg;
    }

    /**
    * Process the Xajax Request if there are any 
    *
    * @return none
    */

    function processAjaxRequest ( )
    {

        # make extension non-cached

        $this -> pi_USER_INT_obj = 1;
        
        require_once ( t3lib_extMgm :: extPath ( 'xajax' ).'class.tx_xajax.php' );
        $this -> xajax = t3lib_div :: makeInstance ( 'tx_xajax' );

        // Decode form vars from utf8 ???
        $this -> xajax -> decodeUTF8InputOn ( );
        $this -> xajax -> setCharEncoding ( 'utf-8' );

        // To prevent conflicts, prepend the extension prefix
        $this -> xajax -> setWrapperPrefix ( $this -> prefixId );

        // Turn only on during testing
        #$this->xajax->debugOn();
        // Register the names of the PHP functions you want to be able to call through xajax
        $this -> xajax -> registerFunction ( array ( 'xajaxPageBrowse', &$this, 'xajaxPageBrowse' ) );
        
        
        if ( !$this -> conf['shownoplayer'] )
        {
          $this -> xajax -> registerFunction ( array ( 'xajaxNewVideo', &$this, 'xajaxNewVideo' ) );        
        }

        // If this is an xajax request, call our registered function, send output and exit
        $this -> xajax -> processRequests ( );

        // Else create javascript and add it to the header output
        $GLOBALS['TSFE'] -> additionalHeaderData[$this -> prefixId] = $this -> xajax -> getJavascript ( t3lib_extMgm :: siteRelPath ( 'xajax' ) );

        //$this->incJsSnippet($this->xajax->getJavascript(t3lib_extMgm::siteRelPath('xajax')));
    }

    /**
    * Function called from Xajax Javascript
    *
    * Got called from an Ajax Request: returned the changed HTML Code
    *
    * @param [type]  $url: ...
    * @return String  changed HTML  parts
    */

    function xajaxPageBrowse ( $url )
    {

        //get Page Number of url
        preg_match ( '/(.*)pointer]=(.*)/i', urldecode ( $url ), $value );
        $page = intval ( $value[2][0] );

        //set Page Number for Pagebrowser
        $this -> piVars['pointer'] = $page;

        //render content
        $playlist = $this -> videoMenu ( );
        
        return $this -> sendResponse ( 'playlist', $playlist );
    }
    
    function findXMLSubtitle( $mediafile )
    {
      $subtitles = $this -> conf['video-subtitle'];
     
      if(!is_array($subtitles) || !$mediafile)
      {
       return FALSE;
      }
          
      foreach ($subtitles as $subtitle) 
      {
      	if(trim(strtolower($subtitle))==trim(strtolower(basename($mediafile).'.xml')))
      	{
         return $subtitle;
        }
      }
    }

    /**
    * Function called from Xajax Javascript
    *
    * Got called from an Ajax Request: returned the changed HTML Code
    *
    * @param [type]  $id: ...
    * @return String  changed HTML  parts
    */

    function xajaxNewVideo ( $id = 0 )
    {
        
        $this -> vIndex = $id;

        //render content
        
        $playlist = $this -> videoMenu ( );
        $caption = $this -> videoCaption ( );

        // Once having prepared the content we still need to send it to the browser ...
        // Instantiate the tx_xajax_response object
        $objResponse = new tx_xajax_response ( );

        // Add the content to videodiv
        $objResponse -> addAssign ( 'flvcaption', 'innerHTML', $caption );

        //loadFile('jstest2',{file:'/upload/corrie.flv',image:'/upload/corrie.jpg',captions:'/upload/corrie.xml'})
        
        $videofile = t3lib_loadModules :: getRelativePath ( t3lib_extMgm :: extPath ( $this -> extKey ).'js/', $this -> conf['video'][$id] ) ;
        
        $xmlFile = $this -> findXMLSubtitle($this -> conf['video'][$id]);
        
        if ( !$xmlFile )
        {
            $objResponse -> addScript ( "var x = loadFile({file:'$videofile'},'true');" );
        } else
        {

            $xmlfile = '../../../../uploads/'.$this->conf['ExtUploadPath'].'/'.$this->conf['video-subtitle'][$id];
           // $xmlfile = 'http://testarea.thoughtdivision.de/uploads/tx_bddbflvvideogallery/example_timetext.xml';

            //   $objResponse->addScript("var x =loadFile('mpl',{file:'http://testarea.thoughtdivision.de/fileadmin/ghost-o-one/videos/".basename($this->conf['video'][$id]).")',usecaptions:'true',captions:'$xmlfile'},'true');");
            $objResponse -> addScript ( "var x = loadFile({file:'$videofile',captions:'$xmlfile'},'true');" );
        }

        //return the XML response
        return $objResponse -> getXML ( );
    }

    /**
    * Sends the respond back to the Javascript
    *
    * @param [type]  $divID: ...
    * @param [type]  $content: ...
    * @return String  The XML Content for the Ajax request
    */

    function sendResponse ( $divID, $content )
    {

        // Once having prepared the content we still need to send it to the browser ...
        // Instantiate the tx_xajax_response object
        $objResponse = new tx_xajax_response ( );

        // Add the content to videodiv
        
        $objResponse -> addAssign ( $divID, 'innerHTML', $content );

        //output for debugging
        //if ($this->piVars['xajaxdebug']) $objResponse->addAssign('tx-bddbflvvideogallery-pi1', 'innerHTML', $this->debug);
        //return the XML response
        return $objResponse -> getXML ( );
    }

    /**
    * Return the Playlist for the Flv PLayer
    *
    * @return String  HTML Template for the Playlist
    */

    function videoMenu ( )
    {
        
        $playlist = $this -> cObj -> getSubpart ( $this -> conf['templateCode'], '###PLAYLIST###' );

        //Get single thumb template
        $item = $this -> cObj -> getSubpart ( $playlist, '###ITEM###' );

        //Get the Javascript Path
        $jspath = t3lib_extMgm :: extPath ( $this -> extKey ).'js/';
        $imgpath = 'uploads/'.$this -> conf['ExtUploadPath'].'/';
        
        $thumbs           = count ( $this -> conf['video'] );
        $maxthumbsperpage = t3lib_div :: intInRange ( $this -> conf['maxcolumn'], 1, 100 ) * t3lib_div :: intInRange ( $this -> conf['maxrows'], 1, 100 );
        $start            = t3lib_div :: intInRange ( $this -> piVars['pointer'], 0, 18 ) * $maxthumbsperpage;
        $to               = $start + $maxthumbsperpage - 1;
        $thumbwidth = $this -> conf['thumbwidth'] ? $this -> conf['thumbwidth'] : 0;
        $thumbheight = $this -> conf['thumbheight'] ? $this -> conf['thumbheight'] : 0;       
        
        for ( $k = $start; $k < $thumbs and $k <= $to; $k++ )
        {

            //Get relative Path to Video
            if ( $this -> conf['video'][$k] )
            {
                
                $videofile = t3lib_loadModules :: getRelativePath ( $jspath, $this -> conf['video'][$k] );

                //Flashvars must always be urlencoded
                $videofile = urlencode ( $videofile );

                //set Thumbnail or set default one
                if ( $this -> conf['video-thumb'][$k] )
                {
                    $image = $imgpath.$this -> conf['video-thumb'][$k];
                    
                    if($thumbwidth || $thumbheight) 
                    {
                      //$file,$width, $height, $minWidth=0, $minHeight=0, $maxWidth=0, $maxHeight=0, $altText = '', $titleText = '', $img_res = 0
                      $image = $this->resizeThumb($image,$thumbwidth, $thumbheight, 0, 0,0, 0, '','',1);
                    }
                                                                            
                } else
                {
                    $image = $this -> conf['defaultVideoThumb'];
                }

                # Build content from template

                $markerArray['###FILE###']      = $videofile;
                $markerArray['###IMAGE###']     = $image;
                $markerArray['###AUTOSTART###'] = $this -> conf['autostart'];
                $markerArray['###TITLE###']     = $this -> conf['videotitle'][$k] ? $this -> conf['videotitle'][$k] : basename ( $this -> conf['video'][$k] );
                
                if ( $this -> xajaxLoaded )
                {
                    $markerArray['###AJAX###'] = 'onclick="'.$this -> prefixId.'xajaxNewVideo('.$k.');return false;"';
                } else
                {
                    $markerArray['###AJAX###'] = '';
                }
                
                $markerArray['###LINK###'] = $this -> pi_getPageLink ( $GLOBALS['TSFE'] -> id, '', array ( $this -> prefixId.'[video]' => $k ) );

                //Every Maxcolumn a newline
                $isLine = ( $k + 1 ) % intval ( $this -> conf['maxcolumn'] );

                //Set act css
                if ( $k == $this -> vIndex )
                {
                    $actimg = 'flvplayer_listpic_pic_act';
                    $act = 'flvplayer_listpic_act';
                } else
                {
                    $actimg = 'flvplayer_listpic_pic_no';
                    $act = 'flvplayer_listpic_no';
                }

                //Set Css dependent on row position
                if ( $isLine == 0 )
                {

                    //Last row
                    
                    $markerArray['###NEWLINE###']        = '';
                    $markerArray['###SELECTED###']       = $act;
                    $markerArray['###ROWPOSITION###']    = 'flvplayer_listpic_lastofrow';
                    $markerArray['###PICSELECTED###']    = $actimg;
                    $markerArray['###PICROWPOSITION###'] = 'flvplayer_listpic_pic_lastofrow';
                } elseif ( $isLine == 1 )
                {

                    //First row
                    $markerArray['###NEWLINE###']        = ' clear: left;';
                    $markerArray['###SELECTED###']       = $act;
                    $markerArray['###ROWPOSITION###']    = 'flvplayer_listpic_firstofrow';
                    $markerArray['###PICSELECTED###']    = $actimg;
                    $markerArray['###PICROWPOSITION###'] = 'flvplayer_listpic_pic_firstofrow';
                } else
                {

                    //Between first and last
                    $markerArray['###NEWLINE###']        = '';
                    $markerArray['###SELECTED###']       = $act;
                    $markerArray['###ROWPOSITION###']    = '';
                    $markerArray['###PICSELECTED###']    = $actimg;
                    $markerArray['###PICROWPOSITION###'] = '';
                }
                
                $content_item .= $this -> cObj -> substituteMarkerArrayCached ( $item, $markerArray );
            }
        }

        //Set all Thumbnails
        $subpartArray['###CONTENT###'] = $content_item;

        //Set Pagebrowser if needed
        if ( $thumbs > $maxthumbsperpage )
        {
            $page = $this -> getPageBrowserMarkers ( $thumbs, $maxthumbsperpage, $this -> xajaxLoaded );
            $subpartArray['###PAGEBROWSER###'] = $page['###PAGEBROWSER###'];
        } else
        {
            $subpartArray['###PAGEBROWSER###'] = '';
        }

        //replace all Markers
        $content = $this -> cObj -> substituteMarkerArrayCached ( $playlist, array ( ), $subpartArray );
        
        return $content;
    }

    /**
    * Return the XML Playlist for the Flv PLayer
    *
    * @return String  HTML Template for the Playlist
    */

    function xmlPlaylist ( )
    {
        
        $playlist = $this -> cObj -> getSubpart ( $this -> conf['templateCode'], '###XMLPLAYLIST###' );

        //Get single thumb template
        $item = $this -> cObj -> getSubpart ( $playlist, '###ITEM###' );

        //Get the Javascript Path
        $imgpath = t3lib_loadModules :: getRelativePath ( $jspath, PATH_site.'uploads/'.$this -> conf['ExtUploadPath'].'/' );
        
        $thumbs = count ( $this -> conf['video'] );
        
        $start = 0;
        $to = $thumbs - 1;
        
        for ( $k = $start; $k < $thumbs; $k++ )
        {

            //Get relative Path to Video
            if ( $this -> conf['video'][$k] )
            {
                
                $videofile = t3lib_loadModules :: getRelativePath ( $jspath, $this -> conf['video'][$k] );

                //Flashvars must always be urlencoded
                $videofile = urlencode ( $videofile );

                //set Thumbnail or set default one
                if ( $this -> conf['video-thumb'][$k] )
                {
                    $image = $imgpath.$this -> conf['video-thumb'][$k];
                } else
                {
                    $image = $this -> conf['defaultVideoThumb'];
                }

                # Build content from template

                $markerArray['###FILE###']  = $videofile;
                $markerArray['###IMAGE###'] = $image;
                $markerArray['###TITLE###'] = $this -> conf['videotitle'][$k] ? $this -> conf['videotitle'][$k] : basename ( $this -> conf['video'][$k] );
                $markerArray['###LINK###']  = $this -> pi_getPageLink ( $GLOBALS['TSFE'] -> id, '', array ( $this -> prefixId.'[video]' => $k ) );
                

                $content_item .= $this -> cObj -> substituteMarkerArrayCached ( $item, $markerArray );
            }
        }

        //Set all Thumbnails
        $subpartArray['###CONTENT###'] = $content_item;

        //replace all Markers
        $content = $this -> cObj -> substituteMarkerArrayCached ( $playlist, array ( ), $subpartArray );
        
        return $content;
    }

    /**
    * [Describe function...]
    *
    * @param [type]  $count: ...
    * @param [type]  $limit: ...
    * @param [type]  $AjaxLoaded: ...
    * @return [type]  ...
    */

    function getPageBrowserMarkers ( $count, $limit, $AjaxLoaded )
    {

        //*******pagebrowser*****
        //**********************
        //$this->piVars['pointer']
        $this -> internal['res_count']          = $count;
        $this -> internal['results_at_a_time']  = $limit;
        $this -> internal['maxPages']           = 18;
        $this -> internal['dontLinkActivePage'] = 0;
        $this -> internal['showFirstLast']      = 0;
        $this -> internal['pagefloat']          = 'center';
        $this -> internal['showRange']          = 0;
        
        $wrapArr = array
        (
            'browseBoxWrap'          => '<div class="browseBoxWrap">|</div>',
            'showResultsWrap'        => '<div class="showResultsWrap">|</div>',
            'browseLinksWrap'        => '<div class="browseLinksWrap">|</div>',
            'showResultsNumbersWrap' => '<span class="numwrap">|</span>',
            'disabledLinkWrap'       => '<span class="disabledLinkWrap">|</span>',
            'inactiveLinkWrap'       => '<span class="inactiveLinkWrap">|</span>',
            'activeLinkWrap'         => '<span class="activeLinkWrap">|</span>',
        );
        
        $marker                          = array ( );
        $marker['###PAGEBROWSER###']     = $this -> pi_list_browseresults ( 0, '', $wrapArr, 'pointer', FALSE );
        $marker['###PAGEBROWSERTEXT###'] = $this -> pi_list_browseresults ( 2, '', $wrapArr, 'pointer', FALSE );

        //Change Links for Ajax
        if ( $AjaxLoaded )
        {
            $marker['###PAGEBROWSER###'] = preg_replace ( '/<a href="(.*)"/iU', '<a href="" onclick="'.$this -> prefixId.'xajaxPageBrowse(\'$1\');return false;"', $marker['###PAGEBROWSER###'] );
        }
        

        return $marker;
    }
    
    /**
       * Check if mootools is anywhere on the website.
       * Check is based on if t3mootools is configured or if $check is true      *
       * @param   boolean     $check: Just an additional value
       * @return   array      Set the window.addEvent
       */
     function checkForMootools($check=false) 
     {
         $mootools = array();
  
         if (t3lib_extMgm::isLoaded('t3mootools'))    
         {
            require_once(t3lib_extMgm::extPath('t3mootools').'class.tx_t3mootools.php');
         } 
         
         if (defined('T3MOOTOOLS') || $check) 
         {
           if (defined('T3MOOTOOLS')) 
           {
             tx_t3mootools::addMooJS();
           }
           $mootools['begin'] = 'window.addEvent("load", function(){';
           $mootools['end'] = ' });';
         }          
         
         return $mootools;
     }     

    /**
    * Return the Flash HTMl Code to include the Flash Player
    *
    * @return String  The Flash HTML Code Template
    */

    function videoObject ( )
    {

        //Ini Variables
        if ( $this -> conf['video-default'] )
        {
            $defaultimage = urlencode ( t3lib_loadModules :: getRelativePath ( t3lib_extMgm :: extPath ( $this -> extKey ).'js/', PATH_site.'uploads/'.$this -> conf['ExtUploadPath'].'/'.$this -> conf['video-default'] ) );
        }

        //Insert SWFObject
        $this -> incJsFile ( 'swfobject' );

        //Insert flvplayer JS
        $this -> incJsFile ( 'flvplayer' );

        //Load Player JS + Default Settings
        $JS .= '<div id="player">'.$this -> conf['altFlashContent'].'</div>';
        

        $videofile = urlencode ( t3lib_loadModules :: getRelativePath ( t3lib_extMgm :: extPath ( $this -> extKey ).'js/', $this -> videoFile ) );
        
        $mootools = $this -> checkForMootools();
        
        $JS .= '<script type="text/javascript">';
        $JS .= $mootools['begin'];
        $JS .= 'var s1 = new SWFObject("'.$this -> swfFile.'","mpl","'.$this -> width.'","'.$this -> height.'","'.$this -> conf['FlashVersion'].'");';
        
        $JS .= 's1.addVariable("file","'.$videofile.'");';
        
        if ( $xmlFile = $this -> findXMLSubtitle($videofile) )
        {  
          $JS .= 's1.addVariable("caption","'.$xmlFile.'");';         
        }     
        
        $JS .= 's1.addVariable("image","'.$defaultimage.'");';
        $JS .= 's1.addVariable("autostart","'.$this -> conf['autostart'].'");';
        $JS .= 's1.addVariable("enablejs","true");';
        $JS .= 's1.addVariable("javascriptid","mpl");';
        $JS .= 's1.addVariable("width","'.$this -> width.'");';
        $JS .= 's1.addVariable("height","'.$this -> height.'");';

        //Add redirect Adress
        $base = t3lib_div :: getIndpEnv ( 'TYPO3_SITE_URL' );
        $JS .= $this -> conf['RedirectFlash'] ? 's1.setAttribute("redirectUrl","'.$base.$this -> pi_getPageLink ( $this -> conf['RedirectFlash'] ).'");' : '';

        //Add extra Flash Parameter (via TypoScript)
        $JS .= $this -> conf['SwfObjectJS'];
        
        $JS .= 's1.write("player");';
        $JS .= $mootools['end'];
        $JS .= '</script>';

        //$JS.='<ul id="data" style="display:none;"></ul>';
        //return Flash Object
        return $JS;
    }

    /**
    * Return the Videodescription for the flv file
    *
    * @return String  Wrapped Description
    */

    function videoCaption ( )
    {
        
        return $this -> cObj -> stdWrap ( $this -> conf['videocaption'][$this -> vIndex], $this -> conf['caption.'] );
    }

    /**
     * Resize a thumbnail with Typoscript functions
     *
     * @param	[type]		$file: Picture with path to resize
     * @param	[type]		$width: optional width to resize
     * @param	[type]		$height: optional
     * @param	[type]		$minWidth: optional
     * @param	[type]		$minHeight: optional
     * @param	[type]		$maxWidth: optional
     * @param	[type]		$maxHeight: optional
     * @param	[type]		$altText: optional
     * @param	[type]		$titleText: optional
     * @param	[type]		$img_res: optional
     * @return	[string] Path and image name or false
     */

    function resizeThumb ( $file, $width, $height, $minWidth = 0, $minHeight = 0, $maxWidth = 0, $maxHeight = 0, $altText = '', $titleText = '', $img_res = 0 )
    {

        if ( file_exists ( $file ) )
        {
            $imgTSConfig = Array ( );
            $imgTSConfig['file'] = $file;
            if ( $maxWidth )
            {
                $imgTSConfig['file.']['maxW'] = $maxWidth;
            }
            if ( $maxHeight )
            {
                $imgTSConfig['file.']['maxH'] = $maxHeight;
            }
            if ( $minWidth )
            {
                $imgTSConfig['file.']['minW'] = $minWidth;
            }
            if ( $minHeight )
            {
                $imgTSConfig['file.']['minH'] = $minHeight;
            }
            if ( $width )
            {
                $imgTSConfig['file.']['width'] = $width;
            }
            if ( $height )
            {
                $imgTSConfig['file.']['height'] = $height;
            }
            $imgTSConfig['altText'] = $altText;
            $imgTSConfig['titleText'] = $titleText;
            if ( $img_res )
            {
                return $this -> cObj -> IMG_RESOURCE ( $imgTSConfig );
            } else
            {
                return $this -> cObj -> IMAGE ( $imgTSConfig );
            }
        } else
        {
            return false;
        }
    }
}


if ( defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bddb_flvvideogallery/pi1/class.tx_bddbflvvideogallery_pi1.php'] )
{
    include_once ( $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bddb_flvvideogallery/pi1/class.tx_bddbflvvideogallery_pi1.php'] );
}

?>