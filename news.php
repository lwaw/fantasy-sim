<?php 
require 'navigationbar.php';
require 'db.php';
//require 'regionborders.php';
require_once 'purifier/library/HTMLPurifier.auto.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Displays user information and some useful messages */
//session_start();

// Check if user is logged in using the session variable
//if (isset($_SESSION['logged_in'])) { $topic  = $_GET["topic"]; } else { $topic=0; };
//$topic=$mysqli->escape_string($topic);

if ( isset($_SESSION['logged_in']) AND $_SESSION['logged_in'] != 1 ) {
  //$_SESSION['message'] = "You must log in before viewing your profile page!";
  //header("location: error.php");  
  $login = 0;
}
elseif(isset($_SESSION['logged_in']) AND $_SESSION['logged_in'] == 1) {
    // Makes it easier to read
    $username = $_SESSION['username'];
    //$last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
	$login = 1;
}else{
	$login=0;
}

//html purifier setup
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$config->set('HTML.SafeIframe', true); //iframes
$config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
?>

<!DOCTYPE html>

<html>
	
<head>
  <title>Fantasy-Sim</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Fantasy-Sim is a massively multiplayer online strategy game in which players can join a country community and join the economy, war and religion modules.">
  <meta name="keywords" content="rts,realtime,strategy,game,fantasy,lotr,lord,of,the,rings,economy,politics,religion,countries,soicial,simulation,erepublik,online,mmo,massive,multiplayer,free,to,play,orc,human,elve,dwarf,war,political,economy">
  <link rel="stylesheet" href="css/styletot.css">
   <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
   <link rel="manifest" href="/site.webmanifest">
   <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#add8e6">
   <meta name="msapplication-TileColor" content="#add8e6">
   <meta name="theme-color" content="#ffffff">
  <script type="text/javascript" src="tinymce/tinymce.js"></script>
  <script>
  tinymce.init({
    selector: '#mytextarea',
	plugins: ["emoticons autolink help preview textcolor"],
   toolbar: [
     'undo redo | styleselect | forecolor backcolor | bold italic | emoticons | alignleft aligncenter alignright | help | preview'
   ]
  });
  </script>
  

  
</head>

<body>

<div class="boxedtot">
<?php
//require 'ageing.php';

if (isset($_GET["view"])) { $view  = $_GET["view"]; } else { $view="std"; };
$view=$mysqli->escape_string($view);

if (isset($_GET["id"])) { $id  = $_GET["id"]; } else { $id=0; };
$id=$mysqli->escape_string($id);

if (isset($_GET["newspaperid"])) { $newspaperid  = $_GET["newspaperid"]; } else { $newspaperid=0; };
$newspaperid=$mysqli->escape_string($newspaperid);

if (isset($_GET["sort"])) { $sort  = $_GET["sort"]; } else { $sort="global"; };
$sort=$mysqli->escape_string($sort);

if (isset($_GET["articleid"])) { $articleid  = $_GET["articleid"]; } else { $articleid=0; };
$articleid=$mysqli->escape_string($articleid);

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$moderator = $row['moderator'];

//banned
$result2 = $mysqli->query("SELECT * FROM ban WHERE user='$username'") or die($mysqli->error());
$count = $result2->num_rows;

