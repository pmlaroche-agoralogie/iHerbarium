<?php
require_once("common_functions.php");

define('repertoire_sources', './medias/sources/');
define('repertoire_vignettes', './medias/vignettes/');
define('repertoire_big', './medias/big/');

/* Fonction qui permet de redimensionner l'image que l'utilisateur nous a envoyé */
function redimensionner_image($nom,$taillemax,$repertoire_destination){
  $content.="";
  $image = repertoire_sources."/$nom";
  $dim=getimagesize($image);  //la variable dim contiendra la taille de l'image passée en paramètre 
  $largeur=$dim[0];
  $hauteur=$dim[1];
	
  //calcul des nouvelles dimensions de l'image
  if($largeur>$hauteur){
    $new_hauteur=$hauteur*(($taillemax/$largeur));
    $new_largeur=$taillemax;
  }
  else {
    $new_largeur=$largeur*(($taillemax)/$hauteur);
    $new_hauteur=$taillemax;
  }

  // Redimensionnement
  $image_p = imagecreatetruecolor($new_largeur, $new_hauteur);
  $image_cree = imagecreatefromjpeg($image);
  imagecopyresampled($image_p, $image_cree, 0, 0, 0, 0, $new_largeur, $new_hauteur, $largeur, $hauteur);
	
  // on place l'image redimensionnée dans le répertoire repertoire_vignettes
  imagejpeg($image_p,$repertoire_destination."$nom", 100);
	
  return $content;
}



/*Cette fonction retourne la date au format exif contenue dans l'image passée en paramètre*/
function dateexif($image){
  $exif = exif_read_data($image, 0, true);
  /* on récupère la date de création au format exif, contenue dans l'image que l'utilisateur envoie
   * Cette date est contenue dans le champ DateTimeOriginal. Cependant, l'heure y figure aussi,et le format de la date est en anglais,
   * on utilisera donc la fonction explode afin d'obtenir uniquement la date au format "jour/mois/année"	*/
  $dateHeure=$exif['EXIF']['DateTimeOriginal'];//cette variable contient la date suivie de l'heure
  //on découpe notre variable $dateHeure
  $date=explode(" ",$dateHeure);
  /* $date[0] contiendra la date (en anglais) -- $date[1] contiendra l'heure à laquelle la photo a été prise*/
  $datedecoupee=explode(":",$date[0]);
  /* on récupère les valeurs de $datedecoupee[0],$datedecoupee[1] et $datedecoupee[2]
   * La date en français est la variable $datedecoupee[2] suivi par $datedecoupee[1] suivi par datedecoupee[0]
   * chacun de ces trois champs étant séparés pas un "/"
   * (la date sera alors au format jj/mm/aaaa),elle sera contenue dans la variable $dateFr*/
  $dateFr=$datedecoupee[2]."/".$datedecoupee[1]."/".$datedecoupee[0];
  return $dateFr;
}

/*Fonction qui calcule la latitude contenue dans l'image envoyée */
function calcul_latitude_exif($exif){
  $lat1=$exif["GPS"]["GPSLatitude"][0];
  $lat1decoupee=explode("/",$lat1);
  $lat2=$exif["GPS"]["GPSLatitude"][1];
  $lat2decoupee=explode("/",$lat2);
  $lat2final=($lat2decoupee[0]/$lat2decoupee[1])/60;
  $latitude=$lat1decoupee[0]+$lat2final;
	
  if ($exif["GPS"]["GPSLatitudeRef"] =="S")$latitude = -$latitude;
  return $latitude;
}

/*fonction permettant d'obtenir la longitude contenue dans l'image */
function calcul_longitude_exif($exif){
  $long1=$exif["GPS"]["GPSLongitude"][0];
  $long1decoupee=explode("/",$long1);
  $long2=$exif["GPS"]["GPSLongitude"][1];
  $long2decoupee=explode("/",$long2);
  $long2final=($long2decoupee[0]/$long2decoupee[1])/60;
  $longitude=$long1decoupee[0]+$long2final;
		
  if ($exif["GPS"]["GPSLongitudeRef"] =="W")$longitude = -$longitude;
  return $longitude;
}

function choisir_langue(){
  $langues = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
  $langue_selectionnee=$langues[0];
  return $langue_selectionnee;
}

