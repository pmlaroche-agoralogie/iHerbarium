<?php

// This script send data to the gbif
// but only the observations of the best quality

include("../bibliotheque/common_functions.php");


$base_request = "
SELECT * 
FROM  `iherba_observations` , iherba_determination, iherba_determination_reaction, fe_users
WHERE public = 'oui' and iherba_observations.id_user = fe_users.uid
AND id_obs = idobs
AND id_determination = iherba_determination.id
AND iherba_determination.certitude_level =1
AND reactioncase =  'sure'
AND latitude !=  '0'
ORDER BY  `iherba_determination_reaction`.`creation_ts` ASC 
";

bd_connect();
$result_sure = mysql_query($base_request) or die ('Erreur SQL ! <br />');

$csv_separator_field = ";";
$csv_end_of_line = "\n\r";

$export_csv = '"institutionCode"'.$csv_separator_field ;
$export_csv .= '"collectionCode"'.$csv_separator_field ;
$export_csv .= '"catalogNumber"'.$csv_separator_field ;
$export_csv .= '"scientificName"'.$csv_separator_field ;

$export_csv .= '"decimalLatitude"'.$csv_separator_field ;
$export_csv .= '"decimalLongitude"'.$csv_separator_field ;
$export_csv .= '"coordinatePrecision"'.$csv_separator_field ;

$export_csv .= '"eventDate"'.$csv_separator_field ;//iso 
$export_csv .= '"recordedBy"'.$csv_separator_field ;
$export_csv .= '"associatedMedia"'.$csv_separator_field ;
$export_csv .= '"basisOfRecord"'.$csv_end_of_line;

while($row_obs= mysql_fetch_assoc($result_sure) ){
        $export_csv .= '"agoralogie"'. $csv_separator_field ;
        $export_csv .= '"iherbarium"'. $csv_separator_field ;
        $export_csv .= '"'.$row_obs['uuid_observation'].'"'. $csv_separator_field ;
        $export_csv .= '"'.$row_obs['nom_scientifique'].'"'. $csv_separator_field ;
        
        $export_csv .= '"'.$row_obs['latitude'].'"'. $csv_separator_field ;
        $export_csv .= '"'.$row_obs['longitude'].'"'. $csv_separator_field ;
        $export_csv .= '"50meters"'. $csv_separator_field ;
        
        $export_csv .= '"'.substr($row_obs['deposit_timestamp'],0,10).'"'. $csv_separator_field ;
        $export_csv .= '"'.utf8_encode($row_obs['name']).'"'. $csv_separator_field ;
        $export_csv .= '"'."http://www.iherbarium.fr/observation/data/".$row_obs['idobs'].'"'. $csv_separator_field ;
        $export_csv .= '"'."HumanObservation".'"';
        $export_csv .= $csv_end_of_line;
       }


$filename = "gbif_".time().".csv";
file_put_contents("gbif_sets/".$filename,$export_csv);
echo "<a href=http://www.iherbarium.fr/scripts/gbif_sets/".$filename." >Lien vers le fichier $filename </a>"."<br>";
?>
