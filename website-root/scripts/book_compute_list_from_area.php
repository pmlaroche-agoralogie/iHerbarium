<?php
// cree la liste des taxon-modele dans la table iherbarium_book_project_list_taxon
include("../bibliotheque/common_functions.php");
bd_connect();

$myzone = '30960';
//if(isset($_GET['numerozone']))if(is_numeric($_GET['numerozone']))$myzone=desamorcer($_GET['numerozone']);

$sql="select * from iherba_area where name='".$myzone."'";
$result = mysql_query($sql) or die ();
$nb_lignes_resultats=mysql_num_rows($result);
if($nb_lignes_resultats>0){
        $area = mysql_fetch_assoc($result);
        }
        else die();//area unknown
$etat_requete_where = '';
$etat_requete_where .= " AND iherba_observations.latitude >".($area['center_lat']-$area['radius']). "";
$etat_requete_where .= " AND iherba_observations.latitude < ".($area['center_lat']+$area['radius']). "";
$etat_requete_where .= " AND iherba_observations.longitude > ".($area['center_long']-$area['radius']). "";
$etat_requete_where .= " AND iherba_observations.longitude < ".($area['center_long']+$area['radius']). "";
	
        
$base_request = "
SELECT distinct (computed_best_tropicos_id)
FROM  `iherba_observations` where 1  $etat_requete_where ";

$result = mysql_query($base_request) or die ($base_request);

while($row_obs= mysql_fetch_assoc($result) ){
        //print_r($row_obs);
        $montaxon = $row_obs['computed_best_tropicos_id'];
        $modele = "select * from iherba_observations, iherba_goodmodel_observation where idobs = observation_id and iherba_observations.computed_best_tropicos_id = '$montaxon';";
        $modeleres = mysql_query($modele) or die ();
        $nb_lignes_resultats=mysql_num_rows($modeleres);
        if($nb_lignes_resultats>0)
        {
                $mod = mysql_fetch_assoc($modeleres);
                $monmodele =$mod['idobs'];
        }  
        else
        $monmodele = 0;

        $insert = " INSERT INTO `typoherbarium`.`iherbarium_book_project_list_taxon` (`project_name`, `api_taxon`, `observation_modele`, `exclusion_remarques`) ";
        $insert .= " VALUES ('$myzone', '$montaxon', '$monmodele', '');";
        $insertres = mysql_query($insert) or die ();
        echo $insert;
}

        
        

?>
