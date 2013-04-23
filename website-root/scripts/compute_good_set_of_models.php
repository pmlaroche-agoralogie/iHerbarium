<?php
// study the known facts about the observation and create a set of possible observations to compare with

include("../bibliotheque/common_functions.php");

//return a array of id of observation to use as model, with a weight to add
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
  $proche = array();
  $result_determ= mysql_query($sql_mydet) or die ("Pb");
     while($row_quest= mysql_fetch_assoc($result_determ) ){
	echo $row_quest['idobs']." - ".$row_quest['poids']." - ".$row_quest['url_rewriting_fr']."<br>";
	$proche[] = array('id'=>$row_quest['idobs'] , 'weight' => 1);// nota : i insert a weigth of 1 instead of  $row_quest['poids']
	}
  return $proche;
}

// insert the set of observation to compare in a tabl
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

function add_good_set($observation_etudiee,$idtask)
{
  bd_connect();
  $sql_set_exists = "SELECT *  FROM  `iherba_similarity_set`  WHERE  `observation_id` = $observation_etudiee";
  $result_ref = mysql_query($sql_set_exists)or die ('exists');
  if(mysql_num_rows($result_ref)<1)
    {
    $sql_obs = "SELECT * FROM iherba_observations WHERE idobs = $observation_etudiee ";
    $result_ref = mysql_query($sql_obs) or die ('select');
    if(mysql_num_rows($result_ref)<1)
      {
	//missing observation, deleted ?
	$sql_del_task = "delete FROM iherba_task WHERE Id = $id_task ";
	$result_del = mysql_query($sql_del_task)or die ('sql_del_task');
      }
      else
      {
      
	$row_quest= mysql_fetch_assoc($result_ref) ;
	if($row_quest['latitude']==0)
	  {
	    //if the observation is not localized, we can't help
	    $proche = array(); //insert_no_good_set($observation_etudiee);
	  }
	  else 
	  {
	    // we search what we know about the place where the observation was done
	    $proche = study_chorologie($row_quest['latitude'],$row_quest['longitude']);
	  }
		
	// we store the result ; if the $proche array is empty, we will send an "unknown place" message
	insert_similarity_set($observation_etudiee,$proche);
      }
    }
}

$observation_etudiee = -1;
$id_task = -1;
//verify if tasks are waiting for sets
bd_connect();

$sql_ref = "SELECT * FROM iherba_task WHERE Type='AddObservationToDeterminationFlow' and ContextType ='Observation' order by id ASC";
$result_ref = mysql_query($sql_ref)or die ('select');
while($row_quest= mysql_fetch_assoc($result_ref) )
  {
    add_good_set($row_quest['Context'],$row_quest['Id']);
  }


//if nothing to do, search for a parameter
//verify the quality of the parameters
    if(!isset($_GET['observationid']))
      {
	die('observationid not set');
      }
    if(!is_numeric($_GET['observationid']))
      {
	die('observationid must be an integer');
      }
    $observation_etudiee = desamorcer($_GET['observationid']);

echo "obs : $observation_etudiee";

if($observation_etudiee == -1)
  die("no obs");// no observation need to be computed , nothing to do

add_good_set($observation_etudiee,-1);

echo "obs : $observation_etudiee";

?>
