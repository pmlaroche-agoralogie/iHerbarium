<?php 
require_once("connexion.php");

define('repertoire_sources', './medias/sources');
define('repertoire_big', './medias/big');
define('repertoire_vignettes', '/medias/vignettes');
define('roi_sources', '/medias/roi');
define('roi_vignettes', '/medias/roi_vignettes');
define('vignettes_questions','../medias/roi_vignettes');
define('repertoire_iphone','./fromiphone/');
define('template_carte','./fileadmin/template/code_js_cartographie_ent.html');
define('debut_carto_aff_multi','./fileadmin/template/debut_carto_aff_multi.txt');

function get_string_languagex(&$tableau,$identifiant,$chosen_language,$default_language='en')
{
  if(isset($tableau[$chosen_language][$identifiant]))
    {$key = $tableau[$chosen_language][$identifiant];
    //if(!mb_check_encoding($key, 'UTF-8')) $key = utf8_encode($key); 
   return $key;
    }
   else
   return utf8_encode($tableau[$default_language][$identifiant]);
}

function get_string_language_sql($identifiant,$chosen_language,$default_language='en')
{
  bd_connect();
  
  $sql_list_translate =
    "SELECT * FROM  `ih_translation` 
    WHERE  `label` LIKE  '$identifiant' and lang LIKE '$chosen_language%'";
    
  $result_translate=mysql_query ($sql_list_translate) or die ();
  
  if(mysql_num_rows($result_translate)==0)
    {
      $sql_list_translate =
      "SELECT * FROM  `ih_translation` 
      WHERE  `label` LIKE  '$identifiant' and lang LIKE 'en%'";
      $result_translate=mysql_query ($sql_list_translate) or die ();
    }
  if(mysql_num_rows($result_translate)==0)
    {//identifiant not found
    return $identifiant;
    }
    else
    {
    $ligne = mysql_fetch_array($result_translate);
    return $ligne['translated_text'];
    }
}

function niveau_testeur()
{
  if(strpos($GLOBALS['TSFE']->fe_user->user['usergroup'],"3")>0)
    return 1;
  else return 0;
}

function calcul_checksumv1($param1,$param2)
{
  return "V1".md5($param1 + ($param2*7));
}
function obtenir_checksum($param1,$param2="")
{
  return calcul_checksumv1($param1,$param2);
}
function verification_checksum($lechecksum,$param1,$param2="")
{
  $bonchecksum = calcul_checksumv1($param1,$param2);
  if($lechecksum != $bonchecksum)die();
}

/*fonction qui permet d'obtenir la langue utilis»e de pr»f»rence par le navigateur, sur deux caracteres */
function choisir_langue(){
  $langues = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
  $langue_selectionnee=$langues[0];
  if($langue_selectionnee=="fr-FR")$langue_selectionnee="fr";
  if($langue_selectionnee=="de-DE")$langue_selectionnee="de";
  if(($langue_selectionnee=="pt-PT")||($langue_selectionnee=="pt-BR"))$langue_selectionnee="pt";
  if(($langue_selectionnee!="fr")&&($langue_selectionnee!="pt")&&($langue_selectionnee!="de"))$langue_selectionnee="en";
  return $langue_selectionnee;
}

// empeche les injections sql
function desarmorcer($chaine)
{
  $chaine = str_replace("'","?",$chaine);
  $chaine = str_replace('"',"?",$chaine);
  $chaine = str_replace(';',",",$chaine);
  $chaine = str_replace('://',"---",$chaine);
  return $chaine;
}
// empeche les injections sql, sans faute de frappe
function desamorcer($chaine)
{
  $chaine = str_replace("'","?",$chaine);
  $chaine = str_replace('"',"?",$chaine);
  $chaine = str_replace(';',",",$chaine);
  $chaine = str_replace('/',"-",$chaine);
  $chaine = str_replace('\\',"-",$chaine);
  return $chaine;
}



// construit une chaine de caracteres decrivant la r»gion observ»e et dont le pattern de r»ponse a »t» enregistr»
function build_response($ligne,$cibleaction = "#",$show_delete_button=0)
{
  $content = "";

  if($ligne[choice_detail]=="")return "<!-- warning no text for question $ligne[id_question] -->";
  $reponsespossibles = explode("!",$ligne['choice_detail']);

  if($ligne[prob_most_common]>90)
    $textexplication = $ligne['choice_explicitation_one'];
  else
    {
      if($ligne[prob_most_common]>80)
	$textexplication = $ligne['choice_explicitation_two_seldom'];
      else
	$textexplication = $ligne['choice_explicitation_two_often'];
    }

  $textexplication = str_replace("#1",$reponsespossibles[$ligne['id_answer_most_common']],$textexplication);
  $textexplication = str_replace("#2",$reponsespossibles[$ligne['id_just_less_common']],$textexplication);
  // Kuba ->
if($show_delete_button==1)
	{
  // Delete Line button (delete action executed in class.tx_iherbariumobservations_pi3.php).

  $form   = array();
  $form[] = '<form action="' . $cibleaction . '" method="post">';
  $form[] = $textexplication;
  $form[] = '<input name="typaction" value="deleteLine" type="hidden" />'; // Action
  $form[] = '<input name="deleteLineId" value="' . $ligne['lineid'] . '" type="hidden" />'; // Line Id
  $form[] = '<button name="deleteLineSubmit" type="submit">Delete</button>'; // Submit button
  $form[] = '</form>';
$content = implode("\n", $form);
}
else
$content = $textexplication;


  // <- Kuba	

  return 	$content;
}

function information_analyse($id_obs,$idlangue,$explication="Region of interest show the following answers ",$cibleaction="##",$show_delete_button=0,$libellespeciesname='')
{	
  $langue="en";
  switch ($idlangue) {
  case 1:
    $langue = "fr";
    break;
  case 2:
    $langue = "pt";
    break;
  case 3:
    $langue = "de";
    break;
  }
  $content = "";	
 //$langue = "fr";$show_delete_button=0;
  bd_connect();
  //same request as the following one, but group by id of roi
  $requete_lignes_pattern="select distinct iherba_roi.id,iherba_tags.tag,
	 iherba_roi_answers_pattern.id AS lineid " . 
      "from iherba_roi_answers_pattern,iherba_roi,iherba_photos,iherba_tags,iherba_roi_tag
	  where iherba_photos.id_obs=$id_obs and
	  iherba_photos.idphotos=iherba_roi.id_photo and
	  iherba_roi.id=iherba_roi_answers_pattern.id_roi 
	  and
	  iherba_tags.id_tag = iherba_roi_tag.id_tag and iherba_roi_tag.id_roi = iherba_roi.id group by iherba_roi.id";

  $lignes_reponse = mysql_query($requete_lignes_pattern);

  $liste_roi= array();
  $liste_roi_tag= array();
  if(mysql_num_rows($lignes_reponse)>0)
    {
      while ($ligne = mysql_fetch_array($lignes_reponse)) {
		$liste_roi[] = $ligne['id'];
		$liste_roi_tag[] = $ligne['tag'];
	    }
    }
    

  foreach($liste_roi as $key => $value)
    {
    $content .= "<img src=/medias/roi_vignettes/roi_".$value.".jpg alt='".$liste_roi_tag[$key] ."  : $libellespeciesname ' >";
    $requete_lignes_pattern="select iherba_roi_answers_pattern.id_roi,
	  iherba_roi_answers_pattern.id_question,
	  iherba_roi_answers_pattern.id_answer_most_common,iherba_roi_answers_pattern.prob_most_common,	iherba_roi_answers_pattern.id_just_less_common,	iherba_roi_answers_pattern.prob_just_less,
	  iherba_question.choice_explicitation_one , iherba_question.choice_explicitation_two_seldom , iherba_question.choice_explicitation_two_often , iherba_question.choice_detail" 
     
      . " , iherba_roi_answers_pattern.id AS lineid " .
      
      "from iherba_roi_answers_pattern,iherba_roi,iherba_photos,iherba_question
	  where iherba_roi.id = $value and iherba_photos.id_obs=$id_obs and
	  iherba_photos.idphotos=iherba_roi.id_photo and
	  iherba_roi.id=iherba_roi_answers_pattern.id_roi and iherba_question.id_langue='$langue' 
	  and
	  iherba_roi_answers_pattern.id_question = iherba_question.id_question  ";
    //$content .= "<!-- $requete_lignes_pattern -->";
    $lignes_reponse = mysql_query($requete_lignes_pattern); 
    if(mysql_num_rows($lignes_reponse)>0)
      {
	$content .= "<br>".$explication." <br>";
	while ($ligne = mysql_fetch_array($lignes_reponse)) { 
	  $content .= build_response($ligne,$cibleaction,$show_delete_button)."<br>";
	}
      }
    }
  
  return $content;
}
/*
//charge un tableau decrivant les caracteres de l'observation en parametre
function charge_description($id_obs)
{	
  bd_connect();
  $requete_lignes_pattern="select iherba_roi_answers_pattern.id_roi,
	iherba_roi_answers_pattern.id_question,
	iherba_roi_answers_pattern.id_answer_most_common
	from iherba_roi_answers_pattern,iherba_roi,iherba_photos
	where iherba_photos.id_obs=$id_obs and
	iherba_photos.idphotos=iherba_roi.id_photo and
	iherba_roi.id=iherba_roi_answers_pattern.id_roi";
  $resultat = mysql_query($requete_lignes_pattern);   
  $i=0;
  while ($v = mysql_fetch_array($resultat)) {  
    $description[] = $v; 
  }
  return $description;
}
*/

