<?php 
/* Main page with two forms: sign up and log in */
//require 'navigationbar.php';
require 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// Check if user is logged in using the session variable
if (isset($_SESSION['logged_in'])) {
	if ( $_SESSION['logged_in'] != 1 ) {
	  //$_SESSION['message'] = "You must log in before viewing your profile page!";
	  //header("location: error.php");    
	}
	else {
		header("location: home.php");  
	}
} else {
	//$country="None"; 
};

//remember me
$cookie = isset($_COOKIE['remembermefsim']) ? $_COOKIE['remembermefsim'] : '';
    if ($cookie) {
        list ($user, $token, $mac) = explode(':', $cookie);
        if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, $hash_secret), $mac)) {
            return false;
        }
		
		$user = $mysqli->escape_string($user);
		$result = $mysqli->query("SELECT rememberme FROM users WHERE username='$user'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$usertoken=$row['rememberme'];
		
        if (hash_equals($usertoken, $token)) {
			$result = $mysqli->query("SELECT * FROM users WHERE username='$user'");
			
			$row = $result->fetch_assoc();
			
	        $_SESSION['email'] = $row['email'];
	        $_SESSION['username'] = $mysqli->escape_string($row['username']);
	        $_SESSION['last_name'] = $row['last_name'];
	        $_SESSION['active'] = $row['active'];
	        
	        // This is how we'll know the user is logged in
	        $_SESSION['logged_in'] = true;
			
			//select character
			$result3 = $mysqli->query("SELECT * FROM characters WHERE user='$user' AND alive='1' LIMIT 1");
			$row3 = mysqli_fetch_array($result3);
			$count = $result3->num_rows;
	
			if($count != 0){
				$usercharacterid = $row3['id'];
				$_SESSION['usercharacterid'] = $usercharacterid;
			}
			
			header("location: home.php");
        }
    }

?>
<!DOCTYPE html>
<html>
<head>
<div class="flowercastle-image">
  <title>Fantasy-Sim</title>
  <meta name="description" content="Fantasy-Sim is a massively multiplayer online strategy game in which players can join a country consisting of a community of players. Together they can participate in the economy, wars and religions.">
  <meta name="keywords" content="rts,realtime,strategy,game,fantasy,lotr,lord,of,the,rings,economy,politics,religion,countries,soicial,simulation,erepublik,online,mmo,massive,multiplayer,free,to,play,orc,human,elve,dwarf,war,political,economy">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styletot.css">
   <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
   <link rel="manifest" href="/site.webmanifest">
   <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#add8e6">
   <meta name="msapplication-TileColor" content="#add8e6">
   <meta name="theme-color" content="#ffffff">
  <script src='https://www.google.com/recaptcha/api.js'></script> 
  

  
	<div class="indexlogobox">
		<img src="img/logo.png" width="100%" height="100%" object-fit="contain">
	</div>
	
  <br />
  <div class="indextextbox2">
  	<?php echo nl2br ("<div class=\"bold\">The fantasy massively multiplayer online strategy game</div>"); ?>
  </div>
  <br />
  <br />
  <br />
	
</head>

<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    if (isset($_POST['login'])) { //user logging in

        require 'login.php';
        
    }
    
    elseif (isset($_POST['register'])) { //user registering
        
        require 'register.php';
        
    }
}
?>
<body>

	<div class="indexformbox">
		<form method="post" action="">
			<button type="submit" name="registerform" />Register for free</button>
			<button type="submit" name="loginform" />Log in</button>
		</form>
	</div>
	<br />
	
	<div class="indexformbox">
		<?php
		if(isset($_POST['loginform'])){
			?>
			<h1>Welcome Back!</h1>
			<form method="post" action="index.php">
				<input type="email" required autocomplete="off" placeholder="e-mail adress*" name="email"/>
				<br />
				<input type="password" required autocomplete="off" placeholder="password*" name="password"/>
				<br />
				<input type="hidden" name="rememberme" value="false" />
				<input type="checkbox" name="rememberme" value="true"> Stay logged in<br />
				<p class="textbox"><a href="forgot.php">Forgot Password?</a></p>
				<button type="submit" name="login" />Log in</button>
			</form>
			<?php
			
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
		}
		?>
	</div>
	
	<div class="indexformbox">
		<?php
		if(isset($_POST['registerform'])){
			?>
			<h1>Sign Up for Free</h1>
			<form method="post" action="index.php">
				<input type="text" pattern="[a-zA-Z0-9-]+" required autocomplete="off" maxlength="40" placeholder="username*" name='username' />
				<br />
				<input type="email" required autocomplete="off" maxlength="100" placeholder="e-mail adress*" name="email"/>
				<br />
				<input type="password" required autocomplete="off" maxlength="40" placeholder="password*" name="password"/>
				<br />
			    <select required name="race" type="text">  
			  	  	  <option value="" disabled selected hidden>Choose A Race</option>   
					  <option value="dwarf">dwarf</option>
					  <option value="elf">elf</option>
					  <option value="ent">ent</option>
					  <option value="human">human</option>
					  <option value="orc">orc</option>
			    </select>
			    <br />
                                <div class="g-recaptcha" data-sitekey="6LetFYEUAAAAAE8OKM03o0WWhbqghy2y7UmPc3s8"></div>
                            <br />
				<button type="submit" name="register" />Register</button>
			</form>
			<?php
			
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
		}
		?>
	</div>
	
	<div class="indextextbox">
	    <br />
		<?php echo "- Claim your rightfull place in the world and built your dynasty through strategic marriages and via military strength."; ?>
		<br />
		<br />
		<?php echo "- In depth strategy with different modules such as an economic module, a political module and a religion module."; ?>
		<br />
		<br />
		<?php echo "- There is no possibility to buy in game currency with real money which creates an equal playing field for all players."; ?>
		<br />
		<br />
		<?php echo "- There is no imbalance between older and newer players as strength is limited by age."; ?>
		<br />
		<br />
	</div>

</div>

</body>
<footer>
<?php require 'bottombar.php'; ?>
<div class="invisiblebox">
	<?php echo nl2br ("<div class=\"t1\">rts realtime strategy game fantasy lotr lord of the rings economy politics religion countries soicial simulation erepublik online mmo massive multiplayer free to play
		orc human elve dwarf war political economy</div>"); ?>
</div>
</footer>
</html>
