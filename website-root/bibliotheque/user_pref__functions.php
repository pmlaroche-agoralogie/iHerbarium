<?php 
require_once("connexion.php");


function history_list_obshistory_list_obs($history_user,$mylanguage,$is_owner)
{
  if($is_owner)
    $sql_proprio =" AND 1 ";
    else
    $sql_proprio =" AND public = 'oui' ";
    
  $sql_recherche_obs="select * 
	from iherba_observations
	where iherba_observations.id_users=$history_user $sql_proprio order by deposit_timestamp ";
	
  
		
  $result_recherche_obs= mysql_query($sql_recherche_obs) or die ();
  while ($row_recherche_obs= mysql_fetch_assoc($result_recherche_obs)) {
    
    $content.= affiche_une_observation_dans_liste($cetobjet, $idobs,$type_recherche);
  }
	
  return $content;
}		

?>
