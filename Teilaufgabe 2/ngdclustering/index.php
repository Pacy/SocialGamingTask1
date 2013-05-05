<?php
// Database values
define("NUMBER_OF_PAGES_INDEXED", 8058044651); // FROM "THE GOOGLE SIMILARITY DISTANCE.pdf"
require_once ("config.php");

$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($mysqli -> connect_errno) { // Connect to Database
	die("Connect failed: " . $mysqli -> connect_error);
}

$picIds = array();
$get_pics = $mysqli->query("SELECT * FROM pics");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>The Google Similarity Distance</title>
		<link rel="stylesheet" type="text/css" href="css/app-style.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		
		<script type="text/javascript">
		
			$(window).scroll(function(){
	    		$('#header').css({
      				"margin-left" : $(this).scrollLeft()
    			});
			});
		
		function showHideRows(idToHideOrShow){
			$('.distance_table tr').hide();
			$('#'+idToHideOrShow).show();
		}
		</script>
	</head>

	<body>
		<table>
			<tr>
		<?php 
			while($row = $get_pics -> fetch_assoc()){ // SHOW IMAGES
			$picIds[] = $row["picid"];
		?>
	
				<td class="pic_table">
					<div class='wrapper'>
						<img src="http://lodsb.org/socialcomp/artemis/<?php echo $row['path']; ?>" >
						
						<div class='description'>  
	        				<a href="#" onclick="showHideRows(<?php echo $row["picid"]; ?>)"><p class='description_content'>Show Google Distances for this image</p></a>
	    				</div>  
					</div>  
				</td>
		<?php 
			}
		?>
			</tr>
		</table>
		<div id="header">
			<table class="distance_table">
			<?php
				foreach ($picIds as $pId){
					$get_distances = $mysqli->query("SELECT * FROM distances WHERE picid = '".$pId."'");
			?>
				<tr id=<?php echo $pId; ?>>
					<td>
					<?php
						while($dist_row = $get_distances -> fetch_assoc()){ // GET DISTANCES FOR EACH IMAGE
							if($dist_row["bothcount"] == 0){
								$ngd = number_format(1.000, 3);
							}else{
								$zaehler = max( log($dist_row["sourcecount"]) , log($dist_row["targetcount"]) ) - log($dist_row["bothcount"]);
								$nenner = log(NUMBER_OF_PAGES_INDEXED) - min(log($dist_row["sourcecount"]), log($dist_row["targetcount"]));
								$ngd = number_format($zaehler / $nenner, 3);
								$ngd = ($ngd > 1) ? number_format(1.000, 3) : $ngd;
								$ngd = ($ngd < 0) ? number_format(1.000, 3) : $ngd;
							}
							
							$sqlInsert = "INSERT IGNORE INTO nodes (id, label, picid) VALUES ('".$dist_row["source"]."', '".$dist_row["sourcename"]."', '".$dist_row["picid"]."')";
							$return = $mysqli->query($sqlInsert);
							if(!$return){
								printf("%s\n", $mysqli->error);
   								exit();
							}
							
							$sqlInsert = "INSERT IGNORE INTO nodes (id, label, picid) VALUES ('".$dist_row["target"]."', '".$dist_row["targetname"]."', '".$dist_row["picid"]."')";
							$return = $mysqli->query($sqlInsert);
							if(!$return){
								printf("%s\n", $mysqli->error);
   								exit();
							}
							
							$sqlInsert = "INSERT IGNORE INTO edges (source, target, weight, picid) VALUES ('".$dist_row["source"]."', '".$dist_row["target"]."', FORMAT('".$ngd."',3), '".$dist_row["picid"]."')";
							$return = $mysqli->query($sqlInsert);
							if(!$return){
								printf("%s\n", $mysqli->error);
   								exit();
							}
							?>
							<span style="width: 200px"><?php echo $dist_row["sourcename"]?></span>
							<span style="width: 25px">-></span>
							<span style="width: 200px"><?php echo $dist_row["targetname"]; ?></span>
							<span style="width: 25">=</span>
							<span style="width: 200px"><?php echo $ngd; ?></span><br>
					<?php
						}
					?>
					</td>
				</tr>
			<?php
				}
			?>
			</table>
		</div>
	</body>
</html>