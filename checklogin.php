<?php 
require 'db.php';
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

/* Displays user information and some useful messages */
//session_start();

// Check if user is logged in using the session variable
if ($_SESSION['logged_in'] == 1 ) {
    // Makes it easier to read
    $username = $_SESSION['username'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];   
}
else {
	session_start();
	$_SESSION['message'] = "You must log in before viewing your profile page!";
	header("location: error.php"); 
}

?>

<!DOCTYPE html>

<html>
	
<head>   
</head>
<body>
<?php
?>
</body>
</html>
