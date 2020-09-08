<html>
<head>
	<title>Games Today</title>
	<meta charset="utf-8">
	<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%2290%22 font-size=%2290%22>&#x26bd;</text></svg>">
    	<link href="style/games-today.css" rel="stylesheet" type="text/css">
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
include 'games-today-description.html';
// thanks to football-data api!
require "football-data-functions.php";

$auth_header_array = array('X-AUTH-TOKEN: 1a65e8acccdb47949431186d2d4ea406');
if (!is_null($_GET["DATE"])) {
	$d_obj = DateTimeImmutable::createFromFormat('Y-m-d', $_GET["DATE"]);
}
else {
	$d_obj = new DateTimeImmutable();
}

$qs_date =  $d_obj->format('Y-m-d');

$day_before = $d_obj->sub(new DateInterval('P1D'))->format('Y-m-d');
$day_after = $d_obj->add(new DateInterval('P1D'))->format('Y-m-d');

$ruri = strtok($_SERVER['REQUEST_URI'], '?');
$day_before_full = $ruri . '?' . http_build_query(array('DATE' => $day_before));
$day_after_full = $ruri . '?' . http_build_query(array('DATE' => $day_after));

$matches_suffix_plus_qs = '/matches?competitions=2021,2016,2001,2015,2002,2019,2014&dateFrom=' . $qs_date . '&dateTo=' . $qs_date;
$fn = './json/football-data/matches/' . $qs_date . '.json';

$rj = hitMatchesFileOrAPI($fn, $matches_suffix_plus_qs, $auth_header_array);
$rj_E = hitMatchesFileOrSecondAPI($qs_date);

echo <<<TBTH
<h2>Soccer Games for $qs_date</h2>
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
foreach ($rj_E->api->fixtures as $f) {
	$fl = $f->league;
	$a = $f->awayTeam;
	$h = $f->homeTeam;
	echo <<<SOME_E
<tr>
	<td class="reg-cell">
	$fl->name<br>
	<img class="flag" src="$fl->logo" alt="No Country Flag"><br>
	$fl->name
	</td>
	<td class="reg-cell">
	<img class="badge" src="$h->logo" alt="No Badge"><br>
	$h->team_name
	</td>
	<td class="reg-cell">
	<img class="badge" src="$a->logo" alt="No Badge"><br>
	$a->team_name
	</td>
	<td class="score-cell">
	$f->goalsHomeTeam - $f->goalsAwayTeam
	</td>
</tr>
SOME_E;
}
echo "</table>";
?>
</table>
</body>
</html>

