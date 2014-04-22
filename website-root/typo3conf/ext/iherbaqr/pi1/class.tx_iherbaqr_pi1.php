<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011  <>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_tslib.'../../../../bibliotheque/common_functions.php');


/**
 * Plugin 'page' for the 'iherbaqr' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbaqr
 */
class tx_iherbaqr_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_iherbaqr_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_iherbaqr_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'iherbaqr';	// The extension key.
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		
		$mylanguage = language_iso_from_lang_id($this->cObj->data['sys_language_uid']);
		$param_label=array();
		
		$template_name = "complete";
		if($_GET['template']=='classic')$template_name="classic";
		if($_GET['template']=='compact')$template_name="compact";
		
		$currentobs = desamorcer($_GET['numero_observation']);
		$base_url = 'http://www'.substr(t3lib_div::getIndpEnv('HTTP_HOST'),strpos(t3lib_div::getIndpEnv('HTTP_HOST'),".",0));
		$urlqrencode = $base_url."/observation/data/".$currentobs;
		
		$urlgoogle =  urlencode($urlqrencode);
		$content='<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2"> <tbody> <tr> <td style="width: 100px;">
			<img src=http://chart.apis.google.com/chart?chs=380x380&cht=qr&chld=H&chl='.$urlgoogle.' width=100><br>	
			</td> <td> 
		';
		$param_label['img_qr_code']= 'http://chart.apis.google.com/chart?chs=380x380&cht=qr&chld=H&chl='.$urlgoogle;
		
	/*Affichage des informations concernant l'observation */
		bd_connect();
		$sql="select date_depot,idobs,id_user,commentaires,address,personnal_ref,longitude,latitude from iherba_observations where idobs=".$currentobs." ";
		$result = mysql_query($sql)or die ();
		if(!($lobervation = mysql_fetch_assoc($result))) return ; 
		$date_depot = $lobervation["date_depot"];
		$dateaffichage = str_replace("-","/",$date_depot) ;
		
		$param_label['legend_obs_number']= get_string_language_sql("label_legend_observation_number",$mylanguage);
		$param_label['value_obs_number']= $currentobs;
		
		$content.= $this->pi_getLL('text_before_obs_num', '', 1)." ".$currentobs."<br/>\n";
		
		$param_label['legend_date']= get_string_language_sql("label_legend_date_recolte",$mylanguage); //$this->pi_getLL('text_before_date', '', 1);
		$param_label['value_date']= $date_depot; //iso format with - not /
		
		
		$content.= $this->pi_getLL('text_before_date', '', 1)." ".$dateaffichage."<br/>\n";
		
		$param_label['legend_localisation']=get_string_language_sql("label_legend_observation_localisation",$mylanguage);;
		$param_label['value_localisation']="-";
		
		if($lobervation["latitude"]!='0' && $lobervation["longitude"]!=0)
			{
				$param_label['value_localisation']=$lobervation["latitude"]." , ".$lobervation["longitude"];
			}
		
		$param_label['legend_address_detail']=get_string_language_sql("label_legend_address_detail",$mylanguage);;
		$param_label['value_address_detail']=$lobervation["address"];
		
		$param_label['legend_personnal_ref']=get_string_language_sql("label_legend_personnal_ref",$mylanguage);;
		$param_label['value_personnal_ref']=$lobervation["personnal_ref"];
		
		$param_label['legend_notes']=get_string_language_sql("label_legend_comment",$mylanguage);;
		$param_label['value_notes']=$lobervation["commentaires"];
		
		

		$sql_recolteur = "SELECT *  FROM  `fe_users`  WHERE  `uid` = ".$lobervation["id_user"];
		$result_recolteur = mysql_query($sql_recolteur) or die ();
		if(!($lerecolteur = mysql_fetch_assoc($result_recolteur))) return ;
		$param_label['legend_recolteur']=get_string_language_sql("label_legend_recolteur",$mylanguage);
		$param_label['value_recolteur']=$lerecolteur['name'];
		
		//display last given name
		$champscomment = 'email_comment';
		$sql_determination.="select iherba_determination.id ,iherba_determination.id_user , nom_commun,nom_scientifique,date, famille,genre ,id_cases, iherba_determination_cases.$champscomment ,iherba_certitude_level.value as certitude_level, iherba_certitude_level.comment as certitude_comment,";
		$sql_determination.=" iherba_determination.comment,iherba_precision_level.value as precision_level,iherba_precision_level.$champscomment as precisioncomment from iherba_determination,iherba_determination_cases,iherba_certitude_level, iherba_precision_level ";
		$sql_determination.="where  iherba_determination_cases.language = 'fr' and iherba_determination_cases.id_cases = iherba_determination.comment_case AND iherba_determination.precision_level = iherba_precision_level.value ";
		$sql_determination.= " AND iherba_determination.certitude_level = iherba_certitude_level.value AND iherba_determination.id_obs=$currentobs ";
		$sql_determination.= " order by creation_timestamp desc";
		$sql_determination .= " limit 1";
		
		 // echo "<!-- sql $sql_determination -->";
		
		$result_determination = mysql_query($sql_determination) or die ($sql_determination);
		$num_rows = mysql_num_rows($result_determination); //nombre de lignes rÈsultats
			
		if ($row_determination = mysql_fetch_assoc($result_determination)) {
		    $nom_commun=$row_determination["nom_commun"];
		    $nom_scientifique=$row_determination["nom_scientifique"];
		    $date_determination=$row_determination["date"];
		    
		    $param_label['legend_determinavit']=get_string_language_sql("label_legend_determinavit",$mylanguage);
		    $param_label['value_determinavit']= $nom_scientifique ."(".$nom_commun. ") ";
		    
		    $param_label['legend_determinavit_famille']=get_string_language_sql("label_legend_determinavit_famille",$mylanguage);
		    $param_label['value_determinavit_famille']= $row_determination["famille"];
		  }
		  
		
		$sql_determineur = "SELECT *  FROM  `fe_users`  WHERE  `uid` = ".$row_determination["id_user"];
		$result_determineur = mysql_query($sql_determineur) or die ();
		if(!($ledetermineur = mysql_fetch_assoc($result_determineur))) return ;
		$param_label['legend_determinavit_author']=get_string_language_sql("label_legend_determineur",$mylanguage);
		$param_label['value_determinavit_author']=$ledetermineur['name']." ( ".$date_determination." )";
		
		//display pictures
		$sql="select nom_photo_final from iherba_photos where id_obs=".$currentobs;
		$result = mysql_query($sql)or die ();
		/* On affiche les photos que l'utilisateur nous a déjà fait parvenir */
		
		$param_label['pictures_list']="";
		
		$repertoire = "/medias/big/";
	    	while ($row = mysql_fetch_assoc($result)) {
			$image="$repertoire/".$row["nom_photo_final"];
			$content.='<img src="'.$image.'" border=0 height="65"  /></blank>&nbsp;';
			$param_label['pictures_list'] .= '<img src="'.$image.'" border=0 height="65"  /></blank>&nbsp;';
			}
	
		
		//print_r($param_label);
		$modele = file_get_contents(PATH_tslib.'../../../../fileadmin/template/label_'.$template_name.'.tmpl');
		foreach ($param_label as $key => $value)
			{
				$modele = str_replace('{'.$key.'}', $param_label[$key],$modele);
			}
		
		return $this->pi_wrapInBaseClass($modele);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbaqr/pi1/class.tx_iherbaqr_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbaqr/pi1/class.tx_iherbaqr_pi1.php']);
}

?>
