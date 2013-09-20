<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010  <> 
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

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_tslib.'../../../../bibliotheque/common_functions.php');

$balPath = PATH_tslib . "../../../../boiteauxlettres/";

require_once($balPath . "myPhpLib.php");

require_once($balPath . "debug.php");
require_once($balPath . "config.php");
require_once($balPath . "logger.php");
iHerbarium\Debug::init("iHerbariumAddObservationPlugin", False);

require_once($balPath . "transferableModel.php");
require_once($balPath . "dbConnection.php");

require_once($balPath . "persistentObject.php");

require_once($balPath . "simpleTreeTool.php");

/*Possibilité pour le propriétaire de modifier le statut de son observation , c'est-à-dire de définir son observation comme publique (visible par les
 * autres internautes), semi-publique (tous les champs de l'observation seront visibles sauf sa localisation), ou privée( l'observation ne sera pas 
 * visible par d'autres) */
function codehtml_statut($statut,$cetobjet){
$cibleaction = $cetobjet->pi_getPageLink($GLOBALS['TSFE']->id,'',null);
  $content="";
  $content.=$cetobjet->pi_getLL('visibiliteobs', '', 1);
	
  if($statut=="semi"){
    $content.=$cetobjet->pi_getLL('semi', '', 1)."<br/>";
  }
  elseif($statut=="oui"){
    $content.=$cetobjet->pi_getLL('public', '', 1)."<br/>";
  }
	
  elseif($statut=="non"){
    $content.=$cetobjet->pi_getLL('prive', '', 1)."<br/>";
  }
	
  $content.=$cetobjet->pi_getLL('changerStatut', '', 1)."<br/>";
  /*if($statut !="non"){
    $content .= '<form method="post" action="'.$cibleaction.'">';
    $content.='<input type="hidden" name="typaction" value="changestatut">';
    $content.='<input type="hidden" name="nouvellevaleur" value="non">
    <input type="submit" value="'.$cetobjet->pi_getLL('changerVersPrivee', '', 1).'" >';
    $content.="</form>";
    }
  */
  if($statut !="oui"){
    $content .= '<form method="post" action="'.$cibleaction.'">';
    $content.='<input type="hidden" name="typaction" value="changestatut">';
    $content.='<input type="hidden" name="nouvellevaleur" value="oui">
		<input type="submit" value="'.$cetobjet->pi_getLL('changerVersPublic', '', 1).'">';
    $content.="</form>";
  }
  if($statut !="semi"){
    $content .= '<form method="post" action="'.$cibleaction.'">';
    $content.='<input type="hidden" name="typaction" value="changestatut">';
    $content.='<input type="hidden" name="nouvellevaleur" value="semi">
		<input type="submit" value="'.$cetobjet->pi_getLL('changerVersSemi', '', 1).'">';
    $content.="</form>";
  }
  return $content;
}

/*Possibilité pour un admin de "moderer" une observation (disparait de la vue des autres) */
function codehtml_moderation($cetobjet,$numero_observation){
$cibleaction = $cetobjet->pi_getPageLink($GLOBALS['TSFE']->id,'',array('numero_observation' => $numero_observation));

  $content .= '<form method="post" action="'.$cibleaction.'">';
  $content.='<input type="hidden" name="typaction" value="modere">';
  $content.='<input type="hidden" name="nouvellevaleur" value="1">
		<input type="submit" value="'.$cetobjet->pi_getLL('donotshowforall', '', 1).'">';
  $content .= '</form>';

  return $content;
}


/*Possibilité pour le propriétaire de modifier le statut de son observation , c'est-à-dire de définir son observation comme publique (visible par les
 * autres internautes), semi-publique (tous les champs de l'observation seront visibles sauf sa localisation), ou privée( l'observation ne sera pas 
 * visible par d'autres) */
