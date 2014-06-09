<?php
require_once("common_functions.php");

function liste_obs_determine_proche($numobs){
  
 $liste_proche = array();
 
  bd_connect();
  $sql_obs =
      " SELECT * 
	FROM  `iherba_observations` 
	WHERE  `idobs` = $numobs";

  $result = mysql_query($sql_obs) or die(mysql_error());
  $row = mysql_fetch_array($result);
  
  $radius = 0.01;
  if($row['latitude']!=0)
    
    {
    // observation is localized
    // search observations which are near AND named
    
    $etat_requete_where = " iherba_observations.latitude >".($row['latitude']-$radius). "";
    $etat_requete_where .= " AND iherba_observations.latitude < ".($row['latitude']+$radius). "";
    $etat_requete_where .= " AND iherba_observations.longitude > ".($row['longitude']-$radius). "";
    $etat_requete_where .= " AND iherba_observations.longitude < ".($row['longitude']+$radius). "";
    
    
    
  $sql_obs =
	" SELECT  idobs, tropicosid , nom_scientifique
	  FROM  `iherba_observations` , iherba_determination
	  WHERE  $etat_requete_where AND idobs = id_obs  and tropicosid != '' and instanciation = 0 "; // group by idobs 
  
    $result = mysql_query($sql_obs) or die(mysql_error());
  $nbproche=0;
    while ($row = mysql_fetch_array($result)) { 
      $liste_proche[$nbproche]['ref'] = $row['idobs'];
      $liste_proche[$nbproche]['expertise'] = $row['nom_scientifique'];
      $liste_proche[$nbproche]['tropicosid'] = $row['tropicosid'];
      $nbproche++;
    }
   
      
    }
  //print_r( $liste_proche);
  return $liste_proche;

}
/* Cette fonction dÈfinit un formulaire qui va nous permettre de demander à l'utilisateur l'identifiant de la plante de son observation */

