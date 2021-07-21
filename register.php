<?php
require 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

$userIP = $_SERVER["REMOTE_ADDR"];
$recaptchaResponse = $_POST['g-recaptcha-response'];
//echo "$recaptchaResponse";

$request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$g_secretKey}&response={$recaptchaResponse}&remoteip={$userIP}");

if(!strstr($request, "true")){
  $_SESSION['message'] = "Failed verification!";
  header("location: error.php");
}else{
	/* Registration process, inserts user info into the database 
	   and sends account confirmation email message
	 */
	
	// Set session variables to be used on profile.php page
	$_SESSION['email'] = $_POST['email'];
	$_SESSION['username'] = $_POST['username'];
	//$_SESSION['nationality'] = $_POST['nationality'];
	//$_SESSION['last_name'] = $_POST['last_name'];
	
	// Escape all $_POST variables to protect against SQL injections
	$username = $mysqli->escape_string($_POST['username']);
	$race = $mysqli->escape_string($_POST['race']);
	$email = $mysqli->escape_string($_POST['email']);
	$passwordlength = $mysqli->escape_string($_POST['password']);
	$password = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
	$hash = $mysqli->escape_string( md5( rand(0,1000) ) ); //hash alleen gebruikt voor mail
	
	if($race=="dwarf"){
		$nationality="Buldihndan";
		$location="Buldihndan";
		$location2="stoukarbolg";
	}elseif($race=="elf"){
		$nationality="Sun cashai";
		$location="Sun cashai";
		$location2="nai'ur";
	}elseif($race=="ent"){
		$nationality="Arbor lean";
		$location="Arbor lean";
		$location2="bor norex";
	}elseif($race=="human"){
		$nationality="Arados";
		$location="Arados";
		$location2="cartacia";
	}elseif($race=="orc"){
		$nationality="Orcmorsult";
		$location="Orcmorsult";
		$location2="utokuzter";
	}
	
	$location2 = $mysqli->escape_string($location2);
	      
	// Check if user with that email already exists
	$result = $mysqli->query("SELECT * FROM users WHERE email='$email'") or die($mysqli->error());
	$result2 = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	
	// We know user email exists if the rows returned are more than 0
	$usernamecheck = strtolower($username);
	if($usernamecheck == "null" OR $usernamecheck == "free" OR $usernamecheck == "admin"){
	    $_SESSION['message'] = 'This username is not allowed!';
	    header("location: error.php");
	}else{
	
		if ( $result->num_rows > 0 ) {
		    
		    $_SESSION['message'] = 'User with this email already exists!';
		    header("location: error.php");
		    
		}
		else { // Email doesn't already exist in a database, proceed...
		
		    // active is 0 by DEFAULT (no need to include it here)
		    $sql = "INSERT INTO users (username, last_name, email, password, hash, accountcreated, nationality, race, location, location2, lastonline) " 
		            . "VALUES ('$username', 'N', '$email','$password', '$hash', NOW(), '$nationality', '$race', '$location', '$location2', NOW())";
		
		// We know username exists if the rows returned are more than 0
		if ( $result2->num_rows > 0 ) {
		    
		    $_SESSION['message'] = 'User with this username already exists!';
		    header("location: error.php");
		    
		}
		else { // Username doesn't already exist in a database, proceed...
		
		    // active is 0 by DEFAULT (no need to include it here)
		    $sql = "INSERT INTO users (username, last_name, email, password, hash, accountcreated, nationality, race, location, location2, lastonline) " 
		            . "VALUES ('$username', 'N','$email','$password', '$hash', NOW(), '$nationality', '$race', '$location', '$location2', NOW())";
		
		
		    // Add user to the database
		    if(ctype_alnum($username) AND strlen($username) <= 400 AND strlen($passwordlength) <= 400 AND strlen($email) <= 1000){ //check for correct username
			    if ( $mysqli->query($sql) ){
			
			        $_SESSION['active'] = 0; //0 until user activates their account with verify.php
			        $_SESSION['logged_in'] = true; // So we know the user has logged in
			        $_SESSION['message'] =
			                
			                 "Confirmation link has been sent to $email, please verify
			                 your account by clicking on the link in the message!";
			
			        // Send registration confirmation link (verify.php)
					$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
					try {
					    //Server settings
					    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
					    $mail->isSMTP();                                      // Set mailer to use SMTP
					    $mail->Host = $mail_host;  // Specify main and backup SMTP servers
					    $mail->SMTPAuth = true;                               // Enable SMTP authentication
					    $mail->Username = 'donotreply@fantasy-sim.com';                 // SMTP username
					    $mail->Password = $mail_pass_donotreply;                           // SMTP password
					    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					    $mail->Port = 587;                                    // TCP port to connect 
					
					    //Recipients
					    $mail->setFrom('donotreply@fantasy-sim.com', 'Mailer');
					    $mail->addAddress($email);               // Name is optional
					
					    //Content
					    $mail->isHTML(true);                                  // Set email format to HTML
					    $mail->Subject = 'Account Verification ( fantasy-sim.com )';
					    $mail->Body    = '
					    Hello '.$username.',
			
			        	Thank you for signing up!
			
			       		Please click this link to activate your account:
			
			        	http://fantasy-sim.com/verify.php?email='.$email.'&hash='.$hash;
					    $mail->send();
					    echo 'Message has been sent';
					    //header("location: home.php");
						echo "<script>window.location.replace = 'home.php'</script>";
					} catch (Exception $e) {
					    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
						//header("location: home.php");
						echo "<script>window.location.replace = 'home.php'</script>";
					} 
			    }else {
			        $_SESSION['message'] = 'Registration failed 1!';
					echo "$sql";
			        header("location: error.php");
			    }
			}else{
		        $_SESSION['message'] = 'Registration failed 2!';
		        header("location: error.php");
			}
	
	}}}
}