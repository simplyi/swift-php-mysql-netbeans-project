<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PasswordReset
 *
 * @author sergeykargopolov
 */
class PasswordReset {
    //put your code here
    
    function generateUniqueToken($tokenLenth)
    {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $tokenLenth; $i++) {
         $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString; 
    }
    
       function sendEmailMessage($messageDetails)
    {
        $message_subject = $messageDetails["message_subject"];
        $to_email = $messageDetails["to_email"];
        $from_name = $messageDetails["from_name"];
        $from_email = $messageDetails["from_email"];
        $message_body = $messageDetails["message_body"];
     
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . $from_name  ." <".$from_email.">" . "\r\n";
       
        mail($to_email,$message_subject,$message_body, $headers);   
    }  
    
        
    function generateMessageBody()
    {
       $myfile = fopen("../inc/passwordResetMessage.html", "r");
       $returnValue =  fread($myfile,filesize("../inc/passwordResetMessage.html"));
       fclose($myfile);
       
       return $returnValue;
    }
    
    
}

?>
