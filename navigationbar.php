<?php 
require 'db.php';
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
session_start();

if ($_SESSION['logged_in'] == 1 ) {
	$username = $_SESSION['username'];  
	
	//get nationality
	$result = $mysqli->query("SELECT nationality FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$nationality=$row['nationality'];
	
	$link = "account.php?user=$username";
	$link2 = "country.php?country=$nationality";
}else{
	$link = "index.php";
	$link2 = "index.php";
}

?>

<!DOCTYPE html>

<html>
	
<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
html, body{
    margin: 0;

    padding: 0;

    min-width: 100%;
    width: 100%;
    max-width: 100%;

    min-height: 100%;
    height: 100%;
    max-height: 100%;
}

body {margin:0;font-family:Arial}

.topnav {
  overflow: hidden;
  background-color: #2F4F2F;
  width: 60%;
  margin-left: auto;
  margin-right: auto;
}

.topnav a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.active {
  background-color: #4CAF50;
  color: white;
}

.topnav .icon {
  display: none;
}

.dropdown {
  float: left;
  overflow: hidden;
}

.dropdown .dropbtn {
  font-size: 17px;    
  border: none;
  outline: none;
  color: white;
  padding: 14px 16px;
  background-color: inherit;
  font-family: inherit;
  margin: 0;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  float: none;
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  text-align: left;
}

.topnav a:hover, .dropdown:hover .dropbtn {
  background-color: #555;
  color: white;
}

.dropdown-content a:hover {
  background-color: #ddd;
  color: black;
}

.dropdown:hover .dropdown-content {
  display: block;
}

@media screen and (max-width: 1400px) {
  .topnav{
  	width: 100%;
  }
	
  .topnav a:not(:first-child), .dropdown .dropbtn {
    display: none;
  }
  .topnav a.icon {
    float: right;
    display: block;
  }
}

@media screen and (max-width: 1400px) {
  .topnav.responsive {position: relative;}
  .topnav.responsive .icon {
    position: absolute;
    right: 0;
    top: 0;
  }
  .topnav.responsive a {
    float: none;
    display: block;
    text-align: left;
  }
  .topnav.responsive .dropdown {float: none;}
  .topnav.responsive .dropdown-content {position: relative;}
  .topnav.responsive .dropdown .dropbtn {
    display: block;
    width: 100%;
    text-align: left;
  }
 }
/*
??????????????????????????????/

ul {
    list-style-type: none;
    overflow: hidden;
    background-color: #2F4F2F;
	margin-left: auto;
    margin-right: auto;
    width: 60%;
    padding: 0px;
    margin-bottom: 0px;
    margin-top: 0px;
}

li {
    float: left;
}

li a, .dropbtn {
    #display: inline-block;
    color: white;
    text-align: left;
    padding: 14px 16px;
    text-decoration: none;
    max-height: 100%;
}

li a:hover, .dropdown:hover .dropbtn {
    background-color: red;
}

li.dropdown {
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
}

.dropdown-content a:hover {background-color: #f1f1f1}

.dropdown:hover .dropdown-content {
    display: block;
}

.boxed {
  	color: white;
  	background-color: red;
} 
*/
/* logo */
.logobox {
	height: auto;
	width: 10%;
	margin-left: 0%;
	margin-right: auto;
	margin-bottom: 0px;
	position: absolute;
}

img {
    width: 100%;
    height: 100%;
}

.electionbox {
	margin-left: auto;
    margin-right: auto;
    width: 60%;
}

@media only screen and (max-width: 1400px) {
	.logobox{
		height: auto;
		width: 20%;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 0px;
		position: relative;
	}
	
	.electionbox{
		width: 100%;
	}
}

</style>

</head>
<body>

<div class="logobox">
	<a href="<?php if($_SESSION['logged_in'] == 1){echo "home.php";}else{echo $link;} ?>">
	<img src="img/logo_2.png" width="100%" height="100%" object-fit="contain">
	</a>
</div>

<div class="topnav" id="myTopnav">
  <a href="<?php if($_SESSION['logged_in'] == 1){echo "home.php";}else{echo $link;} ?>">Home</a>
  <div class="dropdown">
    <button class="dropbtn">User 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="<?php echo $link;?>">Account</a>
      <a href="<?php if($_SESSION['logged_in'] == 1){echo "wayoflife.php";}else{echo $link;} ?>">Way of life</a>
      <a href="<?php if($_SESSION['logged_in'] == 1){echo "trainingground.php";}else{echo $link;} ?>">Training ground</a>
      <a href="<?php if($_SESSION['logged_in'] == 1){echo "work.php";}else{echo $link;} ?>">Work</a>
      <a href="<?php if($_SESSION['logged_in'] == 1){echo "politicalparty.php";}else{echo $link;} ?>">Politics</a>
      <a href="<?php if($_SESSION['logged_in'] == 1){echo "militaryunit.php";}else{echo $link;} ?>">Military unit</a>
      <a href="<?php if($_SESSION['logged_in'] == 1){echo "religion.php";}else{echo $link;} ?>">Religion</a>
    </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn">Market 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "jobmarket.php";}else{echo $link;} ?>">Jobmarket</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "marketplace.php";}else{echo $link;} ?>">Market place</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "currencymarket.php";}else{echo $link;} ?>">Currency market</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "tavern.php";}else{echo $link;} ?>">Tavern</a>
    </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn">Country 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
     <a href="<?php echo $link2;?>">Country</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "elections.php";}else{echo $link;} ?>">Elections</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "wars.php";}else{echo $link;} ?>">Wars</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "resistancewar.php";}else{echo $link;} ?>">Resistancewar</a>
    </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn">Community 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
     <a href="forum.php">Forum</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "messages.php";}else{echo $link;} ?>">Messages</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "chat.php?type=global&typeid=0";}else{echo $link;} ?>">Chat</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "news.php?view=std&sort=global";}else{echo $link;} ?>">News</a>
    </div>
  </div> 
  <div class="dropdown">
    <button class="dropbtn">Statistics 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "rankings.php?type=users&country=Arados&sort=lastonline&order=desc";}else{echo $link;} ?>">Users</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "rankings.php?type=characters&country=&sort=name&order=asc";}else{echo $link;} ?>">Characters</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "rankings.php?type=country&sort=country&order=asc";}else{echo $link;} ?>">Kingdoms</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "rankings.php?type=region&sort=name&order=asc";}else{echo $link;} ?>">Duchies</a>
     <a href="<?php if($_SESSION['logged_in'] == 1){echo "rankings.php?type=battles&sort=name&order=asc";}else{echo $link;} ?>">Battles</a>
    </div>
  </div> 
  <a href="world_map.php">world map</a>
  <a href="<?php if($_SESSION['logged_in'] == 1){echo "shop.php";}else{echo $link;} ?>">Shop</a>
  <a href="#">  <?php date_default_timezone_set('UTC'); $date = date("l j F\, Y\. H:i:s"); echo "$date"; ?></a>
  <a href="logout.php">Logout</a>
  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
</div>



<div class="electionbox">
<?php
date_default_timezone_set('UTC');
$day = date("d");

if($day==1){
	echo nl2br("<div class=\"boxed\"><a href='elections.php'>Vote for new president</a></div>");
}elseif($day==15){
	echo nl2br("<div class=\"boxed\"><a href='elections.php'>Vote for new congress</a></div>");
}
?>
</div>

<script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>

</body>
</html>
