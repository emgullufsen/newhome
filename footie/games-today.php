<html>
<head>
	<title>&#x26bd; Footie Today</title>
	<style>
		@font-face {
			font-family: eg-scoreboard;
			src: url('fonts/scoreboard.ttf');
		}
		a:link, a:visited {
			text-decoration: none;
			color: orange;
		}
		body {
			text-align: center;
			background-color: black;
			color: orange;
			text-shadow: 0px 0px 3px orange;
			font-family: eg-scoreboard;
		}	
		.badge {
			height: 50px;
			padding: 7px;
			margin: 5px;
			border-radius: 10px;
			background-color: white;
		}
		.flag {
			height: 25px;
		}
		.prev-next-links {
			margin: 15px;
		}
		.prev-link, .next-link {
			border: solid 3px white;
			
			padding: 5px;
		}
		.prev-link:hover, .next-link:hover {
			background-color: orange;
			color: black;
		}
		/*.reg-cell {
			font-family: serif;
			background-color: white;
			color: black;
			text-shadow: none;
		}*/
		td, th {
			text-align: center;
			border-bottom-style: solid;
			border-color: white;
			
		}
		table {
			border-collapse: collapse;
			border-style: solid;
			margin: auto;
			border-color: white;
		}
		
	</style>
</head>
<body>
	<a href="../newhome.html">
        <h1>
            ricky, squid, & wid
        </h1>
    </a>
    <a href="../saltcakedsmokestacks.html">
        <h1>
            &#x1f6a2;SALTCAKED SMOKESTACKS
        </h1>    
    </a>
<?php
// thanks to football-data api!
function hitAPI($suffix_url) {
	$base_url = 'https://api.football-data.org/v2/';
	$endpoint = $base_url . $suffix_url;
	$auth_header_array = array('X-AUTH-TOKEN: 1a65e8acccdb47949431186d2d4ea406');

	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, $endpoint); 
	curl_setopt($c, CURLOPT_HTTPHEADER, $auth_header_array); 
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	$resp = curl_exec($c);
	$resp_json = json_decode($resp);
	return $resp_json;
}

function getTeamJSON($identifier) {
	$prefix = './json/football-data/teams/';
	$suffix = '.json';
	$teams_suffix = 'teams/';
	$path = $prefix . $identifier . $suffix;
	if (file_exists($path)){
		$r = json_decode(file_get_contents($path));
		if (empty($r->id)){
			$r = hitAPI($teams_suffix . $identifier);
		}
	} else {
		$r = hitAPI($teams_suffix . $identifier);
	}
	if (is_null($r->crestUrl) or empty($r->crestUrl)){ $r->crestUrl = "images/blank.png"; }-
	file_put_contents($path, json_encode($r));
	return $r;
}

$qs_date = $_GET["DATE"] ?? date("Y-m-d");

$day_before = date('Y-m-d', strtotime('-1 day', strtotime($qs_date)));
$day_after = date('Y-m-d', strtotime('+1 day', strtotime($qs_date)));

$ruri = strtok($_SERVER['REQUEST_URI'], '?');
$day_before_full = $ruri . '?' . http_build_query(array('DATE' => $day_before));
$day_after_full = $ruri . '?' . http_build_query(array('DATE' => $day_after));

$matches_suffix_plus_qs = '/matches?competitions=2021,2016,2001,2015,2002,2019,2014&dateFrom=' . $qs_date . '&dateTo=' . $qs_date;
$fn = './json/football-data/matches/' . $qs_date . '.json';

if (file_exists($fn)) {
	$rj = json_decode(file_get_contents($fn));
	$fileOld = (filemtime($fn) < strtotime('-10 minutes'));
	$needsUpdate = false;
	foreach ($rj->matches as $m){
		$needsUpdate = ((time() > strtotime($m->utcDate)) and ($m->status !== "FINISHED"));
	}
	
	if ($fileOld and $needsUpdate){
		$rj = hitAPI($matches_suffix_plus_qs);
		file_put_contents($fn, json_encode($rj));
	}
} else {
	$rj = hitAPI($matches_suffix_plus_qs);
	file_put_contents($fn, json_encode($rj));
}
echo <<<TBTH
<h2>Soccer Games for $qs_date</h3>
<div class="prev-next-links">
<a href="$day_before_full" class="prev-link">&#x2b05; Prev. Day</a>
<a href="$day_after_full" class="next-link">Next Day &#x27a1;</a>
</div>
<table>
<tr class="score-cell">
<th>League</th><th>Home Team</th><th>Away Team</th><th>Score</th>
</tr>
TBTH;
foreach ($rj->matches as $m) {
	$homeS = strval($m->score->fullTime->homeTeam);
	$awayS = strval($m->score->fullTime->awayTeam);
	$homeID = strval($m->homeTeam->id);
	$awayID = strval($m->awayTeam->id);
	$homeName = $m->homeTeam->name;
	$awayName = $m->awayTeam->name;
	
	$homeJSON = getTeamJSON($homeID);
	$awayJSON = getTeamJSON($awayID);
	$area = $m->competition->area;
	$compName = $m->competition->name;
	echo <<<SOME
<tr>
	<td class="reg-cell">
	$compName<br>
	<img class="flag" src="$area->ensignUrl" alt="No Country Flag"><br>
	$area->name
	</td>
	<td class="reg-cell">
	<img class="badge" src="$homeJSON->crestUrl" alt="No Badge"><br>
	$homeName
	</td>
	<td class="reg-cell">
	<img class="badge" src="$awayJSON->crestUrl" alt="No Badge"><br>
	$awayName
	</td>
	<td class="score-cell">
	$homeS - $awayS
	</td>
</tr>
SOME;
}
echo "</table>";
?>
</body>
</html>