function affichage_formulaire($monobjet){
 
  $numobs=desamorcer($_GET['numero_observation']);
  $content="";
  
  // show thumbnail of the current observations
  bd_connect();
  $sql_list_vignettes =
      " SELECT * 
	FROM  `iherba_photos` 
	WHERE  `id_obs` = $numobs";

  $result = mysql_query($sql_list_vignettes) or die(mysql_error());
  $list_vignette = array();
  while ($row = mysql_fetch_array($result)) { 
    $list_vignette[] = $row['nom_photo_final'];
  }
  foreach($list_vignette as $v)
  {
    //$content.= '<img src="http://www.iherbarium.fr/medias/vignettes/'.$v.'" width=100px >';
    $content.= '<img src="/medias/vignettes/'.$v.'" width=150px >';
  }


  //$content.='<html><head></head><title>Id tropicos</title><body><form method="post" enctype="multipart/form-data" action="index.php?id=31&etape=1&numero_observation='.$numobs.'&L='.$GLOBALS['TSFE']->sys_language_uid.'">';
  $content.='<form method="post" enctype="multipart/form-data" action="index.php?id=31&etape=1&numero_observation='.$numobs.'&L='.$GLOBALS['TSFE']->sys_language_uid.'">';
  
  $content.=$monobjet->pi_getLL('indicationNom', '', 1).'<INPUT NAME="nom_commun" TYPE="TEXT" SIZE="15" > <br/><br/>';

  // Kuba ->

  $viewRowOption = function($valueField, $textField) {
    
    $viewFunction =
    function($row) use ($valueField, $textField) {
      return '<option value="' . $row[$valueField] . '">' . $row[$textField] . '</option>';
    };
    return $viewFunction;
  };

  $viewRowsSelect = function($name, $selectId, $valueField, $textField, $rows) use ($viewRowOption) {
    $lines = array();
  
    // Display Rows as Select choice.

    // Beginning
    $lines[] = '<div>' . $name;
    $lines[] = '<select id="' . $selectId . '" name="' . $selectId . '">';
	  
    // Options
    $options = array_map($viewRowOption($valueField, $textField), $rows);
    $lines[] = implode("\n", $options);

    // End
    $lines[] = '</select></div>';
	  
    $content = implode("\n", $lines);

    return $content;
  };
  
  $queryToRows = function($query, $connect = False) {
    
    if($connect)
      bd_connect();

    $result = mysql_query($query) or die(mysql_error());
    
    $rows = array();
    while ($row = mysql_fetch_array($result)) { 
      $rows[] = $row;
    }

    return $rows;
  };

  // Fields Certitude Level and Precision Level

  if(niveau_testeur() > 0) {

    // FETCH DATA

    bd_connect();

    // Fetch Certitude Levels.
    $certitudeLevelsQuery = 
      " SELECT * " .
      " FROM iherba_certitude_level " .
      " WHERE language = " . "'fr'" .
      " ORDER BY value ASC";

    $certitudeLevels = $queryToRows($certitudeLevelsQuery);

    // DISPLAY

    $content .= $viewRowsSelect('Certitude: ', 
				'certitudeLevel', 
				'value', 'short_name', 
				$certitudeLevels);
    
    // Precision Level

    // FETCH DATA

    // Fetch Precision Levels.
    $precisionLevelsQuery = 
      " SELECT * " .
      " FROM iherba_precision_level " .
      " WHERE language = " . "'fr'" .
      " ORDER BY value ASC";

    $precisionLevels = $queryToRows($precisionLevelsQuery);

    
    // DISPLAY

    $content .= $viewRowsSelect('Precision: ', 
				'precisionLevel', 
				'value', 'short_name', 
				$precisionLevels);


    // Fields Comment and Comment Cases.

    // FETCH DATA

    // Fetch Comment Cases.
    $commentCasesQuery = 
      " SELECT * " .
      " FROM iherba_determination_cases" .
      " WHERE language = " . "'fr'" .
      " ORDER BY id_cases ASC";

    $commentCases = $queryToRows($commentCasesQuery);
	  

    // DISPLAY

    $content .= $viewRowsSelect('Case: ', 
				'commentCase', 
				'id_cases', 'short_name', 
				$commentCases);


    // Display Comment textarea.
    $lines[] = '<div>'.$monobjet->pi_getLL('determin_comment', '', $GLOBALS['TSFE']->sys_language_uid).': ';
    $lines[] = '<div><textarea id="comment" name="comment"></textarea></div>';
    $lines[] = '</div>';
	  
    $content .= implode("\n", $lines);
  }

  // <- Kuba
 if(isset($GLOBALS['TSFE']->fe_user->user['uid']))
  {
   

  $content.=$monobjet->pi_getLL('demandeInfo', '', 1)."<br/>";
  $content.=$monobjet->pi_getLL('explication', '', 1)." ";
  $content.=$monobjet->pi_getLL('explication2', '', 1)." ";
  $content.=$monobjet->pi_getLL('explication3', '', 1)." ";
  
  $content.=''.$monobjet->pi_getLL('identifiant', '', 1)." ".' <INPUT NAME="id_tropicos" TYPE="TEXT"   SIZE="15" > ';
    }
  $content.='<input type="hidden" name="retour_suite_form" value="1"><input type="submit" value="'.$monobjet->pi_getLL('valider','',1).'"></form></br></br>';
  
  $proche = liste_obs_determine_proche($numobs);
  if(count($proche)>0)
    $content.="\n <br>".$monobjet->pi_getLL('near_plants_list','',1); //Liste d'observations identifiées proches du même lieu <br>";
  foreach($proche as $p)
    { $numobs_proche = $p['ref'];
    
    $content.= "\n";
	$content.='<form method="post" enctype="multipart/form-data" action="index.php?id=31&etape=1&numero_observation='.$numobs.'&L='.$GLOBALS['TSFE']->sys_language_uid.'">';
$content .= "<font size=-2>(".$p['expertise'].")</font><br>";
  $content.= ' <INPUT NAME="id_tropicos" TYPE="hidden"   SIZE="15" value="'.$p['tropicosid'].'" > ';
  $content.= ' <INPUT NAME="instanciation" TYPE="hidden"   SIZE="15" value="'.$numobs_proche.'" > ';
  $content.='<input type="hidden" name="retour_suite_form" value="1">';
  
      $sql_list_vignettes =
	" SELECT * 
	  FROM  `iherba_photos` 
	  WHERE  `id_obs` = $numobs_proche";
  
      $result = mysql_query($sql_list_vignettes) or die(mysql_error());
      $list_vignette = array();
      while ($row = mysql_fetch_array($result)) { 
	$list_vignette[] = $row['nom_photo_final'];
      }
      
      $content.='<a href="/observation/data/'.$numobs_proche.'" border="0" target="_blank">';
      foreach($list_vignette as $v)
      {
	//$content.= '<img src="http://www.iherbarium.fr/medias/vignettes/'.$v.'" width=100px >';
	$content.= '<img src="/medias/vignettes/'.$v.'" width=150px >';
      }
      $content.='</a>';
      $content.='<input type="submit" value="'.$monobjet->pi_getLL('near_plants_choose','',1).' -> '.$p['expertise'].'">'."</form><br><br><br>";
      
    }
    
  return $content;
}

