<?php
include("../bibliotheque/common_functions.php");

$myzone = 1;
if(isset($_GET['numerozone']))if(is_numeric($_GET['numerozone']))$myzone=desamorcer($_GET['numerozone']);



function traite_square($ilong,$ilat,$nbiter,$detail_zone)
{   global $myzone;

   
    $startlat = $detail_zone['startlat'];
    $startlong=  $detail_zone['startlong'];
    
    $deltalat = $detail_zone['deltalat'];
    $deltalong = $detail_zone['deltalong'];
    

   
    global  $fieldname_lat ,$fieldname_long ;
    global $fieldname_tropicosid ,$fieldname_tropicos_species_name;
    global $fieldname_genusid ,$fieldname_tropicos_genus_name ;
    global $fieldname_familiesid ,$fieldname_tropicos_families_name ;
    global $base_request,$where_clause;
 
    $req_observation_square = $base_request . " AND $fieldname_lat > ".($startlat+(($ilat )* $deltalat)). " AND $fieldname_lat < ".($startlat+(($ilat + 1)* $deltalat)). " ";
    $req_observation_square .= " AND $fieldname_long > ".($startlong+(($ilong )* $deltalong)). " AND $fieldname_long < ".($startlong+(($ilong + 1)* $deltalong)). " ";
     $req_observation_square .= " AND ".$where_clause[$nbiter];
     
    $listresultat =array();
    $list_species = "select distinct($fieldname_tropicosid) , $fieldname_tropicos_species_name ".$req_observation_square;
    $result_ref = mysql_query($list_species)or die ('Erreur SQL !'.$list_species.'<br />'.mysql_error());

    echo $list_species.'<br><br>';
 
    while($row_quest= mysql_fetch_assoc($result_ref) ){
      $listresultat[] = desamorcer($row_quest[$fieldname_tropicos_species_name]);
      }
    $species_names = implode(',',$listresultat);
    $nb_species = count($listresultat);
    
    $listresultat =array();
    $list_species = "select distinct($fieldname_genusid) , $fieldname_tropicos_genus_name ".$req_observation_square;
    $result_ref = mysql_query($list_species)or die ('Erreur SQL !'.$list_species.'<br />'.mysql_error());
    while($row_quest= mysql_fetch_assoc($result_ref) ){
      $listresultat[] = desamorcer($row_quest[$fieldname_tropicos_genus_name]);
      }
    $genus_names = implode(',',$listresultat);
    $nb_genus = count($listresultat);
    
    $listresultat =array();
    $list_species = "select distinct($fieldname_familiesid) , $fieldname_tropicos_families_name ".$req_observation_square;
    $result_ref = mysql_query($list_species)or die ('Erreur SQL !'.$list_species.'<br />'.mysql_error());
    while($row_quest= mysql_fetch_assoc($result_ref) ){
      $listresultat[] = $row_quest[$fieldname_tropicos_families_name];
      }
    $families_names = implode(',',$listresultat);
    $nb_families = count($listresultat);

  
      $zone = "";
    $zone = '"<Polygon><outerBoundaryIs><LinearRing><coordinates>' ;
   $zone .= "  ".$startlong + ($ilong + 0) * $deltalong  . ',';
   $zone .= $startlat + (($ilat + 0)* $deltalat) ."\r";
    $zone .= "  ".$startlong + ($ilong + 1) * $deltalong  . ',';
   $zone .= $startlat + (($ilat + 0)* $deltalat) ."\r";
    $zone .= "  ".$startlong + ($ilong + 1) * $deltalong  . ',';
   $zone .= $startlat + (($ilat + 1)* $deltalat) ."\r";
   $zone .= "  ".$startlong + ($ilong + 0) * $deltalong  . ',';
   $zone .= $startlat + (($ilat + 1)* $deltalat) ;
   $zone .= '</coordinates></LinearRing></outerBoundaryIs></Polygon>"';
   
   $req_insert = "INSERT INTO `iherba_indicateurs_donnees_compilees` (`projet`, `x`, `y`, `polygone`, `iteration`, `nb_species`, `nb_genus`,
        `nb_families`, `species_names`, `genus_names`, `families_names`) VALUES (";
    $req_insert .=     "$myzone, '$ilong', '$ilat', '$zone', '$nbiter', '$nb_species', '$nb_genus', '$nb_families', '$species_names', '$genus_names', '$families_names');";
   $result_ref = mysql_query($req_insert)or die ('Erreur SQL !'.$req_insert.'<br />'.mysql_error());
  echo $req_insert.'<br><br>';

}

bd_connect();

//$fieldname_lat = "lat";
$fieldname_lat = "latitude";
//$fieldname_long = "lng";
$fieldname_long = "longitude";
$fieldname_tropicosid = "tropicosid";
$fieldname_tropicos_species_name = "nom_scientifique";


//$fieldname_genusid = "genusid";
$fieldname_genusid = "tropicosgenusid";
//$fieldname_tropicos_genus_name = "genus_name";
$fieldname_tropicos_genus_name = "genre";
//$fieldname_familiesid = "familyid";
$fieldname_familiesid = "tropicosfamilyid";
//$fieldname_tropicos_families_name = "families_name";
$fieldname_tropicos_families_name = "famille";


$base_request = "
FROM  `newtmp` , lien_ns_tropicos
WHERE newtmp.ns = lien_ns_tropicos.ns
AND lien_ns_tropicos.genusid !=  '-1' ";

$base_request = "
FROM  `iherba_observations` ,  iherba_determination
WHERE iherba_observations.idobs = iherba_determination.id_obs
AND iherba_determination.tropicosid !=  '' ";

$where_clause = array ( "   idobs <  500000 ");


$list_zones = "select * from iherba_indicateurs_zones where uid=$myzone";
$result_ref = mysql_query($list_zones)or die ('Erreur SQL !'.$list_zones.'<br />'.mysql_error());
$row_zone= mysql_fetch_assoc($result_ref) ;

  
$nb_square_lat = $row_zone['nb_square_lat'];
$nb_square_long = $row_zone['nb_square_long'];


$videdonnees = "delete FROM `iherba_indicateurs_donnees_compilees` WHERE projet = $myzone";
$result_ref = mysql_query($videdonnees)or die ('Erreur SQL !'.$videdonnees.'<br />'.mysql_error());

for ($iter =0; $iter < count($where_clause);$iter ++)
    for ( $ilong = 0; $ilong < $nb_square_long; $ilong++ ) {
    
        for ($ilat = 0; $ilat < $nb_square_lat; $ilat++)
            {
              traite_square($ilong,$ilat,$iter,$row_zone);
            }
       }


?>
