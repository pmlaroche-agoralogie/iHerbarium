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

function get_string_language(&$tableau,$identifiant,$chosen_language,$default_language='en')
{
  if(isset($tableau[$chosen_language][$identifiant]))
    {$key = $tableau[$chosen_language][$identifiant];
    //if(!mb_check_encoding($key, 'UTF-8')) $key = utf8_encode($key); 
   return $key;
    }
   else
   return utf8_encode($tableau[$default_language][$identifiant]);
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
    $content = $ligne[choice_explicitation_one];
  else
    {
      if($ligne[prob_most_common]>80)
	$content = $ligne['choice_explicitation_two_seldom'];
      else
	$content = $ligne['choice_explicitation_two_often'];
    }

  $content = str_replace("#1",$reponsespossibles[$ligne['id_answer_most_common']],$content);
  $textexplication = str_replace("#2",$reponsespossibles[$ligne['id_just_less_common']],$content);

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

function information_analyse($id_obs,$idlangue,$explication="Region of interest show the following answers ",$cibleaction="##",$show_delete_button=0)
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
  bd_connect();
//same request as the followinf one, but group by id of roi
$requete_lignes_pattern="select distinct iherba_roi.id,iherba_roi_answers_pattern.id_roi,
        iherba_roi_answers_pattern.id_question,
        iherba_roi_answers_pattern.id_answer_most_common,iherba_roi_answers_pattern.prob_most_common,   iherba_roi_answers_pattern.id_just_less_common, iherba_roi_answers_pattern.prob_just_less,
        iherba_question.choice_explicitation_one , iherba_question.choice_explicitation_two_seldom , iherba_question.choice_explicitation_two_often , iherba_question.choice_detail"    /* Kuba -> */  . " , iherba_roi_answers_pattern.id AS lineid " . /* <- Kuba */
    "from iherba_roi_answers_pattern,iherba_roi,iherba_photos,iherba_question
        where iherba_photos.id_obs=$id_obs and
        iherba_photos.idphotos=iherba_roi.id_photo and
        iherba_roi.id=iherba_roi_answers_pattern.id_roi and iherba_question.id_langue='$langue'
        and
        iherba_roi_answers_pattern.id_question = iherba_question.id_question  group by iherba_roi.id";

  $lignes_reponse = mysql_query($requete_lignes_pattern);

$liste_roi= array();

  if(mysql_num_rows($lignes_reponse)>0)
    {
      while ($ligne = mysql_fetch_array($lignes_reponse)) {
		$liste_roi[] = $ligne['id'];
      }
    }


foreach($liste_roi as $key => $value)
{
$content .= "<img src=/medias/roi_vignettes/roi_".$value.".jpg>";
  $requete_lignes_pattern="select iherba_roi_answers_pattern.id_roi,
	iherba_roi_answers_pattern.id_question,
	iherba_roi_answers_pattern.id_answer_most_common,iherba_roi_answers_pattern.prob_most_common,	iherba_roi_answers_pattern.id_just_less_common,	iherba_roi_answers_pattern.prob_just_less,
	iherba_question.choice_explicitation_one , iherba_question.choice_explicitation_two_seldom , iherba_question.choice_explicitation_two_often , iherba_question.choice_detail" 
    /* Kuba -> */  . " , iherba_roi_answers_pattern.id AS lineid " . /* <- Kuba */
    "from iherba_roi_answers_pattern,iherba_roi,iherba_photos,iherba_question
	where iherba_roi.id = $value and iherba_photos.id_obs=$id_obs and
	iherba_photos.idphotos=iherba_roi.id_photo and
	iherba_roi.id=iherba_roi_answers_pattern.id_roi and iherba_question.id_langue='$langue' 
	and
	iherba_roi_answers_pattern.id_question = iherba_question.id_question  ";

  $lignes_reponse = mysql_query($requete_lignes_pattern); 
  if(mysql_num_rows($lignes_reponse)>0)
    {
      $content .= $explication.": <br>";
      while ($ligne = mysql_fetch_array($lignes_reponse)) { 
	$content .= build_response($ligne,$cibleaction,$show_delete_button)."<br>";
      }
    }
}
  return $content;
}

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

