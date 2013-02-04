<?php
// read a pipeline of notification to send, and send the first one

require_once("../bibliotheque/common_functions.php");

function notifyUserAbout_somebody_say_Determination($determinationId,$targetuser,$mylanguage = 'en') {
 $monobjet=NULL;
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
  
  $subject = get_string_language_sql('mail_notif_somebody_say_title',$mylanguage) . " ".$obs['idobs'];
  $message = get_string_language_sql('mail_notif_somebody_say_message',$mylanguage);
  $url_observation = $obs['idobs']." : \n".'http://www.iherbarium.fr/index.php?id=detail&numero_observation='.$obs['idobs'];
  
  $expertise = affiche_expertise($obs['idobs'],$monobjet,"courte",$x,1,$mylanguage);
  $message = str_replace("%s$1",$url_observation,$message);
  $message = str_replace("%s$2",$expertise,$message);
  
  $message .= get_string_language_sql('mail_notif_footer',$mylanguage) ;

  $headers = 
    "From: " . $agoralogieAddress . "\r\n" .
    "Bcc: " . $bccagoralogieAddress . "\r\n";
  
  // Send mail
 mail($to, $subject, $message, $headers);
 return  "to = \n$to \n Subject = \n".$subject." \n Message = \n".$message." \n Header =".$headers;
}


//test if notification are still waiting
bd_connect();
$notsentnotification =
    " SELECT *" .
    " FROM  iherba_notification" .
    " WHERE  `ts_send` =  '0000-00-00 00:00:00' and   `message_type` =  'somebody-say' ";
  
$notifications = mysql_query($notsentnotification) or die (mysql_error());
if(mysql_num_rows($notifications)==0)die();

//if at least one notification, do the first one
$thenotification = mysql_fetch_assoc($notifications);
$id_current_notification = $thenotification['id_notification'];
$parameters = json_decode($thenotification['parameters']);


$messagetexte = notifyUserAbout_somebody_say_Determination($parameters->determination,$parameters->owner,$thenotification['preferred_language']);
$messagetexte =  str_replace("\n",'\n ', $messagetexte);//desamorcer(
$messagetexte =  str_replace("\r",'', $messagetexte);
$messagetexte =  str_replace("'",'``', $messagetexte);
$sentnotification =
    " update iherba_notification" .
    " set  `ts_send` =  CURRENT_TIMESTAMP where id_notification = $id_current_notification ;";
$sentnotificationresult = mysql_query($sentnotification) or die (mysql_error());
$sentnotification =
    " update iherba_notification" .
    " set  `sent_text` =  '$messagetexte' where id_notification = $id_current_notification ";
  
$sentnotificationresult = mysql_query($sentnotification) or die (mysql_error());

?>
