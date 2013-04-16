<?php
// sert à la moderation, images anormales
$legendemerci = array('fr'=>"Merci", 'en' => "Thank you", 'de' => "Thank you", 'pt' => "Thank you");
include("../bibliotheque/common_functions.php");


function moderation(){

	bd_connect();
	$id_roi= desarmorcer($_GET['id_roi']);
	
	$adresse_ip=$_SERVER['REMOTE_ADDR']; //adresse IP de l'utilisateur
	
	$sql_obs_lie_roi="SELECT iherba_observations.idobs, iherba_photos.idphotos
	FROM iherba_observations, iherba_photos, iherba_roi
	WHERE  iherba_observations.idobs = iherba_photos.id_obs
	AND iherba_roi.id_photo = iherba_photos.idphotos AND iherba_roi.id = $id_roi";
	$result_obs= mysql_query($sql_obs_lie_roi)or die ();
	if(!($row_obs= mysql_fetch_assoc($result_obs) ))
		{//pas d'obs resultat : parametre faux 
		die();
		}
	
	$sql_log_rapport = "INSERT INTO `iherba_moderation` ( `id_observation` , `id_photo` ,`id_roi` , `sens_moderation`, `id_user_ou_ip`   )
VALUES (".$row_obs['idobs'].",".$row_obs['idphotos'].",".$id_roi.", -1,'".$_SERVER['REMOTE_ADDR']."')";
	$enregistre_log_question= mysql_query($sql_log_rapport) or die ();
	
	$sql_nb_pesonnes_moderent = "SELECT distinct id_user_ou_ip  FROM `iherba_moderation` WHERE `id_observation` = ".$row_obs['idobs'];
	$resultat_nb_personne =  mysql_query($sql_nb_pesonnes_moderent) or die ();
	
	$limite_min = 3;
	if(mysql_num_rows($resultat_nb_personne)>$limite_min)
	{
		$sql_exit_observation = "update `iherba_observations` set `moderation` = 1 where idobs = ".$row_obs['idobs']." ;";
		$enregistre_moderation= mysql_query($sql_exit_observation) or die ();
		mail("philippe.laroche@agoralogie.fr","moderation sup".$limite_min,"observation : ".$row_obs['idobs']);
	}

	
	
}


$langue=choisir_langue();

moderation();
$modele = file_get_contents('template_rapport.html');
$page_generee= str_replace('###rapport###',$legendemerci[$langue],$modele);


echo $page_generee;
?>