function affichage_formulaire_comment($monobjet){
 
  $numobs=desamorcer($_GET['numero_observation']);
  $numdet=desamorcer($_GET['numero_det']);
  $content="";
  
  // show thumbnail of the current observations
  bd_connect();
  $sql_list_vignettes =
      " SELECT * 
	FROM  `iherba_photos` 
	WHERE  `id_obs` = $numobs";

  $result = mysql_query($sql_list_vignettes) or die(mysql_error());
  $list_vignette = array();
  while ($row = mysql_fetch_array($result)) { 
    $list_vignette[] = $row['nom_photo_final'];
  }
  foreach($list_vignette as $v)
  {
    //$content.= '<img src="http://www.iherbarium.fr/medias/vignettes/'.$v.'" width=100px >';
    $content.= '<img src="/medias/vignettes/'.$v.'" width=100px >';
  }
  
  $content.= "<br> ";
  $content.= "\n";
  $content.='<form method="post" enctype="multipart/form-data" action="index.php?id=87&etape=record_comment&numero_observation='.$numobs.'&numdet='.$numdet.'">';
//$content .= "<font size=-2>(".$p['expertise'].")</font><br>";
 // $content.= ' <INPUT NAME="id_tropicos" TYPE="hidden"   SIZE="15" value="'.$p['tropicosid'].'" > ';
  //$content.= ' <INPUT NAME="instanciation" TYPE="hidden"   SIZE="15" value="'.$numobs_proche.'" > ';
 
 $content.=' <br> '.$monobjet->pi_getLL('comment_freetext', '', 1).' <INPUT NAME="remarque" TYPE="TEXT" SIZE="15" ><br />';
 if($_GET['sens']=="minus")
       $content.='<input type="radio" name="reaction" value="no">'.$monobjet->pi_getLL('comment_sure_it_is_false', '', 1).'<br>
<input type="radio" name="reaction" value="probablynot">'.$monobjet->pi_getLL('comment_not_sure_seems_false', '', 1).'<br>
<input type="radio" name="reaction" value="difficult" checked>'.$monobjet->pi_getLL('comment_difficult_bad_not_enough', '', 1).' <br>';
   else
       $content.='<input type="radio" name="reaction" value="sure">'.$monobjet->pi_getLL('comment_sure_it_is_goodspecies', '', 1).'<br>
<input type="radio" name="reaction" value="probalyyes">'.$monobjet->pi_getLL('comment_not_sure_seems_good', '', 1).'<br>
<input type="radio" name="reaction" value="difficult" checked>'.$monobjet->pi_getLL('comment_difficult_good_not_enough', '', 1).'<br>';

 $content.='<br><input type="submit" value="'.$monobjet->pi_getLL('comment_submit', '', 1).'">'."</form><br><br><br>";
 return $content;
}


/*Cette fonction nous permet de rechercher le nom de la plante sur le site tropicos.org */
function rechercher_nom_plante($value_name){
  $value = json_decode($value_name);
  return desarmorcer($value->ScientificNameWithAuthors);
}

/* parse data from the data that tropicos api returns */
function rechercher_famille_plante($value_taxa,&$highertaxa){
  $list_taxa = json_decode($value_taxa);
  
  foreach ($list_taxa as $value)
      {
      if ($value->Rank=="family")
	{ $highertaxa['familyname'] =  $value->ScientificName; $highertaxa['familyid'] =  $value->NameId;}
      if ($value->Rank=="genus")
	{ $highertaxa['genusname'] =  $value->ScientificName; $highertaxa['genusid'] =  $value->NameId;}
      }
}