/*Cette fonction permet d'uploader les fichiers que l'utilisateur envoie, et les met dans le répertoire "upload"*/
function genere_formulaire($monobjet){
  $langue=choisir_langue();
  $paramlien = array(etape  => 2);
  $lienform =$monobjet->pi_getPageLink(20,'',$paramlien);
	
  $content.='<form method="post" enctype="multipart/form-data" action="'.$lienform.'"><p>';
  $content.= $monobjet->pi_getLL('votreImage', '', 1). '<input type="file" id="mon_image" name="fichier" size="30">';
  $content.='<input type="hidden" name="MAX_FILE_SIZE" value="100000">  <br/><br/>';
  $content.= '<br> Si vous n\'etes pas l\'auteur de cette photo, merci de préciser son origine <br> type de licence  : <INPUT type="Type de licence" value="" name="licencetype">';
  $content.= '<br> lien vers la licence  : <INPUT type="Lien détaillant la licence" value="" name="licencelink">';
  $content.= '<br> auteur : <INPUT type="Auteur" value="" name="realauthor"><br><br><br>';
	
	
  if(!isset($_POST['commentaire'])){  /* premier envoi de l'utilisateur*/
    $content.=$monobjet->pi_getLL('visibilite', '', 1)."<br/>";
    $content.=$monobjet->pi_getLL('oui', '', 1).'<input type="radio" name="visibilite"  value="oui" checked/><br/> ';
    $content.=$monobjet->pi_getLL('semi', '', 1).'<input type="radio" name="visibilite"  value="semi"/><br/>';
    //$content.=$monobjet->pi_getLL('non', '', 1).'<input type="radio" name="visibilite"  value="non"/><br/>';
    $content.='<br/><input type="submit" name="upload" value='.$monobjet->pi_getLL('envoyer','',1).'></p>';
  }	

  else {  /* si on a  déjà renseigné le champ "commentaire" on ne le redemande pas lors de l'envoi pas l'utilisateur
	     d'autres photo */
    $content.='<input type="submit" name="upload" value='.$monobjet->pi_getLL('envoyer','',1).'>';
    $content.="<br/>\n";
    $content.="<br/><br/>".$monobjet->pi_getLL('imagesDejaEnvoyees', '', 1)."<br/><br/>\n";
	
	    
    //$chemin_web = str_replace('\\','/',dirname($_SERVER['REQUEST_URI']));
    //if(substr($chemin_web, -1)!="/")$chemin_web .= "/";
    $repertoire=repertoire_vignettes;
		
    bd_connect();
    $idobs=$_POST['id_obs'];//on récupère l'identifiant de l'observateur
    /*requête qui permet de sélectionner toutes les photos relatives au numéro d'observation de l'utilisateur courant*/
    $sql="select nom_photo_final from iherba_photos where id_obs=$idobs";
    $result = mysql_query($sql)or die ();
    /* On affiche les photos que l'utilisateur nous a déjà fait parvenir */
    while ($row = mysql_fetch_assoc($result)) {
      $image="$repertoire/".$row["nom_photo_final"];
      $content.='<img src="'.$image.'" border=0 width="200"  /></blank>&nbsp;';
    }
				
			
  }
  $content.='</form>';
  return  $content;
}


function localisationObservation($monobjet){
  $content="";
  /*retour du fichier page3.php 
    /*Si le lieu trouvé grâce aux données gps de l'image est bien l'endroit où les images ont été prises*/
  if (isset($_POST['lieu']) && $_POST['lieu'] == "oui") {
    $content.=$monobjet->pi_getLL('merci', '', 1)."<br/>";
    $content.=$monobjet->pi_getLL('finDepot', '', 1)."<br/>";
  }
	
  elseif (isset($_POST['lieu']) && $_POST['lieu'] == "non") {
    $content.=$monobjet->pi_getLL('erreurLocalisation', '', 1)."<br/> ";
    $content.=carteinitiale($monobjet);
  }
	
  /* cas où l'utilisateur a souhaité identifier une nouvelle localisation des photos(on arrive donc du fichier carteinitiale.php)
   * on souhaite donc remplir les champs _user et longitude_user de la table photos*/
  if( isset($_POST['nouvellecarte']) ){
    $content.=$monobjet->pi_getLL('merci','',1)."<br/>";
    $content.=$monobjet->pi_getLL('finDepot','',1)."<br/>";
    $points = $_POST['nb'];//variable contenant tous les points d'observation(latitude, longitude et id_obs)
	    
		
    $tab = explode(";",$points);//le tableau tab contiendra chaque couple longitude, latitude (séparé par une virgule )
    $coordonnees=$tab[0];
    $id_obs=$tab[1]; //identifiant de l'observateur
    /*on redécoupe la variable coordonnees|0] afin d'isoler la latitude et la longitude */
    $tab2=explode(",",$coordonnees);
    $latitude_user=$tab2[0];
    $longitude_user=$tab2[1];
    /* mise à jour de la table observations avec la longitude et la latitude déterminée par l'utilisateur (longitude_user et latitude_user)
     * On se trouve donc ici dans le cas où soit il y avait des coordonnées dans les photos mais que l'utilisateur nous a donné une autre localisation 
     * de son observation, soit dans le cas où il n'y avait pas de données exif dans les photos et où on a demandé à l'utilisateur d'effectuer 
     * la localisation */
    bd_connect();
    $sql5="UPDATE iherba_observations set latitude='$latitude_user',longitude='$longitude_user' where idobs='$id_obs' ";
    mysql_query ($sql5) or die ('Erreur SQL !'.$sql5.'<br />'.mysql_error());
  }
	
  return $content;
}