/*fonction qui permet d'afficher la carte avec la longitude et la latitude de la table observations*/
// dans le cas de l'affciahe du detail d"une observation
function fairecarte($latitude,$longitude){
  $content=file_get_contents(template_carte);
  $content = str_replace('###longitude###',$longitude,$content);
  $content = str_replace('###latitude###',$latitude,$content);
  return $content;
}


function voir_indicateurs_area($idarea){
  
  $sql_area = "SELECT * from iherba_area  where uid_set = $idarea ";	
  $result = mysql_query ($sql_area) or die ();
  $ligne= mysql_fetch_array($result);
 
  $content = ' <div style="text-align: center;"> <h1><strong>'. $ligne[areaname]." </strong></h1><br></div>";
  $content .= "<br><img width=800 src=http://calcul.indicateurs-biodiversite.com/upload_files/GRAPH_4_10repetitions_setid_$idarea.png>";
  $content .= "<br><img width=800 src=http://calcul.indicateurs-biodiversite.com/upload_files/GRAPH_3_10repetitions_setid_$idarea.png>";
  $content .= "<br><img width=800 src=http://calcul.indicateurs-biodiversite.com/upload_files/GRAPH_5_10repetitions_setid_$idarea.png>";
  $content .= "<br><img width=800 src=http://calcul.indicateurs-biodiversite.com/upload_files/GRAPH_2_setid_$idarea.png>";
  $content .= "<br><img width=800 src=http://calcul.indicateurs-biodiversite.com/upload_files/GRAPH_1_setid_$idarea.png>";
  return $content ;
}

/* fonction qui permet d'afficher toutes les observations  determinÈes */
function liste_espece($monobjet,$numuser = 0,$mylanguage='en'){
    $content= "";
    bd_connect();
    if($numuser==0){  
      $limitnb = "";//"limit 0,75";
      $where = "iherba_observations.public='oui' ";
      }
      else {
      $limitnb = "";//"limit 0,175";
      $where = "iherba_observations.id_user=".$numuser;
      }
    $where .= " AND " . get_requete_where_sousdomaine() ; 
    $order = " order by iherba_determination.famille,iherba_determination.genre ";
    
    
    $sql_list_carto_select = "SELECT distinct iherba_observations.idobs,iherba_observations.longitude,iherba_observations.latitude,iherba_observations.commentaires,iherba_photos.nom_photo_final,iherba_observations.deposit_timestamp ,iherba_determination.nom_commun,iherba_determination.nom_scientifique,iherba_determination.famille,iherba_determination.genre";
    $sql_list_carto_select_family = "SELECT distinct iherba_determination.famille";
    $sql_list_carto_select_genre = "SELECT distinct iherba_determination.genre";
    $sql_list_carto_select_espece = "SELECT distinct iherba_determination.tropicosid";
    $sql_list_carto_select_nbobs = "SELECT distinct iherba_observations.idobs ";	

    $sql_list_carto_from = " FROM iherba_photos,iherba_observations ,iherba_determination where iherba_observations.latitude !=0 AND iherba_observations.idobs=iherba_photos.id_obs ";
    $sql_list_carto_from .= " AND iherba_determination.`tropicosfamilyid` != '' AND iherba_observations.idobs=iherba_determination.id_obs and $where ";
    //$sql_list_carto_from .= " group by iherba_determination.tropicosid ".$order;
    
    $sql_family = "$sql_list_carto_select_family  , count(iherba_observations.idobs) $sql_list_carto_from "." group by iherba_determination.tropicosid ".$order;
    $result = mysql_query ($sql_family) or die ($sql_family);
    $nb_lignes_family=mysql_num_rows($result);

   while( $ligne= mysql_fetch_array($result))
      {
	$nb_obs_famile[] = $ligne;
      }
  //echo  "<!-- sql_family $sql_family  -->";
  
    $sql_genre = "$sql_list_carto_select_genre   $sql_list_carto_from "." group by iherba_determination.genre ".$order;
    $result = mysql_query ($sql_genre) or die ();
    $nb_lignes_genre = mysql_num_rows($result);
    
    $sql_espece = "$sql_list_carto_select_espece  , count(iherba_observations.idobs)  $sql_list_carto_from "." group by iherba_determination.tropicosid  ".$order;
    $result_species_list = mysql_query ($sql_espece) or die ();
    $nb_lignes_especes = mysql_num_rows($result_species_list);
    while( $ligne= mysql_fetch_array($result_species_list))
      {
	$nb_obs_espece[] = $ligne;
      }
    
    $sql_nb_obs = "$sql_list_carto_select_nbobs from iherba_observations where $where";
    $result = mysql_query ($sql_nb_obs) or die ();
    $nb_lignes_nb_obs = mysql_num_rows($result);
    //echo "<!-- $nb_lignes_nb_obs $sql_nb_obs -->";
    
    $sql_list_carto = "$sql_list_carto_select $sql_list_carto_from "." group by iherba_determination.tropicosid ".$order." $limitnb";
    
    echo  "<!-- sql_espece $sql_espece  -->";
    echo  "<!-- nb obs esp ". print_r($nb_obs_espece,true) ." -->";
    
    $result=mysql_query ($sql_list_carto) or die ();
    $nb_lignes_resultats=mysql_num_rows($result);
    

    $content .= "<h3>";
    if (!is_sousdomaine_www())
	  {
	  $content .= get_string_language_sql("ws_speciesinventory_title",$mylanguage)."&nbsp;".get_description_sousdomaine()."<br/>";
	  }
	  else
	  $content .= get_string_language_sql("ws_speciesinventory_title",$mylanguage)."</br>";
    $content .= "</h3>";
    if($nb_lignes_resultats ==0)
	  $content.= "<h3>".get_string_language_sql("ws_nospeciesobservation",$mylanguage)."</h3>";
    $content.= "<h2>".get_string_language_sql("ws_inventory_number_total_observation",$mylanguage)." $nb_lignes_nb_obs </h2>";

    $content.= "<h2>".get_string_language_sql("ws_inventory_number_distinctfamily",$mylanguage)." $nb_lignes_family </h2>";
    $content.= "<h2>".get_string_language_sql("ws_inventory_number_distinctgenus",$mylanguage)." $nb_lignes_genre </h2>";
    $content.= "<h2>".get_string_language_sql("ws_inventory_number_distinctspecies",$mylanguage)." $nb_lignes_especes </h2>";


    $repertoire=repertoire_vignettes;
    $current = array();
    while ($donnees = mysql_fetch_array($result)){
	    $image=repertoire_vignettes."/".$donnees['nom_photo_final'];
	    
	    if($donnees['famille']!=$current['famille'])
		    $content .= "<br> <font size=+2>Famille : ".$donnees['famille']."</font><br>";
	    if($donnees['genre']!=$current['genre'])
		    $content .= "<br> &nbsp;&nbsp;&nbsp;&nbsp; <font size=+1>Genre : ".$donnees['genre']."</font><br>";
	    $content.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$image.'" border="2" width="100"  />
			<a href=http://'.$_SERVER['HTTP_HOST'].'/index.php?id=detail&numero_observation='.$donnees['idobs'].'>'.
	    ' '.$donnees['nom_commun']."/".$donnees['nom_scientifique']."</a>";
	    
	    $current['famille'] = $donnees['famille'];
	    $current['genre'] = $donnees['genre'];
	    $content.= "<br>";
    }
	  
    
    return $content;
}

