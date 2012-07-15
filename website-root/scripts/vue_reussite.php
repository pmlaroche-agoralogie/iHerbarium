<html>
<head>
</head>
<body >
<?php
include("../bibliotheque/common_functions.php");
if(!isset($_GET['limit_pct']))$limit_pct = 0.051;else $limit_pct = desamorcer($_GET['limit_pct']);
if(!isset($_GET['rang']))$limit_pct = 0.051;else $rang = desamorcer($_GET['rang']);
function nettoie($c)
{
 $c = str_replace('(','',  $c);
 $c = str_replace(')','',  $c);
 return ($c);
}

$limit_x=21;
$nb5p = 0;
$total_ordre=$total_envoye=0;
if(isset($_GET['groupe']))$groupeetudie = desamorcer($_GET['groupe']); else 
$groupeetudie = 6; 
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
$nbcomparaison = $nbcomparaison_rg0 = $nbcomparaison_rg1 = $nbcomparaison_rg2 = 0;
$nbcomparaison_genus_rg0 = $nbcomparaison_genus_rg1 = $nbcomparaison_genus_rg2 = $nbcomparaison_genus = 0;
$nbcomparaison_family_rg0 = $nbcomparaison_family_rg1 = $nbcomparaison_family_rg2 = $nbcomparaison_family = 0;
while($row_quest= mysql_fetch_assoc($result_ref) )
    {
    $current_id = json_decode($row_quest['id_this_obs']);
    
    if((!isset($current_id->groupe))&&($previous_id!=$row_quest['ObservationId']))
      {
    $ligne_reponse ="";
$list_spec_comp =array(0 => 0);
	  $list_genus_comp =array(0 => 0);
	  $list_family_comp = array(0 => 0);
	  
    $solutions = json_decode($row_quest['ids_list_obs']);
    $topresults = explode(',',$row_quest['TopResults']);
    foreach ($topresults as &$value) {
	$value = explode(':',$value);
	}
    //print_r($topresults);die();
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
      $current_id->species = "NA Spec";
    

    if(!isset($current_id->genus))
      $current_id->genus = "NA genus";

    
    if(!isset($current_id->family))
      $current_id->family = "na families";


 // $sql_exist_det = "SELECT * FROM  iherba_determination WHERE inGroup( id_obs, 3 ) and iherba_determination.tropicosid = ".$current_id->species;
  //   $result_det = mysql_query($sql_exist_det)or die ('Erreur SQL !'.$row_quest['ObservationId'] ." - ".$sql_exist_det.'<br />'.mysql_error());
 //    $nbdet = mysql_num_rows($result_det);
 $sql_exist_comp = "SELECT * FROM iherba_determination_log  WHERE ObservationId = ".$row_quest['ObservationId']." and Timestamp > '2012-03-20 17:08:04' order by Timestamp ";
 $result_comp = mysql_query($sql_exist_comp)or die ('Erreur SQL !'.$row_quest['ObservationId'] ." - ".$result_comp.'<br />'.mysql_error());
 $nbcomp = mysql_num_rows($result_comp);
$list_spec_comp =array(0 => 0); // au moins une determination, fausse
 if($nbcomp >0)
 {
  $row_comp = mysql_fetch_assoc($result_comp) ;
  $resultatcomp = explode(",",substr($row_comp['Info'],9));
  foreach ($resultatcomp as &$value) {
    $value = str_replace("(","",$value);
    $value = str_replace(")","",$value);
	$value = explode(':',$value);
	}
  //$ligne_reponse .= " resultat ".$resultatcomp[0][0];
  
  $solutionscomp = json_decode($row_comp['ids_list_obs']);
  if (isset($solutionscomp->listspecies))
	{
	  $list_spec_comp = $solutionscomp->listspecies;
	   $list_genus_comp = $solutionscomp->listgenus;
	   $list_family_comp = $solutionscomp->listfamilies;
	}
      else
	{
	  $list_spec_comp =array(0 => 111111);
	  $list_genus_comp =array(0 => 111111);
	  $list_family_comp = array(0 => 111111);
	}
   
 }
  $nbdet=1;
  $key_comp = $key_comp_genus = $key_comp_family = "-";
    //if(($current_id->species !="")&&($current_id->species !="Z"))
      if($nbdet> -1)
      {
	 if($row_quest['order_species'] > $limit_x)
	  {$resultatspec = $value_pas_trouve ;$nbspec_nok ++; }
	  else
	  {
	  $carre_distance += $row_quest['order_species'] ;
	  if($row_quest['order_species']<$rang)$nbtriplespec++;
	  $topscore = nettoie( $topresults[0][1]);;
	  $myscore = nettoie($topresults[$row_quest['order_species']][1]);
	  $mypourcent = ($topscore - $myscore)/$topscore;
	  
	  
	  if($mypourcent < $limit_pct)
	    {
	      $nb5p++;
	      $total_ordre += $row_quest['order_species'];
	      $scorepct = $topscore * (1- $limit_pct);
	      $nbenvoye = 0;
	      while(($topresults[$nbenvoye][1] > $scorepct )&&($nbenvoye <19))$nbenvoye++;
	      $total_envoye += $nbenvoye;
	    }
	    
	  $key_comp = array_search($current_id->species, $list_spec_comp);
	  if($key_comp=== false) $key_comp="-";
	  if($key_comp !== '-'){
		  $nbcomparaison ++;
		  if($key_comp == 0)$nbcomparaison_rg0 ++;
		  if($key_comp == 1)$nbcomparaison_rg1 ++;
		  if($key_comp == 2)$nbcomparaison_rg2 ++;
		}
	  $nbspec_ok ++;
	  //echo " $nbcomparaison - $nbcomparaison_rg0 $nbspec_ok <br> ";
	  }
	
	if($row_quest['order_genus'] > $limit_x)
	  {$resultatgenus = $value_pas_trouve ;$nbgenus_nok ++; }
	  else
	  { 
	   if($row_quest['order_genus']<$rang) $nbtriplegenus ++;
	   $key_comp_genus = array_search($current_id->genus, $list_genus_comp);
	  if($key_comp_genus=== false) $key_comp_genus="-";
	  if($key_comp_genus !== '-'){
		  $nbcomparaison_genus ++;
		  if($key_comp_genus == 0)$nbcomparaison_genus_rg0 ++;
		  if($key_comp_genus == 1)$nbcomparaison_genus_rg1 ++;
		  if($key_comp_genus == 2)$nbcomparaison_genus_rg2 ++;
		}
	$nbgenus_ok ++;
	}
	
	if($row_quest['order_family'] > $limit_x)
	{$resultatfamily = $value_pas_trouve ;$nbfamily_nok ++; }
	else
	{
	   if($row_quest['order_family']<$rang)$nbtriplefamily++;
	   
	   $key_comp_family = array_search($current_id->family, $list_family_comp);
	  if($key_comp_family=== false) $key_comp_family="-";
	  if($key_comp_family !== '-'){
		  $nbcomparaison_family ++;
		  if($key_comp_family == 0)$nbcomparaison_family_rg0 ++;
		  if($key_comp_family == 1)$nbcomparaison_family_rg1 ++;
		  if($key_comp_family == 2)$nbcomparaison_family_rg2 ++;
		}
		
		
	$nbfamily_ok ++;
	}
	
      $surligne="";
      //if( in_array($row_quest['ObservationId'],$grouperef))$surligne.=" non gr3 :";
     

      
      //if(($current_id->species =="")||($current_id->species =="Z"))$surligne.="DET :";
      if($nbdet==0)$nbdet = "  ! CIBLE ";//$resultatspec
      
      
      $ligne_reponse .= '<td>'.$surligne.'<a href="../?id=21&numero_observation='.$row_quest['ObservationId'].'">Observation '.$row_quest['ObservationId'].' </a></td>';
      $ligne_reponse .= " <td>".$current_id->species." - $nbdet -  key $key_comp </td><td>";
      $ligne_reponse .= ($row_quest['order_species'] > $limit_x) ? $value_pas_trouve : $row_quest['order_species'];
      
      $ligne_reponse .= " </td><td>";
      $ligne_reponse .= ($row_quest['order_genus'] > $limit_x) ? $value_pas_trouve : $row_quest['order_genus'];
      $ligne_reponse .= " </td><td>";
      $ligne_reponse .= ($row_quest['order_family'] > $limit_x) ? $value_pas_trouve : $row_quest['order_family'];
      $ligne_reponse .= " </td><td> ";
      
      $ligne_reponse .= " </td><td>";
      $ligne_reponse .= $topscore;
      $ligne_reponse .= " </td><td>";
      $ligne_reponse .= sprintf("%01.2f",$mypourcent )  ;
      $ligne_reponse .= " </td><td> ";
      
      $ligne_reponse .= " </td><td>";
      $ligne_reponse .= $nbenvoye ;
      $ligne_reponse .= " </td>";
    
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
    echo "<td> Arrive dans $rang premiers</td>";
    echo " <td>  </td><td>  ". sprintf("%01.2f",  ($nbtriplespec /$nbligne) ) ." </td><td>".sprintf("%01.2f",($nbtriplegenus/$nbligne)) ." </td><td> ".sprintf("%01.2f",($nbtriplefamily/$nbligne) )."</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td> Apres comp espece </td>";
    echo " <td>  </td><td>  rang 0 : ". sprintf("%01.2f",  ($nbcomparaison_rg0 /$nbcomparaison) ) ." </td><td> rang 1 ".sprintf("%01.2f",($nbcomparaison_rg1 /$nbcomparaison)) ." </td><td> rang 2".sprintf("%01.2f",($nbcomparaison_rg2 /$nbcomparaison) )."</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td> Apres comp genus </td>";
    echo " <td>  </td><td>  rang 0 : ". sprintf("%01.2f",  ($nbcomparaison_genus_rg0 /$nbcomparaison_genus) ) ." </td><td> rang 1 ".sprintf("%01.2f",($nbcomparaison_genus_rg1 /$nbcomparaison_genus)) ." </td><td> rang 2".sprintf("%01.2f",($nbcomparaison_genus_rg2 /$nbcomparaison_genus) )."</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td> Apres comp family </td>";
    echo " <td>  </td><td>  rang 0 : ". sprintf("%01.2f",  ($nbcomparaison_family_rg0 /$nbcomparaison_family) ) ." </td><td> rang 1 ".sprintf("%01.2f",($nbcomparaison_family_rg1 /$nbcomparaison_family)) ." </td><td> rang 2".sprintf("%01.2f",($nbcomparaison_family_rg2 /$nbcomparaison_family) )."</td>";
    echo "</tr>";
    
    
    
    echo "<td>   choix inferieur a $limit_pct %  </td>";
    echo " <td>   </td><td>  nb resultat compris = $nb5p  </td><td>  moyenne compris ".sprintf("%01.2f",  ($nb5p /$nbligne ) ) ."    </td>";
    
    echo "<td>  moyenne ordre ".$total_ordre/$nbspec_ok."</td><td> moyenne nb envoye  ".sprintf("%01.2f",  ($total_envoye/$nbligne ) ) ."</td>"; 
    echo "</tr>";
    
?>
</table>

</body>
</html>
