<?php
// study results after comparisons
include("../bibliotheque/common_functions.php");


bd_connect();
$where = " iherba_observations.latitude >44.21 AND iherba_observations.latitude < 44.25 AND iherba_observations.longitude > 4.124 AND iherba_observations.longitude < 4.164";

$sql_list_carto_select_espece = "SELECT distinct iherba_determination.tropicosid";
$sql_list_carto_from = " FROM iherba_photos,iherba_observations ,iherba_determination where iherba_observations.latitude !=0 AND iherba_observations.idobs=iherba_photos.id_obs ";
$sql_list_carto_from .= " AND iherba_determination.`tropicosfamilyid` != '' AND iherba_observations.idobs=iherba_determination.id_obs and $where group by iherba_determination.tropicosid ".$order;

$sql_species_list = $sql_list_carto_select_espece . $sql_list_carto_from ;

$result_list = mysql_query($sql_species_list) or die ("Pb");
  
$row_quest= mysql_fetch_assoc($result_list);

    
?>