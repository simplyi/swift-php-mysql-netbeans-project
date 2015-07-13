<?php
require("../db/MySQLDAO.php");
require("../Classes/PasswordReset.php");
$config = parse_ini_file('../../../../SwiftCourse2.ini');
$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
$dbpassword = trim($config["dbpassword"]);
$dbname = trim($config["dbname"]);

$returnValue = array();

// Get user email address
if (empty($_POST["userEmail"])) {
    $returnValue["message"] = "Missing email address";
    echo json_encode($returnValue);
    return;
}

$email = htmlentities($_POST["userEmail"]);

$dao = new MySQLDAO($dbhost, $dbuser, $dbpassword, $dbname);
$dao->openConnection();

// Check if email address is found in our database 
$userDetails = $dao->getUserDetails($email);
if (empty($userDetails)) {
    $returnValue["message"] = "Provided email address is not found  in our database";
    echo json_encode($returnValue);
    return;
}

// Generate a unique string token 
$passwordReset = new PasswordReset();
$passwordToken = $passwordReset->generateUniqueToken(16);

// Store unique token in our database 
$user_id = $userDetails["user_id"];
$dao->storePasswordToken($user_id, $passwordToken);

// Prepare email message with Subject, Message, From, To... 
$messageDetails = array();
$messageDetails["message_subject"] = "Password reset requested";
$messageDetails["to_email"] = $userDetails["email"];
$messageDetails["from_name"] = "Sergey Kargopolov";
$messageDetails["from_email"] = "sergey@earthlandia.com";

// Load email message html template and insert html link to click and beging parssword reset
$messageBody = $passwordReset->generateMessageBody();
$emailMessage = str_replace("{token}", $passwordToken, $messageBody);
$messageDetails["message_body"] = $emailMessage;

// Send out email message to user 
$passwordReset->sendEmailMessage($messageDetails);

// Return a message to a mobile App 

$returnValue["userEmail"] = $email;
$returnValue["message"] = "We have sent you email message. Please check your Inbox.";
echo json_encode($returnValue);


?>