function getRewritingObservation() {
	switch($GLOBALS['TSFE']->sys_language_uid) {
      case 1: // Fran¡ais
        $rewriting = "url_rewriting_fr";
        break;
      case 2: // Anglais
        $rewriting = "url_rewriting_en";
        break;
      default: // Par d»faut
        $rewriting = "url_rewriting_en";
    }

    return $rewriting;
}


/* fonction qui permet d'afficher toutes les observations publiques de tous les utilisateurs sur la carte ? l'aide de marqueurs.
 * Lorsqu'on clique sur le marqueur, une bulle appara”t avec la longitude et la latitude du lieu ainsi qu'une des photos
 * constituant l'observation*/
function afficher_carte_observations($monobjet,$numuser = 0,$area = null){
	$content= "";
	
	bd_connect();
	if($numuser==0){
		$limitnb = " order by iherba_observations.idobs DESC limit 0,250"; 
		$where = "iherba_observations.public='oui' ";
		
	}
	else {
	$limitnb = ""; 
	$where = "iherba_observations.id_user=".$numuser;
	}
	$where .= " AND " . get_requete_where_sousdomaine() ; 
	
	$sql_list_carto = "SELECT iherba_observations.idobs,iherba_observations.longitude,iherba_observations.latitude,iherba_observations.commentaires,iherba_photos.nom_photo_final,iherba_observations.deposit_timestamp, iherba_observations.url_rewriting_fr, iherba_observations.url_rewriting_en ";
	$sql_list_carto .= " FROM iherba_photos,iherba_observations  where iherba_observations.latitude !=0 AND iherba_observations.idobs=iherba_photos.id_obs ";
	$sql_list_carto .= "  and $where group by iherba_observations.idobs ".$limitnb;
	//echo $sql_list_carto;die();
	//$content .= "<!-- sqlcarto $sql_list_carto  -->";
	$result=mysql_query ($sql_list_carto) or die ();
	$nb_lignes_resultats=mysql_num_rows($result);
	
	$content .= "<h3>";
	if (!is_sousdomaine_www())
	      {
	      $content .= $monobjet->pi_getLL('lastzoneobservation', '', 1).get_description_sousdomaine()."<br/>";
	      }
	      else
	      $content .= $monobjet->pi_getLL('lastwebsiteobservation', '', 1) ."</br>";
	$content .= "</h3>";
	if($nb_lignes_resultats >0)
	      $content.=file_get_contents(debut_carto_aff_multi);
	      else
	      $content.= "<h3>".$monobjet->pi_getLL('noobservation', '', 1)."</h3>";

	
	$i=0;
	$repertoire=repertoire_vignettes;

	// Pid de la page Observation
	$pid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['observation.']['pid'];

	// On g»nere un lien vers la page Observation
	$linkObservation = $_SERVER['HTTP_HOST'] . $monobjet->pi_getPageLink(21) . $monobjet->pi_getLL('detail') . '/';

	$rewriting = getRewritingObservation();
		       
	while ($donnees = mysql_fetch_array($result)){
		//$photo[$i]=$donnees['nom_photo_final'];
		//$image="$repertoire/$photo[$i]";
		$image=repertoire_vignettes."/".$donnees['nom_photo_final'];

		// On g»nÀre le lien
		if(empty($donnees[$rewriting])) {
			$link = $donnees['idobs'];
		} else {
			$link = $donnees[$rewriting] . '-' . $donnees['idobs'];
		}
		$content.='
			    var latitude=\''.$donnees['latitude'].'\';
			    var longitude=\''.$donnees['longitude'].'\';
			    var point = new google.maps.LatLng(latitude,longitude);';
		$content.='
			    var marker = new google.maps.Marker({
				    position: point,
				    map: map
			    });  //fin marker
			    
			    //on ajoute ? chaque fois la position du marqueur nouvellement cr»e
			    bounds.extend(marker.getPosition());;
			    
			    google.maps.event.addListener(marker, \'click\', function() {
				      var infowindow = new google.maps.InfoWindow({
					      content: \'\',
					      size: new google.maps.Size(30,30),
					      position:point
				  });
			    infowindow.open(map, this);
			    infowindow.setContent(\' <img src="'.$image.'" border="2" width="100"  /><br/><a target=_blank href=//'.$linkObservation . $link.'>'.
		$monobjet->pi_getLL('numeroObservation', '', 1).$donnees['idobs'].' </a>'.$donnees['nom_commun'].$donnees['nom_scientifique'].'<br/> Transmise le : '.$donnees['deposit_timestamp'] .' <br/> Note : '.str_replace("\n"," ",str_replace('"'," ",str_replace("\r"," ",str_replace("'"," ",$donnees['commentaires'])))).'  \');
					    })';
		//$i++;
	}
	      
	if($nb_lignes_resultats >0)
		$content.='
	      //la fonction fitBounds est utilis» pour trouver automatiquement le niveau de zoom optimum afin que le rectangle s\'int»gre dans la carte
	      map.fitBounds(bounds);
	      } //fin initialize
	      </script>
	      <script type="text/javascript"> window.onload = function() { initialize();     }</script>
	      <center><div id="map_canvas" style="width:500px; height:400px"></div></center> ';
	
	
	return $content;
}