function envoiVariablesPages2($id_obs,$nom_fichier,$id_photo,$visibilite,$nom,$image,$latitude,$longitude,$pasGPS,$pasEXIF,$datesansexif){
  $content.='<input type="hidden" name="id_obs" value="'.$id_obs.'">' ;
  $content.='<input type="hidden" name="nom_fichier" value="'.$nom_fichier.'">' ;
  $content.='<input type="hidden" name="id_photo" value="'.$id_photo.'">' ;
  $content.='<input type="hidden" name="visibilite" value="'.$visibilite.'" >';
  $content.='<input type="hidden" name="nom" value="'.$nom.'" >';
  $content.='<input type="hidden" name="image" value="'.$image.'" >';
  $content.='<input type="hidden" name="latitude" value="'.$latitude.'" >';
  $content.='<input type="hidden" name="longitude" value="'.$longitude.'" >';
  if(isset($pasGPS)){
    $content.='<input type="hidden" name="pasGPS" value="'.$pasGPS.'" >';
  }
  if(isset($pasEXIF)){
    $content.='<input type="hidden" name="pasexif" value="'.$pasEXIF.'" >';
  }
  if(isset($datesansexif)){
    $content.='<input type="hidden" name="datesansexif" value="'.$datesansexif.'" >';
  }
  $content.='</form>';
  return $content;
}



