<?php
// use http://wiki.openstreetmap.org/wiki/Nominatim#Example

require_once("../bibliotheque/common_functions.php");

function dieanddebug()
{ echo "die";
 die(mysql_error());
 //die();
}

$legitime=3;

//test if notification are still waiting
bd_connect();
$sql_new_addition =
    " SELECT *" .
    " FROM  iherba_new_observations" .
    " limit 1  ";
  
$notifications = mysql_query($sql_new_addition) or dieanddebug();
if(mysql_num_rows($notifications)==0)die("0 to loc");

//if at least one notification, do the first one
$thenotification = mysql_fetch_assoc($notifications);
$numobs = $thenotification['idobservation'];

$obsQuery =
      " SELECT *" .
      " FROM iherba_observations" .
      " WHERE idobs = $numobs";
    
$obsResult = mysql_query($obsQuery) or dieanddebug ();

$observation = mysql_fetch_assoc($obsResult);
if(mysql_num_rows($obsResult)!=0)
 {
 if(($observation['latitude']!=0) && ($observation['longitude']!=0) )//&& ( $observation['address']=='')
  {
   
 
  //print_r($observation);
  $reversegeocoding = file('http://nominatim.openstreetmap.org/reverse?format=json&lat='.$observation['latitude'].'&lon='.$observation['longitude'].'&zoom=18&addressdetails=1');
  $adressobj = json_decode(join($reversegeocoding));
  print_r($adressobj);
  $array_address = $adressobj->address;
  $town = "";
  if(isset($array_address->city))$town=$array_address->city;
  if(isset($array_address->county))
   if($town != $array_address->county)$town .= ' - '.$array_address->county;
  $town = mysql_escape_string($town);
  //echo $town;
 
  
  $update_address =
      " update iherba_observations" .
      " set  address = '$town [OSM]'  where  idobs = $numobs ;";
  $updateresult = mysql_query($update_address) or  dieanddebug();
 
  $legitime = 2; // au moins geolocalisŽe
  $obsphotosQuery =
	" SELECT *" .
	" FROM iherba_photos" .
	" WHERE id_obs = $numobs";
      
  $obsResult = mysql_query($obsphotosQuery) or dieanddebug ();
  $nbphotos=mysql_num_rows($obsResult);
  if($nbphotos>1)$legitime=1;
  }
 }

$update_flux =
      " update iherba_observations" .
      " set  computed_flux = $legitime where  idobs = $numobs ;";
$updatefluxresult = mysql_query($update_flux) or  dieanddebug();

$update_uuid =
      " update iherba_observations" .
      " set `uuid_observation` = uuid() WHERE `idobs`= $numobs ;";
$updateuuidresult = mysql_query($update_uuid) or  dieanddebug();

$update_uuid =
      " update iherba_observations" .
      " set `uuid_specimen` = uuid() WHERE `idobs`= $numobs ;";
$updateuuidresult = mysql_query($update_uuid) or  dieanddebug();


 
 
$deletesql = " DELETE FROM  iherba_new_observations where idobservation = $numobs";
$obsDelete = mysql_query($deletesql) or dieanddebug ();

?>
