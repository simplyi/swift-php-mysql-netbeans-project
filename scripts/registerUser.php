<?php
//require("../db/Conn.php");
require("../db/MySQLDAO.php");
require("../Classes/EmailConfirmation.php");

$config = parse_ini_file('../../../../SwiftCourse2.ini');

$returnValue = array();

if(empty($_REQUEST["userEmail"]) || empty($_REQUEST["userPassword"]) 
        || empty($_REQUEST["userFirstName"])
        || empty($_REQUEST["userLastName"]))
{
    $returnValue["status"]="400";
    $returnValue["message"]="Missing required information";
    echo json_encode($returnValue);
    return;
}

$userEmail = htmlentities($_REQUEST["userEmail"]);
$userPassword = htmlentities($_REQUEST["userPassword"]);
$userFirstName = htmlentities($_REQUEST["userFirstName"]);
$userLastName = htmlentities($_REQUEST["userLastName"]);

// Generate secure password       
$salt = openssl_random_pseudo_bytes(16);
$secured_password = sha1($userPassword . $salt);


$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
$dbpassword = trim($config["dbpassword"]);
$dbname = trim($config["dbname"]);


$dao = new MySQLDAO($dbhost, $dbuser, $dbpassword, $dbname);
$dao->openConnection();

// Check if user with provided username is available
$userDetails = $dao->getUserDetails($userEmail);
if(!empty($userDetails))
{
    $returnValue["status"]="400";
    $returnValue["message"]="Please choose a different email address"; 
    echo json_encode($returnValue);
    return;
}



// Register new user
$result =$dao->registerUser($userEmail, $userFirstName, $userLastName, $secured_password, $salt);

if($result)
{
    $userDetails = $dao->getUserDetails($userEmail);
    $returnValue["status"]="200";
    $returnValue["message"]="Successfully registered new user";    
    $returnValue["userId"] = $userDetails["user_id"];
    $returnValue["userFirstName"] = $userDetails["first_name"];
    $returnValue["userLastName"] = $userDetails["last_name"];
    $returnValue["userEmail"] = $userDetails["email"]; 
    
    // Generate a unique email confirmation token
    $emailConfirmation = new EmailConfirmation();
    $emailToken = $emailConfirmation->generateUniqueToken(16);
    
    // Store email token in our database table. 
    $dao->storeEmailToken($userDetails["user_id"], $emailToken);
    
    // Prepare email message parameters like Subject, Message, From, To and etc. 
    $messageDetails = array();
    $messageDetails["message_subject"] = "Please confirm your email address";
    $messageDetails["to_email"] = $userDetails["email"];
    $messageDetails["from_name"] = "Sergey Kargopolov";
    $messageDetails["from_email"] = "sergey@earthlandia.com";
    
    // Load up email message from an email template
    $emailMessage = $emailConfirmation->loadEmailEmailMessage();
    $emailMessage = str_replace("{token}", $emailToken, $emailMessage);
    $messageDetails["message_body"] = $emailMessage;
    
    // Send out this email message to user
    $emailConfirmation->sendEmailConfirmation($messageDetails);
    
} else {   
    $returnValue["status"]="400";
    $returnValue["message"]="Could not register user with provided information"; 
}

$dao->closeConnection();

echo json_encode($returnValue);


?>
