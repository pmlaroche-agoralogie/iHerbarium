<?php
// management of widget for social interactions

require_once("connexion.php");

/* area list*/
function best_contributors($monobjet,$areaname ){
  $content= "";
  
  bd_connect();
  
  $sql_list_active_user = "SELECT iherba_observations.id_user, fe_users.name, count(idobs) as nb FROM `iherba_observations` , fe_users WHERE id_user = fe_users.uid
  and id_user != '622' and id_user != '493' and id_user != '855' and id_user != '25'   group by iherba_observations.id_user order by nb desc";	

  $result = mysql_query ($sql_list_zone) or die ();
 
  while ($donnees = mysql_fetch_array($result)){
    $content .= "<h3>".$donnees['name']."</h3>";
    $content .= "".$donnees['nb']."<br>";
    
  }
  
  return $content;
}


?>