function calcule_liste_proche($description,$id_a_exclure=0)
{
  if( count($description)==0)return ; // pas de caractere defini
  $i = 1;
  $sql="";
  foreach ($description as $pattern) {   

    /*requete qui permet de s»lectioner pour chacun des patterns de l'observation consid»r»e les 
     * pattern des observations des experts qui ont les memes valeurs id_question et id_most_answer_common 
     * que l'observation "non expert" (celle que l'on cherche ? identifier)
     */
    $sql=$sql."(select iherba_observations.idobs,iherba_roi_answers_pattern.id_roi,
		iherba_roi_answers_pattern.id_question,
		iherba_roi_answers_pattern.id_answer_most_common
		from iherba_roi,iherba_photos,iherba_determination,iherba_roi_answers_pattern,iherba_observations
		where iherba_photos.idphotos=iherba_roi.id_photo and
		iherba_roi.id=iherba_roi_answers_pattern.id_roi and
		iherba_photos.id_obs=iherba_observations.idobs and
		iherba_observations.idobs=iherba_determination.id_obs 
		and iherba_roi_answers_pattern.id_question=".$pattern["id_question"];
    $sql=$sql." and iherba_roi_answers_pattern.id_answer_most_common=".$pattern["id_answer_most_common"];
    $sql=$sql." and iherba_determination.probabilite >50 )";
		
    if ($i < count($description)) { 
      $sql = $sql . " UNION "; 
				
    }		
    $i++;
  }   
	 
  $requete_obs_proche= mysql_query($sql);
  $nb_enregistrements=mysql_num_rows($requete_obs_proche); //mysql_num_rows Retourne le nombre de lignes r»sultats de la requÕte
  //echo "nombre de lignes r»sultats : ".$nb_enregistrements."<br/>";
  while($row_proche= mysql_fetch_assoc($requete_obs_proche) ){
    if($id_a_exclure !=$row_proche['idobs'])
      {
	$idobs_expertisees[]=$row_proche['idobs']; //on r»cupÀre les num»ro d'observation des experts dans un tableau
	$id_roi=$row_proche['id_roi'];
	$id_question=$row_proche['id_question'];
	$id_answer_most_common=$row_proche['id_answer_most_common'];
      }
    //echo "idobs : ".$idobs_expertisees." id roi : ".$id_roi." id question : ".$id_question." id answer most common : ".$id_answer_most_common."<br/>";
  }
  if(isset ($idobs_expertisees)){
    /*la fonction array_count_values retourne un tableau contenant les valeurs du tableau $idobs comme cl»s et leur fr»quence comme valeurs. */
    $occurencesIDOBSpartiel=array_count_values($idobs_expertisees); 
    //echo " tableau des idobservations et leurs occurences : <br/>";
    $maxcommun =0;
    foreach ( $occurencesIDOBSpartiel as $no => $max)
      if($max > $maxcommun)$maxcommun=$max;
		
    foreach ( $occurencesIDOBSpartiel as $no => $nb)
      if($nb ==  $maxcommun)$occurencesIDOBS[] = $no;
    //print_r($occurencesIDOBS);
    //echo "<br/><br/>";
		
    /*on r»cupÀre le nombre d'»l»ments du tableau $occurencesIDOBS
     * c'est ? dire le nombre d'observations expertis»es qui correspondent ? l'observation idobs que l'on cherche ? identifier*/
    $nb_elements=count($occurencesIDOBS);
		
    $pourcentage=100/$nb_elements;
    foreach ( $occurencesIDOBS as $no )
      {
	$vecteur['numobs'] = $no;
	$vecteur['proba'] = $pourcentage;
	$obs_prob[] = $vecteur;
      }
    //print_r($obs_prob);
  }
  else{
    /* si il n'existe pas de tableau $idobs_expertisees c'est ? dire s'il n'y a pas de plantes qui correspond */
    return ;
  }
  return $obs_prob;
}


/*fonction qui permet d'afficher la carte avec la longitude et la latitude de la table observations*/
// dans le cas de l'affciahe du detail d"une observation
function fairecarte($latitude,$longitude){
  $content=file_get_contents(template_carte);
  $content = str_replace('###longitude###',$longitude,$content);
  $content = str_replace('###latitude###',$latitude,$content);
  return $content;
}


