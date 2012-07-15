<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>


<title>iHerbarium : Listes observation </title>
</head>
<body>
<?php

include("../bibliotheque/common_functions.php");

bd_connect();
	$sql_determ="
        SELECT idobs, COUNT( id_obs ) AS nb
FROM  `iherba_observations` , iherba_photos
WHERE idobs = id_obs and genre_obs = 1 
GROUP BY idobs
ORDER BY idobs desc "; 
	$result_determ= mysql_query($sql_determ) or die ("Pb");
        $nb_ident =0;
        $nb_nonident =0;
        echo "<table>";
	echo "<tr><td>observation </td><td> nb de photo</td><td> group </td><td> nommée / cas </td><td> roi </td></tr>";
                        
	while($row_quest= mysql_fetch_assoc($result_determ) ){
		$observation = $row_quest['idobs'];
		$nbphoto = $row_quest['nb'];
		echo "<tr><td>".'<a href="../?id=21&numero_observation='.$observation.'"> '.$observation.'</a>'."</td><td>".$nbphoto." photos</td>";
		$sql_group="SELECT *  FROM  iherba_group_observations  WHERE ObservationId = $observation ";
		$result_group= mysql_query($sql_group) or die ("Pb");
                $nbr = mysql_num_rows($result_group);
		$group6 = 0;
		echo "<td> ";
		if($nbr==0){
				if ($nbphoto>1)echo " <font color=red>no group </font>";else echo " no group ";
				}
				else {
				echo "gr :";
				while($row_g= mysql_fetch_assoc($result_group) )
						{
						echo $row_g['GroupId']. " ";
						if($row_g['GroupId']==6)$group6=1;
						if($row_g['GroupId']==4)$group6=1;
						if($row_g['GroupId']==3)$group6=1;
						if($row_g['GroupId']==1)$group6=1;
						}
				}
		echo "</td>";
		
		$sql_group="SELECT *  FROM   iherba_determination  WHERE id_obs = $observation ";
		$result_group= mysql_query($sql_group) or die ("Pb");
                $nbr = mysql_num_rows($result_group);
		echo "<td> ";
		if($nbr==0){
		if ($group6==1)echo " <font color=red>no name </font>";else echo " no name ";
		}
				else {
						echo "nomm&eacute;e ";
						$tropicosok = 0; $anomalie=0;
				while($row_g= mysql_fetch_assoc($result_group) )
						{if( $row_g['comment_case']!=0)$anomalie=1;if( $row_g['tropicosid']!="")$tropicosok=1;}
				if($tropicosok!=0)echo " tropicos";else if($group6==1)echo "<font color=red>no tropicos name </font>" ;
				if($anomalie!=0)echo " plante mal document&eacute;e";
				}
		echo "</td>";
		
		

		$sql_group="SELECT COUNT( id_photo ) as nbroi
FROM  `iherba_roi` ,  `iherba_photos` 
WHERE  `id_obs` = $observation
AND idphotos = id_photo";
		$result_group= mysql_query($sql_group) or die ("Pb");
                $row_g= mysql_fetch_assoc($result_group);
		$nbr = $row_g['nbroi'];
		echo "<td> ";
		if($nbr==0){
		if ($group6==1)echo " <font color=red>no roi </font>";else echo " no roi ";
		}
				else { echo $nbr;
				if (($group6==1)&&($nbr<3))echo '<a href="../?id=29&numero_observation='.$observation.'" target="_blank"> '."definir roi".'</a>';
					
				}
		echo "</td>";
		
		
		echo "</tr>";
	}
        echo "</table>";
        //echo " nb_ident $nb_ident nb_nonident $nb_nonident "
?>
</body>