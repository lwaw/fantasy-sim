<?php 
require 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Displays user information and some useful messages 
session_start();

// Check if user is logged in using the session variable
if ( $_SESSION['logged_in'] != 1 ) {
  $_SESSION['message'] = "You must log in before viewing your profile page!";
  header("location: error.php");    
}
else {
    // Makes it easier to read
    $username = $_SESSION['username'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
}*/
?>

<!DOCTYPE html>

<html>
	
<head>
  <title>Fantasy-Sim</title>
   
</head>
<body>
<?php
$borders=array(
//Buldihndan
array("name"=>"stoukarbolg", "border1"=>"gastudur", "border2"=>"kelkat tauvur", "border3"=>"kultavur", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"gastudur", "border1"=>"stoukarbolg", "border2"=>"bor proxis", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"kelkat tauvur", "border1"=>"stoukarbolg", "border2"=>"motragon", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"kultavur", "border1"=>"joalkfell", "border2"=>"NULL", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"joalkfell", "border1"=>"kultavur", "border2"=>"talam'ahard", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
//Arbor lean
array("name"=>"bor prontus", "border1"=>"bor sivellus", "border2"=>"torguer", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"bor sivellus", "border1"=>"bor proxis", "border2"=>"motragon", "border3"=>"bor prontus", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"bor norex", "border1"=>"bor proxis", "border2"=>"NULL", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"bor proxis", "border1"=>"gastudur", "border2"=>"bor sivellus", "border3"=>"bor norex", "border4"=>"bor lamur", "border5"=>"NULL"),
array("name"=>"bor lamur", "border1"=>"bor proxis", "border2"=>"bor norex", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
//Arados
array("name"=>"cartacia", "border1"=>"zallencia", "border2"=>"torguer", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"zallencia", "border1"=>"cartacia", "border2"=>"cahaurin", "border3"=>"ultercher", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"cahaurin", "border1"=>"zallencia", "border2"=>"torguer", "border3"=>"ochrishar", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"torguer", "border1"=>"cartacia", "border2"=>"cahaurin", "border3"=>"motragon", "border4"=>"bor prontus", "border5"=>"NULL"),
array("name"=>"motragon", "border1"=>"cahaurin", "border2"=>"torguer", "border3"=>"bor sivellus", "border4"=>"kelkat tauvur", "border5"=>"NULL"),
//Orcmorsult
array("name"=>"ochrishar", "border1"=>"uldamorf", "border2"=>"cahaurin", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"uldamorf", "border1"=>"ochrishar", "border2"=>"ultercher", "border3"=>"dor'fora", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"ultercher", "border1"=>"uldamorf", "border2"=>"utokuzter", "border3"=>"orzaghaltt", "border4"=>"zallencia", "border5"=>"NULL"),
array("name"=>"utokuzter", "border1"=>"ultercher", "border2"=>"orzaghaltt", "border3"=>"cartacia", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"orzaghaltt", "border1"=>"ultercher", "border2"=>"utokuzter", "border3"=>"talam'chead", "border4"=>"NULL", "border5"=>"NULL"),
//Sun cashai
array("name"=>"talam'chead", "border1"=>"ta'mor", "border2"=>"orzaghaltt", "border3"=>"NULL", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"ta'mor", "border1"=>"talam'chead", "border2"=>"dor'fora", "border3"=>"nai'ur", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"dor'fora", "border1"=>"ta'mor", "border2"=>"talam'ahard", "border3"=>"nai'ur", "border4"=>"uldamorf", "border5"=>"NULL"),
array("name"=>"talam'ahard", "border1"=>"dor'fora", "border2"=>"nai'ur", "border3"=>"joalkfell", "border4"=>"NULL", "border5"=>"NULL"),
array("name"=>"nai'ur", "border1"=>"ta'mor", "border2"=>"dor'fora", "border3"=>"talam'ahard", "border4"=>"NULL", "border5"=>"NULL")

);
?>
</body>
</html>
