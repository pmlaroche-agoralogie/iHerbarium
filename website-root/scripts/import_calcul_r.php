<?php
$couleur = array ("red","yellow","#12FF14","purple","#FF1122","blue", "black","orange","#1212FF","#0608E2","#FF1188","#66FF11","#186790","#F01188","#66F011","#180790");

include("../bibliotheque/common_functions.php");
bd_connect();

$myzone = 1;
if(isset($_GET['numerozone']))$myzone=$_GET['numerozone'];

$myzone = 1;
if(!isset($_GET['numerozone']))die('parameter numerozone is required');
if(!is_numeric($_GET['numerozone']))die('parameter numerozone must be integer');
$myzone=$_GET['numerozone'];

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

//Recupere le fichier output de R
$donneesr = file("http://www.indicateurs-biodiversite.com/scripts/output_".$myzone);
for($zone=1;$zone<count($donneesr);$zone++)
 {
 $ligne = explode(" ",$donneesr[$zone]);
 $droite = substr($ligne[2],5);
 $coord = explode("-",$droite);
 $requete = "Update  iherba_indicateurs_donnees_compilees set numerogroupe = ".$ligne[1]." WHERE  projet =$myzone and x =$coord[1] and y=$coord[0]  ";
 $result_ref = mysql_query($requete)or die ('Erreur SQL !'.$requete.'<br />'.mysql_error());
 }

//charge lise de reference
$requete = "   SELECT * 
FROM  `famille_liste` 
ORDER BY  `famille_liste`.`nom_reference` ASC ";


$finligne = "\r\n";
$separateurfield=',';


$content = "";
$content .= '"carre"'.$separateurfield.'"couleur"'.$separateurfield.'"Nb especes"'.$separateurfield.'"Nb genre"'.$separateurfield.'"liste genre"'.$separateurfield.'"liste fam"'.$separateurfield.'"zone"'.$finligne;
for ( $ilong = 0; $ilong < $nb_square_long; $ilong++ ) {
 for ($ilat = 0; $ilat < $nb_square_lat; $ilat++)
   {
   $requete = "   SELECT * 
   FROM  `iherba_indicateurs_donnees_compilees` 
   WHERE  `x` = $ilong
   AND  `y` = $ilat AND iteration = 0 and projet = $myzone ";
   //echo $requete."<br>";
   $result_ref = mysql_query($requete)or die ('Erreur SQL !'.$requete.'<br />'.mysql_error());
   $row_quest= mysql_fetch_assoc($result_ref) ;
    $content  .= "";
      $content .= 'zone'.$ilong.'-'.$ilat.$separateurfield;
   $content .= $couleur[$row_quest['numerogroupe']].$separateurfield;//$row_quest['numerogroupe']."<br>".
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
$filename = "zone_".rand(100,900)."_".$myzone.".csv";
file_put_contents($filename,$content);
echo "<a href=http://www.iherbarium.fr/scripts/".$filename." >Lien vers le fichier</a>"."<br>";
echo $content;

?>
