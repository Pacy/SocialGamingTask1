<?php
//Facebook authentication and authorization
require_once ("sdk/facebook.php");

$config = array();
$config["appId"] = '###'; // your App ID here
$config["secret"] = '###'; // your App secret here 

$facebook = new Facebook($config);
$user_id = $facebook -> getUser();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>gwap template</title>
		<link type="text/css" rel="stylesheet" href="css/fb-buttons.css" />
		<link type="text/css" rel="stylesheet" href="css/app-style.css" />
	</head>

	<body>
		<!--Content if user is logged in-->
		<?php
if($user_id) {
		?>
		<form action="search.php" method="post">
			<input class="uibutton" type="submit" value="Search for other Player" />
		</form>
		<form action="game.php" method="post">
			<input class="uibutton" type="submit" value="Test game without other Player" />
		</form>

		<!--Content if user is not logged in-->
		<?php
		} else {
		$login_url = $facebook->getLoginUrl();
		?>
		<script>
						this.location.href = "<?php echo $login_url?>
				";
		</script>
		<?php
		}
		?>
	</body>
</html>