function fairepage2($idutilisateur,$monobjet){
  $langues = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
  $langue=$langues[0];
  $content="";
  if(isset($_POST['dateexif'])){ /*si la date exif existe dans l'image et a été transmise de la page3*/
    $dateexif=$_POST['dateexif'];
  }
  if(isset($_POST['datesansexif'])){ /*Si l'image ne contenant pas de date, on récupère celle entrée par l'utilisateur */
    $datesansexif=$_POST['datesansexif'];
  }
	
  $content.=localisationObservation($monobjet);
  $id_user=$idutilisateur;//$GLOBALS['TSFE']->fe_user->user['uid']; il s'agit de l'id de l'utilisateur 

  /* Si on est dans le cas du premier envoi,c'est à dire qu'on vient de remplir le formulaire de la page page1.php */
  if( isset($_POST['upload']) ){
    $visibilite=$_POST['visibilite'];//visibilite de ce que l'utilisateur envoie
    if(isset($_POST['commentaire'])){
      $commentaire=$_POST['commentaire'];/*commentaires de l'utilisateur*/
    }
    if(isset($_POST['taille'])){
      $taille=$_POST['taille'];
    }
	    

    $tmp_file = $_FILES['fichier']['tmp_name']; // $_FILES['fichier']['tmp_name']contient le chemin du fichier temporaire
    if( !is_uploaded_file($tmp_file) )
      {
	exit($monobjet->pi_getLL('fichierIntrouvable','',1));
      }
	
    // on vérifie maintenant l'extension du fichier reçu
    $type_fichier = $_FILES['fichier']['type'];//$_FILES['fichier']['type']contient le type du fichier
    if( !strstr($type_fichier, 'jpg') && !strstr($type_fichier, 'jpeg') && !strstr($type_fichier, 'bmp') && !strstr($type_fichier, 'gif') && !strstr($type_fichier, 'png') ){
      exit($monobjet->pi_getLL('erreurFormat','',1));
    }


    /*connexion à la base de données et remplissage des champs auto-incrémenté des tables photos et obs*/
    bd_connect();
    $sql="INSERT INTO iherba_photos (licence_type,external_licence,external_author) VALUES('".$_POST[licencetype]."','".$_POST[licencelink]."','".$_POST[realauthor]."')"  ; //spock
    mysql_query ($sql) or die ();
    $id_photo=mysql_insert_id();// on récupère l'identifiant de la photo envoyée

    /*si ce n'est pas la première photo de l'observation, on récupère l'identifiant de l'observation id_obs
     * On récupère cet identifiant afin de s'en servir pour le nom final qu'aura l'image envoyée */
    if(isset($_POST['id_obs']))
      {
	$id_obs=$_POST['id_obs'];//identifiant observation
      }
    else
      {
	/*Sinon on fait une insertion dans la table obs et on récupère de cette façon l'identifiant de l'observation
	 * il s'agit donc de la première photo envoyée par l'utilisateur*/
	$sql2="INSERT INTO iherba_observations VALUES()"  ;
	mysql_query ($sql2) or die ();
	$id_obs=mysql_insert_id();
      }


    $nom_fichier = $_FILES['fichier']['name'];	// variable contenant le nom du fichier original (tel que l'utilisateur l'a envoyé
    /*la variable nom contient le nom final qu'aura l'image.
     * En effet, l'image aura pour nom : l'identifiant de la photo, suivi du numéro d'observation et suivi de son numéro
     * d'utilisateur, ceci afin de distinguer chacun des envois, ainsi que les différentes photos envoyés pas chacun des utilisateurs.*/
    $nom=$id_photo."_".$id_obs."_".$id_user.".jpg";

    /* on copie le fichier qui vient d'etre uploadé dans le répertoite $repertoire, c'est-à-dire dans le répertoire "mes_images/sources"*/
    if( !move_uploaded_file($tmp_file, repertoire_sources .$nom) )
      {
	exit($monobjet->pi_getLL('copieImageImpossible','',1)."  repertoire_sources ");
      }

    $image=repertoire_sources."/$nom";
    /*on fait appel à la fonction redimensionner_image qui va nous permettre de redimensionner l'image envoyée et de la mettre dans le 
     * répertoire vignettes avec des dimensions maximales de 200*200.
     * On agrandira également l'image reçue et on la placera dans le répertoire ./mes_images/big (avec une largeur et une hauteur maximale de 
     * 1024 */
	  
    //$exifd = exif_read_data($image);
    //print_r($exifd);echo "rr";die();
	  
    redimensionner_image($nom,200,repertoire_vignettes);
    redimensionner_image($nom,1024,repertoire_big);
    $image_redimensionnee=repertoire_vignettes."$nom";
      
    //affichage de l'image que l'utilisateur vient d'envoyer avec des dimensions ne dépassant pas les 200*200
    $content.=$monobjet->pi_getLL('imageRecu','',1)."<br/>";
    $content.='<img src="'.$image_redimensionnee.'" height=150 id="mon_image" name="fichier"  >';//on affiche l'image à partir du dossier des miniatures(dossier vignettes)

    /* on récupère la date de création au format exif, contenue dans l'image que l'utilisateur envoie*/
    if($exif = exif_read_data($image, 'EXIF', true)){
      $dateFr=dateexif($image);
    }
    else{//pas de données exif
      $pasEXIF=1;
    }

    $paramlien = array(etape  => 3);
    $lienform =$monobjet->pi_getPageLink(20,'',$paramlien);
	
    $content.='<form method="post" action="'.$lienform.'" /><br/>';
	 
    /*si on a pas encore rempli le champ "commentaire", c'est-à-dire si il s'agit de la première image que l'on envoie */
    if(!isset($_POST['commentaire'])){
      $numero_reponse=1; 
      $content.=$monobjet->pi_getLL('commentairesObservation','',1) .'<br/> <textarea name="commentaire" id="com"value="" cols="30" rows="2"/></textarea></p><br/>';
      /* ajout */
      bd_connect();
      $sql_question_taille="select texte_question,textes_reponses from iherba_question 
		where id_question=1 and id_langue='$langue' ";
      $result_question_taille= mysql_query($sql_question_taille)or die ();
		
      while($row_question_taille = mysql_fetch_assoc($result_question_taille) ){
	$texte_question=$row_question_taille['texte_question']; //le texte de la question à poser
	$textes_reponses=$row_question_taille['textes_reponses']; //les réponses possibles pour cette question
	$tab_reponses=explode(";",$textes_reponses); //chaque réponse est séparée par un ";" on va donc utiliser chacune d'entre elles
	/*chacune des réponses sera suivi d'un bouton radio que l'utilisateur devra cocher afin d'indiquer sa réponse */
      }
	    

      $content.=$texte_question."<br/>";
		
      $reponse_milieu=array("3","8","15","30","60","120","200"); // ce tableau contient le milieu de chaque intervalle possible pour la taille de la plante
		
      $i=0;
      foreach ($tab_reponses as $reponse){ //définition des boutons radio et donc des réponses possibles */
	$content.=$reponse.'<input type="radio" name="taille"  value="'.$reponse_milieu[$i].'"/>'."  ";
	$numero_reponse++;
	$i++;
      }
      $content.="<br/>";
	
	
	
	
      if($exif = exif_read_data($image, 'EXIF', true)){
	$content.=$monobjet->pi_getLL('dateExif','',1)."";
	$content.='<input type="text" name="dateexif" value="'.$dateFr.'" >'.$monobjet->pi_getLL('formatDate','',1).'<br/>';
	$content.=$monobjet->pi_getLL('modifierDate','',1)."<br/>";
      }
      else{
	$content.=$monobjet->pi_getLL('saisirDate','',1) ;
	$content.='<input type="text" name="datesansexif" value="">'.$monobjet->pi_getLL('formatDate','',1).''."<br/>";
      }
      $content.="<strong>".$monobjet->pi_getLL('autresPhotosAenvoyer','',1)."&nbsp;";
      $content.=$monobjet->pi_getLL('oui','',1).'<input type="radio" CHECKED name="choix" id="choice" value="oui"/>'."&nbsp;"."&nbsp;";
      $content.=$monobjet->pi_getLL('non','',1).'<input type="radio" name="choix" id="choice" value="non"/>'."<br>";
      $content.='<input type="submit"  value="'.$monobjet->pi_getLL('envoyer','',1).'"  /> </strong>';
    }
		
    else{/* ce n'est pas la première image de l'observation envoyée */
      $content.="<strong>". $monobjet->pi_getLL('autresPhotosAenvoyer','',1)."<br/>";
      $content.=$monobjet->pi_getLL('oui','',1).'<input type="radio" CHECKED name="choix" id="choice" value="oui"/>'."&nbsp;"."&nbsp;";
      $content.=$monobjet->pi_getLL('non','',1).'<input type="radio" name="choix" id="choice" value="non"/><br/>';
      $content.='<INPUT type="submit"  value="'.$monobjet->pi_getLL('envoyer','',1).'" /> </strong>';
      $content.='<input type="hidden" name="commentaire" value="'.$commentaire.'" >' ;
      $content.='<input type="hidden" name="taille" value="'.$taille.'" >' ;
      if($exif = exif_read_data($image, 'EXIF', true)){
	$content.='<input type="hidden" name="dateexif" value="'.$dateexif.'" >';
      }
      if($exif = exif_read_data($image, 'GPS', true)){
	$content.='<input type="hidden" name="dateexif" value="'.$dateexif.'" >';
      }
    }

    /* Calcul de la latitude et de la longitude d'après les données exif contenues dans l'image */
    if($exif = exif_read_data($image, 'GPS', true)){
      $exif = exif_read_data($image, 0, true);
      $latitude=calcul_latitude_exif($exif);
      $longitude=calcul_longitude_exif($exif);
		
      /*On renseigne les champs latitude et longitude de la table observations avec les champs exif(gps) trouvés dans la photo*/
      bd_connect();
      $sql6="UPDATE iherba_observations set latitude='$latitude',longitude='$longitude' where idobs='$id_obs' ";
      mysql_query ($sql6) or die ();
    }
    else{
      $pasGPS=1;
    }

    $content.=envoiVariablesPages2($id_obs,$nom_fichier,$id_photo,$visibilite,$nom,$image,$latitude,$longitude,$pasGPS,$pasEXIF,$datesansexif);
  }
    
  return $content;
}



