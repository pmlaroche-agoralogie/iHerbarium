<?php
include("../bibliotheque/common_functions.php");

  function look_at($id_obs)
  {
  bd_connect();
  
  $requete_owner="select  name,url_rewriting_fr,computed_best_tropicos_id
	from iherba_observations,fe_users
	    where iherba_observations.idobs=$id_obs and iherba_observations.id_user = fe_users.uid";
  $lignes_reponse = mysql_query($requete_owner);
  $ligne = mysql_fetch_array($lignes_reponse);
  $taxonid = $ligne['computed_best_tropicos_id'];
  $owner = $ligne['name'];
  $scname = $ligne['url_rewriting_fr'];
  $besttropicos = $ligne['computed_best_tropicos_id'];
  $commonname = "nom commun de ".$ligne['url_rewriting_fr'];
  $texte_descriptif = " en savoir plus... ";
  $langue= 'fr';
  $novice_tag =array();

  $requete_owner="select  * from `iherba_determination` WHERE  `tropicosid` =  $taxonid order by id desc";
  $lignes_reponse = mysql_query($requete_owner);
  if(mysql_num_rows($lignes_reponse)>0)
    {
      $ligne = mysql_fetch_array($lignes_reponse);
      $scname = $ligne['nom_scientifique'];
    }
    
  $requete_owner="select  * from iherba_taxon_texts
	    where taxon = $taxonid AND  `taxon_api` =  'tropicos' AND  `lng` LIKE  'fr' and categorie = 'vernacular' ";
  $lignes_reponse = mysql_query($requete_owner);
  if(mysql_num_rows($lignes_reponse)>0)
    {
      $ligne = mysql_fetch_array($lignes_reponse);
      $commonname = $ligne['description'];
    }
  
  $requete_owner="select  * from iherba_taxon_texts
	    where taxon = $taxonid AND  `taxon_api` =  'tropicos' AND  `lng` LIKE  'fr' and categorie = 'description' ";
  $lignes_reponse = mysql_query($requete_owner);
  if(mysql_num_rows($lignes_reponse)>0)
    {
      $ligne = mysql_fetch_array($lignes_reponse);
      $texte_descriptif = $ligne['description'];
    }
  
  $requete_owner="select  * from iherba_taxon_texts
	    where taxon = $taxonid AND  `taxon_api` =  'tropicos' AND  `lng` LIKE  'fr' and categorie = 'habitat' ";
  $lignes_reponse = mysql_query($requete_owner);
  if(mysql_num_rows($lignes_reponse)>0)
    {
      $ligne = mysql_fetch_array($lignes_reponse);
      $texte_descriptif .= "Habitat :\n".$ligne['description'];
    }
  $texte_descriptif = str_replace("'",'`',$texte_descriptif);
  $requete_lignes_pattern="select distinct iherba_roi.id,iherba_roi_answers_pattern.id_roi,
	    iherba_roi_answers_pattern.id_question,
	    iherba_roi_answers_pattern.id_answer_most_common,iherba_roi_answers_pattern.prob_most_common,   iherba_roi_answers_pattern.id_just_less_common, iherba_roi_answers_pattern.prob_just_less,
	    iherba_question.choice_explicitation_one , iherba_question.choice_explicitation_two_seldom , iherba_question.choice_explicitation_two_often , iherba_question.choice_detail ,tag ,texte as legendtag"    /* Kuba -> */  . " , iherba_roi_answers_pattern.id AS lineid " . /* <- Kuba */
	"from iherba_roi_answers_pattern,iherba_roi,iherba_photos,iherba_question,iherba_tags,iherba_roi_tag,iherba_tags_translation
	    where iherba_photos.id_obs=$id_obs and
	    iherba_photos.idphotos=iherba_roi.id_photo and
	    iherba_roi.id=iherba_roi_answers_pattern.id_roi and iherba_question.id_langue='$langue'
	    and
	    iherba_tags.id_tag = iherba_roi_tag.id_tag and iherba_roi_tag.id_roi = iherba_roi.id
	    and
	    iherba_tags_translation.id_tag = iherba_tags.id_tag and iherba_tags_translation.id_langue = '$langue'
	    and
	    iherba_roi_answers_pattern.id_question = iherba_question.id_question  ";
  
  $lignes_reponse = mysql_query($requete_lignes_pattern);
  
  $liste_roi= array();
  $liste_roi_tag= array();
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
  
  $requete_lignes_pattern="select distinct iherba_roi.id,iherba_roi_answers_pattern.id_roi,iherba_photos.nom_photo_final,
	  iherba_roi_answers_pattern.id_question,
	  iherba_roi_answers_pattern.id_answer_most_common,iherba_roi_answers_pattern.prob_most_common,   iherba_roi_answers_pattern.id_just_less_common, iherba_roi_answers_pattern.prob_just_less,
	  iherba_question.choice_explicitation_one , iherba_question.choice_explicitation_two_seldom , iherba_question.choice_explicitation_two_often , iherba_question.choice_detail ,tag ,texte as legendtag"    /* Kuba -> */  . " , iherba_roi_answers_pattern.id AS lineid " . /* <- Kuba */
      "from iherba_roi_answers_pattern,iherba_roi,iherba_photos,iherba_question,iherba_tags,iherba_roi_tag,iherba_tags_translation
	  where iherba_photos.id_obs=$id_obs and
	  iherba_photos.idphotos=iherba_roi.id_photo and
	  iherba_roi.id=iherba_roi_answers_pattern.id_roi and iherba_question.id_langue='$langue'
	  and
	  iherba_tags.id_tag = iherba_roi_tag.id_tag and iherba_roi_tag.id_roi = iherba_roi.id
	  and
	  iherba_tags_translation.id_tag = iherba_tags.id_tag and iherba_tags_translation.id_langue = '$langue'
	  and
	  iherba_roi_answers_pattern.id_question = iherba_question.id_question  group by iherba_roi.id";

  $lignes_reponse = mysql_query($requete_lignes_pattern);
  $liste_photo= array();
  $nbphoto=0;
  if(mysql_num_rows($lignes_reponse)>0)
    {
      while ($ligne = mysql_fetch_array($lignes_reponse)) {
		$liste_photo[$nbphoto]['tag']= $ligne['tag'];
		$liste_photo[$nbphoto]['photo'] = "/medias/big/".$ligne['nom_photo_final'];
		$liste_photo[$nbphoto]['legende'] = "photo de ".$ligne['legendtag'];
		$liste_photo[$nbphoto]['droits'] = "license iherbarium, photo prise par $owner" ;
		$nbphoto++;
	    }
    }
  //print_r($novice_tag);
  
  $jsonphoto = json_encode($liste_photo);
  $jsontag = json_encode($novice_tag);
  

  $sql_insert = "INSERT INTO `iherbarium_book_specimen` (`project_name`, `taxonref`, `langue`, `commonname`, `scientificname`, `pictures_with_legends`, `description`, `morphology`)
  VALUES ('pere-lachaise', '$besttropicos', 'fr', '$commonname', '$scname', '$jsonphoto', '$texte_descriptif', '$jsontag');";
  echo $id_obs."-".$commonname;echo "<br>.$sql_insert.<br>";
 $lignes_reponse = mysql_query($sql_insert); 
}

look_at(210);
look_at(356);
look_at(810);
look_at(378);
look_at(293);
look_at(374);
look_at(330);
look_at(158);
look_at(359);
look_at(107);
look_at(466);
look_at(813);


look_at(1467);
look_at(866);
look_at(589);

look_at(443);


look_at(870);

look_at(242);

look_at(357);

look_at(221);

look_at(1029);

look_at(1558);

look_at(444);

look_at(950);



look_at(294);

look_at(291);

look_at(168);

look_at(207);

look_at(441);



?>
