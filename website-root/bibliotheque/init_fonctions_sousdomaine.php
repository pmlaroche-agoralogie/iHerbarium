<?php 
global $etat_sousdomaine ;
global $etat_description_sousdomaine ;
global $etat_requete_where ;

global $searchlimitation;

function test_limitation_parameters()
{
  global $searchlimitation;
  $searchlimitation = null;
  session_start();
  if(isset($_SESSION['searchpref']))
    $searchlimitation=json_decode($_SESSION['searchpref']);
     
  if(isset($_GET['user_limitation']))
    {
    if($_GET['user_limitation']=="null")
      unset($searchlimitation->user_limitation);
	else	
      $searchlimitation->user_limitation = desamorcer($_GET['user_limitation']);
    }
  if(isset($_GET['localisation_limitation']))
    {
    if($_GET['localisation_limitation']=="null")
      unset($searchlimitation->localisation_limitation);
      else
      $searchlimitation->localisation_limitation = desamorcer($_GET['localisation_limitation']);
    }
  if(isset($_GET['area_limitation']))
    {
    if($_GET['area_limitation']=="null")
      unset($searchlimitation->area_limitation);
	else
      $searchlimitation->area_limitation = desamorcer($_GET['area_limitation']);
    }
  if(isset($_GET['species_limitation']))
    {
    if($_GET['species_limitation']=="null")
      unset($searchlimitation->species_limitation);
	else
      $searchlimitation->species_limitation = desamorcer($_GET['species_limitation']);
    }
  
    $_SESSION['searchpref'] = json_encode($searchlimitation);

}

function exists_sousdomaine($sousdomaine,&$data)
{
 // protect from injection sql (from character authorized in url
  $sousdomaine = str_replace("'","?",$sousdomaine);

  //detect if a species is called
 $posuser = strpos($sousdomaine,"species_");
  if($posuser===0)
    {
      $refuser = substr($sousdomaine,8);
          $data[ref] = $refuser;
          $data[type_ssdomaine]='species';
          return true;
    }
  $posuser = strpos($sousdomaine,"user_");
  if($posuser===0)
    {
      $refuser = substr($sousdomaine,5);
      bd_connect();
      $sql="select * from fe_users where uid='$refuser'";
      $result = mysql_query($sql) or die ();
      $nb_lignes_resultats=mysql_num_rows($result);
      $data  = array();
      if($nb_lignes_resultats>0){
	  $data = mysql_fetch_assoc($result);
	  $data[type_ssdomaine]='user';
	  return true;
      }
      
    }
    else
    $refuser = 0;
  bd_connect();
  $sql="select * from iherba_area where name='$sousdomaine'";
  $result = mysql_query($sql) or die ();
  $nb_lignes_resultats=mysql_num_rows($result);
  $data  = array();
  if($nb_lignes_resultats>0){
	  $data = mysql_fetch_assoc($result);
	  $data[type_ssdomaine]='area';
	  return true;
   }
   else {
    return false;
   }
}