/*Cette fonction permet de s»lectionner des zones de l'image */
function selectionner_zones_image($monobjet){
  $content="";
  $numero_observation=desamorcer($_GET['numero_observation']);
  $identifiant_photo=desamorcer($_GET['identifiant_photo']);
	
  bd_connect();
  /* On s»lectionne la photo sur laquelle l'utilisateur souhaite d»finir une zone d'int»rÕt */
  $sql="select nom_photo_final from iherba_photos where id_obs=$numero_observation and idphotos=$identifiant_photo";
  $result = mysql_query($sql)or die ('Erreur SQL !'.$sql.'<br />'.mysql_error());
  $row = mysql_fetch_assoc($result);
  $nom_photo_final=$row["nom_photo_final"];
  $image=repertoire_sources."/$nom_photo_final";
 //echo "<!-- imagesource  $image -->";
	
  $dim=getimagesize($image);
  $largeur_source=$dim[0];
  $hauteur_source=$dim[1]; //dimensions de l'image affich»e
 
    
  /* on souhaite que les dimensions de l'image ne d»passe pas 500.
   * On fixe donc la taille de la largeur ? 500, on calcule le facteur de r»duction appliqu»e ? la largeur de l'image initiale, puis on 
   * applique ce facteur de r»duction ? la hauteur de l'image.*/
  $largeur_affichage=500;
  $facteur_reduction_image=$largeur_source/500;
  $hauteur_affichage=$hauteur_source/$facteur_reduction_image."<br/>";
  $content.=$monobjet->pi_getLL('selectionZones','',1)."<br/>";
  $content.=$monobjet->pi_getLL('recommencerSelectionZones','',1)."<br/>";
  $content.='<div id="container" style="position:relative;"><img src="'."/".$nom_photo_final.'" id="mon_image" width="'.$largeur_affichage.'" height="'.$hauteur_affichage.'">';
  $content.='<script language="JavaScript" type="text/JavaScript">
	// On attend le chargement de la page pour appeler la fonction activate
	window.onload=function() {
	  activate(\'mon_image\');
	};

	// Fonction principale, qui prend en paramÀtre l\'id de l\'image concern»e
	function activate(id) {
		  // On r»cupÀre l\'»l»ment <img> par son id.
		  var el = document.getElementById(id),
	      // On stocke les dimensions de l\'image 
		  width = el.offsetWidth, height = el.offsetHeight,
	      // et on d»clare plusieurs autres variables
		  // down repr»sente l\'action du tra¡age. Actuellement on ne trace rien.
		  oX, oY, down = false,
		  div, s;
		  
		  
          // Lors du mousedown sur l\'image
		     el.onmousedown = function(e) {
			    // On stocke les coordonn»es X et Y
			    /*Sauvegarde la position horizontale en pixels (offsetX) et la position verticale en pixels
			   (offsetY) du curseur par rapport au coin sup»rieur gauche de l\'»l»ment qui a d»clench» un »v»nement.*/
			    oX = offset_X(el,e);
			    oY = offset_Y(el,e);
			    // On active le bool»en de tra¡age
			    down = true;
			    // On cr»e un »l»ment <div>
			    div = document.createElement(\'div\');
			    // On modifie son style
			    s = div.style;
			    // On le masque, pour commencer
			    s.display = \'none\';
			    // On le met en position absolue
			    s.position = \'absolute\';
			    // On le place ? l\'endroit du curseur
			    s.left = oX+\'px\';
			    s.top = oY+\'px\';
			    // Et on lui mets une bordure
			    s.border = \'2px solid #666666\';
		        s.background=\'rgba(150,150,256,0.3)\';
		        // Puis on l\'insÀre r»ellement dans le <div> contenant l\'image
			    this.parentNode.appendChild(div);
		        C = document.forms[\'carte\'][\'coordonnee\'];
		        C.value=\'\';
		        // Ces trois lignes permettent d\'empÕcher le comportement
			    // par d»faut lors du mousedown, autrement dit un drag\'n\'drop (glisser-d»poser)
			    // de l\'image totalement ind»sir»
			    if(e.preventDefault) { e.preventDefault(); }
			    else { e.returnValue = false; }
			    return false;
		    };

		       // Lorsqu\'on bouge la souris dans le document
			    document.body.onmousemove = function(e) {
			    // Si on est en train de tracer
				    if(down) {
					      // On stocke la position du curseur
					      // En consid»rant les deux bornes minimale et maximale
					      var newX = Math.max(Math.min(offset_X(el,e),width),0),
					      newY = Math.max(Math.min(offset_Y(el,e),height),0);
					      // On affiche le <div>
					      s.display = \'\';
					      // Et on le place, gr?ce aux 4 lignes suivantes
					      if(newX<oX) { s.left = newX+\'px\'; }
					      if(newY<oY) { s.top = newY+\'px\'; }
					      s.width = Math.abs(newX-oX)+\'px\';
					      s.height = Math.abs(newY-oY)+\'px\';
			        }
		      };

			  // Lorsqu\'on rel?che le clic
			  document.body.onmouseup = function(e) {
				    // Si on »tait en train de tracer
				    if(down) {
					      // On stocke la position du curseur
					      // En consid»rant les deux bornes minimale et maximale
					      var newX = Math.max(Math.min(offset_X(el,e),width),0),
					      newY =  Math.max(Math.min(offset_Y(el,e),height),0);
					      // On affiche le <div>
					      s.display = \'\';
					      // Et on le place, gr?ce aux 4 lignes suivantes
					      if(newX<oX) { s.left = newX+\'px\'; }
					      if(newY<oY) { s.top = newY+\'px\'; }
					      s.width = Math.abs(newX-oX)+\'px\';
					      s.height = Math.abs(newY-oY)+\'px\';
				
					      // On affiche les coordonn»es de d»part et d\'arriv»e du rectangle
					      //alert(\'D»part : (\'+oX+\',\'+oY+\')\nArriv»e : (\'+newX+\',\'+newY+\')\');
				          nbre = \'(\'+oX+\',\'+oY+\')\'+\' - \'+\'(\'+newX+\',\'+newY+\')\' ;
						  C.value = nbre;
				          document.getElementById("nombres").value +=  \'\'+oX+\',\'+oY+\',\'+newX+\',\'+newY+\' ;  \';
				    }
			    // On indique qu\'on a fini de tracer.
			    down = false;
			  }


	}


            // On  utilise ces deux fonctions ici pour r»cup»rer la position du curseur
			// relative ? l\'image lors des »v»nements
			// Permet de tenir compte des offsets des »l»ments parents,
			// du scroll de la page
			function offset_X(el,event){
			  if(event.offsetX) return event.offsetX;
			  var ox = -el.offsetLeft;
			  while(el=el.offsetParent){ ox += el.scrollLeft - el.offsetLeft; }
			  return event.clientX + ox;
			}
			
			function offset_Y(el,event){
			  if(event.offsetY) return event.offsetY;
			  var oy = -el.offsetTop;
			  while(el=el.offsetParent){ oy += el.scrollTop - el.offsetTop; }
			  return event.clientY + oy;
			}
		
		
			/* fonction de remise ? z»ro des champs du formulaire
			 * void window.location.reload() recharge la page en oours
			 *L\'appel ? cette fonction »quivalent ? l\'action du bouton Actualiser ou ? la touche F5 du navigateur.
			 *On remet la valeur du champ envoy» au formulaire ? z»ro */
			function raz() {
				//window.location.reload();
				document.getElementById("nombres").value ="";
				window.location.reload();
			}

    </script></div>';
  $paramlien = array(etape  => 2);
  $lienform =$monobjet->pi_getPageLink(30,'',$paramlien);
  //$content.='<form name="carte" method="post" action="index.php?id=30&etape=2&L='.$GLOBALS['TSFE']->sys_language_uid.'"/>
    		
  $content.='<form name="carte" method="post" action="'.$lienform.'"/>
    <input type="hidden" value="" name="nb" id="nombres"/><br />
	<input type="hidden" value="'.$image.'" id="mon_image" name="fichier" />
	<input type="hidden" value="'.$nom_photo_final.'" id="mon_image" name="nom_photo_final" />
	<input type="hidden" value="'.$largeur_affichage.'"  name="largeur_affichage"/>
	<input type="hidden" value="'.$hauteur_affichage.'"  name="hauteur_affichage"/>
	<input type="hidden" value="'.$facteur_reduction_image.'"  name="facteur_reduction_image"/>
	<input type="hidden" value="'.$identifiant_photo.'"  name="identifiant_photo"/>
	<INPUT type="submit"  value="'.$monobjet->pi_getLL('boutonValiderZones', '', 1).'" />
	<input type="reset" name="Effacer" value="'.$monobjet->pi_getLL('boutonEffacerZones', '', 1).'" onclick="raz();"><br/><br/>';
  $content.='<div id=controle style="DISPLAY: none">'.$monobjet->pi_getLL('coordonneesZoneSelectionnee', '', 1).'<br><input type="text" name="coordonnee"  size="21"/></div></form>';
	
  return $content;
}

function redimensionner_roi($nom,$target_size = "vignettes")
{
  $inclusive_square=130;
  if($target_size == "vignettes")$inclusive_square=130;
  if($target_size == "medium")$inclusive_square=320;

  $image=roi_sources."/$nom";
  $dim=getimagesize($image);  //la variable dim contiendra la taille de l'image pass»e en paramÀtre 
  $largeur=$dim[0];
  $hauteur=$dim[1];
	
  //calcul des nouvelles dimensions de l'image
  if($largeur>$hauteur){
    $new_hauteur=$hauteur*(($inclusive_square/$largeur));
    $new_largeur=$inclusive_square;
  }
  else {
    $new_largeur=$largeur*(($inclusive_square)/$hauteur);
    $new_hauteur=$inclusive_square;
  }
	

  // Redimensionnement
  $image_p = imagecreatetruecolor($new_largeur, $new_hauteur);
  $image_cree = imagecreatefromjpeg($image);
  imagecopyresampled($image_p, $image_cree, 0, 0, 0, 0, $new_largeur, $new_hauteur, $largeur, $hauteur);
	
  // on place l'image redimensionn»e dans le r»pertoire roi_vignettes
  imagejpeg($image_p,roi_sources."_$target_size/$nom", 100);
}


function detailler_zone($monobjet){
  $langue=$GLOBALS['TSFE']->config[config][language];//variable qui va nous permettre de conna”tre la langue de la page et donc de s»lectionner par la suite des champs dans la table iherba_tags_translation

  //$rep_roi=roi_sources; //r»pertoire o? l'on va stocker les zones d'int»rÕts
  $identifiant_photo=$_POST['identifiant_photo'];
  $fichier_image=$_POST['fichier'];
  $nom_photo_final=$_POST['nom_photo_final'];
  $largeur_affichage=$_POST['largeur_affichage'];
  $hauteur_affichage=$_POST['hauteur_affichage'];
  $facteur_reduction_image=$_POST['facteur_reduction_image'];
  $coordonnees_points=$_POST['nb']; //coordonn»es des zones d'int»rÕt
  $tab=explode(";",$coordonnees_points);
	
  for($i=0;$i<count($tab);$i++){
    $couple[$i]=explode(',',$tab[$i]); //couple contient les coordonn»es des couples des points des zones d'int»rÕt
  } 
   
  $compteur=1;
  $paramlien = array(etape  => 3);
  $lienform =$monobjet->pi_getPageLink(30,'',$paramlien);
	
  //$content='<form method="post" action="index.php?id=30&etape=3&L='.$GLOBALS['TSFE']->sys_language_uid.'"/>';
  $content='<form method="post" action="'.$lienform.'"/>';
  for($j=0;$j<count($couple);$j++){
    if($couple[$j][0]==0){
      break;
    }
    else{
		
      $larg1=min($couple[$j][0]+0,$couple[$j][2]+0);
      $haut1=min($couple[$j][1]+0,$couple[$j][3]+0);
      $larg2=max($couple[$j][0]+0,$couple[$j][2]+0);
      $haut2=max($couple[$j][1]+0,$couple[$j][3]+0);
			
			
      /* 2 points permettant de tracer le rectangle en pourcentage */
      $larg1_pourcentage=$larg1/$largeur_affichage;
      $haut1_pourcentage=$haut1/$hauteur_affichage;
      $larg2_pourcentage=$larg2/$largeur_affichage;
      $haut2_pourcentage=$haut2/$hauteur_affichage;
      $date_decoupe=date("d-m-Y");/* La date de d»coupe est la date du jour */
		   
      /*remplissage de la table iherba_roi qui contient les champs id, id_photo, date_decoupe, ainsi que les coordonn»es en pourcentage
       * (x1,y1,x2,y2) des deux points qui ont servi ? la d»finition de le zone d'int»rÕt */
      $sql_roi="insert into iherba_roi(id_photo,date_decoupe,x1,y1,x2,y2) values('$identifiant_photo','$date_decoupe','$larg1_pourcentage','$haut1_pourcentage','$larg2_pourcentage','$haut2_pourcentage')";
      $result_roi = mysql_query($sql_roi)or die ();
      $id_roi=mysql_insert_id(); /* On r»cupÀre l'identifiant de l'image*/
	  
			
      //calcul de la largeur et de la hauteur des zones s»lectionn»es
      $largeur=$larg2-$larg1;
      $largeur_pourcentage=$largeur/$largeur_affichage;
      $hauteur=$haut2-$haut1;
      $hauteur_pourcentage=$hauteur/$hauteur_affichage;
      //$content.="largeur zone : ".$largeur." hauteur zone : ".$hauteur."<br/>";
			
      $image = imagecreatefromjpeg($fichier_image);
      $image_d = imagecreatetruecolor($largeur*$facteur_reduction_image,$hauteur*$facteur_reduction_image);
      imagecopy($image_d, $image, 0, 0, $larg1*$facteur_reduction_image,$haut1*$facteur_reduction_image, $largeur*$facteur_reduction_image,$hauteur*$facteur_reduction_image);
      $montest ="roi_".$id_roi.".jpg";
      imagejpeg($image_d,roi_sources."/$montest", 95);
      $content.=redimensionner_roi($montest);/*on redimensionne la zone d'int»rÕt et on va placer la nouvelle image dans le r»pertoire
					       roi_vignettes*/
			
      if($compteur==1){
	$content.=$monobjet->pi_getLL('zoneSelectionnee', '', 1).'<br/><br/>';
      }
			 
      $content.="<img src='".roi_sources."/$montest'  width='$largeur' heigth='$hauteur'>"	;      
      $question="question$compteur";
      $content.='<input type="hidden" name="compteur" value="'.$compteur.'"> ';
      $num_roi="num_roi$compteur";
      $content.='<input type="hidden" name="'.$num_roi.'" value="'.$id_roi.'"> ';
		 
      /* Pour chacune des zones d'int»rÕt s»lectionn»e par l'utilisateur, on va lui demander sa nature, c'est-?-dire est ce qu'il s'agit 
       * de la fleur, de la feuille, de la plante entiÀre ou du fruit.
       * Les valeurs de ces diff»rents choix proviennent de la table iherba_tags et plus pr»cis»ment du champ id_tag (associ» au champ tag)*/
      bd_connect();
      $sql="select iherba_tags_translation.texte_question,iherba_tags.id_tag,iherba_tags.tag from iherba_tags,iherba_tags_translation 
			where iherba_tags.pid=0 and iherba_tags.id_genre=1 and iherba_tags_translation.id_tag=iherba_tags.id_tag and iherba_tags_translation.id_langue='$langue'";
      $result = mysql_query($sql)or die ('Erreur SQL !'.$sql.'<br />'.mysql_error());
      $content.='<br/>'.$monobjet->pi_getLL('natureZone', '', 1). '<br/>';
      while($row = mysql_fetch_assoc($result)){
	$id_tag=$row['id_tag'];
	$tag=$row['tag'];
	$content.='<input type="radio" name="'.$question.'" value="'.$id_tag.'" />'.$row['texte_question']."<br>";
      }
      $content.="<br/><br/>";
			
      $compteur++;
    }
			
  }
	          
  $content.='<br/><br/>'.$monobjet->pi_getLL('validerZone', '', 1).
    '<input type="submit" value="'.$monobjet->pi_getLL('boutonValider', '', 1).'">'.'</form>';
  return $content;

}

function decoupe_roi($fichier_image,$couple,$id_roi) // couple contient les coordonnes des 2 coins
{			
  $largeur_affichage =1;$hauteur_affichage=1;
  $larg1=min($couple[0]+0,$couple[2]+0);
  $haut1=min($couple[1]+0,$couple[3]+0);
  $larg2=max($couple[0]+0,$couple[2]+0);
  $haut2=max($couple[1]+0,$couple[3]+0);
			
  /* 2 points permettant de tracer le rectangle en pourcentage */
  $larg1_pourcentage=$larg1/$largeur_affichage;
  $haut1_pourcentage=$haut1/$hauteur_affichage;
  $larg2_pourcentage=$larg2/$largeur_affichage;
  $haut2_pourcentage=$haut2/$hauteur_affichage;
			
  //calcul de la largeur et de la hauteur des zones s»lectionn»es
  $largeur=$larg2-$larg1;
  $largeur_pourcentage=$largeur/$largeur_affichage;
  $hauteur=$haut2-$haut1;
  $hauteur_pourcentage=$hauteur/$hauteur_affichage;
  //echo "largeur zone : ".$largeur." hauteur zone : ".$hauteur."<br/>";
			
  $imagelu = imagecreatefromjpeg($fichier_image);
  $larg_ratio_lu=imagesx($imagelu);
  $haut_ratio_lu=imagesy($imagelu); 
			
  $image_d = imagecreatetruecolor($largeur*$larg_ratio_lu,$hauteur*$haut_ratio_lu);
  imagecopy($image_d, $imagelu, 0, 0, $larg_ratio_lu*$larg1,$haut_ratio_lu*$haut1, $larg_ratio_lu*$largeur,$haut_ratio_lu*$hauteur);
			
  $fichierroi ="roi_".$id_roi.".jpg";
  imagejpeg($image_d,roi_sources."/$fichierroi", 95);
  redimensionner_roi($fichierroi);/*on redimensionne la zone d'int»rÕt et on va placer la nouvelle image dans le r»pertoire roi_vignettes*/
			
}

function remplir_tables($monobjet){
  $content="";
  $idutilisateur=$GLOBALS['TSFE']->fe_user->user['uid'];
  $compteur=$_POST['compteur']; /* nombres de zones d'int»rets localis»es par l'utilisateur */
	
  for($i=0;$i<=$compteur;$i++){
    $nature_roi[]=$_POST["question$i"]; /*le tableau nature_roi contiendra les valeurs des boutons radio,c'est-?-dire la nature de la zone d»finie*/
    $numero_roi[]=$_POST["num_roi$i"];
  }
	
  /*remplissage de la table iherba_roi_tag qui contient les champs : id, id_roi, id_tag, id_user */
  bd_connect();
  for($i=1;$i<=$compteur;$i++){
    $sql_iherba_roi_tag="insert into iherba_roi_tag(id_roi,id_tag,id_user)values('$numero_roi[$i]','$nature_roi[$i]','$idutilisateur')";
    $result_iherba_roi_tag = mysql_query($sql_iherba_roi_tag)or die ('');
  }
  $content.=$monobjet->pi_getLL('finSelection', '', 1)."<br/>";
  $content.=$monobjet->pi_linkToPage($monobjet->pi_getLL('retourHerbier', '', 1),19,'',$paramlien);
  return $content;
}



function affiche_expertise($numero_observation,$cetobjet,$publication="liste",&$demandenom,$texteseul=0,$mylanguage='en')
{ 
  $display_reaction = array(
			    "sure" => "+++",
			    "probalyyes"=>"++",
			    "difficult"=>"??",
			    "probablynot"=>"--",
			    "no"=>"---"
			    );
  $content = "";
  if($cetobjet!=null)
      $mylanguage = language_iso_from_lang_id($cetobjet->cObj->data['sys_language_uid']);

  $demandenom =1;
  // define extension for the names of the strings
  if($texteseul==0){$finchamps ="_forweb"; $finligne = "<br/>";} else {$finchamps ="_formail";$finligne = " \n";}
  if($texteseul==0)$champscomment = 'web_comment'; else $champscomment = 'email_comment';
  //$sql_determination="select nom_commun,nom_scientifique,date,famille,genre,comment from iherba_determination where (nom_commun !='' OR nom_scientifique != '') AND id_obs=$numero_observation ";
  $sql_determination.="select iherba_determination.id , tropicosid, tropicosgenusid, tropicosfamilyid, nom_commun,nom_scientifique,date, famille,genre ,id_cases,tag_for_translation, iherba_determination_cases.$champscomment ,iherba_certitude_level.value as certitude_level, iherba_certitude_level.comment as certitude_comment,";
  $sql_determination.=" iherba_determination.comment,iherba_precision_level.value as precision_level,iherba_precision_level.$champscomment as precisioncomment from iherba_determination,iherba_determination_cases,iherba_certitude_level, iherba_precision_level ";
  
  $sql_determination.=" where  iherba_determination_cases.language = 'fr' and iherba_determination_cases.id_cases = iherba_determination.comment_case AND iherba_determination.precision_level = iherba_precision_level.value AND iherba_determination.certitude_level = iherba_certitude_level.value ";
  $sql_determination.=" AND iherba_determination.id_obs=$numero_observation ";
  $sql_determination.= " order by creation_timestamp desc";
  
  // if list in a compact mode, only last two determination
  if($publication=="liste")
    $sql_determination .= " limit 2";
  // if text only, probably for sending a mail, only the last one
  if($texteseul!=0)$sql_determination .= " limit 1";

  $result_determination = mysql_query($sql_determination) or die ();
  $num_rows = mysql_num_rows($result_determination);
	
  if($num_rows !=0){
    if($texteseul!=2)$content.= get_string_language_sql('expertises'.$finchamps,$mylanguage).$finligne;
  }
	
  while ($row_determination = mysql_fetch_assoc($result_determination)) {
    //$demandenom = 0; //si un nom deja donn» on ne demande plus
    $nom_commun=$row_determination["nom_commun"];
    $nom_scientifique=$row_determination["nom_scientifique"];
    $date=$row_determination["date"];
    
    list( $jour,$mois, $annee,) = explode("-", $date);
    if($texteseul==0)
      $content.= get_string_language_sql('expertise_prefixe_date',$mylanguage)." " .$jour."-".$mois."-".$annee." ";
    
    
    if($nom_commun!=""){
      $content.= get_string_language_sql('nomCommun'.$finchamps,$mylanguage) .$nom_commun . " ";
    }
    
    $naming_string = "";
if($texteseul==0)
{
    $current_url = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
    if(strpos($current_url,'?')===false)$current_url .= '?addzoom=1';
}	  
    $title_link = " title='".get_string_language_sql('ws_view_limitation_alt_add_limit',$mylanguage)."' class=drillzoom ";
    if($nom_scientifique !=""){
       $naming_string.= get_string_language_sql('nom_scientifique'.$finchamps,$mylanguage) .$nom_scientifique." ";
      if(($row_determination["tropicosid"]!="")&&($texteseul==0))
	{
	  $url=$current_url."&species_limitation=species:".$row_determination["tropicosid"];
	  $naming_string.=  "<a  rel=\"nofollow,noindex\"  href=$url $title_link >&nbsp;&gt;</a>";
	}
	else
	$naming_string.= get_string_language_sql('nom_scientifique'.$finchamps,$mylanguage) .$nom_scientifique." ";
      if(($row_determination["famille"]!="")||($row_determination["genre"]!=""))
	{
	  if(($row_determination["famille"]!="")||($row_determination["genre"]!=""))$virgule=", ";else $virgule=" ";
	  if($texteseul==0)$naming_string.= "&nbsp;&nbsp;";
	  if(($row_determination["tropicosgenusid"]!="")&&($row_determination["tropicosfamilyid"]!="")&&($texteseul==0))
	    {
	      $urlgenus=$current_url."&species_limitation=genus:".$row_determination["tropicosgenusid"];
	      $urlfamily=$current_url."&species_limitation=family:".$row_determination["tropicosfamilyid"];
	      $naming_string.=  "[".$row_determination["genre"]."<a  rel=\"nofollow,noindex\" href=$urlgenus $title_link >&nbsp;&gt;</a>".$virgule.$row_determination["famille"]."<a  href=$urlfamily $title_link >&nbsp;&gt;</a>"."]";
	    }
	    else
	    $naming_string.="[".$row_determination["genre"].$virgule.$row_determination["famille"]."]";
	}
    }
    
    $content.= $naming_string;
    
    if($publication!="liste")
      {
	if($row_determination["precision_level"]!=0)
	  {
	    if($texteseul==0)
	      $content.=' <img src="/interface/target_'.$row_determination["precision_level"].'.gif" width=24 title="'.$row_determination["precisioncomment"].'"> ';
	    else
	      $content.= $finligne.get_string_language_sql('aboutprecision_formail',$mylanguage) .$row_determination["precisioncomment"];
	  }
	if($row_determination["certitude_level"]!=0)
	  {
	    if($texteseul==0)
	      $content.=' <img src="/interface/certitude_'.$row_determination["certitude_level"].'.gif"  title="'.get_string_language_sql('aboutcertitude'.$finchamps,$mylanguage)." ".$row_determination["certitude_comment"].'"> ';
	    else
	      $content.= $finligne.get_string_language_sql('aboutcertitude_formail',$mylanguage).$row_determination["certitude_comment"];
	  }
	
	  if($texteseul==0)
	    if($publication!="liste")
	      {
	      $numero_id_determination = $row_determination["id"];
	      $paramlien = array(numero_observation  => $numero_observation,numero_det  => $numero_id_determination, sens => "minus", etape => 'comment',check=>456789);
	      $lien_minus=$cetobjet->pi_linkToPage('<img alt="je ne suis pas d\'accord avec ce nom" title="je ne suis pas d\'accord avec ce nom" src="/interface/minus16.png">',87,'',$paramlien);
	      $paramlien = array(numero_observation  => $numero_observation,numero_det  => $numero_id_determination, sens => "plus", etape => 'comment', check=>456789);
	      $lien_plus=$cetobjet->pi_linkToPage('<img src="/interface/plus16.png" alt="je suis d\'accord avec ce nom" title="je suis d\'accord avec ce nom">',87,'',$paramlien);

	      $content.='&nbsp;'.$lien_minus.$lien_plus;
	      
	      $sql_reaction = "select * from  iherba_determination_reaction where id_determination = $numero_id_determination and disabled = 0 ";
	      $result_reaction = mysql_query($sql_reaction) or die ();
	      if( mysql_num_rows($result_reaction)>0)
		{
		$content.= " ( ";
		$first_iteration = 1;
		while ($row_reaction = mysql_fetch_assoc($result_reaction)) {
		  if($row_reaction['reactioncase']!="")
		    {
		    if($first_iteration==0)$content.= ",";
		     $content.=  "&nbsp;".$display_reaction[$row_reaction['reactioncase']];
		     $onecomment = desamorcer($row_reaction['comment']);
		     if(strlen($onecomment)>35)$onecomment="";
		     if($onecomment!="")
			$content .= " :" . $onecomment;
		     $content .= "&nbsp;";
		     $first_iteration = 0;
		    }
		  }
		$content.= " ) ";
		}
	      
	      }
	}

	if($row_determination["id_cases"]!=0){
		$content.= $finligne;
		$content.= get_string_language_sql('expertise_abstract_note',$mylanguage);
		//$content.= " = ";
		$content.= get_string_language_sql('expertise_legend_case_'.$row_determination["tag_for_translation"].$finchamps,$mylanguage); 
	}
	if($row_determination["comment"]!=""){
		$content.= $finligne;
		$content.= get_string_language_sql('expertise_abstract_note',$mylanguage);;
		$content.=$row_determination["comment"];
	}
	
    if($texteseul!=2)
      {		
      $content.= $finligne;
      $content.= $finligne;
      }
    else
      $content = utf8_encode($content);		
  }
  return $content;	
}

function language_domainename_mail_from_lang_iso($iso2c="en")
{
  $mydomain="";
  $iso2c = strtolower(substr($iso2c,0,2));
  if($iso2c=="")$iso2c='en';
  
  if($iso2c=="en")$mydomain="iherbarium.org";
  if($iso2c=="fr")$mydomain="iherbarium.fr";
  if($iso2c=="pt")$mydomain="iherbarium.com.br";
  if($iso2c=="it")$mydomain="iherbarium.it";
  if($iso2c=="de")$mydomain="iherbarium.de";
  
  if($mydomain=="")$mydomain="iherbarium.org";
  return $mydomain;
}

function language_url_observation_from_lang_iso($iso2c="en")
{
  $mydomain="";
  $iso2c = strtolower(substr($iso2c,0,2));
  if($iso2c=="")$iso2c='en';
  
  if($iso2c=="en")$mydomain="www.iherbarium.org/observation/data/";
  if($iso2c=="fr")$mydomain="www.iherbarium.fr/observation/data/";
  if($iso2c=="pt")$mydomain="www.iherbarium.com.br/observation/data/";
  if($iso2c=="it")$mydomain="www.iherbarium.it/observation/data/";
  if($iso2c=="de")$mydomain="www.iherbarium.de/observation/data/";
  
  if($mydomain=="")$mydomain="www.iherbarium.org/observation/data/";
  return $mydomain;
}


function language_iso_from_lang_id($sys_language_uid=0)
{
$mylanguage='en';
if($sys_language_uid==1)$mylanguage='fr';
if($sys_language_uid==2)$mylanguage='pt';
if($sys_language_uid==3)$mylanguage='de';
if($sys_language_uid==4)$mylanguage='it';
if($sys_language_uid==5)$mylanguage='sp';
return $mylanguage;
}

function affiche_une_observation_dans_liste($cetobjet,$numobs,$publication="public")
{
  if($cetobjet!=null)
      $mylanguage = language_iso_from_lang_id($cetobjet->cObj->data['sys_language_uid']);

  $expertise = affiche_expertise($numobs,$cetobjet,"liste",$demandenom,0);
  //echo "<!-- $expertise -->";
  if(isset($_GET['nouveau']))if(strlen($expertise)>5)return "";

  $rewriting = getRewritingObservation();
	
  $content ='<div id="bloc_contenu"><h1>';
  
  
  $aumoinsunnom=0;
  $limitsqlphoto ="";
  if($publication == "public"){
    $limitsqlphoto .= " limit 0,3";
    $sql_iduser="select id_user,name, $rewriting , computed_usable_for_similarity from iherba_observations,fe_users where idobs=$numobs and id_user = uid and moderation = 0 ";
    $result_iduser = mysql_query($sql_iduser)or die ();
    if($row_iduser = mysql_fetch_assoc($result_iduser)) {
      $iduser=$row_iduser["id_user"];
      $iduser_name=$row_iduser["name"];
      $rewriting = $row_iduser[$rewriting];
    }
    else {
      return "";// cas moderation = 1 surement
    }
  }
  
  
      
  $sqlphoto="select date_depot,nom_photo_final from iherba_photos where id_obs=$numobs ".$limitsqlphoto;
  $result_photo = mysql_query($sqlphoto) or die ();
  $compteur=0; //Compteur
  while ($row2 = mysql_fetch_assoc($result_photo)) {
    $date_depot=$row2["date_depot"];
    list($annee, $mois, $jour) = explode("-", $date_depot);
    $nom_photo_final=$row2["nom_photo_final"];
			
    $paramlien = array(numero_observation  => $numobs, check=>456789);
    if($compteur==0){
      
      $entete_content = get_string_language_sql("ws_observation_send_on",$mylanguage).$jour.'-'.$mois.'-'.$annee;
      if($row_iduser['computed_usable_for_similarity']=="1")$entete_content .= " (Modele) ";
      if (isset($iduser)){
	  $entete_content.=get_string_language_sql("ws_observation_author",$mylanguage).$iduser_name;

	  $title_link = " title='".get_string_language_sql('ws_view_limitation_alt_add_limit',$mylanguage)."' class=drillzoom ";
	  $current_url = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
	  if(strpos($current_url,'?')===false)$current_url .= '?addzoom=1';
	  $urluser = $current_url."&user_limitation=".$iduser;
	  $entete_content.=  "<a rel=\"nofollow,noindex\"  href=$urluser $title_link >&nbsp;&gt;</a> \n";
	    
	}
      $entete_content.= "<br/><br/> </h1>";
      $content.= $entete_content . '<div id="bloc_contenu_texte">'; 
      $content.="<br/>\n";
	        
	                     
      $content.=affiche_expertise($numobs,$cetobjet,"liste",$demandenom,0);
	
      if(($publication == "prive") ){
	$content.=get_string_language_sql("ws_observation_before_number",$mylanguage).$numobs."<br/><br/>";
      }   
					
      if( $publication=="prive"){
	$content.="<br/>";
	if($demandenom>0)
	  {
	    $content.=$cetobjet->pi_linkToPage(get_string_language_sql("ws_give_a_plant_name",$mylanguage),31,'',$paramlien);
	    $content.= "<br/>";
	  }
      }
      $content.= get_string_language_sql("ws_list_gotoobservationdetail",$mylanguage)."<br/>";
    }	
    $image=repertoire_vignettes."/$nom_photo_final";
    $linkObservation = $cetobjet->pi_getPageLink(21) . $cetobjet->pi_getLL('detail') . '/';
    
    // link to the most friendly url
    if(empty($rewriting)) {
	    $link = $numobs;
    } else {
	    $link = $rewriting . '-' . $numobs;
    }
    $content.= '<a href="'.$linkObservation . $link .'"><img src="'.$image.'" ></a>';
    
    
    $compteur++;
  }
  
  
  //if no photo, return empty content
  if($compteur==0) return ;
    
  $content.="</div><!--fin bloc_contenu_texte--></div><!--fin bloc_contenu--> \n";  
  return $content;
}

/*Les fonctions qui suivent permettent d'afficher le r»sultat d'une recherche d'un utilisateur */

function type_recherche()
{
  return 1;
}

function rechercher_plantes_par_criteres($cetobjet){
  $content="";
  $type_recherche_plante=type_recherche();
	
  if(isset($_POST['searchedname']) && ($type_recherche_plante==1)){
    $critere=$_POST['searchedname'];
    $content.=resultat_recherche_plantes("public",$critere,$cetobjet);
  }
  elseif(isset($_POST['searchedname']) && ($type_recherche_plante==0)){
    $critere=$_POST['searchedname'];
    $content.=resultat_recherche_plantes("prive",$critere,$cetobjet);
  }
  return $content;
}

function resultat_recherche_plantes($type_recherche,$critere,$cetobjet)
{
  $content="";
  $sql_recherche_critere="select distinct iherba_observations.idobs 
	from iherba_observations,iherba_determination
	where iherba_observations.idobs=iherba_determination.id_obs and (iherba_determination.nom_commun LIKE '%$critere%' or iherba_determination.nom_scientifique LIKE '%$critere%'
	or iherba_determination.famille LIKE '%$critere%' or iherba_determination.genre LIKE '%$critere%' or iherba_determination.espece LIKE '%$critere%' )";
	
  if($type_recherche=="prive"){
    $id=$GLOBALS['TSFE']->fe_user->user['uid'];//identifiant utilisateur
    $sql_recherche_critere.="and iherba_observations.id_user='$id'";
  }
		
  $result_recherche_critere= mysql_query($sql_recherche_critere) or die ();
  while ($row_recherche_critere= mysql_fetch_assoc($result_recherche_critere)) {
    $idobs = $row_recherche_critere["idobs"];
    $content.= affiche_une_observation_dans_liste($cetobjet, $idobs,$type_recherche);
  }
	
  return $content;
}		

// Kuba ->

function viewAsOptionFunction($valueFieldFun, $textFieldFun) {
  
  $viewFunction =
    function($row) use ($valueFieldFun, $textFieldFun) {
    return '<option value="' . $valueFieldFun($row) . '">' . $textFieldFun($row) . '</option>';
  };
  
  return $viewFunction;
  
}

function viewArrayAsSelect($selectId, $valueFieldFun, $textFieldFun, $array) {
  $lines = array();
  
  // Display Rows as Select choice.
  
  // Beginning
  $lines[] = '<select id="' . $selectId . '" name="' . $selectId . '">';
  
  // Options
  $viewAsOptionFunction = viewAsOptionFunction($valueFieldFun, $textFieldFun);

  $options = array_map($viewAsOptionFunction, $array);
  
  $lines[] = implode("\n", $options);
  
  // End
  $lines[] = '</select>';
  
  $content = implode("\n", $lines);

  return $content;
}

function extractArrayFieldFunction ($fieldName) { 
  return function($array) use ($fieldName) { return $array[$fieldName]; };
}

function extractObjectFieldFunction ($fieldName) { 
  return function($object) use ($fieldName) { return $object->$fieldName; };
}
  
function queryToRowsArray($query, $connect = false) {
  
  if($connect)
    bd_connect();
  
  $result = mysql_query($query) or die(mysql_error());
  
  $rows = array();
  while ($row = mysql_fetch_array($result)) { 
    $rows[] = $row;
  }
  
  return $rows;
}

// <- Kuba

function relocate_observation($numobs,$localisation,$id_user)
{ // record a new localisation
  
  $sql_list_carto = "SELECT * from iherba_observations where iherba_observations.idobs = $numobs";
  bd_connect();
  $result=mysql_query ($sql_list_carto) or die ("");
  $ligne = mysql_fetch_array($result);
  //if($ligne['id_user']!=$id_user)die("h");// tentative de hack
  
  $gps = explode(',',$localisation);
  $sql_carto = "update iherba_observations set latitude = '$gps[0]' , longitude = '$gps[1]'  where iherba_observations.idobs = $numobs";
  $result=mysql_query ($sql_carto) or die ("");
  //echo "<!-- $sql_carto  -->";
}
?>