/* insert data on determination */
function remplir_table_determination($nom_commun,$nom_plante,$famille_plante,$genre_plante,$espece_plante,$iduserident, 
				     /* Kuba -> */ 
				     $comment = "",
				     $commentCase = 0,
				     $certitudeLevel = 0,
				     $precisionLevel = 0
				     /* <- Kuba */
					,$id_tropicos, $familyid,$genusid,
				      $instanciation
				     ){
  $id_obs=desamorcer($_GET['numero_observation']);

  bd_connect();
 $probabilite=100;
 if($refuser == $GLOBALS['TSFE']->fe_user->user['uid'])$probabilite=0;
  $sql_insertion_determination =
    " INSERT INTO iherba_determination" .
    " (id_obs, nom_commun, nom_scientifique, famille, genre, espece, tropicosid, probabilite, date, id_user" .
    " , comment, comment_case" . // ADDED comment & commentCase
    " , certitude_level, precision_level, tropicosfamilyid,tropicosgenusid,instanciation )" . // ADDED certitudeLevel & precisionLevel
    " VALUES('$id_obs','$nom_commun','$nom_plante','$famille_plante','$genre_plante','$espece_plante','$id_tropicos','$probabilite',now(),'$iduserident'" .
    " , '" . $comment . "' , '" . $commentCase . "'" . // ADDED comment & commentCase
    " , '" . $certitudeLevel . "' , '" . $precisionLevel . "' , '" . $familyid . "' , '" . $genusid ."', $instanciation )"; // ADDED certitudeLevel & precisionLevel
	 

  $result_insertion_determination = mysql_query($sql_insertion_determination)or die ('Erreur SQL !'.$sql_insertion_determination.'<br />'.mysql_error());	 
	 
  $determinationId = mysql_insert_id();
  return $determinationId;
}


function notifyUserAboutDetermination($determinationId,$monobjet=null) {
 
  // we will talk to an observation owner using the same language a contributor has used to add a determination
  // good if people work on flower of thier own country
  
  $mylanguage = 'en';
  if($monobjet!=null)
    {
      if($monobjet->cObj->data['sys_language_uid']==1)$mylanguage='fr';
      if($monobjet->cObj->data['sys_language_uid']==3)$mylanguage='de';
      if($monobjet->cObj->data['sys_language_uid']==4)$mylanguage='it';
      if($monobjet->cObj->data['sys_language_uid']==2)$mylanguage='pt';
      if($monobjet->cObj->data['sys_language_uid']==5)$mylanguage='es';
      }
  
  // Determination
  $determinationQuery =
    " SELECT *" .
    " FROM iherba_determination" .
    " WHERE id = '" . $determinationId . "'";
  
  $determinationResult = mysql_query($determinationQuery) or die (mysql_error());

  assert(mysql_num_rows($determinationResult) == 1);
  $determination = mysql_fetch_assoc($determinationResult);
  
  // Obs
  $obsQuery =
    " SELECT *" .
    " FROM iherba_observations" .
    " WHERE idobs = '" . $determination["id_obs"] . "'";
  
  $obsResult = mysql_query($obsQuery) or die (mysql_error());

  assert(mysql_num_rows($obsResult) == 1);
  $obs = mysql_fetch_assoc($obsResult);
  
  // User
  $userQuery =
    " SELECT *" .
    " FROM fe_users" .
    " WHERE uid = '" . $obs['id_user'] . "'";
  
  $userResult = mysql_query($userQuery) or die (mysql_error());

  assert(mysql_num_rows($userResult) == 1);
  $user = mysql_fetch_assoc($userResult);
  
  // notification
  $parameters = array();
  $parameters[determination]=$determinationId;
  $parameters[owner]=$obs['id_user'];
  $notifQuery =
   " INSERT INTO `iherba_notification` (   `message_type`, `preferred_language`, `parameters`)
   VALUES ('somebody-say', '$mylanguage', '".json_encode($parameters)."');";
  $notifResult = mysql_query($notifQuery) or die (mysql_error());
  
}

/* record comment about a determination*/
function preciser_determination_comment($monobjet){
  global $id_tropicos;
  $content=$monobjet->pi_getLL('comment_submitted_ok', '', 1);
  $reaction_case = desamorcer($_POST['reaction']);
  $reaction_comment = desamorcer($_POST['remarque']);
  $numdet = desamorcer($_GET['numdet']);
  $iduser = $_SERVER['REMOTE_ADDR'];
  
  //if empty return
  if(($reaction_case =="") && ($reaction_comment==""))
     return $content;
  
  //
  $disabled = 0;
  if(strpos($reaction_comment,"http")!== false)$disabled = 1;
  if(strpos($reaction_comment,"www")!== false)$disabled = 1;
  bd_connect();
  $sql_insertion_determination =
    " INSERT INTO iherba_determination_reaction " .
    " (id_determination, id_user, comment, reactioncase ,disabled)" . 
    " VALUES ('$numdet','$iduser','$reaction_comment','$reaction_case' ,$disabled)"; // ADDED certitudeLevel & precisionLevel
	 

  $result_insertion_determination = mysql_query($sql_insertion_determination) or die ('Erreur SQL !'.$sql_insertion_determination.'<br />'.mysql_error());	 


  return $content;

}

/* Cette fonction affiche les informations trouvÈes gr‚ce ‡ l'identifiant que nous fournit l'utilisateur (le nom de la plante, la famille ‡ laquelle
 * elle appartient, son genre, son espËce)*/
