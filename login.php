<?php
/* User login process, checks if user exists and password is correct */

// Escape email to protect against SQL injections
$email = $mysqli->escape_string($_POST['email']);
$rememberme = $mysqli->escape_string($_POST['rememberme']);
$result = $mysqli->query("SELECT * FROM users WHERE email='$email'");

if ( $result->num_rows == 0 ){ // User doesn't exist
    $_SESSION['message'] = "User with that email doesn't exist!";
    header("location: error.php");
}
else { // User exists
    $user = $result->fetch_assoc();
	$usernamelogin=$mysqli->escape_string($user['username']);

    if ( password_verify($_POST['password'], $user['password']) ) {
        
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $mysqli->escape_string($user['username']);
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['active'] = $user['active'];
        
        // This is how we'll know the user is logged in
        $_SESSION['logged_in'] = 1;
		
		//select character
		$result3 = $mysqli->query("SELECT * FROM characters WHERE user='$usernamelogin' AND alive='1' LIMIT 1");
		$row3 = mysqli_fetch_array($result3);
		$count = $result3->num_rows;

		if($count != 0){
			$usercharacterid = $row3['id'];
			$_SESSION['usercharacterid'] = $usercharacterid;
		}
		
		//remember me
		if($rememberme=="true"){
			$token = openssl_random_pseudo_bytes(256);
			//Convert the binary data into hexadecimal representation.
			$token = bin2hex($token);
			
			$cookie = $usernamelogin . ':' . $token;
			$mac = hash_hmac('sha256', $cookie, $hash_secret);  
			$cookie .= ':' . $mac;
			setcookie('remembermefsim', $cookie,  time() + (86400 * 30)); //86400 is een dag
			
			$sql = "UPDATE users SET rememberme='$token' WHERE email='$email'";
			mysqli_query($mysqli, $sql);
		}

        header("location: home.php");
    }
    else {
        $_SESSION['message'] = "You have entered wrong password, try again!";
        header("location: error.php");
    }
}