/* fonction qui permet d'afficher toutes les observations  determinÈes */
function liste_espece($monobjet,$numuser = 0){
	$content= "";
	
	bd_connect();
	if($numuser==0){
		
		$limitnb = "limit 0,175";
		$where = "iherba_observations.public='oui' ";
		
	}
	else {
	$limitnb = "limit 0,175";
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
	$sql_list_carto_from .= " AND iherba_determination.`tropicosfamilyid` != '' AND iherba_observations.idobs=iherba_determination.id_obs and $where group by iherba_determination.tropicosid ".$order;
	
	$sql_family = "$sql_list_carto_select_family $sql_list_carto_from";
	$result = mysql_query ($sql_family) or die ();
	$nb_lignes_family=mysql_num_rows($result);
	
	$sql_genre = "$sql_list_carto_select_genre $sql_list_carto_from";
	$result = mysql_query ($sql_genre) or die ();
	$nb_lignes_genre = mysql_num_rows($result);
	
	 $sql_espece = "$sql_list_carto_select_espece $sql_list_carto_from";
	$result = mysql_query ($sql_espece) or die ();
	$nb_lignes_especes = mysql_num_rows($result);
	
 $sql_nb_obs = "$sql_list_carto_select_nbobs from iherba_observations where $where";
       $result = mysql_query ($sql_nb_obs) or die ();
        $nb_lignes_nb_obs = mysql_num_rows($result);
echo "<!-- $nb_lignes_nb_obs $sql_nb_obs -->";
	
	$sql_list_carto = "$sql_list_carto_select $sql_list_carto_from $limitnb";
	
	echo  "<!-- sqlcarto $sql_list_carto  -->";
	$result=mysql_query ($sql_list_carto) or die ();
	$nb_lignes_resultats=mysql_num_rows($result);
	

	$content .= "<h3>";
	if (!is_sousdomaine_www())
	      {
	      $content .= $monobjet->pi_getLL('speciesinventory', '', 1)."&nbsp;".get_description_sousdomaine()."<br/>";
	      }
	      else
	      $content .= $monobjet->pi_getLL('speciesinventory', '', 1) ."</br>";
	$content .= "</h3>";
	if($nb_lignes_resultats ==0)
	      $content.= "<h3>".$monobjet->pi_getLL('nospeciesobservation', '', 1)."</h3>";
	$content.= "<h2>".$monobjet->pi_getLL('number_total_observation', '', 1)." $nb_lignes_nb_obs </h2>";

	$content.= "<h2>".$monobjet->pi_getLL('numberdistinctfamily', '', 1)." $nb_lignes_family </h2>";
	$content.= "<h2>".$monobjet->pi_getLL('numberdistinctgenus', '', 1)." $nb_lignes_genre </h2>";
	$content.= "<h2>".$monobjet->pi_getLL('numberdistinctspecies', '', 1)." $nb_lignes_especes </h2>";


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
			    infowindow.setContent(\' <img src="'.$image.'" border="2" width="100"  /><br/><a href=//'.$linkObservation . $link.'>'.
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
  $numero_observation=$_GET['numero_observation'];
  $identifiant_photo=$_GET['identifiant_photo'];
	
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

function redimensionner_roi($nom,$target_size = "vignettes"){
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
{ //echo "<!-- mylanguage : $mylanguage -->";
  if($cetobjet!=null)
    {
      if($cetobjet->cObj->data['sys_language_uid']==1)$mylanguage='fr';
      if($cetobjet->cObj->data['sys_language_uid']==3)$mylanguage='de';
      if($cetobjet->cObj->data['sys_language_uid']==4)$mylanguage='it';
      if($cetobjet->cObj->data['sys_language_uid']==2)$mylanguage='pt';
      if($cetobjet->cObj->data['sys_language_uid']==5)$mylanguage='es';
      }
   //echo "<!-- mylanguage : ".$monobjet->cObj->data['sys_language_uid']." -->";
  $expertise_translate = array(
  'en' => array(
		'ledate' => 'On ',
		'nomCommun_forweb'=> 'Common name: ',
		'nomCommun_formail'=> 'Common name: ',
		'nom_scientifique_forweb'=> 'Scientific name: ',
		'nom_scientifique_formail'=> 'Scientific name: ',
		
		'aboutprecision_formail'=> 'About the precision of this name : ',
		'aboutcertitude_formail'=> 'About the certitude of this name : ',
		'aboutcertitude_forweb'=> 'Expert certitude :  ',
		'note'=> 'Note : ',
		'expertises_forweb'=> 'Here are the latest remarks that have been made on your observation  : ',
		'expertises_formail'=> 'Here are the latest remarks that have been made on your observation  : ',
		),
  'fr' => array (
		 'ledate' => 'le ',
		 'nomCommun_forweb'=> 'nom commun : ',
		 'nomCommun_formail'=> 'nom commun : ',
		 'nom_scientifique_forweb'=> 'Nom scientifique : ',
		'nom_scientifique_formail'=> 'Nom scientifique : ',
		
		'aboutprecision_formail'=> 'About the precision of this name : ',
		'aboutcertitude_formail'=> 'About the certitude of this name : ',
		'aboutcertitude_forweb'=> 'Expert certitude :  ',
		
		'note'=> 'Note : ',
		'expertises_forweb'=> 'DerniËres remarques apportÈes  : ',
		'expertises_formail'=> 'DerniËres remarques apportÈes  : ',
		 ),
  'es' => array (
         'ledate' => ' ',
         'nomCommun_forweb'=> 'nombre com˙n: ',
         'nomCommun_formail'=> 'nombre com˙n: ',
         'nom_scientifique_forweb'=> 'Nombre CientÌfico : ',
        'nom_scientifique_formail'=> 'Nombre CientÌfico : ',

        'aboutprecision_formail'=> 'Acerca de la certeza de este nombre : ',
        'aboutcertitude_formail'=> 'Acerca de la certeza de este nombre : ',
        'aboutcertitude_forweb'=> 'la certeza es experta :  ',

        'note'=> 'Notas : ',
        'expertises_forweb'=> 'AquÌ son los ˙ltimos comentarios que se han hecho sobre tu observaciÛn  : ',
        'expertises_formail'=> 'AquÌ son los ˙ltimos comentarios que se han hecho sobre tu observaciÛn  : ',
         ),

  'pt' => array (
		 'ledate' => ' ',
		 'nomCommun_forweb'=> 'Nome comum : ',
		 'nomCommun_formail'=> 'Nome comum : ',
		 'nom_scientifique_forweb'=> 'nome cientÌfico : ',
		'nom_scientifique_formail'=> 'nome cientÌfico : ',
		
		'aboutprecision_formail'=> 'Sobre a precis„o deste nome : ',
		'aboutcertitude_formail'=> 'Sobre o certitude deste nome : ',
		'aboutcertitude_forweb'=> 'Certitude perito :  ',
		
		'note'=> 'Note : ',
		'expertises_forweb'=> 'Est„o aqui as observaÁıes as mais atrasadas que foram feitas nesta planta  : ',
		'expertises_formail'=> 'Est„o aqui as observaÁıes as mais atrasadas que foram feitas em sua observaÁ„o  : ',
		 ),
  
);
  
  $demandenom =1;
  // define extension for the names of the strings
  if($texteseul==0){$finchamps ="_forweb"; $finligne = "<br/>";} else {$finchamps ="_formail";$finligne = " \n";}
  if($texteseul==0)$champscomment = 'web_comment'; else $champscomment = 'email_comment';
  //$sql_determination="select nom_commun,nom_scientifique,date,famille,genre,comment from iherba_determination where (nom_commun !='' OR nom_scientifique != '') AND id_obs=$numero_observation ";
  $sql_determination.="select nom_commun,nom_scientifique,date, famille,genre ,id_cases, iherba_determination_cases.$champscomment ,iherba_certitude_level.value as certitude_level, iherba_certitude_level.comment as certitude_comment,";
  $sql_determination.=" iherba_determination.comment,iherba_precision_level.value as precision_level,iherba_precision_level.$champscomment as precisioncomment from iherba_determination,iherba_determination_cases,iherba_certitude_level, iherba_precision_level ";
  $sql_determination.="where  iherba_determination_cases.language = 'fr' and iherba_determination_cases.id_cases = iherba_determination.comment_case AND iherba_determination.precision_level = iherba_precision_level.value AND iherba_determination.certitude_level = iherba_certitude_level.value AND iherba_determination.id_obs=$numero_observation ";
  $sql_determination.= " order by creation_timestamp desc";
  // if list in a compact mode, only last two determination
  if($publication=="liste")
    $sql_determination .= " limit 2";
  // if text only, probably for sending a mail, only the last one
  if($texteseul!=0)$sql_determination .= " limit 1";

  //echo "<!-- icisql $sql_determination -->";

  $result_determination = mysql_query($sql_determination) or die ();
  $num_rows = mysql_num_rows($result_determination); //nombre de lignes r»sultats
	
  if($num_rows !=0){
   // echo "<!-- mylanguage 2: $mylanguage -->";
    if($texteseul!=2)$content.= get_string_language($expertise_translate,'expertises'.$finchamps,$mylanguage).$finligne;
   // echo "<!-- mylanguage expert : ".get_string_language($expertise_translate,'expertises'.$finchamps,$mylanguage)." -->";
  }
	
  while ($row_determination = mysql_fetch_assoc($result_determination)) {
    //$demandenom = 0; //si un nom deja donn» on ne demande plus
    $nom_commun=$row_determination["nom_commun"];
    $nom_scientifique=$row_determination["nom_scientifique"];
    $date=$row_determination["date"];
		
    list( $jour,$mois, $annee,) = explode("-", $date);
    if($texteseul==0)
      $content.= get_string_language($expertise_translate,'ledate',$mylanguage)." " .$jour."-".$mois."-".$annee." ";
		
    if($nom_commun!=""){
      $content.= get_string_language($expertise_translate,'nomCommun'.$finchamps,$mylanguage) .$nom_commun . " ";
    }
	  
    if($nom_scientifique !=""){
		
      $content.= get_string_language($expertise_translate,'nom_scientifique'.$finchamps,$mylanguage) .$nom_scientifique." ";
			
      if(($row_determination["famille"]!="")||($row_determination["genre"]!=""))
	{
	  if(($row_determination["famille"]!="")||($row_determination["genre"]!=""))$virgule=", ";else $virgule=" ";
	  if($texteseul==0)$content.= "&nbsp;&nbsp;";
	  $content.="[".$row_determination["genre"].$virgule.$row_determination["famille"]."]";
	}
			
    }
    if($publication!="liste")
      {
	if($row_determination["precision_level"]!=0)
	  {
	    if($texteseul==0)
	      $content.=' <img src="/interface/target_'.$row_determination["precision_level"].'.gif" width=24 title="'.$row_determination["precisioncomment"].'"> ';
	    else
	      $content.= $finligne.get_string_language($expertise_translate,'aboutprecision_formail',$mylanguage) .$row_determination["precisioncomment"];
	  }
	if($row_determination["certitude_level"]!=0)
	  {
	    if($texteseul==0)
	      $content.=' <img src="/interface/certitude_'.$row_determination["certitude_level"].'.gif"  title="'.get_string_language($expertise_translate,'aboutcertitude'.$finchamps,$mylanguage)." ".$row_determination["certitude_comment"].'"> ';
	    else
	      $content.= $finligne.get_string_language($expertise_translate,'aboutcertitude_formail',$mylanguage).$row_determination["certitude_comment"];
	  }
	/*if($texteseul==0)$content.=' <img src="/interface/valid_green.png" width=24> ';
	  if($texteseul==0)
	  if($publication!="liste")
	  $content.='&nbsp;<img alt="je ne suis pas d\'accord avec ce nom" title="je ne suis pas d\'accord avec ce nom" src="/interface/minus16.png"><img src="/interface/plus16.png" alt="je suis d\'accord avec ce nom" title="je suis d\'accord avec ce nom">';
	*/	
	}

	
	if($row_determination["web_comment"]!=""){
		$content.= $finligne;
		$content.= get_string_language($expertise_translate,'note',$mylanguage);
		$content.=$row_determination["web_comment"];
	}
	if($row_determination["comment"]!=""){
		$content.= $finligne;
		$content.= get_string_language($expertise_translate,'note',$mylanguage);;
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


function affiche_une_observation_dans_liste($cetobjet,$numobs,$publication="public")
{
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
      
      $entete_content = $cetobjet->pi_getLL('deposele', '', 1).$jour.'-'.$mois.'-'.$annee;
      if($row_iduser['computed_usable_for_similarity']!="0")$entete_content .= " (Modele) ";
      if (isset($iduser)){
	  $entete_content.=$cetobjet->pi_getLL('utilisateur', '', 1).$iduser_name."<br/><br/> </h1>";
	}
      
      $content.= $entete_content . '<div id="bloc_contenu_texte">'; 
      $content.="<br/>\n";
	        
	                     
      $content.=affiche_expertise($numobs,$cetobjet,"liste",$demandenom,0);
	
      if(($publication == "prive") ){
	$content.=$cetobjet->pi_getLL('obs', '', 1).$numobs."<br/><br/>";
      }   
					
      if( $publication=="prive"){
	$content.="<br/>";
	if($demandenom>0)
	  {
	    $content.=$cetobjet->pi_linkToPage($cetobjet->pi_getLL('DonnerNomPlante', '', 1),31,'',$paramlien);
	    $content.= "<br/>";
	  }
	//$content.=$cetobjet->pi_linkToPage($cetobjet->pi_getLL('voirobservationdetail', '', 1),21,'',$paramlien);
	//$content.="<br/>";
      }
      $content.= $cetobjet->pi_getLL('voirobservationdetail', '', 1)."<br/>";
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

function type_recherche(){
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

function resultat_recherche_plantes($type_recherche,$critere,$cetobjet){
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
			
?>
