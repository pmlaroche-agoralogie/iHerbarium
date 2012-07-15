<html>
<head>
</head>
<body >
<table>
  <tr> <td> Observation class&eacute;e</td><td>Espece Tropicos</td><td>Rang dans la solution trouv&eacute;e</td><td>Rang du genre</td><td>Rang de la famille </td></tr>
<?php
include("../bibliotheque/common_functions.php");
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
ObservationId, 6
)
AND NOT (
id_this_obs LIKE  '%null%'
)
ORDER BY ObservationId, TIMESTAMP DESC  ";

$result_ref = mysql_query($sql_ref)or die ('Erreur SQL !'.$sql_ref.'<br />'.mysql_error());
 
$previous_id = 0;
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
  
    if (isset($solutions->listgenus))
	$list_genus = $solutions->listgenus;
      else
	$list_genus =array();
	
    if (isset($solutions->listfamilies))
	$list_family = $solutions->listfamilies;
      else
	$list_family =array();

    if(!isset($current_id->species))
      $current_id->species = "Z";
    $resultatspec = array_search($current_id->species,$list_spec);
    

    if(!isset($current_id->genus))
      $current_id->genus = "XXXXXXX";
    $resultatgenus = array_search($current_id->genus,$list_genus);

    
    if(!isset($current_id->family))
      $current_id->family = "XXXXX";
    $resultatfamily = array_search($current_id->family,$list_family);
      
   $sql_exist_det = "SELECT * FROM iherba_determination WHERE inGroup( id_obs, 3 ) and iherba_determination.tropicosid = ".$current_id->species;
      $result_det = mysql_query($sql_exist_det)or die ('Erreur SQL !'.$sql_exist_det.'<br />'.mysql_error());
      $nbdet = mysql_num_rows($result_det);
      
    //if(($current_id->species !="")&&($current_id->species !="Z"))
      if($nbdet>0)
      {
	 if($resultatspec  === false)
	  {$resultatspec = $value_pas_trouve ;$nbspec_nok ++; }
	  else
	  {if($resultatspec<6)$nbtriplespec++;
	$resultatspec = '<font color="green">'.$resultatspec."</font>";
	$nbspec_ok ++;
	}
	
	if($resultatgenus  === false)
	  {$resultatgenus = $value_pas_trouve ;$nbgenus_nok ++; }
	  else
	  {
	   if($resultatgenus<6) $nbtriplegenus ++;
	$resultatgenus = "<font color=green>".$resultatgenus."</font>";
	$nbgenus_ok ++;
	}
	
	if($resultatfamily === false)
	{$resultatfamily = $value_pas_trouve ;$nbfamily_nok ++; }
	else
	{
	   if($resultatfamily<6)$nbtriplefamily++;
	$resultatfamily = "<font color=green>".$resultatfamily."</font>";
	$nbfamily_ok ++;
	}
	
      $surligne="";
      //if( in_array($row_quest['ObservationId'],$grouperef))$surligne.=" non gr3 :";
     

      
      if(($current_id->species =="")||($current_id->species =="Z"))$surligne.="DET :";
      echo "<tr>";
      echo '<td>'.$surligne.'<a href="../?id=21&numero_observation='.$row_quest['ObservationId'].'">Observation '.$row_quest['ObservationId'].' </a></td>';
      echo " <td>".$current_id->species." - $nbdet -</td><td>  ".$resultatspec." </td><td>".$resultatgenus." </td><td> ".$resultatfamily."</td>";
      echo "</tr>";

      $nbligne ++;
      $previous_id=$row_quest['ObservationId'];
      }
      else
      echo "<tr><td>".'<a href="../?id=21&numero_observation='.$row_quest['ObservationId'].'">Observation '.$row_quest['ObservationId']." sans nom tropicos dans gr 3</td></tr>";
      }
    }
    echo "<tr>";
    echo "<td> Nb ligne groupe 6  </a></td>";
    echo " <td> $nbligne </td><td>  ". sprintf("%01.2f",  ($nbspec_ok /$nbligne) ) ." </td><td>".sprintf("%01.2f",($nbgenus_ok/$nbligne)) ." </td><td> ".sprintf("%01.2f",($nbfamily_ok/$nbligne) )."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td> Arrivé placé</a></td>";
    echo " <td>  </td><td>  ". sprintf("%01.2f",  ($nbtriplespec /$nbligne) ) ." </td><td>".sprintf("%01.2f",($nbtriplegenus/$nbligne)) ." </td><td> ".sprintf("%01.2f",($nbtriplefamily/$nbligne) )."</td>";
    echo "</tr>";
?>
</table>

</body>
</html>