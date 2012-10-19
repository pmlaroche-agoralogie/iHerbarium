<?php
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
$result_sure = mysql_query($base_request)or die ('Erreur SQL !'.$list_zones.'<br />'.mysql_error());

while($row_obs= mysql_fetch_assoc($result_sure) ){
    
        echo $row_obs['nom_scientifique'];
        echo $row_obs['latitude']. " ";echo $row_obs['longitude']. " ";
        echo $row_obs['longitude']. " ";
        echo $row_obs['name']. " ";
        echo "http://www.iherbarium.fr/observation/data/".$row_obs['idobs']. " <br>";
       }


?>
