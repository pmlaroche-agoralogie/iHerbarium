<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010  <>
*  All rights reserved

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

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_tslib.'../../../../bibliotheque/common_functions.php');

/**
 * Plugin 'liste publique' for the 'iherbarium_observations' extension.
 *
 * @author     <>
 * @package    TYPO3
 * @subpackage    tx_iherbariumobservations
 */
class tx_iherbariumobservations_pi1 extends tslib_pibase {
    var $prefixId      = 'tx_iherbariumobservations_pi1';        // Same as class name
    var $scriptRelPath = 'pi1/class.tx_iherbariumobservations_pi1.php';    // Path to this script relative to the extension dir.
    var $extKey        = 'iherbarium_observations';    // The extension key.
    
    /**
     * The main method of the PlugIn
     *
     * @param    string        $content: The PlugIn content
     * @param    array        $conf: The PlugIn configuration
     * @return    The content that is displayed on the website
     */
    
    
    /*Ce programme permet d'afficher au maximum trois photos des cinq dernières observations déposées comme publiques ou semi-publiques,*/
    
    function main($content,$conf)    {
	global $etat_sousdomaine;
        $this->conf=$conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_USER_INT_obj=1;    // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
    
        
        $content="";
        
        /* Requête qui permet de sélectionner les dernières observations (si elles sont publiques ou partiellement publiques)*/
        $sql_order =" order by deposit_timestamp desc";
	
	if(is_sousdomaine_www())	$sql_limit = " limit 0,19 "; else $sql_limit = " limit 0,150 ";
	
	$display_type = "public";
	bd_connect();
	    
	if($GLOBALS['TSFE']->page["uid"]==15)
	    {
	    $sql_where =  "where id_user=".$GLOBALS['TSFE']->fe_user->user['uid'] ;
	    $sql_limit = "";
	    $display_type = "prive";
	    }
	else
	    $sql_where = "where ( public='oui' or public='semi' ) AND ".get_requete_where_sousdomaine();
	
	// legitims plants on home page
	if($GLOBALS['TSFE']->page["uid"]==6)
	    {
		$sql_where .= ' AND  computed_flux < 2 ';
		
	    }
	    
	// plant with no name at all
	if($GLOBALS['TSFE']->page["uid"]==81)
	    {
		$sql_where .= ' AND idobs not in (select id_obs from  iherba_determination where iherba_observations.idobs =  iherba_determination.id_obs ) ';
		if(!exists_sousdomaine())   	$sql_limit = " limit 0,210 ";
		$sql_limit = " limit 0,150 ";
	    }
	
	// plant with no scientif name
	if($GLOBALS['TSFE']->page["uid"]==82)
	    {
		$sql_where .= " AND idobs not in (select id_obs from  iherba_determination where iherba_observations.idobs =  iherba_determination.id_obs and `tropicosid` !=  '' ) ";
		if(!exists_sousdomaine())    $sql_limit = " limit 0,190 ";
	    }
	    
        $sql="select idobs from iherba_observations  $sql_where $sql_order $sql_limit ";
        /*$result = mysql_query($sql) or die ();
        while ($row = mysql_fetch_assoc($result)) {
			
			$content .= affiche_une_observation_dans_liste($this,$row["idobs"],"public");
        }
        
	bd_connect();
	$sql="select idobs,id_user from iherba_observations ;//on sélectionne l'ensemble des observations de l'utilisateur
	
	*/
	$result = mysql_query($sql)or die ();
	$nb_lignes_resultats=mysql_num_rows($result);
	$liste_idobs = array();
	
	if($nb_lignes_resultats==0){
		 /*on est dans le cas où l'utilisateur n'a pas encore constitué son herbier virtuel */
		 $content.=$this->pi_getLL('herbierVide', '', 1)."<br/>\n";
		 $content.=$this->pi_getLL('commencerHerbier', '', 1)."<br/>\n";
		 if(!is_sousdomaine_www())$content.= "[".get_description_sousdomaine()."]<br/>\n";
	 }
	//si il y a au moins une observation, on commence la liste
	while ($row = mysql_fetch_assoc($result)) {
		    $liste_idobs[] = $row["idobs"];
		}
	foreach($liste_idobs as $thisidobs)
	    {
		$content .= affiche_une_observation_dans_liste($this,$thisidobs,$display_type);     
	    }
        return $this->pi_wrapInBaseClass($content);
    }
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_observations/pi1/class.tx_iherbariumobservations_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_observations/pi1/class.tx_iherbariumobservations_pi1.php']);
}
?>