/*Cette fonction permet de définir le formulaire de la page3 en définissant notamment des champs de type hidden afin de pouvoir récupérer la
 * valeur de différentes variables dans d'autres pages.
 * Cette fonction permet également d'aller, selon le cas dans lequel on se trouve, soit sur la page d'envoi d'autres fichiers, soit
 * sur la page permettant de montrer la localisation obtenue grace aux données exif ou alors cela nous permet d'être redirigé 
 * vers la page affichée par la fonction carteinitiale, qui permet à l'utilisateur d'effectuer la localisation de son observation */ 
function formulaireEtRedirectionPage($monobjet,$choix,$id_obs,$visibilite,$commentaire,$taille,$latitude,$longitude,$dateexif,$datesansexif) {
  $paramlien = array(etape  => 2);
  $lienform =$monobjet->pi_getPageLink(20,'',$paramlien);
	
  $content.="<html><head><title></title></head>";
  $content.='<form method="post" enctype="multipart/form-data" action="'.$lienform.'">';
  $content.='<input type="hidden" name="choix" value="'.$choix.'" >';
  $content.='<input type="hidden" name="id_obs" value="'.$id_obs.'">' ;
  $content.='<input type="hidden" name="visibilite" value="'.$visibilite.'">' ;
  $content.='<input type="hidden" name="commentaire" value="'.$commentaire.'">' ;
  $content.='<input type="hidden" name="latitude" value="'.$latitude.'">' ;
  $content.='<input type="hidden" name="longitude" value="'.$longitude.'">' ;
  $content.='<input type="hidden" name="taille" value="'.$taille.'">' ;

  if(isset($_POST['dateexif'])){
    $content.='<input type="hidden" name="dateexif" value="'.$dateexif.'" >';
  }
	
  if(isset($_POST['datesansexif'])){
    $content.='<input type="hidden" name="datesansexif" value="'.$datesansexif.'" >';
  }

  if(isset($_POST['pasGPS'])){
    $pasGPS=$_POST['pasGPS'];
  }
	
  /*Si le bouton radio de valeur 'oui' a été coché,alors on sera redirigé vers la page page1.php qui nous permettra d'uploadé d'autres fichiers*/
  if (isset($_POST['choix']) && $_POST['choix'] == "oui") {
    $content.=genere_formulaire($monobjet);
  }

  /* si l'utilisateur n'a pas d'autres fichiers à envoyer */
  elseif (isset($_POST['choix']) && $_POST['choix'] == "non") {
		
    if((isset($pasGPS)) && ($pasGPS==1)){
      //si pas d'information de gps, alors on part sur une carte vierge
      $content.=carteinitiale($monobjet);
    }
    else{
      /* on est redirigé automatiquement vers la google map qui permet de localiser le lieu en fonction de la latitude et de la longitude
       * des fichiers images reçus*/
      $content.=$monobjet->pi_getLL('localisationExif', '', 1)."<br/> ";
      $content.=affichercarte($monobjet);	
    }
  }
  $content.='</form>';
  return $content;
}


