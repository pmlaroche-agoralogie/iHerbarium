<?php
// study the known facts about the observation and create a set of possible observations to compare with

include("../bibliotheque/common_functions.php");

function study_chorologie($lat,$long)
{
  $size_cell = 2;
  $sql_mydet = "
    SELECT distinct idobs , poids ,url_rewriting_fr FROM iherba_chorologie , iherba_observations
    WHERE	iherba_chorologie.lat > ".($lat -$size_cell)." and  iherba_chorologie.lat < ".($lat + $size_cell).
	      " AND iherba_chorologie.long > ".($long -$size_cell)." AND iherba_chorologie.long > ".($long -$size_cell).
	      " AND iherba_chorologie.tropicos_id = iherba_observations.computed_best_tropicos_id
	        AND computed_usable_for_similarity = 1 order by idobs"
		;
 // echo $sql_mydet;
  $proche = array();
  $result_determ= mysql_query($sql_mydet) or die ("Pb");
     while($row_quest= mysql_fetch_assoc($result_determ) ){
	echo $row_quest['idobs']." - ".$row_quest['poids']." - ".$row_quest['url_rewriting_fr']."<br>";
	$proche[] = array('id'=>$row_quest['idobs'] , 'weight' => $row_quest['poids']);
	}
return $proche;
}


function insert_similarity_set($id,$proche){
  bd_connect();
  $sql_insert =
  "INSERT INTO  `typoherbarium`.`iherba_similarity_set` (
`uid` ,
`observation_id` ,
`creation_ts` ,
`weight_for_nearest_common_plants`
)
VALUES (
NULL ,  '$id', 
CURRENT_TIMESTAMP ,  '".json_encode($proche)."'
);";

$result_insert = mysql_query($sql_insert) or die ('insert'); 
}


if(!isset($_GET['observationid']))
  {
    die('observationid not set');
  }
if(!is_numeric($_GET['observationid']))
  {
    die('observationid must be an integer');
  }

$observation_etudiee = desamorcer($_GET['observationid']);
bd_connect();

$sql_ref = "SELECT * FROM iherba_observations WHERE idobs = $observation_etudiee ";
$result_ref = mysql_query($sql_ref)or die ('select');
$row_quest= mysql_fetch_assoc($result_ref) ;


if($row_quest['latitude']==0)
	{
	  $proche = array(); //insert_no_good_set($observation_etudiee);
	}
	else 
	{
	  $proche = study_chorologie($row_quest['latitude'],$row_quest['longitude']);
	}
insert_similarity_set($observation_etudiee,$proche)
?>
