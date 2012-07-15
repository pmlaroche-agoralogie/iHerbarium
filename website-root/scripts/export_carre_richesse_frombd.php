<?php

include("../bibliotheque/common_functions.php");
bd_connect();

$myzone = 1;
if(!isset($_GET['numerozone']))die('parameter numerozone is required');
if(!is_numeric($_GET['numerozone']))die('parameter numerozone must be integer');
$myzone=$_GET['numerozone'];
$dist_methode=2;

if(isset($_GET['methode']))if(is_numeric($_GET['methode']))$dist_methode = $_GET['methode'];

echo "zone etudiee : $myzone avec methode $dist_methode <br>";
$list_zones = "select * from iherba_indicateurs_zones where uid=$myzone";
$result_ref = mysql_query($list_zones)or die ('Erreur SQL !'.$list_zones.'<br />'.mysql_error());
$row_zone= mysql_fetch_assoc($result_ref) ;
  
  
$startlat = $row_zone['startlat'];
$startlong=  $row_zone['startlong'];
$deltalat = $row_zone['deltalat'];
$deltalong = $row_zone['deltalong'];
$nb_square_lat = $row_zone['nb_square_lat'];
$nb_square_long = $row_zone['nb_square_long'];
$nbgroupes = $row_zone['nbgroupes'];


$finligne = "\r\n";
$separateurfield=',';

$content = "";
$content .= '"carre"'.$separateurfield.'"Nb especes"'.$separateurfield.'"Nb genre"'.$separateurfield.'"liste genre"'.$separateurfield.'"liste fam"'.$separateurfield.'"zone"'.$finligne;
for ( $ilong = 0; $ilong < $nb_square_long; $ilong++ ) {

 for ($ilat = 0; $ilat < $nb_square_lat; $ilat++)
  {
$requete = "   SELECT * 
FROM  `iherba_indicateurs_donnees_compilees` 
WHERE  `x` = $ilong
AND  `y` = $ilat AND iteration = 0 AND projet = $myzone ";
$result_ref = mysql_query($requete)or die ('Erreur SQL !'.$requete.'<br />'.mysql_error());
$row_quest= mysql_fetch_assoc($result_ref) ;
 $content  .= "";
   $content .= 'zone'.$ilat.'-'.$ilong.$separateurfield;
   $content .= $row_quest['nb_species'].$separateurfield;
   $content .= $row_quest['nb_genus'].$separateurfield;
   //$content .= '"'.$row_quest['species_names'].'"'.$separateurfield;
   $content .= '"'.$row_quest['genus_names'].'"'.$separateurfield;
   $content .= '"'.$row_quest['families_names'].'"'.$separateurfield;
   $content .= '"<Polygon><outerBoundaryIs><LinearRing><coordinates>';
    $content .= "  ".$startlong + ($ilong + 0) * $deltalong  . ',';
   $content .= $startlat + (($ilat + 0)* $deltalat) ."\r";
    $content .= "  ".$startlong + ($ilong + 1) * $deltalong  . ',';
   $content .= $startlat + (($ilat + 0)* $deltalat) ."\r";
    $content .= "  ".$startlong + ($ilong + 1) * $deltalong  . ',';
   $content .= $startlat + (($ilat + 1)* $deltalat) ."\r";
   $content .= "  ".$startlong + ($ilong + 0) * $deltalong  . ',';
   $content .= $startlat + (($ilat + 1)* $deltalat) ;
   $content .= '</coordinates></LinearRing></outerBoundaryIs></Polygon>"' .$finligne;
  }
}

file_put_contents("export_zone.csv",$content);

if($row_zone['type_matrice']=='espece')$nomfieldref = 'species_names';
if($row_zone['type_matrice']=='genre')$nomfieldref = 'genus_names';
if($row_zone['type_matrice']=='famille')$nomfieldref = 'families_names';

$requete = "   SELECT $nomfieldref FROM  `iherba_indicateurs_donnees_compilees` WHERE  iteration = 0 AND projet = $myzone ";
$result_ref = mysql_query($requete)or die ('Erreur SQL !'.$requete.'<br />'.mysql_error());
$liste_reference = array();
while($row_quest= mysql_fetch_assoc($result_ref) ){
	$tableau_present = explode(",",$row_quest[$nomfieldref]);
foreach($tableau_present as $fam)
	if($fam!='')
	if(!(in_array($fam,$liste_reference)))$liste_reference[] = $fam;
  }
 if(count($liste_reference)==0)
  {
   echo "Il n'y aucune espece dans l'ensemble de la zone";
   die();
  }
  
echo "nb de ".$row_zone['type_matrice']." = ".count($liste_reference)."<br>";
$content = ' '.$separateurfield;//depart ligne vide avec uniquement un espace
for($ref=0;$ref<(count($liste_reference)-1);$ref++)
 $content .= $liste_reference[$ref].$separateurfield;
$content .= $liste_reference[count($liste_reference)-1];
$content .= $finligne;

for ( $ilong = 0; $ilong < $nb_square_long; $ilong++ ) {

 for ($ilat = 0; $ilat < $nb_square_lat; $ilat++)
  {
$requete = "   SELECT * 
FROM  `iherba_indicateurs_donnees_compilees` 
WHERE  `x` = $ilong
AND  `y` = $ilat AND iteration = 0 AND projet = $myzone ";
$result_ref = mysql_query($requete)or die ('Erreur SQL !'.$requete.'<br />'.mysql_error());
$row_quest= mysql_fetch_assoc($result_ref) ;
 $content  .= "";
   $content .= 'zone_'.$ilat.'-'.$ilong.$separateurfield;
   for($ref=0;$ref<count($liste_reference);$ref++)
	 {
	  $present = strpos('sentinelle'.$row_quest[$nomfieldref], $liste_reference[$ref]);
	  if($present>0)
	   $content .= '1';
	   else
	   $content .= '0';
	   if($ref < (count($liste_reference) -1))
	     $content .= $separateurfield;
	 }
    $content .= $finligne;
  }
}
file_put_contents("export_zone_enum.csv",$content);

$data = base64_encode($content);

$params = array(
      'http' => array
      (
          'method' => 'POST',
'header'=>
      "Accept-language: en\r\n".
      "Content-type: application/x-www-form-urlencoded\r\n",
'content'=>http_build_query(array('idquestion'=>$myzone ,'data'=>$data , 'methode' => $dist_methode , 'nbgroupes' => $nbgroupes))
      )
   );
   $ctx = stream_context_create($params);

$resultat = file_get_contents('http://www.indicateurs-biodiversite.com/scripts/manage_matrix.php',false,$ctx);

echo $resultat;
echo $content;
?>
