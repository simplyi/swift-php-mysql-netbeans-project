<?php
require("../db/MySQLDAO.php");

 
$config = parse_ini_file('../../../../SwiftCourse2.ini');
$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
$dbpassword = trim($config["dbpassword"]);
$dbname = trim($config["dbname"]);
 

$dao = new MySQLDAO($dbhost, $dbuser, $dbpassword, $dbname);
$dao->openConnection();
 
$searchWord = null;

if(!empty($_REQUEST["searchWord"]))
{
   $searchWord = htmlentities($_REQUEST["searchWord"]);
}

$friends = $dao->searchFriends($searchWord);

 
if(!empty($friends))
{
    $returnValue["friends"] = $friends;
} else {
    $returnValue["message"] = "Could not find records";
}

$dao->closeConnection();
 
echo json_encode($returnValue);
 
?>
