<?php
// display list of species of the database
// if update is set, modify the url_rewriting of the observation, to use the most up to date explicit name
$GLOBALS['normalizeChars'] = array(
    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 
    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 
    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 
    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 
    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 
    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 
    'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
);

function cleanForShortURL($toClean) {
    $toClean     =     str_replace('&', '-and-', $toClean);
    $toClean     =    trim(preg_replace('/[^\w\d_ -]/si', '', $toClean));//remove all illegal chars
    $toClean     =     str_replace(' ', '-', $toClean);
    $toClean     =     str_replace('--', '-', $toClean);
    
    return strtr($toClean, $GLOBALS['normalizeChars']);
}

$xml=0;
$update=0;
if(isset($_GET['xml']))$xml=1;
if(isset($_GET['update']))$update=1;

$content = "";
if( $xml !=1) $content .= '
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>iHerbarium : Listes especes </title>
</head>
<body>
';
else
$content .= '<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

include("../bibliotheque/common_functions.php");

bd_connect();
	$sql_determ="
        SELECT distinct idobs ,  iherba_determination.nom_scientifique,iherba_determination.nom_commun,tropicosid,tropicosgenusid,tropicosfamilyid
            FROM iherba_observations, iherba_determination
                WHERE  iherba_observations.idobs =  iherba_determination.id_obs and tropicosid != ''
	 ORDER BY nom_scientifique "; 
	$result_determ= mysql_query($sql_determ) or die ("Pb");
        $nb_ident =0;
        $nb_nonident =0;
        if( $xml !=1)
		$content .=  "<table><td><tr>Nom scientifique </td></tr>";
		
	while($row_quest= mysql_fetch_assoc($result_determ) ){
			if( $xml !=1)
				$content .=  '<tr>';
				else
				$content .= '<url>';
			if( $xml !=1)
				$content .=  '<td>  <a href="/observation/data/'.$row_quest['idobs'].'" target=_blanck >'.$row_quest['nom_scientifique']."</a></td>";
				else
				$content .= '<loc>http://www.iherbarium.fr/observation/data/'.$row_quest['idobs'].'</loc><priority>1.00</priority>';
				if(($update==1)&&($row_quest['nom_scientifique'] !=''))
				 {
					$sqlmajurl = "UPDATE  `typoherbarium`.`iherba_observations` SET  `url_rewriting_fr` =  '".cleanForShortURL($row_quest['nom_scientifique'])."' WHERE  `iherba_observations`.`idobs` =".$row_quest['idobs'];
					$result_z= mysql_query($sqlmajurl) or die ("Pb");
					$sqlmajurl = "UPDATE  `typoherbarium`.`iherba_observations` SET  `url_rewriting_en` =  '".cleanForShortURL($row_quest['nom_scientifique'])."' WHERE  `iherba_observations`.`idobs` =".$row_quest['idobs'];
					$result_z= mysql_query($sqlmajurl) or die ("Pb");
					$sqlmajurl = "UPDATE  `typoherbarium`.`iherba_observations` SET  `computed_best_tropicos_id` =  '".$row_quest['tropicosid']."' WHERE  `iherba_observations`.`idobs` =".$row_quest['idobs'];
                                        $result_z= mysql_query($sqlmajurl) or die ("Pb");
					$sqlmajurl = "UPDATE  `typoherbarium`.`iherba_observations` SET  `computed_best_genus_id` =  '".$row_quest['tropicosgenusid']."' WHERE  `iherba_observations`.`idobs` =".$row_quest['idobs'];
                                        $result_z= mysql_query($sqlmajurl) or die ("Pb");
					$sqlmajurl = "UPDATE  `typoherbarium`.`iherba_observations` SET  `computed_best_family_id` =  '".$row_quest['tropicosfamilyid']."' WHERE  `iherba_observations`.`idobs` =".$row_quest['idobs'];
                                        $result_z= mysql_query($sqlmajurl) or die ("Pb");
						
				 }
				$nb_ident++;
                        /*if($row_quest['tropicosid'] != "")
                            {$nb_ident++;
                            echo "<td>".$row_quest['tropicosid']."</td>";
                            }
                            else
                            {
                                $nb_nonident++;
                                echo '<td><a href="../?id=21&numero_observation='.$row_quest['idobs'].'">A DETERMINER TROPICOS </a></td>';
                            }
			*/
                        if( $xml !=1)
				$content .=  '</tr>';
				else
				$content .= '</url>'."\n";
	}
if( $xml !=1)
        $content .=  "</table> <!-- $nb_ident --> </body>";
       else
       $content .=  "  </urlset>";//nb_nonident $nb_nonident
echo $content;
?>
