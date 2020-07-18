<html>
<head>
	<title>&#x26bd; Footie Today</title>
	<style>
		body {
			text-align: center;
		}	
		td, th {
			text-align: center;
			border-bottom-style: solid;
			
		}
		table {
			border-collapse: collapse;
			border-style: solid;
			margin: auto;
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

$matches_suffix = '/matches?competitions=2021,2016,2001,2015,2002,2019,2014&';
$df = 'dateFrom=';
$dt = '&dateTo=';
$qs_date = $_GET["DATE"] ?? date("Y-m-d");
$day_before = date('Y-m-d', strtotime('-1 day', strtotime($qs_date)));
$day_after = date('Y-m-d', strtotime('+1 day', strtotime($qs_date)));
$qm = "?";
$ruri = strtok($_SERVER['REQUEST_URI'], '?');
$day_before_full = $ruri . $qm . http_build_query(array('DATE' => $day_before));
$day_after_full = $ruri . $qm . http_build_query(array('DATE' => $day_after));
$matches_suffix_plus_qs = $matches_suffix . $df . $qs_date . $dt . $qs_date;
$fn = './json/football-data/matches/' . $qs_date . '.json';
if (file_exists($fn)) {
	$rj = json_decode(file_get_contents($fn));
} else {
	$rj = hitAPI($matches_suffix_plus_qs);
	file_put_contents($fn, json_encode($rj));
}
echo <<<TBTH
<h2>Soccer Games for $qs_date</h3>
<div>
<a href="$day_before_full">Prev. Day</a>
<a href="$day_after_full">Next Day</a>
</div>
<table>
<tr>
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
	<td>
	<img src="$area->ensignUrl" alt="No Country Flag" style="width: 100px;"><br>
	$compName<br>
	$area->name
	</td>
	<td>
	<img src="$homeJSON->crestUrl" style="height: 100px; width: 100px;" alt="No Badge"><br>
	$homeName
	</td>
	<td>
	<img src="$awayJSON->crestUrl" style="height: 100px; width: 100px;" alt="No Badge"><br>
	$awayName
	</td>
	<td>
	$homeS - $awayS
	</td>
</tr>
SOME;
}
echo "</table>";
?>
</body>
</html>