function preciser_determination($monobjet){
  global $id_tropicos;
  $content="";
  $site = "";
  if(isset($_POST['id_tropicos'])){
    $id_tropicos=desamorcer($_POST['id_tropicos']); // on rÈcupËre l'identifiant entrÈ dans le formulaire par l'utilisateur
    if(($id_tropicos!="")&&(!(ctype_digit($id_tropicos))))die("<!--warning not ctype -->"); // anti sql injection
    $name_url= "http://services.tropicos.org/Name/"."$id_tropicos"."?apikey=ea95b5c7-e6e9-41af-8b1b-4bd5e8db61c3&format=json";
    $highertaxa_url= "http://services.tropicos.org/Name/"."$id_tropicos"."/HigherTaxa?apikey=ea95b5c7-e6e9-41af-8b1b-4bd5e8db61c3&format=json";
  }
	

  if(isset($_POST['nom_commun'])){
    $nom_commun=desamorcer($_POST ['nom_commun']);
    $nom_commun= desarmorcer($nom_commun);
  }

  // Kuba ->
	
  // Comment & Comment Case
	
  $paramFromPost = function($param, $default) {
	  	  
    if(isset($_POST[$param]))
      return $_POST[$param];
    else
      return $default;

  };

  $comment = $paramFromPost('comment', "");	
  $commentCase = $paramFromPost('commentCase', 0); // P Laroche = the post value seems to be missing, even with legitimate post, so i prefer 0 as default rather than -1
  $certitudeLevel = $paramFromPost('certitudeLevel', 0);	
  $precisionLevel = $paramFromPost('precisionLevel', 0);
  
  $instanciation_observation = $paramFromPost('instanciation', 0);
  // <- Kuba

  $value_name_url = file_get_contents($name_url);
  $value_highertaxa = file_get_contents($highertaxa_url);
  $nom_plante=rechercher_nom_plante($value_name_url);

  rechercher_famille_plante($value_highertaxa,$arrayhighertaxa);
	
  if($_POST['id_tropicos'] !=""){
    $content.=$monobjet->pi_getLL('infoTropicos', '', 1).$id_tropicos.$monobjet->pi_getLL('sont', '', 1)."<br/>";
  }
  
  if($nom_commun !=""){
    $content.=$monobjet->pi_getLL('infoUser', '', 1)."<br/>";
    $content.="<br/>".$monobjet->pi_getLL('nomCommun', '', 1).$nom_commun."<br/>";
  }
	
  if($nom_plante !=""){
    $content.="<br/>".$monobjet->pi_getLL('nomScientifique', '', 1).$nom_plante."<br/>";
  }
  
  if($arrayhighertaxa['familyname'] !=""){
    $content.="<br/> ".$monobjet->pi_getLL('famille', '', 1).$arrayhighertaxa['familyname']."<br/>" ;
  }
  if($arrayhighertaxa['genusname'] !=""){
    $content.="<br/>". $monobjet->pi_getLL('genre', '', 1).$arrayhighertaxa['genusname']."<br/>";
  }
  if($espece_plante !=""){
    $content.="<br/>".$monobjet->pi_getLL('espece', '', 1).$espece_plante."<br/>";
  }
	
  $content.="<br/><a href=index.php?id=19&L=".$GLOBALS['TSFE']->sys_language_uid.">".$monobjet->pi_getLL('retourHerbier', '', 1)."</a><br/>";
	
  $refuser = $GLOBALS['TSFE']->fe_user->user['uid'];
  if ($refuser==0)$refuser=$_SERVER['REMOTE_ADDR']; //adresse IP de l'utilisateur
	
  // Kuba ->

  $determinationId = 
    remplir_table_determination($nom_commun,
				$nom_plante,
				$arrayhighertaxa['familyname'],
				$arrayhighertaxa['genusname'],
				$espece_plante,
				$refuser, 
				/* Kuba -> */ 
				$comment, 
				$commentCase,
				$certitudeLevel,
				$precisionLevel
				/* <- Kuba */ 
				,$id_tropicos,
				$arrayhighertaxa['familyid'],
				$arrayhighertaxa['genusid'],
				$instanciation_observation);

  if($refuser == $GLOBALS['TSFE']->fe_user->user['uid'])
   notifyUserAboutDetermination($determinationId,$monobjet);
   else
   mail('agoralogie@gmail.com','commentaire anonyme'," observation : ".desamorcer($_GET['numero_observation'])." commentaire $nom_commun $comment determination $determinationId");
	
  // <- Kuba

  return $content;
}
?>
