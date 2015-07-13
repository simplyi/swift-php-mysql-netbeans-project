<?php
require("../db/MySQLDAO.php");
$config = parse_ini_file('../../../../SwiftCourse2.ini');
 
$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
$dbpassword = trim($config["dbpassword"]);
$dbname = trim($config["dbname"]);

$emailToken = htmlentities($_GET["token"]);
if(empty($emailToken))
{
    echo "Missing required parameter";
    return;
}

$dao = new MySQLDAO($dbhost, $dbuser, $dbpassword, $dbname);
$dao->openConnection();

$user_id = $dao->getUserIdWithToken($emailToken);

if(empty($user_id))
{
    echo "User with this email token is not found";
    return;
}

$result = $dao->setEmailConfirmedStatus(1, $user_id);
if($result)
{ 
  $dao->deleteUsedToken($emailToken);  
  echo "Thank you! Your email is now confirmed!"; 
}

$dao->closeConnection();

?>