function fairepage3($idutilisateur,$monobjet){
  $content="";
  if(isset($_POST['pasEXIF'])){
    $pasEXIF=$_POST['pasexif'];
  }
	
  if(isset($_POST['datesansexif'])){
    $datesansexif=$_POST['datesansexif'];
  }
  if(isset($_POST['taille'])){
    $taille=$_POST['taille'];
  }
  $id_user=$idutilisateur;
  $commentaire=$_POST['commentaire'];/*commentaires de l'utilisateur*/
  $id_obs=$_POST['id_obs'];/* numéro de l'observation */
  $nom_fichier=$_POST['nom_fichier'];/*nom du fichier (tel que l'utilisateur l'a envoyé)*/
  $nom=$_POST['nom'];/*nom final du fichier (nom qu'on va donner au fichier (tel qu'il sera nommé dans le répertoire upload) */
  $choix=$_POST['choix'];/*choix bouton choix*/
  $id_photo=$_POST['id_photo'];/*identifiant de la photo */
  $visibilite=$_POST['visibilite'];/*visibilite des photos envoyés (est ce que les images envoyées seront publiques ou privées)*/
  $image=$_POST['image'];/*nom de l'image (différent de $nom_fichier) car ici on stocke également le nom du répertoire dans lequel l'image se trouve */
	
  if(isset($_POST['dateexif'])){
    $dateexif=$_POST['dateexif'];/* date entrée par l'utilisateur */
  }
	
  $latitude=$_POST['latitude'];/*latitude de la dernière image envoyée par l'utilisateur*/
  $longitude=$_POST['longitude'];/*longitude de la dernière image envoyée par l'utilisateur*/

  bd_connect();/*connexion à la base de données et remplissage de la table user*/
		

  /*on récupère la date à laquelle l'image a été prise (pour cela, on se sert des champs exif de l'image*/
  if($exif = exif_read_data($image, 'EXIF', true)){
    $dateDepot=date("d-m-Y");/* La date de dépot est la date du jour */
    $sql3="UPDATE iherba_observations SET id_user='$id_user',date_depot=now(),
		 commentaires='$commentaire',taille_plante='$taille',public='$visibilite' where idobs='$id_obs'";
    mysql_query ($sql3) or die ();
    $dateHeure=$exif['EXIF']['DateTimeOriginal'];//cette variable contient la date suivie de l'heure
    $date=explode(" ",$dateHeure);
    $datedecoupee=explode(":",$date[0]);
    $dateimage=$datedecoupee[2]."/".$datedecoupee[1]."/".$datedecoupee[0];//date au format jj/mm/aaaa
	
    if(isset($dateexif) &&($dateexif==$dateimage)){
      /*la date entrée par l'utilisateur est la même que celle contenue dans l'image*/
      $content.='<input type="hidden" name="dateexif" value="'.$dateexif.'" >';
      $sql2="UPDATE iherba_photos set id_obs='$id_obs',date_depot=now(),
				date_exif='$dateimage',date_user='$dateimage',latitude_exif='$latitude',longitude_exif='$longitude',
				nom_photo_initial='$nom_fichier',nom_photo_final='$nom' where idphotos='$id_photo'";
      mysql_query ($sql2) or die ();
    }

    //Ici la date entrée par l'utilisateur est différente de celle trouvée dans l'image
    $dateexif=$_POST['dateexif'];
    $sql2="UPDATE iherba_photos set id_obs='$id_obs',date_depot=now(),
			date_exif='$dateimage',date_user='$dateexif',latitude_exif='$latitude',longitude_exif='$longitude',
			nom_photo_initial='$nom_fichier',nom_photo_final='$nom' where idphotos='$id_photo'";
    mysql_query ($sql2) or die ();
  }
  else{
    //si il n'y a pas de données exif
    $dateDepot=date("d-m-Y");
    $sql2="UPDATE iherba_photos set id_obs='$id_obs',date_depot=now(),date_user='$datesansexif',latitude_exif='$latitude',longitude_exif='$longitude',
		nom_photo_initial='$nom_fichier',nom_photo_final='$nom' where idphotos='$id_photo'";
    mysql_query ($sql2) or die ('Erreur SQL !'.$sql2.'<br />'.mysql_error());

    $sql3="UPDATE iherba_observations SET id_user='$id_user',taille_plante='$taille',
		commentaires='$commentaire',public='$visibilite',date_depot=now() where idobs='$id_obs'";
    mysql_query ($sql3) or die ();
  }

  $content.=formulaireEtRedirectionPage($monobjet,$choix,$id_obs,$visibilite,$commentaire,$taille,$latitude,$longitude,$dateexif,$datesansexif);
  return $content;
}