function codehtml_demande_morpho($statutdemande,$cetobjet,$numero_obs){
  $content="";
  //$content.=$cetobjet->pi_getLL('visibiliteobs', '', 1);
	
  $checksum=calcul_checksum($numero_observation,1);
  $paramlien = array(numero_observation  => $numero_observation,check=>456789);
  $url = $cetobjet->pi_getPageLink(1,'',$paramlien);
  if($statutdemande=="0"){
    $content .= '<form method="post" action="#">';
    $content.='<input type="hidden" name="typaction" value="changestatut">';
    $content.='<input type="hidden" name="nouvellevaleur" value="oui">
		<input type="submit" alt="'.$cetobjet->pi_getLL('creerroialt', '', 1).'"value="'.$cetobjet->pi_getLL('creerroi', '', 1).'">';
    $content.="</form>";
  }
  /*if($statut !="semi"){
    $content .= '<form method="post" action="#">';
    $content.='<input type="hidden" name="typaction" value="changestatut">';
    $content.='<input type="hidden" name="nouvellevaleur" value="semi">
    <input type="submit" value="'.$cetobjet->pi_getLL('changerVersSemi', '', 1).'">';
    $content.="</form>";
    }
  */
  return $content;
}


function getRewriting($id, $language) {
    $list_tables = "iherba_observations";
    switch($language) {
      case 1: // Français
        $rewriting = "url_rewriting_fr";
        break;
      case 2: // Anglais
        $rewriting = "url_rewriting_en";
        break;
      default: // Par défaut
        $rewriting = "url_rewriting_en";
    }

    $clause_where = "idobs = '{$id}'";
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $list_tables, $clause_where, '', '', '');

    $array = mysql_fetch_array($res);
    if(empty($array))
      return false;
    else
      return $array[$rewriting];
}



function traite_action(){
	$num=desamorcer($_GET['numero_observation']);
	if ($_POST['typaction']=="changestatut"){
		$new_statut=$_POST['nouvellevaleur'];
		$sql_changement_visibilite="UPDATE iherba_observations set public='$new_statut' where idobs='$num'";
		mysql_query ($sql_changement_visibilite) or die ('Erreur SQL !'.mysql_error());
	}
	if ($_POST['typaction']=="modere"){
		$new_statut=$_POST['nouvellevaleur'];
		$sql_changement_moderation="UPDATE iherba_observations set moderation='$new_statut' where idobs='$num'";
		mysql_query ($sql_changement_moderation) or die ('Erreur SQL !'.$sql_changement_moderation.mysql_error());
	}

  // Kuba ->
	
  // Delete lines in:
  // - iherba_roi_answers_pattern
  // - iherba_roi_answer

  if ($_POST['typaction'] == "deleteLine"){
    $deleteLineId = $_POST['deleteLineId'];
    assert($deleteLineId && ($deleteLineId > 0) );

    $deleteLinesQuery =
      " DELETE ap, a" .
      " FROM iherba_roi_answers_pattern AS ap, iherba_roi_answer AS a" .
      " WHERE ap.id = '" . $deleteLineId . "'" .
      " AND ap.id_roi = a.id_roi" .
      " AND ap.id_question = a.id_question";

    //echo "<h2>" . $deleteLinesQuery . "</h2>";

    mysql_query($deleteLinesQuery) or die (mysql_error());

    /*
    $deleteLineQuery =
      " DELETE FROM iherba_roi_answers_pattern" .
      " WHERE id = '" . $deleteLineId . "'";

    echo "<h2>" . $deleteLineQuery . "</h2>";
    */

    //mysql_query($deleteLineQuery) or die (mysql_error());
  }

  // <- Kuba
}

/**
 * Plugin 'page d'une observation' for the 'iherbarium_observations' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_iherbariumobservations
 */
class tx_iherbariumobservations_pi3 extends tslib_pibase {
  var $prefixId      = 'tx_iherbariumobservations_pi3';		// Same as class name
  var $scriptRelPath = 'pi3/class.tx_iherbariumobservations_pi3.php';	// Path to this script relative to the extension dir.
  var $extKey        = 'iherbarium_observations';	// The extension key.
	
  /**
   * The main method of the PlugIn
   *
   * @param	string		$content: The PlugIn content
   * @param	array		$conf: The PlugIn configuration
   * @return	The content that is displayed on the website
   */
	
