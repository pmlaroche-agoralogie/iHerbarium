<?php
// use http://wiki.openstreetmap.org/wiki/Nominatim#Example

require_once("../bibliotheque/common_functions.php");

function dieanddebug()
{ echo "die";
 die(mysql_error());
 //die();
}
bd_connect();

$sql_new_addition =
    "SELECT * 
FROM  `iherba_determination` 
WHERE  `tropicosfamilyid` !=  '' and tropicosgenusid != '' ORDER BY id DESC  ";
  echo $sql_new_addition;
$notifications = mysql_query($sql_new_addition) or dieanddebug();
if(mysql_num_rows($notifications)==0)die("0 to loc");

while($fiche= mysql_fetch_assoc($notifications))
 {
  
    $update_uuid =" INSERT INTO `typoherbarium`.`iherba_ref_taxonomique` (`id`, `referentiel_taxonomique`, `id_referentiel`, `rang`, `nom_commun`, `nom_scientifique`, `genus`, `family`, `reftaxoplusidgenus`, `familyreftaxoplusid`, `creation_timestamp`, `reftaxonomiqueplusid`, `scientificname_wo_authors`, `scientificname_html`) VALUES ";
    $update_uuid .="(NULL, 'tropicos', '$fiche[tropicosid]', 'species', '$fiche[nom_commun]', '$fiche[nom_scientifique]', '$fiche[genre]', '$fiche[famille]', 'tropicos:$fiche[tropicosgenusid]', 'tropicos:$fiche[tropicosfamilyid]', CURRENT_TIMESTAMP, '"."tropicos:".$fiche[tropicosid]."', '$fiche[scientificname_wo_authors]', '$fiche[scientificname_html]');";
echo $update_uuid;
 $updateuuidresult = mysql_query($update_uuid) ;
 
 }

 

?>