function affichercarte($monobjet){
  $id_obs=$_POST['id_obs'];
  $content="";
	
  $paramlien = array(etape  => 2);
  $lienform =$monobjet->pi_getPageLink(20,'',$paramlien);
	
	
  $content= '

	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script><script type="text/javascript">
	var map;
	function initialize() {
    /* on récupère la latitude et la longitude de la page page2.php */
		var latitude=\''.$_POST['latitude'].'\';
		var longitude=\''.$_POST['longitude'].'\';
		/* on va centrer la carte d\'après les coordonnées récupérées*/
		var myLatlng = new google.maps.LatLng(latitude,longitude);
		
		var myOptions = {
		    zoom: 15,
		    center: myLatlng,
		    mapTypeId: google.maps.MapTypeId.ROADMAP
		  }
		  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		
		  var marker = new google.maps.Marker({
		    position: myLatlng, 
		    map: map,
		  });
    }
	
	</script>
<script type="text/javascript">
window.onload = function() {
   initialize();
}
</script>
    '.$monobjet->pi_getLL('validerGeolocalisation', '', 1).'
	'.$monobjet->pi_getLL('oui', '', 1).'<input type="radio" name="lieu" value="oui"/>
	'.$monobjet->pi_getLL('non', '', 1).'<input type="radio" name="lieu" value="non"/>
	<input type="submit"  value="'.$monobjet->pi_getLL('boutonValiderLocalisation', '', 1).'" />
	<center><div id="map_canvas" style="width:500px; height:400px"></div>
	<br/><br/>
	<form name="carte" method="post" action="'.$lienform.'"/>'.
    /*$monobjet->pi_getLL('validerGeolocalisation', '', 1).'
      '.$monobjet->pi_getLL('oui', '', 1).'<input type="radio" name="lieu" value="oui"/>
      '.$monobjet->pi_getLL('non', '', 1).'<input type="radio" name="lieu" value="non"/><br/><br/>
      <input type="submit"  value="'.$monobjet->pi_getLL('boutonValiderLocalisation', '', 1).'" />*/
    '</form>
	</center> 
	
';	
  return $content;
}



