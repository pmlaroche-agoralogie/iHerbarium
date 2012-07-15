<?php
//display the large version of a send picture

include("../bibliotheque/common_functions.php");

function cherche_photo($nomphoto){
	$content="";
	
	bd_connect();
	$sql_cherche_image="select * from iherba_photos,iherba_observations,fe_users where nom_photo_final= '$nomphoto' and idobs=id_obs and id_user = uid ;";
	
	$result_image = mysql_query($sql_cherche_image)or die ('');
	if(mysql_num_rows($result_image)!=1)die();
	$laphoto = mysql_fetch_assoc($result_image);
	$content=file_get_contents("../fileadmin/template/template_large_picture.html");
	$content = str_replace('<!--###imagefile###-->',"/medias/big/".$nomphoto,$content);
	if($laphoto["licence_type"]=='CCBYSA')
		$texte_licence = "This picture is licensed under Creative Commons, Attribution required,
Share alike (CC BY-SA 3.0).&nbsp;";
		else
		if($laphoto["licence_type"]=='')
		$texte_licence = "This picture is licensed by Agoralogie (<a href=/index.php?id=43&L=0>Link</a>).&nbsp;";
		else
		$texte_licence = "This picture is licensed under the acronym".$laphoto["licence_type"].".&nbsp;";
		
	if($laphoto["external_licence"]!='')
		{
		$lienlicence = $laphoto["external_licence"];
		$texte_licence .= "The author is ".$laphoto['external_author']."<br> This file is a copy for management purpose.";
		$texte_licence .= "See <a href=###link###>here</a> for full original authorship and license attribution.";
		}
		else
		{
		$lienlicence = "/scripts/licence.php?name=".$nomphoto;
		$texte_licence .= "The author is ".$laphoto['name']."<br>";
		$texte_licence .= "See <a href=###link###>here</a> for full original authorship and license attribution.";
		}
	$texte_licence = str_replace("###link###",$lienlicence,$texte_licence);
	$texte_licence .= "This picture is associated to <a href=/observation/data/".$laphoto['idobs']."> this observation</a><br>";
	$content = str_replace('<!--###about_licence###-->',$texte_licence,$content);
	return $content;
}

$name=desamorcer($_GET['name']);

if(substr($name,0,4)=="roi_")
	{
	$roiid = substr($name,4,strlen($name)-8);
	bd_connect();
	$sql_cherche_image="select * from iherba_photos,iherba_roi where id_photo = idphotos and id= $roiid;";
	
	$result_image = mysql_query($sql_cherche_image)or die ('');
	if(mysql_num_rows($result_image)!=1)die();
	$laphoto = mysql_fetch_assoc($result_image);
	$name =  $laphoto['nom_photo_final'];
	};
	
echo cherche_photo($name);

?>
