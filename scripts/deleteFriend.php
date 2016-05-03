<?php
require("../db/MySQLDAO.php");

$config = parse_ini_file('../../../../SwiftCourse2.ini');

$returnValue = array();


if(empty($_REQUEST["friendToken"]))
{
    $returnValue["status"]="400";
    $returnValue["message"]="Missing required information";
    echo json_encode($returnValue);
    return;
}

$friend_token = htmlentities($_REQUEST["friendToken"]);
 
$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
$dbpassword = trim($config["dbpassword"]);
$dbname = trim($config["dbname"]);

$dao = new MySQLDAO($dbhost, $dbuser, $dbpassword, $dbname);
$dao->openConnection();

$result = $dao->deleteFriendRecord($friend_token);
 
if($result > 0)
{
    $returnValue["status"] = "200";
    $returnValue["operation_result"] = $result;
    $returnValue["message"]="Deleted";
    echo json_encode($returnValue);
} else {
    $returnValue["status"]="200";
    $returnValue["operation_result"] = $result;
    $returnValue["message"]="Could not delete record";
    echo json_encode($returnValue);
}

$dao->closeConnection();

?>