function set_view_limitation($mylanguage="fr")
  {
  global $etat_description_limitation ;
  global $control_remove_limitation ;
  global $etat_requete_where ;
  global $searchlimitation;
  
  bd_connect();
  //default value
  $etat_requete_where = " 1 ";
  $etat_description_sousdomaine = "";
  $control_remove_limitation  = "";
  
  $alt_text = ' class="drilldown" title="'.get_string_language_sql("ws_view_limitation_alt_remove",$mylanguage).'" ' ;
  
  //limition to an area  
  if(isset($searchlimitation->area_limitation)){
    
    $detail_limitation = explode(":",$searchlimitation->area_limitation);
    if($detail_limitation[0]=="areaname")
	{
	$sql="select * from iherba_area where name='".$detail_limitation[1]."'";
	$result = mysql_query($sql) or die ();
	$nb_lignes_resultats=mysql_num_rows($result);
	if($nb_lignes_resultats>0){
		$area = mysql_fetch_assoc($result);
	 }
	 else die();//area unknown
	 
	$etat_requete_where .= " AND iherba_observations.latitude >".($area['center_lat']-$area['radius']). "";
	$etat_requete_where .= " AND iherba_observations.latitude < ".($area['center_lat']+$area['radius']). "";
	$etat_requete_where .= " AND iherba_observations.longitude > ".($area['center_long']-$area['radius']). "";
	$etat_requete_where .= " AND iherba_observations.longitude < ".($area['center_long']+$area['radius']). "";
	
	$etat_description_sousdomaine .= " ".get_string_language_sql("ws_view_limitation_area",$mylanguage)." : ". $area['areaname'];
	$control_remove_limitation .= "<a $alt_text class=drilldown href=###samepage###&area_limitation=null>".get_string_language_sql("ws_view_limitation_area",$mylanguage)." : ". $area['areaname']."</a><br>";
	}
      if($detail_limitation[0]=="circle")
	{
	  $area=explode(",",$detail_limitation[1]);
	  print_r($area);
	  $etat_requete_where .= " AND iherba_observations.latitude >".($area[0]-$area[2]). "";
	  $etat_requete_where .= " AND iherba_observations.latitude < ".($area[0]+$area[2]). "";
	  $etat_requete_where .= " AND iherba_observations.longitude > ".($area[1]-$area[2]). "";
	  $etat_requete_where .= " AND iherba_observations.longitude < ".($area[1]+$area[2]). "";
	  
	  $area['areaname'] = get_string_language_sql("ws_view_limitation_area_circle",$mylanguage)." ".$area[0]." ".$area[1]." R : ".$area[2];
	  $etat_description_sousdomaine .= " ".get_string_language_sql("ws_view_limitation_area",$mylanguage)." : ". $area['areaname'];
	  $control_remove_limitation .= "<a $alt_text class=drilldown href=###samepage###&area_limitation=null>".get_string_language_sql("ws_view_limitation_area",$mylanguage)." : ". $area['areaname']."</a><br>";
	}
    }
    
 //limitation to a user 
  if(isset($searchlimitation->user_limitation)){
    $etat_requete_where .=  " AND `iherba_observations`.id_user = '".$searchlimitation->user_limitation."' " ;
    
    $sqluser="select * from fe_users where uid=".$searchlimitation->user_limitation."";
    $result_user = mysql_query($sqluser) or die ();
    $nb_lignes_resultats=mysql_num_rows($result_user);
    if($nb_lignes_resultats>0){
	    $monuser = mysql_fetch_assoc($result_user);
     }
     else die();//user unknown
     
    $etat_description_sousdomaine .= " ".get_string_language_sql("ws_view_limitation_user",$mylanguage)."  : ".$monuser['name'];
    $control_remove_limitation .= "<a $alt_text class=drilldown href=###samepage###&user_limitation=null>".get_string_language_sql("ws_view_limitation_user",$mylanguage)." : ". $monuser['name']."</a><br>";
  }

 //limitation to a species
  if(isset($searchlimitation->species_limitation)){
    $detail_limitation = explode(":",$searchlimitation->species_limitation);
    $field_names = array("species"=>"computed_best_tropicos_id","genus"=>"computed_best_genus_id","family"=>"computed_best_family_id");
    if(!isset($field_names[$detail_limitation[0]]))
      {
	print_r($searchlimitation->species_limitation);
	die(" error ; loggued");
      }
    $field_text_names = array("species"=>"nom_scientifique","genus"=>"genre","family"=>"famille");
    
    $etat_requete_where .=  " AND `iherba_observations`.".$field_names[$detail_limitation[0]]." = '".$detail_limitation[1]."' " ;
    
    $field_det_names = array("species"=>"tropicosid","genus"=>"tropicosgenusid","family"=>"tropicosfamilyid");
    
    $sql_species="select * from iherba_determination where ".$field_det_names[$detail_limitation[0]]." = '".$detail_limitation[1]."' limit 1" ;
    $result_species = mysql_query($sql_species) or die ();
    $nb_lignes_resultats=mysql_num_rows($result_species);
    if($nb_lignes_resultats>0){
	    $ma_description= mysql_fetch_assoc($result_species);
     }
     else die();//user unknown
      
      
      $etat_description_sousdomaine .= " ".get_string_language_sql("ws_view_limitation_phylum_".$detail_limitation[0],$mylanguage)."  : ".$ma_description[$field_text_names[$detail_limitation[0]]];
      $control_remove_limitation .= "<a $alt_text  href=###samepage###&species_limitation=null> ".get_string_language_sql("ws_view_limitation_phylum_".$detail_limitation[0],$mylanguage)." : ".$ma_description[$field_text_names[$detail_limitation[0]]]."</a><br>";
  }

}


function set_sousdomaine($sousdomaine,$area)
  {
  global $etat_sousdomaine ;
  global $etat_description_sousdomaine ;
  global $etat_requete_where ;

  $etat_sousdomaine = $sousdomaine;
  
  //default value
  $etat_requete_where = " 1 ";
  $etat_description_sousdomaine = "";
  //limition to an area  
  if(($etat_sousdomaine != "www")&&($area[type_ssdomaine]=='area')&&($etat_sousdomaine != "wwwtest")){
    $etat_requete_where = " iherba_observations.latitude >".($area['center_lat']-$area['radius']). "";
    $etat_requete_where .= " AND iherba_observations.latitude < ".($area['center_lat']+$area['radius']). "";
    $etat_requete_where .= " AND iherba_observations.longitude > ".($area['center_long']-$area['radius']). "";
    $etat_requete_where .= " AND iherba_observations.longitude < ".($area['center_long']+$area['radius']). "";
    
    $etat_description_sousdomaine = $area['areaname'];
  }
 //limitation to a user 
  if(($etat_sousdomaine != "www")&&($area[type_ssdomaine]=='user')&&($etat_sousdomaine != "wwwtest")){
    $etat_requete_where =  " `iherba_observations`.id_user = '".$area['uid']."' " ;
    
    $etat_description_sousdomaine = "User : ".$area['name'];
  }

 //limitation to a species
  if(($etat_sousdomaine != "www")&&($area[type_ssdomaine]=='species')&&($etat_sousdomaine != "wwwtest")){
      $etat_requete_where =  " `iherba_observations`.computed_best_tropicos_id = '".$area['ref']."' " ;
      $etat_description_sousdomaine = "Species : ".$area;
  }
  
}

function get_description_sousdomaine($language = "fr")
{
  global $etat_description_sousdomaine ;

  // prevoir gestion langue
  return $etat_description_sousdomaine;
}

function get_requete_where_sousdomaine($language = "fr")
{
  global $etat_requete_where ;
  // prevoir gestion langue
  return $etat_requete_where;
}

function get_sousdomaine()
{
  global $etat_sousdomaine ;
  return $etat_sousdomaine;
}

function is_sousdomaine_www()
{
  global $etat_sousdomaine ;
  if(($etat_sousdomaine == "www")||($etat_sousdomaine == "api-test")||($etat_sousdomaine == "wwwtest"))
    return true;
      else
    return false;
}

//test a supprimer
//include("common_functions.php");
//test_limitation_parameters();
//set_view_limitation();
?>
