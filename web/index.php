<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<?php
	require_once 'includes/config.inc.php';
	require_once 'includes/utils.inc.php';
	echo "<title>$page_title</title>";
	echo "<base href=\"$page_href\" />";
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
?>

<link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico" />
<link href="css/style.css" rel="stylesheet" type="text/css" media="all">
</head>
<body>
<div class="container">
	<div class="everything">
		<div class="statsholer">

			<?php
			if (!isset($_GET['id']) && empty($_GET['id']) || $_GET['id']==''){
				echo "<a href=\"$page_website\"><img src=\"$page_image\"/></a>";
				echo "<div class=\"searchnoinfo\">";
			} else{
				echo "<div class=\"search\">";
			}
			?>

				<form action="search.php" method="GET">
				<h6>Search player stats:</h6><input type="text" name="searchquery">
				<input type="submit" value="Submit">
				</form>
			</div>

			<?php
			if (!isset($_GET['id']) && empty($_GET['id']) || $_GET['id']==''){
				echo "<div class=\"topPlayers\"><h1>Top 15 Players</h1>";
				include 'includes/generatetopplayers.php';
				echo "</div>";
			}

			if (isset($_GET['id']) && !empty($_GET['id'])){

				$server_limit = 'AND serverID=0';
				$server_id = '0';
				if (isset($_GET['serverid'])) {
					$server_id = (int)$_GET['serverid'];
				$server_limit = 'AND serverID='.(int)$_GET['serverid'];
				}
			// TODO: Fix serverid. Combine user's stats if the serverid is not set.
			
				$totalplayers = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $mysql_table WHERE serverID=$server_id"));
				$run_query = "SELECT s1.*, (SELECT COUNT(*) FROM $mysql_table AS s2 WHERE s2.rating > s1.rating $server_limit)+1 AS rank FROM $mysql_table AS s1 WHERE accountID=".(int)$_GET['id']." $server_limit";
				$query = mysqli_query($connect, $run_query);

				if (mysqli_num_rows($query) > 0){
					if ($query){
						while ($row = mysqli_fetch_assoc($query)){
							$accountID = $row['accountID'];
							$auth = $row['auth'];
							$rank = $row['rank'];
							$name = htmlentities($row['name']);
							$wins = $row['wins'];
							$losses = $row['losses'];
							$rating = $row['rating'];
							$lastTime = $row['lastTime'];

						if ($losses == 0) {
								$WL = $wins;
							} else{
								$WL = round($wins/$losses, 2);
							}

							<div class=\"playerAvatar ".getPlayerState(GetCommunityID($auth))."\"><a href=\"http://steamcommunity.com/profiles/".GetCommunityID($auth)."\"><img src=\"".getAvatar(GetCommunityID($auth))."\" height=\"184\" width=\"184\"/></a>";
							if (isPlayerActive($lastTime) == true){
								echo "<div class=\"activity inactive\">INACTIVE</div></div>";
							} else{
								echo "<div class=\"activity\">ACTIVE</div></div>";
							}
							echo "<h3><span id="statscolor">Stats for</span> <a href=\"http://steamcommunity.com/profiles/".GetCommunityID($auth)."\">$name</a></h3>
							<div class=\"stats\">
							<h4>Rank: <span id="statscolor">$rank</span> of <span id="statscolor">$totalplayers</span></h4><br>
							<h4>Wins: <span id="statscolor">$wins</span></h4><br>
							<h4>Losses: <span id="statscolor">$losses</span></h4><br>
							<h4>W/L Ratio: <span id="statscolor">$WL</span></h4><br>
							<h4>ELO Rating: <span id="statscolor">$rating</span></h4><br>
							
						}
						}
				} else{
					die("<h1>User not found.</h1>");
				}
			}
			?>

			</div>

			<?php
			if (isset($_GET['id']) && !empty($_GET['id'])){
				$time = microtime();
				$time = explode(' ', $time);
				$time = $time[1] + $time[0];
				$finish = $time;
				$total_time = round(($finish - $start), 4);
				echo '<center>Page generated in '.$total_time.' seconds.</center>';
			}
			?>
		</div>
	</div>
</div>
</body>
</html>
