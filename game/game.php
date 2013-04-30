<?php
//Facebook authentication and authorization
require_once ("../facebook_sdk/src/facebook.php");
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
		<meta charset="ISO-8859-1">

		<script>
			function allowDrop(ev) {
				ev.preventDefault();
			}

			function drag(ev) {
				ev.dataTransfer.setData("Text", ev.target.id);
			}

			function drop(ev) {
				ev.preventDefault();
				var data = ev.dataTransfer.getData("Text");
				ev.target.appendChild(document.getElementById(data));
			}
		</script>
		</head>

	<body>
		<!--Content if user is logged in-->
		<?php
if($user_id) {
		?>
		<table style="text-align: left; width: 100%;">
			<?php
			$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
			if ($mysqli -> connect_errno) {
			die("Connect failed: " . $mysqli -> connect_error);
			}
			// a random picture gets chosen
			$picresult = $mysqli->query("SELECT picid FROM tag ORDER BY RAND() LIMIT 1");
			$pic;
			while ($row = $picresult->fetch_array()) {
			$pic = $row["picid"];
			}
			?>
			<!--here comes the part that the player will see-->
			<!--for now there is only the picture and two boxes where drag and drop of tags is enabled-->
			<tbody>
				<tr>
					<td><img style="width: 100%; height: auto;" alt="Image not available" src="http://lodsb.org/socialcomp/artemis/<?php echo $pic?>.jpg"></td>
					<td>
					<form>
						<select id="taglist1" name="taglist1" size=7 ondrop="drop(event)" ondragover="allowDrop(event)">
							<!--Print tags as options-->
							<?php
							$tagresult = $mysqli -> query("SELECT name FROM tag WHERE picid = " . $pic);
							while ($row = $tagresult -> fetch_array()) {
								echo "<option id='" . $row["name"] . "' draggable='true' ondragstart='drag(event)'>" . $row["name"] . "</option>";
							}
							?>
						</select>
						<select id="taglist2" name="taglist2" size=7 ondrop="drop(event)" ondragover="allowDrop(event)"></select>
					</td>
					</form>
				</tr>
				<tr>
					<td></td>
					<td>
					<form action="game.php" method="post">
						<input class="uibutton" type="submit" value="Submit" />
					</form></td>
				</tr>
			</tbody>
		</table>

		<!--Content if user is not logged in-->
		<?php
		} else {
		?>
		<script>
			this.location.href = "index.php";
		</script>
		<?php
		}
		?>
	</body>
</html>