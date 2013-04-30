<?php
//Facebook authentication and authorization
require_once ("sdk/facebook.php");
require_once ("config.php");

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
		<meta http-equiv="refresh" content="2" />
	</head>

	<body>
		<!--Content if user is logged in-->
		<?php
if($user_id) {
		?>
		<!--Connect to MySql Database-->
		<?php
		$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
		if ($mysqli->connect_errno) {
    		die("Connect failed: ". $mysqli->connect_error);
		}
		//Connection works!
		?><img src="img/loader.gif" />
		<br />
		Waiting for Player... <br />
		<?php
		//Register as online user
		$mysqli->query("DELETE FROM online_users WHERE last_connection <'" . date("Y-m-d H:i:s", time() - 10) . "'");
		if(!$mysqli->query("INSERT INTO online_users VALUES ('" . $user_id . "', '" . date("Y-m-d H:i:s") . "', FALSE)")) {
			$mysqli->query("UPDATE online_users SET last_connection = '" . date("Y-m-d H:i:s") . "' WHERE id = '" . $user_id . "'");
		}
		
		//Search for available Players
		$result = $mysqli->query("SELECT * FROM online_users");
		while ($row = $result->fetch_array()) {
			//echo all available Players
			echo $row["id"];
			echo "<br />";
		}
		
			/*
			 * You could start a game here if there are two players
			 */
		
		?>

		<!--Content if user is not logged in-->
		<?php
		} else {
		$login_url = $facebook->getLoginUrl();
		?>
		<script>
			this.location.href = "index.php";
		</script>
		<?php
		}
		?>
	</body>
</html>