if ( $count != 0 ) {
	$result = $mysqli->query("SELECT * FROM ban WHERE user='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$reason = $row['reason'];
	$date = $row['date'];

 	$_SESSION['message'] = "You have been banned from the forum until $date for the following reason: $reason";
	header("location: error.php");    
}

if($newspaperid != 0 AND ($view == "owner" OR $view == "news")){
	$result = $mysqli->query("SELECT * FROM newspaper WHERE id='$newspaperid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$newspapername = $row['name'];
	
	echo nl2br ("<div class=\"h1\">$newspapername</div>");
}else{
	echo nl2br ("<div class=\"h1\">News</div>");
}

?> <hr /> <?php

if($login==1){
	if($view == "std"){
		$result2 = $mysqli->query("SELECT * FROM newspaper WHERE owner='$username'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		?>
		<form method="post" action="">
			<?php if($count != 0){ ?> <button type="submit" name="ownednewspapersform" /><?php echo "View owned newspapers"; ?></button> <?php } ?>
			<button type="submit" name="createnewspaperform" /><?php echo "Create new newspaper"; ?></button>
		</form>
		<?php
		
		?> <hr /> <?php
	}
}

if(isset($_POST['createnewspaperform'])){
	echo nl2br ("<div class=\"t1\">Buy a newspaper to write news articles. Starting a new newspaper costs 5 gold. The region in which you are currently positioned in will be the registered origin region of the newspaper. You will pay taxes to the owner of this region.</div>");
	?>
	<form method="post" action="">
		<input type="text" pattern="[a-zA-Z0-9]+[a-zA-Z0-9 ]+" size="25" required autocomplete="off" placeholder="Enter newspaper name here" maxlength="30" name='newspapername'/>
		<button type="submit" name="createnewspaper" /><?php echo "Create new newspaper"; ?></button>
	</form>
	<?php
}

if(isset($_POST['createnewspaper'])){
	$newspapername = $mysqli->escape_string($_POST['newspapername']);
	$newspapersafe = str_replace(' ', '', $newspapername);
	if(strlen($newspapername) <= 30 AND strlen($newspapername) >= 1 AND ctype_alnum($newspapersafe) AND $login == 1){
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$location2 = $row['location2'];
		
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];
		
		$gold = $gold - 5;
		
		if($gold >= 0){
			$sql = "INSERT INTO newspaper (owner, name, region) " 
		     . "VALUES ('$username','$newspapername','$location2')";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			echo'<div class="boxed">Done!</div>';
		}
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['ownednewspapersform'])){
	$result = $mysqli->query("SELECT id, name FROM newspaper WHERE owner='$username'") or die($mysqli->error());
	
	?>
	<form method="post" action="">
	    <select name="id" type="text">
	    <option selected="selected">Choose one</option>
	    <?php       
	    // Iterating through the product array
	    while($row=mysqli_fetch_array($result)) {
		$id=$row["id"];
		$name=$row["name"];
	    ?>
	    <option value="<?php echo strtolower($id); ?>"><?php echo $name; ?></option>
	    <?php
	    }
	    ?>
	    </select> 
	    <button type="submit" name="selectnewspaper" />Select newspaper</button>
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

if(isset($_POST['selectnewspaper'])){
	$id = $mysqli->escape_string($_POST['id']);
	
	?>
	<script>
		var val = "<?php echo $id ?>"
	    window.location = 'news.php?view=owner&newspaperid='+val;
	</script>
	<?php
}

if(isset($_POST['newarticle'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$price = $mysqli->escape_string($_POST['price']);
	$price = (int) $price;
	
	$articlename = $mysqli->escape_string($_POST['articlename']);
	$articlenamesafe = str_replace(' ', '', $articlename);
	
	$abstract = $mysqli->escape_string($_POST['abstract']);
	$abstractsafe = str_replace(' ', '', $abstract);
	
	$content = $mysqli->escape_string($_POST['content']);
	
	$result = $mysqli->query("SELECT * FROM newspaper WHERE id='$id' AND owner='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$owner = $row['owner'];
	$region = $row['region'];
	
	$result = $mysqli->query("SELECT * FROM region WHERE name='$region'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$curowner = $row['curowner'];

	if(strlen($articlename) <= 30 AND strlen($articlename) >= 1 AND ctype_alnum($articlenamesafe) AND strlen($abstract) <= 300 AND strlen($abstract) >= 1){
		if($owner == $username AND $login == 1){
			$sql = "INSERT INTO newsarticle (newspaperid, date, country, price, title, abstract, content) " 
		     . "VALUES ('$id',NOW(), '$curowner', '$price','$articlename', '$abstract', '$content')";
			mysqli_query($mysqli2, $sql);
			
			echo'<div class="boxed">Done!</div>';
		}
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

//ownerpage
if($view=="owner" AND $newspaperid==$newspaperid){
	$result = $mysqli->query("SELECT * FROM newspaper WHERE id='$newspaperid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$owner = $row['owner'];
	
	if($owner==$username AND $login==1){
		?>
		<div class="everythingOnOneLine">
		<form method="post" action="">
			<button type="submit" name="newarticleform" />Submit new article</button>
			<button type="submit" name="submittedarticles" />View submitted articles</button>
		</form>
		</div> 
		<?php
		
		if(isset($_POST['newarticleform'])){
			?>
			<div class="textbox">
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo $newspaperid; ?>" />
					<input type="text" pattern="[a-zA-Z0-9]+[a-zA-Z0-9 ]+" size="25" required autocomplete="off" placeholder="Enter article name here" maxlength="30" name='articlename'/>
					<input type="number" size="25" required autocomplete="off" id="price" name="price" min="0" max="9999" step="1" placeholder="Enter price here"/>
					<?php echo'<div class="bold">Abstract:</div>'; ?>
					<textarea rows="4" cols="50" name="abstract" maxlength="300" placeholder="Enter abstract here..."></textarea>
					<?php echo'<div class="bold">Content:</div>'; ?>
					<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="6000" placeholder="Enter post here..."></textarea>
					<button type="submit" name="newarticle" />Submit new article</button>
				</form>
			</div>
			<?php
			
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
		}
		
		if(isset($_POST['submittedarticles'])){
			$sql = "SELECT * FROM newsarticle WHERE newspaperid='$newspaperid' ORDER BY date DESC LIMIT $start_from, ".$results_per_page;
			$rs_result = $mysqli2->query($sql);
			
			?>	
			<table id="table1">
				<tr>
			   		<th>
			   			<?php echo nl2br ("<div class=\"bold\">Article</div>"); ?>
			   		</th>
			   		<th>
			   			<?php echo nl2br ("<div class=\"bold\">Date published</div>"); ?>
			   		</th>
			   		<th>
			   			<?php echo nl2br ("<div class=\"bold\">Price</div>"); ?>
			   		</th>
			   		<th>
			   			<?php echo nl2br ("<div class=\"bold\">Buyers</div>"); ?>
			   		</th>
				</tr> 
			<?php
			while($row = $rs_result->fetch_assoc()) {
				$title=$row["title"];
				$abstract=$row["abstract"];
				$articleid=$row["id"];
				$newspaperid=$row["newspaperid"];
				$price=$row["price"];
				$buyers=$row["buyers"];
				$date=$row["date"];
				
				?> 
	           <tr>
		           <td>
		           		<?php echo "<a href='news.php?view=news&articleid=$articleid'>$title </a>";?>
		           </td>
		           <td>
						<?php echo "$date"; ?>
		           </td>
		           <td>
						<?php echo "$price"; ?>
		           </td>
		           <td>
						<?php echo "$buyers"; ?>
		           </td>
	           </tr>
				<?php 
				
			}; 
			?> 
			</table>
			</div>
			<?php 
			
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
		}
	}
}

if($view=="std" AND $login==1){
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$location = $row['location'];
	
	if($sort=="global"){
		$sql = "SELECT * FROM newsarticle ORDER BY date DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli2->query($sql);
	}elseif($sort=="local"){
		$sql = "SELECT * FROM newsarticle WHERE country='$location' ORDER BY date DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli2->query($sql);
	}elseif($sort=="subscription"){
		//select * from newsarticle waar kolommen waar newspaperid==newsarticleid in table newsextra
		$sql = "SELECT newsarticle.* FROM newsarticle INNER JOIN newsextra ON newsarticle.newspaperid=newsextra.newsarticleid WHERE newsextra.user='$username' AND newsextra.type='subscription' ORDER BY date DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli2->query($sql);
	}
	
	?> 
	
	<div class="everythingOnOneLine">
	<form method="post" action="">
		<button type="submit" name="globalnewsform" />Global news</button>
		<button type="submit" name="localnewsform" />Local news</button>
		<button type="submit" name="subscriptionnewsform" />Subscriptions</button>
	</form>
	</div> 
			
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th>
			<?php echo nl2br ("<div class=\"bold\">$sort news</div>"); ?>
		</th>
	    <th> </th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$title=$row["title"];
		$abstract=$row["abstract"];
		$articleid=$row["id"];
		$newspaperid=$row["newspaperid"];
		$price=$row["price"];
		
		$result = $mysqli->query("SELECT * FROM newspaper WHERE id='$newspaperid'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$newspapername = $row['name'];
		?> 
           <tr>
           <td class="rightpart">
           		<?php echo "<a href='news.php?view=news&articleid=$articleid'>$title </a>";?>
           </td>
           <td class="leftpart">
	           	<?php echo nl2br ("<div class=\"bold\">$newspapername</div>"); ?>
	           	<br />
	           	<?php echo'<div class="bold">Abstract:</div>'; ?>
	           	<br />
				<?php 
           		$clean_html = $purifier->purify($abstract);
				echo "$clean_html";
				echo "$abstract"; 
				?>
				<br />
				<?php 
				if($price != 0){ echo'<div class="boxed">Paid article</div>'; }else{  } ?>
           </td>
           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	if($sort=="global"){$sql = "SELECT COUNT(id) AS total FROM newsarticle";}
	if($sort=="local"){$sql = "SELECT COUNT(id) AS total FROM newsarticle WHERE country='$location'";}
	if($sort=="subscription"){$sql = "SELECT COUNT(price) AS total FROM newsarticle INNER JOIN newsextra ON newsarticle.newspaperid=newsextra.newsarticleid WHERE newsextra.user='$username' AND newsextra.type='subscription'";}
	$result = $mysqli2->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results	
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
	            echo "<a href='news.php?view=$view&sort=$sort&page=".$i."'";
	            if ($i==$page)  echo " class='curPage'";
	            echo ">".$i."</a> "; 
	};	
}

if($view=="news"){
	$result = $mysqli2->query("SELECT * FROM newsarticle WHERE id='$articleid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$title = $row['title'];
	$content = $row['content'];
	$price = $row['price'];
	$newspaperid = $row['newspaperid'];
	
	$result = $mysqli->query("SELECT * FROM newspaper WHERE id='$newspaperid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$region = $row['region'];
	$name = $row['name'];
	
	$result = $mysqli->query("SELECT curowner FROM region WHERE name='$region'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$country = $row['curowner'];
	
	$result = $mysqli->query("SELECT currency FROM countryinfo WHERE country='$country'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$currency = $row['currency'];
	
	$result = $mysqli2->query("SELECT * FROM newsextra WHERE user='$username' AND type='subscription'") or die($mysqli->error());
	$countsub = $result->num_rows;
	
	$paid=0;
	
	if($price != 0 AND $paid == 0){
		$result2 = $mysqli2->query("SELECT * FROM newsextra WHERE newsarticleid='$articleid' AND type='paid' AND user='$username'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count != 0){
			$paid=1;
		}else{
			?>
			<form method="post" action="">
				<input type="hidden" name="articlepayid" value="<?php echo $articleid; ?>" />
				<button type="submit" name="pay" /><?php echo "Pay $price $currency to view article"; ?></button>
			</form>
			
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>

			</div>
			<?php
		}
		
		if($paid==1){
			?> 
			<div class="scroll">
			<table id="table1">
			<tr>
			    <th class="rightpart"> 
					<?php if($countsub == 0){ ?>
						<form method="post" action="">
							<input type="hidden" name="type" value="<?php echo "0";; ?>" />
							<input type="hidden" name="newspaperid" value="<?php echo $newspaperid; ?>" />
							<button type="submit" name="subsribe" />Subscribe to newspaper</button>
						</form>
					<?php }else{ ?>
						<form method="post" action="">
							<input type="hidden" name="type" value="<?php echo "1";; ?>" />
							<input type="hidden" name="newspaperid" value="<?php echo $newspaperid; ?>" />
							<button type="submit" name="subsribe" />Unsubscribe to newspaper</button>
						</form>
					<?php } ?>
			    </th>
			    <th class="leftpart"> <?php echo nl2br("<div class=\"h1\">$title</div>"); ?> </th>
			</tr>
			<tr>
				<?php $clean_html = $purifier->purify($content); ?>
			    <td colspan="2"> <?php echo nl2br("<div class=\"t1\">$clean_html</div>"); ?> </td>
			</tr>
			<?php
			$sql = "SELECT * FROM newsextra WHERE type='comment' AND newsarticleid='$articleid' ORDER BY date ASC LIMIT $start_from, ".$results_per_page;
			$rs_result = $mysqli2->query($sql);
			
			while($row = $rs_result->fetch_assoc()) {
				$postid=$row["id"];
				?> 
		           <tr>
		           <td class="rightpart">
			           	<?php $creator = $row["user"]; ?>
			           	<?php echo nl2br("<div class=\"t1\"><a href='account.php?user=$creator'>$creator</a></div>"); ?>
			           	<br />
			           	<?php echo $row["date"]; ?>
			           	<br />
						<div class="textbox">
							<form method="post" action="">
								<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
								<input type="hidden" name="topid" value="<?php echo $articleid; ?>" />
								<?php if($login==1){ ?> <button type="submit" name="reply" />Write a reply</button> <?php } ?>
							</form>
						</div>
			           	<br />
			           	<?php if($moderator==1 || $moderator==2 || $moderator==5){ ?>
						<div class="textbox">
							<form method="post" action="">
								<input type="hidden" name="topid" value="<?php echo $articleid; ?>" />
								<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
								<button type="submit" name="moderate" />Moderate post</button>
							</form>
						</div>
						<?php
						echo "post id: $postid";
						?>
						<?php } ?>
		           </td>
		           <td class="leftpart">
		           		<?php
		           		$clean_html = $purifier->purify($row["content"]);
						echo "$clean_html";
		           		?>
			           	<?php
			           	// write reply
			           	if(isset($_POST['reply'])){
			           			$postid = $mysqli->escape_string($_POST['postid']);
				           		$topid = $mysqli->escape_string($_POST['topid']);
								$creator = $row["user"];
								$content = $row["content"];
								if($postid == $row["id"]){
				           		?>
					           	<div class="textbox">
									<form method="post" action="">
										<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
										<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
										<input type="hidden" name="creator" value="<?php echo $creator; ?>" />
										<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="4000" autofocus placeholder="Enter reply here..."></textarea>
										<button type="submit" name="reply2" />Submit reply</button>
									</form>
								</div>
				           		<?php
							}
			           	}
						
						//report
			           	if(isset($_POST['report'])){
				           		$topid = $mysqli->escape_string($_POST['topid']);
								$postid = $mysqli->escape_string($_POST['postid']);
								$creator = $row["creator"];
								$content = $row["content"];
								if($postid == $row["id"]){
				           		?>
					           	<div class="textbox">
									<form method="post" action="">
										<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
										<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
										<input type="hidden" name="creator" value="<?php echo $creator; ?>" />
										<textarea rows="4" cols="50" name="content" maxlength="200" autofocus placeholder="Enter reason here..."></textarea>
										<button type="submit" name="report2" />Submit</button>
									</form>
								</div>
				           		<?php
							}
			           	}
						//moderate post
			           	if(isset($_POST['moderate'])){
				           		$topid = $mysqli->escape_string($_POST['topid']);
								$postid = $mysqli->escape_string($_POST['postid']);
								
								$result2 = $mysqli2->query("SELECT * FROM newsextra WHERE id='$postid'") or die($mysqli->error());
								$row2 = mysqli_fetch_array($result2);
				           		$clean_html = $purifier->purify($row2["content"]);
								
								if($postid == $row["id"]){
				           		?>
					           	<div class="textbox">
									<form method="post" action="">
										<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
										<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
										<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="4000" autofocus><?php echo $clean_html; ?></textarea>
										<button type="submit" name="moderate2" />Moderate</button>
									</form>
								</div>
				           		<?php
							}
			           	}
						?>
		           </td>
		           </tr>
				<?php 
				
			}; 
			?>
			</table>
			</div>
			<?php 
			
			//add post
			if($login==1){
				?>
				<div class="textbox">
					<form method="post" action="">
						<input type="hidden" name="topid" value="<?php echo $articleid; ?>" />
						<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="4000" placeholder="Enter post here..."></textarea>
						<button type="submit" name="addpost" />Submit new post</button>
					</form>
				</div>
				<?php
			}
			
			$sql = "SELECT COUNT(id) AS total FROM newsextra WHERE newsarticleid='$articleid'";
			$result = $mysqli2->query($sql);
			$row = $result->fetch_assoc();
			$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results	
			
			for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
			            echo "<a href='news.php?view=news&articleid=$articleid&page=".$i."'";
			            if ($i==$page)  echo " class='curPage'";
			            echo ">".$i."</a> "; 
			};	
		}
	}
}

if(isset($_POST['pay'])){
	$articlepayid = $mysqli->escape_string($_POST['articlepayid']);
	
	$result = $mysqli2->query("SELECT * FROM newsarticle WHERE id='$articlepayid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$newspaperid = $row['newspaperid'];
	$title = $row['title'];
	$content = $row['content'];
	$price = $row['price'];
	$buyers = $row['buyers'];
	
	$result = $mysqli->query("SELECT * FROM newspaper WHERE id='$newspaperid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$owner = $row['owner'];
	$region = $row['region'];
	
	$result = $mysqli->query("SELECT curowner, taxtoday FROM region WHERE name='$region'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$country = $row['curowner'];
	$taxtoday = $row['taxtoday'];
	
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$currency = $row['currency'];
	$countrymoney = $row['money'];
	$vat = $row['vat'];
	
	$result = $mysqli->query("SELECT $currency FROM currency WHERE usercur='$owner'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$ownermoney = $row[$currency];
	
	$result = $mysqli->query("SELECT $currency FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$usermoney = $row[$currency];
	
	$buyers=$buyers+1;
	
	$tax=($vat/100)*$price;
	$taxtoday=$taxtoday+$tax;
	$ownermoney=$ownermoney+$price-$tax;
	$usermoney=$usermoney-$price;
	
	if($usermoney >= 0){
		$sql = "UPDATE usercur SET $currency='$ownermoney' WHERE usercur='$owner'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE usercur SET $currency='$usermoney' WHERE usercur='$username'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE region SET taxtoday='$taxtoday' WHERE name='$region'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE countryinfo SET money='$countrymoney' WHERE country='$country'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE newsarticle SET buyers='$buyers' WHERE id='$articlepayid'";
		mysqli_query($mysqli2, $sql);
		
		$sql = "INSERT INTO newsextra (newsarticleid, type, date, user) " 
	     . "VALUES ('$articlepayid','paid',NOW(),'$username')";
		mysqli_query($mysqli2, $sql);
		
		echo "Done.";
		
		?>
		<script>
			window.location.reload();
		</script>
		<?php
	}else{
		echo'<div class="boxed">You don\'t have enough money!</div>';
	}
}

//topid=articleid
if(isset($_POST['addpost'])){
	$topid = $mysqli->escape_string($_POST['topid']);
	$topid = (int) $topid;
	$content = $mysqli->escape_string($_POST['content']);
	
	$sql = "INSERT INTO newsextra (newsarticleid, type, date, user, content) " 
     . "VALUES ('$topid','comment',NOW(),'$username','$content')";
	mysqli_query($mysqli2, $sql);
			
	echo'<div class="boxed">Done!</div>';
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

//postid=replyto en topid=articleid
if(isset($_POST['reply2'])){
	$topid = $mysqli->escape_string($_POST['topid']);
	$topid = (int) $topid;
	$postid = $mysqli->escape_string($_POST['postid']);
	$postid = (int) $postid;
	$content = $mysqli->escape_string($_POST['content']);
	$creator = $mysqli->escape_string($_POST['creator']);
	
	if(strlen($content) <= 4100){
		$result2 = $mysqli2->query("SELECT * FROM newsextra WHERE id='$postid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$replyto = $row2['content'];
		
		$final = '<div class="replybox">' . '<div class="bold">' . $creator . ' wrote:' . '</div>' . '<br />' . $replyto . '</div>' . $content;
		
		$sql = "INSERT INTO newsextra (newsarticleid, type, date, user, content) " 
	     . "VALUES ('$topid','comment',NOW(),'$username','$final')";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
}

if(isset($_POST['moderate2'])){
	$topid = $mysqli->escape_string($_POST['topid']);
	$topid = (int) $topid;
	$postid = $mysqli->escape_string($_POST['postid']);
	$postid = (int) $postid;
	$content = $mysqli->escape_string($_POST['content']);
	
	$sql = "UPDATE newsextra SET content='$content' WHERE id='$postid'";
	mysqli_query($mysqli2, $sql);
	
	echo'<div class="boxed">Done!</div>';
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['subsribe'])){
	$type = $mysqli->escape_string($_POST['type']);
	$newspaperid = $mysqli->escape_string($_POST['newspaperid']);
	
	if($type==0){
		$sql = "INSERT INTO newsextra (newsarticleid, type, date, user) " 
	     . "VALUES ('$newspaperid','subscription',NOW(),'$username')";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
	}else{
	$sql = "DELETE FROM newsextra WHERE user='$username' AND type='subscription' AND newsarticleid='$newspaperid')";
	mysqli_query($mysqli2, $sql);
	
	echo'<div class="boxed">Done!</div>';
	}
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['globalnewsform'])){
	?>
	<script>
	    window.location = 'news.php?view=std&sort=global';
	</script>
	<?php
}
if(isset($_POST['localnewsform'])){
	?>
	<script>
	    window.location = 'news.php?view=std&sort=local';
	</script>
	<?php
}

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