function carteinitiale($monobjet){
  $id_obs=$_POST['id_obs'];
  $content.=$monobjet->pi_getLL('carteLocalisation', '', 1)."<br/> ";
  $content.=$monobjet->pi_getLL('carteInitialeCentre', '', 1)."<br/> ";
  $content.=$monobjet->pi_getLL('carteInitialeDeplacement', '', 1)."<br/> ";
  $content.=$monobjet->pi_getLL('carteInitialeAdresse', '', 1)."<br/> ";
  $content.=$monobjet->pi_getLL('validerLocalisation', '', 1)."<br/> ";	
  $content.=$monobjet->pi_getLL('recommencerSaisie', '', 1)."<br/> ";
	
  $paramlien = array(etape  => 2);
  $lienform =$monobjet->pi_getPageLink(20,'',$paramlien);
	
	
  $content.='
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script><script type="text/javascript">
	var geocoder;
	var map;
	var lastmaker;
	var id_obs=\''.$_POST['id_obs'].'\';
	function initialize() {
	    geocoder = new google.maps.Geocoder();
			/* par défaut, la carte est centrée sur Paris */
		  var myLatlng = new google.maps.LatLng(48,1);
		  var myOptions = {
			    zoom: 5,
			    center: myLatlng,
			    mapTypeId: google.maps.MapTypeId.ROADMAP
		  }
		  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		
		  
		 /* fonction qui définit un marqueur lorsque l\'on clique sur la carte, c\'est-à-dire lorsque l\'on a trouvé la zone que l\'on recherche 
		 * les variables lat et long représentent respectivement la latitude et la longitude du point sélectionné */
		 google.maps.event.addListener(map, \'click\', function(event) {
	          placeMarker(event.latLng);
			  var latlong =event.latLng;
			  var lat = latlong.lat();
			  var long = latlong.lng();
		   //   alert("latitude:"+lat+"longitude :"+long);
		      
		     /* on récupère ici le couple "latitude,longitude;id_obs"*/
		     document.getElementById("nombres").value =  \'\'+lat+\',\'+long+\';\'+id_obs;
		    });
	}
	

    /* cette fonction place le marqueur lors du clic de l\'utilisateur */
	 function placeMarker(location) {
		 	if(typeof(lastmaker)!=\'undefined\'){
			lastmaker.setVisible(false);
		   }
		    var clickedLocation = new google.maps.LatLng(location);
		    var marker = new google.maps.Marker({
		          position: location,
		          map: map,
		       
		     });
		    lastmaker = marker;
		    map.setCenter(location);
     }
	
	
	/* fonction de remise à zéro des champs du formulaire
	* void window.location.reload() recharge la page en oours
	*L\'appel à cette fonction est équivalent à l\'action du bouton Actualiser ou à la touche F5 du navigateur.
	*On remet la valeur du champ envoyé au formulaire à zéro */
	 function raz() {
        document.getElementById("nombres").value ="";
        document.getElementById("address").value="";
        //window.location.reload();
		window.initialize();
	  }

	 </script>
	 <script type="text/javascript">
window.onload = function() {
   initialize();
}
</script>

	<form name="ou"  >
	<input id="address" type="textbox" value="Paris,France" onkeypress="return event.keyCode!=13">
	<input type="button" value="'.$monobjet->pi_getLL('LocaliserAvecAdresse', '', 1).'" onclick="codeAddress()">
	</form>
	<form name="carte" method="post" action="'.$lienform.'"/><br/>
	
	<input type="submit"  name="nouvellecarte" value="'.$monobjet->pi_getLL('boutonValiderLocalisation', '', 1).'" />'.$monobjet->pi_getLL('recommencerlocalisation', '', 1).
    '<input type="reset" name="Effacer" value="'.$monobjet->pi_getLL('boutonEffacerInformations', '', 1).'" onclick="raz();"><br/><br/>
	<div id="map_canvas" style="width:500px; height:400px"></div>
	<input type="hidden" value="" name="nb" id="nombres"/><br/>
	</form>
	
	';


  return $content;

}
?>