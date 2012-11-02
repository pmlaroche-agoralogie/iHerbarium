<?php
require_once("../bibliotheque/common_functions.php");

function notifyUserAbout_somebody_say_Determination($determinationId,$targetuser,$mylanguage = 'en') {
 $monobjet=NULL;
 $email_translate = array(
  'en' => array(
		'mail_notif_subject' => 'iHerbarium : answer on observation :',
		'mail_notif_body'=> 'A botanist or the expert system add a note to the observation ',
		'mail_notif_footer'=> 'This website is still an experiment, please report any problem. \n iHerbarium Team '),
  'fr' => array (
		 'mail_notif_subject' => 'iHerbarium : une reponse faite sur l\'observation :',
		 'mail_notif_body'=> 'Un botaniste a repondu au sujet de l\'observation',
		 'mail_notif_footer'=> 'Ce programme est en cours d\'experimentation, merci de nous signaler toute anomalie ou de nous proposer toute modification. \n
L\'equipe de iherbarium '),
  'es' => array(
        'mail_notif_subject' => 'Una respuesta fue haciendo sobre la observaci—n :',
        'mail_notif_body'=> 'Un bot‡nico ha respondido sobre la observaci—n',
        'mail_notif_footer'=> 'Este programa est‡ en fase de experimentaci—n. Gracias a reportarnos cualquier anomal’a o proponernos ningœn cambio. \n El equipo de iherbarium.'
        ) ,
  'pt' => array(
		'mail_notif_subject' => 'iHerbarium : resposta na observa‹o :',
		'mail_notif_body'=> 'Um bot‰nico ou o sistema perito adicionam uma nota ˆ observa‹o ',
		'mail_notif_footer'=> 'Este Web site Ž ainda uma experincia, relate por favor todo o problema. \n equipe do iHerbarium'
		)
);

  ;
  
  // Determination
  $determinationQuery =
    " SELECT *" .
    " FROM iherba_determination" .
    " WHERE id = '" . $determinationId . "'";
  
  $determinationResult = mysql_query($determinationQuery) or die (mysql_error());

  assert(mysql_num_rows($determinationResult) == 1);
  $determination = mysql_fetch_assoc($determinationResult);
  
  // Obs
  $obsQuery =
    " SELECT *" .
    " FROM iherba_observations" .
    " WHERE idobs = '" . $determination["id_obs"] . "'";
  
  $obsResult = mysql_query($obsQuery) or die (mysql_error());

  assert(mysql_num_rows($obsResult) == 1);
  $obs = mysql_fetch_assoc($obsResult);
  
  // User
  $userQuery =
    " SELECT *" .
    " FROM fe_users" .
    " WHERE uid = '" . $obs['id_user'] . "'";
  
  $userResult = mysql_query($userQuery) or die (mysql_error());

  assert(mysql_num_rows($userResult) == 1);
  $user = mysql_fetch_assoc($userResult);
  
  
  // Prepare Mail
  $agoralogieAddress = 'notification@iherbarium.fr';
  $bccagoralogieAddress = 'agoralogie@gmail.com';

  $to = $user['email'];
  
	
  $subject = get_string_language($email_translate,'mail_notif_subject',$mylanguage) . $obs['idobs'];
  $message = get_string_language($email_translate,'mail_notif_body',$mylanguage);
  $message .= $obs['idobs']." : \n".'http://www.iherbarium.fr/index.php?id=detail&numero_observation='.$obs['idobs']."  \n";
  $message .= affiche_expertise($obs['idobs'],$monobjet,"courte",$x,1,$mylanguage);
  $message .= get_string_language($email_translate,'mail_notif_footer',$mylanguage) ;
  $message = str_replace('\n ',"\n",$message);
  $message = str_replace('\n',"\n",$message);
  $headers = 
    "From: " . $agoralogieAddress . "\r\n" .
    "Bcc: " . $bccagoralogieAddress . "\r\n";
  
  // Send mail
  mail($to, $subject, $message, $headers);
  return $subject."---".$message."---".$headers;
}

bd_connect();
$notsentnotification =
    " SELECT *" .
    " FROM  iherba_notification" .
    " WHERE  `ts_send` =  '0000-00-00 00:00:00' and   `message_type` =  'somebody-say' ";
  
$notifications = mysql_query($notsentnotification) or die (mysql_error());
if(mysql_num_rows($notifications)==0)die();
$thenotification = mysql_fetch_assoc($notifications);
$parameters = json_decode($thenotification['parameters']);
print_r($parameters);
echo $parameters->determination;
$messagetexte = notifyUserAbout_somebody_say_Determination($parameters->determination,$thenotification['id_dest'],$thenotification['preferred_language']);

$sentnotification =
    " update iherba_notification" .
    " set  `ts_send` =  CURRENT_TIMESTAMP ";
  
$sentnotificationresult = mysql_query($sentnotification) or die (mysql_error());

?>
