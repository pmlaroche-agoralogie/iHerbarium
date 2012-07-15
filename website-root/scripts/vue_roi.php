<html>
<head>
</head>
<body >
<?php $groupeetudie = 6; echo "Groupe &eacute;tudi&eacute; :".$groupeetudie ."<br>"; ?>
<table>
  <tr> <td> Observation class&eacute;e</td></tr>
<?php
include("../bibliotheque/common_functions.php");
bd_connect();
$grouperef = array();
$sql_ref = "SELECT distinct idobs   FROM iherba_observations    WHERE inGroup(  idobs, 3  )";
$result_ref = mysql_query($sql_ref)or die ('Erreur SQL !'.$sql_ref.'<br />'.mysql_error());
while($row_quest= mysql_fetch_assoc($result_ref) ){
  $grouperef[] = $row_quest['idobs'];
  }
    
$value_pas_trouve='<font color="red">X</font>';
$nbtriplespec = $nbtriplegenus = $nbtriplefamily  = $nbligne = $nbspec_ok = $nbspec_nok = $nbgenus_ok = $nbgenus_nok = $nbfamily_ok = $nbfamily_nok = 0 ;


$sql_ref = "SELECT * 
FROM iherba_observations
WHERE inGroup(
idobs, $groupeetudie
)

ORDER BY idobs ASC  ";

$result_ref = mysql_query($sql_ref)or die ('Erreur SQL !'.$sql_ref.'<br />'.mysql_error());
 
$previous_id = 0;
while($row_quest= mysql_fetch_assoc($result_ref) )
    {
    $observation = $row_quest['idobs'];
    echo "<tr>";
    $sql_roi="SELECT * 
	      FROM  `iherba_roi` ,  `iherba_photos` 
	      WHERE  `id_obs` = $observation
	      AND idphotos = id_photo";
    $result_roi= mysql_query($sql_roi) or die ("Pb");

    
    $listeroi ="";
    while($row_roi= mysql_fetch_assoc($result_roi) )
    {
      $listeroi .= "<img src=/medias/roi_vignettes/roi_".$row_roi['id'].".jpg>";
    }
    if($listeroi=="")$listeroi = "NO ROI";
      
    echo '<td>'.'<a href="../?id=21&numero_observation='.$row_quest['idobs'].'" target=_blanck >Observation '.$row_quest['idobs'].' </a></td>';
    echo " <td>".$listeroi."</td>";
    
    
    echo "</tr>";

    $nbligne ++;
    //$previous_id=$row_quest['ObservationId'];
    }
      
    
    echo "<tr>";
    echo "<td> Nb ligne groupe 6  </a></td>";
    echo " <td> $nbligne </td></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td> Arrivé placé</a></td>";
    echo " <td>  </td></tr>";
?>
</table>

</body>
</html>
