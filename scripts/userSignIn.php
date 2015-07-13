<?php
require("../db/MySQLDAO.php");
$config = parse_ini_file('../../../../SwiftCourse2.ini');

$returnValue = array();
if(empty($_REQUEST["userEmail"]) || empty($_REQUEST["userPassword"]))
{
    $returnValue["status"]="400";
    $returnValue["message"]="Missing required information";
    echo json_encode($returnValue);
    return;
}

$userEmail = htmlentities($_REQUEST["userEmail"]);
$userPassword = htmlentities($_REQUEST["userPassword"]);

$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
$dbpassword = trim($config["dbpassword"]);
$dbname = trim($config["dbname"]);
 
$dao = new MySQLDAO($dbhost, $dbuser, $dbpassword, $dbname);
$dao->openConnection();
$userDetails =$dao->getUserDetails($userEmail);

if(empty($userDetails))
{
    $returnValue["status"]="403";
    $returnValue["message"]="User not found";
    echo json_encode($returnValue);
    return;   
}

$userSecuredPassword = $userDetails["user_password"];
$userSalt = $userDetails["salt"];

if($userSecuredPassword === sha1($userPassword . $userSalt))
{
    $returnValue["status"]="200";
    $returnValue["userFirstName"] = $userDetails["first_name"];
    $returnValue["userLastName"] = $userDetails["last_name"];
    $returnValue["userEmail"] = $userDetails["email"];
    $returnValue["userId"] = $userDetails["user_id"];
} else {
    $returnValue["status"]="403";
    $returnValue["message"]="User not found";
    echo json_encode($returnValue);
    return;
}

$dao->closeConnection();

echo json_encode($returnValue);

?>