  /* Ce programme affiche le détail d'une observation lorsque l'on clique sur le lien situé à la page définit dans 'pi1'*/
  function main($content,$conf)	{
    $this->conf=$conf;
    $this->pi_setPiVarDefaults();
    $this->pi_loadLL();
    $language = $GLOBALS['TSFE']->sys_language_uid;
    $GLOBALS["TSFE"]->set_no_cache();
    $this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

    $mylanguage = language_iso_from_lang_id($this->cObj->data['sys_language_uid']);
    // Kuba ->

    // jQuery.                           
    $content .= file_get_contents('typo3conf/ext/iherbarium_observations/pi4/scripts.html');

    // <- Kuba
		
    traite_action();

    $numero_observation = desamorcer($_GET['numero_observation']);
		
    if($this->piVars['detail']) {
      $observation = explode('-', $this->piVars['detail']);
      $numero_observation = $observation[count($observation) - 1];
      if(intval($numero_observation)) {
        $rewriting = getRewriting($numero_observation, $language);
        if($rewriting) {
          $rewriting .= "-" . $numero_observation;
          if($rewriting != $this->piVars['detail']) {
            $redirect = explode($this->piVars['detail'], $_SERVER['REQUEST_URI']);
            header('Location: ' . t3lib_div::locationHeaderUrl($redirect[0] . $rewriting));
          }
        } else if(empty($rewriting)) {
          $rewriting .= $numero_observation;
          if($rewriting != $this->piVars['detail']) {
            $redirect = explode($this->piVars['detail'], $_SERVER['REQUEST_URI']);
            header('Location: ' . t3lib_div::locationHeaderUrl($redirect[0] . $rewriting));
          }
        }
        else {
          header('Location: ' . t3lib_div::locationHeaderUrl($this->pi_getPageLink(1)));
        }
      }
      // Numéro observation n'est pas un int
      else {
        header('Location: ' . t3lib_div::locationHeaderUrl($this->pi_getPageLink(1)));
      }
    }

    if(!(ctype_digit($numero_observation)))die(""); // anti sql injection
		
    $content.='<div id="bloc_contenu"><h1>';
 $content.='<a href="https://twitter.com/share" class="twitter-share-button " data-lang="en" data-count="none" data-related="iHerbarium : Un botaniste dans votre smartphone">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

    /* on récupère la visibilité de l'observation dont on souhaite avoir le détail,
     * si le propriétaire de cette observation la déclarée comme partiellement public (champ public
     * de la table iherba_observations à 'semi') alors la carte ne s'affichera pas dans le détail pour les autres membres du site */
    $sql_visibilite="select public,id_user from iherba_observations where idobs=$numero_observation";
    $result_visibilite= mysql_query($sql_visibilite)or die ();
    $row_visibilite = mysql_fetch_assoc($result_visibilite);
    $visibilite=$row_visibilite["public"];
    $iduser=$row_visibilite["id_user"];//utilisateur qui a déposé cette observation
	    
    $content.= get_string_language_sql("ws_observation_before_number",$mylanguage).$numero_observation."";
		
    $content.='</h1><div id="bloc_contenu_texte">';
		 
    /* Liste des dernières expertises faites sur la plante */
    $content.= affiche_expertise($numero_observation,$this,"detail",$demandenom);
    $montitre = affiche_expertise($numero_observation,$this,"detail",$demandenom,2);
    $libelle_roi_morphoexplication = desamorcer($montitre );
    
    //title header of the generated html
    $GLOBALS['TSFE']->content = preg_replace(
       '@<title>(.+):(.*)</title>@i',
       '<title> '.$montitre.' $1 $2</title>',
	$GLOBALS['TSFE']->content);


    /*Affichage des informations concernant l'observation */
    bd_connect();
    $sql="select date_depot,idobs,commentaires,longitude,latitude,computed_date_seen_exif_or_smartphone from iherba_observations where idobs=$numero_observation";
    $result = mysql_query($sql) or die ();
    
    if(! ($lobervation = mysql_fetch_assoc($result)))
	return ; // no observation with this observation number

    if(($demandenom>0) && ($GLOBALS['TSFE']->fe_user->user['uid']!=0))
      {
	
	$paramlien = array(numero_observation  => $numero_observation,check=>456789);
	$link_text = get_string_language_sql("ws_give_a_plant_name",$mylanguage);
	$content.= $this->pi_linkToPage($link_text,31,'',$paramlien);
	$content.=" <br/><br/>";
      }
		
    //$content.= codehtml_demande_morpho("0",$this)." <br/><br/>";
    if(niveau_testeur($this)>0)
      {
	$content.="<br/>";
	$content.=$this->pi_linkToPage(get_string_language_sql("ws_edit_roi_image",$mylanguage),29,'',$paramlien);
	$content.="<br/><br/>";
	
	// allow to "moderate" this observation
	$content.=codehtml_moderation($this,$numero_observation)."<br/><br/>";	
      }
				

    if($lobervation["commentaires"] !=""){
      $content.= get_string_language_sql("ws_observation_comment",$mylanguage) ." ".$lobervation["commentaires"]."<br/><br/>\n";
    }
    
    $nom_photo_final = array();
    $sql_photos="select id_obs,date_user,nom_photo_final from iherba_photos where id_obs=$numero_observation";
    $result_photos = mysql_query($sql_photos) or die ();
    while ($row2 = mysql_fetch_assoc($result_photos)) {
      $date_user=$row2["date_user"]; //date de la prise de l'observation
      $nom_photo_final[]=$row2["nom_photo_final"];
    }
    
    //eventually, some medias video or sound
    $nom_media_final=array();
    $sql_media="select id_observation, date_depot, nom_media_final from iherba_medias where id_observation=$numero_observation ";
    $result_media = mysql_query($sql_media) or die ($sql_media);
    while ($row_media = mysql_fetch_assoc($result_media)) {
      $nom_media_final[]=$row_media["nom_media_final"];
    }
    
    $date_uploaded = str_replace("-","/",$lobervation["date_depot"]) ;
    $date_seen = $lobervation["computed_date_seen_exif_or_smartphone"];

    if(($date_seen !="0000-00-00 00:00:00")&&($date_seen !="")&&($date_seen !="0")){
      $content.= "<br>".get_string_language_sql("ws_seen_date_observation",$mylanguage)." ".str_replace("-","/",$date_seen)."\n";
    }
    
    $content.= "<br>".get_string_language_sql("ws_upload_date_observation",$mylanguage)." ".$date_uploaded."<br/>\n";
    // Kuba ->

    if($GLOBALS['TSFE']->fe_user->user['uid'] == $iduser) {    
		
      // Text
      $editButtonText   = $this->pi_getLL('editButton', 'editButton', True);
      $deleteButtonText = $this->pi_getLL('deleteButton', 'deleteButton', True);
      $areYouSureText   = $this->pi_getLL('deleteAreYouSure', 'deleteAreYouSure', True);
		  
      // Edit Button
      $editLink = $this->pi_getPageLink('obsedit', '', array('obsId' => $numero_observation, 'obsAction' => 'edit'));
      $content .= '<form id="editForm" method="post" action="' . $editLink . '">';
      $content .= '<input id="editLink" type="submit" value="' . $editButtonText .  '" />';
      //$content.= '<script>$("#editLink").button({label : "' . $editButtonText . '"});</script>';
      $content .= '</form>';
		  
      // Delete Button
      $deleteLink = $this->pi_getPageLink('obssave', '', array('obsId' => $numero_observation, 'obsAction' => 'delete', 'nextAction' => 'show'));
      $content .= '<form id="deleteForm" method="post" action="' . $deleteLink . '">';
      $content .= '<input id="deleteLink" type="submit" value="' . $deleteButtonText . '" />';
      $content .= '<script>$("#deleteForm").submit(function(e) { var answer = confirm("' . $areYouSureText . '"); return answer; })</script>';
      $content .= '</form>';
		
    }
    
    // <- Kuba


    // Kuba ->

    if( niveau_testeur()>0 ) {
      $actions = array("GenerateQuestions", "GenerateComparisons", "CleanAnswers");

      $links =
	array_map(
		  function($action) use ($numero_observation) {
		    return
		    '<a ' . 
		    'href="collaborative/question.php?action=' . $action .
		    '&obsId=' . $numero_observation .
		    '">' . $action . '</a>';
		  },
		  $actions
		  );

      $content .= iHerbarium\mkString($links, "<div style='margin: 10px;'>", "<br/>", "</div>");
    }

    // <- Kuba

    $content.= get_string_language_sql("ws_observation_list_of_pictures",$mylanguage)."<br/>\n";
    foreach($nom_photo_final as $value){
      //Permet d'afficher l'image en taille réelle lorsque l'on clique dessus
      // si on est le propriétaire, on a l'image en qualité max et sans licence
      if($GLOBALS['TSFE']->fe_user->user['uid']==$iduser)
	$content.='<a href="'.repertoire_sources."/".$value.'" ><img src="'.repertoire_vignettes."/".$value.'" border=2 width="200"  /></blank></a>';
      else
	$content.='<a href="/scripts/large.php?name='.$value.'" ><img src="'.repertoire_vignettes."/".$value.'" border=2 width="200"  /></blank></a>';
    }
    
    if(!empty($nom_media_final)){
      $content.= "<br>".get_string_language_sql("ws_observation_list_of_media",$mylanguage)."<br/>\n";
      foreach($nom_media_final as $value){
	$content.='<a href='.repertoire_sources."/".$value.' >Video </a>';
      }
      $content.= "<br>";
    }
    
    // localisation is shown if public or for the owner 
    if(($GLOBALS['TSFE']->fe_user->user['uid']==$iduser)|| ($visibilite=="oui")){
      if($lobervation["latitude"]!=0 && $lobervation["longitude"]!=0){
	$content.="<br/><br/>".get_string_language_sql("ws_observation_was_localized",$mylanguage);
	$localstring = get_string_language_sql("ws_observation_localized_lat_long",$mylanguage);
	
	$localstring = str_replace('%1',round($lobervation["latitude"],4),$localstring) ;
	$localstring = str_replace('%2',round($lobervation["longitude"],4),$localstring) ;
	$content.= $localstring."<br/><br/>\n";
	$content.=fairecarte($lobervation["latitude"],$lobervation["longitude"]);
	
	$current_url = 'http://www'.substr(t3lib_div::getIndpEnv('HTTP_HOST'),strpos(t3lib_div::getIndpEnv('HTTP_HOST'),".",0));
	if(strpos($current_url,'?')===false)$current_url .= '?addzoom=1';
	$title_link = " title='".get_string_language_sql('ws_view_limitation_alt_add_limit',$mylanguage)."' class=drillzoom ";
	$url=$current_url."&area_limitation=circle:".$lobervation["latitude"].','.$lobervation["longitude"].',0.04';
	$content.="<br/>".  "<a  href=$url $title_link >".get_string_language_sql("ws_set_limitation_circle",$mylanguage)."</a><br><br/>";
      }
      else{
	$content.="<br/><br/>".get_string_language_sql("ws_observation_was_not_localized",$mylanguage);
      }
    }
    if(niveau_testeur()>0)
	{
	$show_delete_button=1;
	}
	else
	$show_delete_button=0;
	
	
    //$destination_morpho = $this->pi_getPageLink($GLOBALS['TSFE']->id);
    //$content.=information_analyse($numero_observation,$GLOBALS['TSFE']->sys_language_uid,get_string_language_sql("ws_roi_morpho_explanation",$mylanguage),$destination_morpho,$show_delete_button,$libelle_roi_morphoexplication);
	
    /*
    if(niveau_testeur()>0)
      //if($GLOBALS['TSFE']->fe_user->user['uid']==$iduser)
      {
	$desc = charge_description($numero_observation);
	$obs_prob = calcule_liste_proche ($desc);
	if(count($obs_prob) >0)
	  {
	    $paramlien = array(numero_observation  => $numero_observation,check=>456789);
	    $content.= $this->pi_linkToPage($this->pi_getLL('seeproximity', '', 1),52,'',$paramlien);
	    $content.= "</br>";
	  }
	else
	  {
	    $content.= "<!-- no proximity -->";
	  }
      }
     */
    
    if($GLOBALS['TSFE']->fe_user->user['uid']==$iduser){
	$paramlien = array(numero_observation  => $numero_observation,check=>456789);
	$content.= "<br>".$this->pi_linkToPage(get_string_language_sql("ws_go_page_with_qrcode",$mylanguage),47,'',$paramlien);
      }
    //if(niveau_testeur()>0)
      

    if(isset($_GET['cmpAll'])) {

      $local = iHerbarium\LocalTypoherbariumDB::get();

      // COMPARATOR
      $questionsOptions = $local->loadQuestionsOptions();
      $palette          = $local->loadColorPalette("Basic");
      $tagsOptions      = $local->loadTagsOptions();
      //$content .= "<h4>QuestionsOptions</h4><pre>" . var_export($questionsOptions, True) . "</pre>";
      //$content .= "<h4>Palette</h4><pre>" . var_export($palette, True) . "</pre>";
      //$content .= "<h4>TagsOptions</h4><pre>" . var_export($tagsOptions, True) . "</pre>";
      
      $comparator = new iHerbarium\MyComparator($questionsOptions, $palette, $tagsOptions);

      // CURRENT MODEL
      $obs = $local->loadObservation($numero_observation);
      $model = iHerbarium\APModel::create($obs);
      //$content .= "<h4>Model</h4><pre>" . var_export($model, True) . "</pre>";

      // OTHER MODELS
      $uid = $iduser;
      
      // Observations' Group
      $groupId = 1; // Alexandre
      if($_GET['cmpAll'])
	$groupId = desamorcer($_GET['cmpAll']);

      $group = $local->loadGroup($groupId);
	    
      // Models
      $models = array_map(function($obs) { return iHerbarium\APModel::create($obs); }, $group->getAllObservations());
      //$content .= "<h4>Models</h4><pre>" . var_export($models, True) . "</pre>";

      // Comparing results
      $cmpResults = 
	array_map(
		  function($model2) use ($comparator, $model) {
		    $cmpModels = $comparator->compareModels($model, $model2);
		    return $cmpModels;
		  }, $models);
      //$content .= "<h4>CmpModels</h4><pre>" . var_export($cmpResults, True) . "</pre>";
      
      // Sort by similarity of a given tag.
      /*
	$tagToSort = 7; // Tag 7 - flower.
	uasort($cmpResults, 
	function($r1, $r2) { return - iHerbarium\cmp(
	isset($r1[7]) ? $r1[7]['similarity'] : 0, 
	isset($r2[7]) ? $r2[7]['similarity'] : 0
	); } );
      */

      // Sort by similarity of models.
      uasort($cmpResults, 
	     function($r1, $r2) { return - iHerbarium\cmp(
							  $r1['similarity'],
							  $r2['similarity']
							  ); } );
      

      //$content .= "<h4>CmpModels</h4><pre>" . var_export($cmpResults, True) . "</pre>";

      // ListView
      $thisPlugin = $this;

      $cmpToContent = 
	array_map(
		  function($obsId, $cmp) use ($thisPlugin) {
		    $content = "<h2>" . $obsId . "</h2>";
		    $content .= affiche_une_observation_dans_liste($thisPlugin, $obsId, "public");
		    //$content .= "<h4>Cmp</h4><pre>" . var_export($cmp, True) . "</pre>";
		    $content .= "<h4>Cmp</h4>" . iHerbarium\arrayToHTML($cmp);
		    return $content;
		  }, 
		  array_keys($cmpResults), $cmpResults
		  );
      
      $content .= implode($cmpToContent, "\n");

    }


    $content.="
		<!--fin bloc_contenu--> \n";
    return $this->pi_wrapInBaseClass($content);
  }
}



  if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_observations/pi3/class.tx_iherbariumobservations_pi3.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/iherbarium_observations/pi3/class.tx_iherbariumobservations_pi3.php']);
  }
  ?>
