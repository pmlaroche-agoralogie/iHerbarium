<?php
include("../bibliotheque/common_functions.php");

function cherche_photo($nomphoto){
	$content="";
	$content=file_get_contents("../fileadmin/template/template_licence.html");
	bd_connect();
	$sql_cherche_image="select * from iherba_photos,iherba_observations,fe_users where nom_photo_final= '$nomphoto' and idobs=id_obs and id_user = uid ;";
	
	$result_image = mysql_query($sql_cherche_image)or die ('');
	if(mysql_num_rows($result_image)==1)
		{
		$laphoto = mysql_fetch_assoc($result_image);
		$lillustreation = "/medias/big/".$nomphoto. ' height="400"';
		
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
		$texte_licence .= "The author is ".$laphoto['external_author']."<br> &nbsp; This file is a copy for management purpose.";
		
		}
		else
		{
		$lienlicence = "/licence.php?name=".$nomphoto;
		$texte_licence .= "The author is ".$laphoto['name']."<br>";
		
		}
		$lalicence= $texte_licence;
	
	
		}
	else
		{
		$sql_cherche_image="select * from iherba_drawing_ref where filename = '$nomphoto' ";
		$result_image = mysql_query($sql_cherche_image)or die ('');
		if(mysql_num_rows($result_image)!=1)die();// parametre non trouvé
		$laphoto = mysql_fetch_assoc($result_image);
		$lillustreation = "/dessins/w130/".$nomphoto;
		$lalicence = "This picture is licensed under Creative Commons, Attribution required, Share alike (CC BY-SA 3.0)<br>";
		
		if($laphoto['additionnal_text']!="")$lalicence .=  $laphoto["additionnal_text"]."<br>";
		
		if($laphoto['licencelink']!="") $lalicence .= "See <a href=". $laphoto['licencelink']  .'>there</a>  for full original authorship and license attribution. <br>' ;
		}
	
	$content = str_replace('<!--###imagefile###-->',$lillustreation,$content);
	$content = str_replace('<!--###detail_licence###-->',"$lalicence",$content);
	

	return $content;
}

$name=desamorcer($_GET['name']);

echo cherche_photo($name);

?>
