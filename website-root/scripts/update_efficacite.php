<?php
// compute fields to help measure result

include("../bibliotheque/common_functions.php");

function lister_determination($ObservationId) //$ids_list_obs
{
$sql_mydet = "  SELECT distinct idobs ,  iherba_determination.nom_scientifique,iherba_determination.tropicosgenusid,tropicosid,tropicosfamilyid
            FROM iherba_observations, iherba_determination
                WHERE  idobs = '$ObservationId' and iherba_observations.idobs =  iherba_determination.id_obs and tropicosid != '' ";

    $result_determ= mysql_query($sql_mydet) or die ("Pb");
        
	$row_quest= mysql_fetch_assoc($result_determ);
$myid = array('species'=>$row_quest['tropicosid'],'genus'=>$row_quest['tropicosgenusid'],'family'=>$row_quest['tropicosfamilyid']);
	  return $myid;
}


function update_tropicos_data($id,$myid,$listobs){
  bd_connect();
  $sql_update =
    " UPDATE iherba_questions_finished_log set " .
    " id_this_obs = '". json_encode($myid)."' , ids_list_obs = '". json_encode($listobs)."'".
    " where iherba_questions_finished_log.id = $id"; 
  $result_update = mysql_query($sql_update)or die ('Erreur SQL !'.$sql_update.'<br />'.mysql_error()); 
}

function update_resultat_data($id,$myid,$listobs){
  
  $current_id = $myid;
  $solutions = $listobs;
  
   $list_spec = $solutions['listspecies'];
  
  $list_genus = $solutions['listgenus'];
   
   $list_family = $solutions['listfamilies'];


    $resultatspec = array_search($current_id['species'],$list_spec);
    if($resultatspec  === false)$resultatspec = 999;

    $resultatgenus = array_search($current_id['genus'],$list_genus);
  if($resultatgenus  === false)$resultatgenus = 999;
    
 
    $resultatfamily = array_search($current_id['family'],$list_family);
      if($resultatfamily  === false)$resultatfamily = 999;
      
  bd_connect();
  $sql_update =
    " UPDATE iherba_questions_finished_log set " .
    " order_species = ". $resultatspec." ,order_genus = ". $resultatgenus.", order_family = ". $resultatfamily." ".
    " where iherba_questions_finished_log.id = $id"; 
  $result_update = mysql_query($sql_update)or die ('Erreur SQL !'.$sql_update.'<br />'.mysql_error()); 
}




bd_connect();

$grouperef = array();
$sql_ref = "SELECT distinct idobs   FROM iherba_observations    WHERE inGroup(  idobs, 3  )";
 $result_ref = mysql_query($sql_ref)or die ('Erreur SQL !'.$sql_ref.'<br />'.mysql_error());
  while($row_quest= mysql_fetch_assoc($result_ref) )
    {$grouperef[] = $row_quest['idobs'];}


  $sql_afaire =
    " SELECT *
FROM `iherba_questions_finished_log`
WHERE `id_this_obs` = ''
LIMIT 0 , 1
"; 
	 
  $result_afaire = mysql_query($sql_afaire)or die ('Erreur SQL !'.$sql_afaire.'<br />'.mysql_error());
  $row_quest= mysql_fetch_assoc($result_afaire) ;
  
 if($row_quest['Id']>0)
   {
    $myid= lister_determination($row_quest['ObservationId']);
   if( in_array($row_quest['ObservationId'],$grouperef))
     $myid['groupe'] = 3;
   $tableauobs = explode(',',$row_quest['TopObservations']);
   print_r($tableauobs);
   
   foreach($tableauobs as $oneobs)
    {
      if( $row_quest['ObservationId'] != $oneobs)//in_array($oneobs,$grouperef)
      
      {//echo "xxx". $row_quest['ObservationId'] ." ". $oneobs."<br>";
      $oneid= lister_determination($oneobs);//$row_quest['ids_list_obs']
      //print_r($oneid);
      $listspec[] = $oneid['species'];
      $listgenus[] = $oneid['genus'];
      $listfamily[] = $oneid['family'];
    }
    }
    print_r($listspec);
    $resultat = array("listspecies" => $listspec,"listgenus" => $listgenus,"listfamilies" => $listfamily );
   update_tropicos_data($row_quest['Id'],$myid,$resultat);
   update_resultat_data($row_quest['Id'],$myid,$resultat);
   }
?>
<html>
<head>
<script type="text/javascript">
<!--
function delayer(){
    window.location = "/scripts/update_efficacite.php"
}
//-->
</script>
</head>
<body onLoad="setTimeout('delayer()', 5000)">
<?php echo "<font size=+4>".$row_quest['Id']."--".$row_quest['ObservationId']."</font>"; ?>

</body>
</html>
