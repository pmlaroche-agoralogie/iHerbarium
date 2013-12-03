<?php

// alimente la table iherbarium_book_specimen qui decrit une micro-flore
include("../bibliotheque/common_functions.php");
$debug = 1;

function clean_text($chaine)
  {
    return str_replace("'",'`',$chaine);
  }
  
function decrire_observation($id_obs)
  {

  global $debug;
  
  $langue='fr';
    
  $requete_lignes_pattern="select distinct iherba_roi.id,iherba_roi_answers_pattern.id_roi,
	    iherba_roi_answers_pattern.id_question,
	    iherba_roi_answers_pattern.id_answer_most_common,iherba_roi_answers_pattern.prob_most_common,   iherba_roi_answers_pattern.id_just_less_common, iherba_roi_answers_pattern.prob_just_less,
	   tag     from iherba_roi_answers_pattern,iherba_roi,iherba_photos,iherba_tags,iherba_roi_tag, iherba_question
	    where iherba_photos.id_obs=$id_obs and
	    iherba_photos.idphotos=iherba_roi.id_photo and
	    iherba_roi.id=iherba_roi_answers_pattern.id_roi 
	    and
	    iherba_tags.id_tag = iherba_roi_tag.id_tag and iherba_roi_tag.id_roi = iherba_roi.id and iherba_question.id_question = iherba_roi_answers_pattern.id_question";
  echo $requete_lignes_pattern;
  $lignes_reponse = mysql_query($requete_lignes_pattern);
  
  $liste_roi= array();
  $liste_roi_tag= array();
  $novice_tag = array();
  if(mysql_num_rows($lignes_reponse)>0)
    {
      while ($ligne = mysql_fetch_array($lignes_reponse)) {
		$liste_roi[] = $ligne['id'];
		$liste_roi_tag[] = $ligne['legendtag'];
		$possible_answer = explode('!',$ligne['choice_detail']);
		if( $ligne['id_question']==714)
		      $novice_tag[]= "couleur_".$possible_answer[$ligne['id_answer_most_common']];
		
		if( $ligne['id_question']==707)
		      {
		      if($ligne['id_answer_most_common']==0)
			      $novice_tag[]= "fleur_radiale";
			      else
			      $novice_tag[]= "fleur_radiale";
		      }
		 if( $ligne['id_question']==702)
		      {
		      $novice_tag[]= "petale_ordre".$ligne['id_answer_most_common'];
		      }
		if( $ligne['id_question']==302)
		      {
		      if($ligne['id_answer_most_common']==0)
			  $novice_tag[]= "plante_basse";
		      }
		if( $ligne['id_question']==230)
		      {
		      if($ligne['id_answer_most_common']==0)
			  $novice_tag[]= "feuille_simple";
		      }
			
    }
  }
  
  $sql_insert = "INSERT INTO `typoherbarium`.`iherba_observations_cached` (`id_obs`, `id_data`, `value`) VALUES ('$id_obs', 'description_pour_ebook', '".json_encode($novice_tag)."
');";
  if($debug)echo $sql_insert;
 $lignes_reponse = mysql_query($sql_insert); 
}

bd_connect();

//truncate `iherbarium_book_specimen`
//$liste_projet = array('cimetiere-du-pere-lachaise', '30960');
$liste_projet = array('cimetiere-du-pere-lachaise');
foreach ($liste_projet as $project_name)
{
  echo "<br> <br> <br> Debut projet : $project_name <br>";
  $vide_table = "delete from iherba_observations_cached where id_data='description_pour_ebook' ";
  $lignes_reponse = mysql_query($vide_table);
    
  $requete_project="select  *
	      from  iherbarium_book_project_list_taxon
	      where observation_modele >0  and exclusion_remarques = '' AND project_name = '$project_name'";
  $lignes_reponse = mysql_query($requete_project);
  if($debug)echo $requete_project;
  while ($ligne = mysql_fetch_array($lignes_reponse)) {
    if($debug)
      echo "<br>".$ligne['observation_modele']."<br>";
    if(!($debug==2))
      {
	decrire_observation($ligne['observation_modele']);
      }
     
    }
 
}
 

?>
