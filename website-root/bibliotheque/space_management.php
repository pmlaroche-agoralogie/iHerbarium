<?php
// management of study of little areas

require_once("connexion.php");

/* fonction qui permet d'afficher toutes les observations  determinées */
function liste_area($monobjet,$account_manager ){
  $content= "";
  
  bd_connect();
  
  
  //$curent_zone = get_sousdomaine();
  
  $sql_list_zone = "SELECT * from iherba_area,iherba_indicateurs_zones where account_manager = $account_manager and iherba_area.best_indicateurs_zones = iherba_indicateurs_zones.uid";	

 $result = mysql_query ($sql_list_zone) or die ();
 
while ($donnees = mysql_fetch_array($result)){
	  
	  
	  $content .= "<h3>".$donnees['areaname']."</h3>";
	  $content .= "".$donnees['notes']."<br>";
	  $content.= "<strong>Grille d'analyse pr&eacute;f&eacute;r&eacute;e : </strong><br>";
	  $content .= "Quadrillage de ".$donnees['nb_square_lat']. " x ".$donnees['nb_square_long'].", quadrats de ".$donnees['deltalat']. "&deg; par ".$donnees['deltalong']."&deg;<br>";
	  $content .= "Filtrage actif : certitude elev&eacute;e, &eacute;cretage des observations uniques<br>";
	  $content.= "<br>Sous-zones d&eacute;taill&eacute;es : ";
	  $zones = json_decode($donnees['zonage_quadrats']);
	  foreach($zones as $onezone)
	    {
	      $content.= $onezone->name ;
	      $listeq = $onezone->quadlist;
	      $content.= "(".count($listeq)." quadrats)";
	      $content.= " ";
	    }
	  $content.= "<br><strong>R&eacute;sultats : </strong><br>";
	  $resultats = json_decode($donnees['resultatIHB']);
	  foreach($resultats as $oneyear)
	    {
	      $content.= $oneyear->period ;
	      $listearea = $oneyear->arearesult;
	      $allzone = array();
	      foreach($listearea as $onezone)
		  {
		    if( $onezone->name =="all")
		    {
		    $allzone = $onezone;
		    }
		  }
	      $indice = (($allzone->nbspecies * 1) + ($allzone->nbgenus * 2.3) + ($allzone->nbfmilies*3))/3;
	      $indice = round(log($indice,12),1);
	      $content.= " : Indice (sur l'ensemble du p&eacute;rim&egrave;tre d&eacute;fini) <strong>$indice </strong> Especes ".$allzone->nbspecies." Genres ".$allzone->nbgenus." Familles ".$allzone->nbfamilies." ";
	      $content.= " <br> ";
	    }
  }
  $resulta=array();
  $resulta[] = array("period"=>"2006","arearesult"=>$zones);
  $resulta[] = array("period"=>"2007","arearesult"=>$zones);
  //echo json_encode($resulta);
  return $content;
}


?>