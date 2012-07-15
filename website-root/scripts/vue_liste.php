<html>
<head>
</head>
<body >
<?php
$limit_x=21;

include("../bibliotheque/common_functions.php");
$groupeetudie = 6; 
if(isset($_GET['groupe']))$groupeetudie = desamorcer($_GET['groupe']); 

echo "Groupe &eacute;tudi&eacute; :".$groupeetudie ."<br>"; ?>
<table>
  <tr> <td> Observation class&eacute;e</td><td>Espece Tropicos</td><td>Rang dans la solution trouv&eacute;e</td><td>Rang du genre</td><td>Rang de la famille </td></tr>
<?php

bd_connect();
$grouperef = array();
$sql_ref = "SELECT distinct idobs   FROM iherba_observations    WHERE inGroup(  idobs, 3  )";
$result_ref = mysql_query($sql_ref)or die ('Erreur SQL !'.$sql_ref.'<br />'.mysql_error());
while($row_quest= mysql_fetch_assoc($result_ref) ){
  $grouperef[] = $row_quest['idobs'];
  }
    
$value_pas_trouve='<font color="red">X</font>';
$nbtriplespec = $nbtriplegenus = $nbtriplefamily  = $nbligne = $nbspec_ok = $nbspec_nok = $nbgenus_ok = $nbgenus_nok = $nbfamily_ok = $nbfamily_nok = 0 ;


$sql_ref = "SELECT * 
FROM iherba_questions_finished_log
WHERE inGroup(
ObservationId, $groupeetudie
)

ORDER BY ObservationId, TIMESTAMP DESC  ";

$result_ref = mysql_query($sql_ref)or die ('Erreur SQL !'.$sql_ref.'<br />'.mysql_error());
 
$previous_id = 0;
$codetableau = "";
$carre_distance =0;

while($row_quest= mysql_fetch_assoc($result_ref) )
    {
    $current_id = json_decode($row_quest['id_this_obs']);
    
    if((!isset($current_id->groupe))&&($previous_id!=$row_quest['ObservationId']))
      {


    $solutions = json_decode($row_quest['ids_list_obs']);
    
    if (isset($solutions->listspecies))
	$list_spec = $solutions->listspecies;
      else
	$list_spec =array();
 /* 
    if (isset($solutions->listgenus))
	$list_genus = $solutions->listgenus;
      else
	$list_genus =array();
	
    if (isset($solutions->listfamilies))
	$list_family = $solutions->listfamilies;
      else
	$list_family =array();
 */
    if(!isset($current_id->species))
      $current_id->species = "11111111111";
    

    if(!isset($current_id->genus))
      $current_id->genus = "22222222222222222";

    
    if(!isset($current_id->family))
      $current_id->family = "333333333333";

 
 // $sql_exist_det = "SELECT * FROM iherba_determination WHERE inGroup( id_obs, 3 ) and iherba_determination.tropicosid = ".$current_id->species;
  //   $result_det = mysql_query($sql_exist_det)or die ('Erreur SQL !'.$row_quest['ObservationId'] ." - ".$sql_exist_det.'<br />'.mysql_error());
 //    $nbdet = mysql_num_rows($result_det);
  $nbdet=1;
  
    //if(($current_id->species !="")&&($current_id->species !="Z"))
      if($nbdet> -1)
      { 
	 if($row_quest['order_species'] > $limit_x)
	  {$resultatspec = $value_pas_trouve ;$nbspec_nok ++; }
	  else
	  {
	    $carre_distance += $row_quest['order_species'] * $row_quest['order_species'];
	    if($row_quest['order_species']<6)$nbtriplespec++;

	$nbspec_ok ++;
	}
	
	if($row_quest['order_genus'] > $limit_x)
	  {$resultatgenus = $value_pas_trouve ;$nbgenus_nok ++; }
	  else
	  { 
	   if($row_quest['order_genus']<6) $nbtriplegenus ++;
	$nbgenus_ok ++;
	}
	
	if($row_quest['order_family'] > $limit_x)
	{$resultatfamily = $value_pas_trouve ;$nbfamily_nok ++; }
	else
	{
	   if($row_quest['order_family']<6)$nbtriplefamily++;
	$nbfamily_ok ++;
	}
	
      $surligne="";
      //if( in_array($row_quest['ObservationId'],$grouperef))$surligne.=" non gr3 :";
     

      
      //if(($current_id->species =="")||($current_id->species =="Z"))$surligne.="DET :";
      if($nbdet==0)$nbdet = "  ! CIBLE ";//$resultatspec
      
      $ligne_reponse ="";
      $ligne_reponse .= '<td>'.$surligne.'<a href="../?id=21&numero_observation='.$row_quest['ObservationId'].'">Observation '.$row_quest['ObservationId'].' </a></td>';
      $ligne_reponse .= " <td>".$current_id->species." - $nbdet -</td><td>";
      $ligne_reponse .= ($row_quest['order_species'] > $limit_x) ? $value_pas_trouve : $row_quest['order_species'];
      
      $ligne_reponse .= " </td><td>";
      $ligne_reponse .= ($row_quest['order_genus'] > $limit_x) ? $value_pas_trouve : $row_quest['order_genus'];
      $ligne_reponse .= " </td><td>";
      $ligne_reponse .= ($row_quest['order_family'] > $limit_x) ? $value_pas_trouve : $row_quest['order_family'];
      $ligne_reponse .= " </td><td> ";
      
      
      $nbligne ++;
      //$previous_id=$row_quest['ObservationId'];
      }
      else
      $ligne_reponse = "<td>".'<a href="../?id=21&numero_observation='.$row_quest['ObservationId'].'">Observation '.$row_quest['ObservationId']." sans nom tropicos dans gr 3</td>";
      
      $codetableau .= "<tr>" .$ligne_reponse . "</tr>";
      }
    $previous_id=$row_quest['ObservationId'];
    //if($current_id->species == "2711967")die();
    }
    echo $codetableau;
    echo "<tr>";
    echo "<td> Nb ligne groupe $groupeetudie  </a></td>";
    echo " <td> $nbligne </td><td>  ". sprintf("%01.2f",  ($nbspec_ok /$nbligne) ) ." ".$row_quest['order_species']."".$row_quest['order_genus']."".$row_quest['order_family']." </td><td>".sprintf("%01.2f",($nbgenus_ok/$nbligne)) ." </td><td> ".sprintf("%01.2f",($nbfamily_ok/$nbligne) )."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td> Arrive dans 6 premiers</td>";
    echo " <td>  </td><td>  ". sprintf("%01.2f",  ($nbtriplespec /$nbligne) ) ." </td><td>".sprintf("%01.2f",($nbtriplegenus/$nbligne)) ." </td><td> ".sprintf("%01.2f",($nbtriplefamily/$nbligne) )."</td>";
    echo "</tr>";
    
    echo "<td> sigma carre distance </td>";
    echo " <td>  $carre_distance  </td><td>  sqrt sigma2 / nb2 = ". sqrt( $carre_distance / ( $nbspec_ok*$nbspec_ok))." </td><td> sqrt sigma2 / nb = ". sqrt( $carre_distance / $nbspec_ok) ." </td><td> ".""."</td>";
    echo "</tr>";
    
?>
</table>

</body>
</html>
