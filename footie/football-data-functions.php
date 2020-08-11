<?php

$HEADERS_FIRST_API = array('X-AUTH-TOKEN: 1a65e8acccdb47949431186d2d4ea406');
$HEADERS_SECOND_API = array('x-rapidapi-host: api-football-v1.p.rapidapi.com',
            'x-rapidapi-key: db96f2fc0emshaf33007386630a2p16e5fbjsn09ecad2e38ae',
            'useQueryString: true');
$EUROPA_LEAGUE_ENDPOINT = 'https://api-football-v1.p.rapidapi.com/v2/fixtures/league/514/';
$FOOTBALL_DATA_BASE_URL = 'https://api.football-data.org/v2/';

function hitAPI($base_url, $suffix_url = NULL, $headers) {
	$endpoint = $base_url . $suffix_url;
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, $endpoint); 
	curl_setopt($c, CURLOPT_HTTPHEADER, $headers); 
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	$resp = curl_exec($c);
	$resp_json = json_decode($resp);
	return $resp_json;
}

function hitMatchesFileOrAPI($file_descriptor, $matches_suffix_URL) {
    if (file_exists($file_descriptor)) {
        $rj = json_decode(file_get_contents($file_descriptor));
        $fileOld = (filemtime($file_descriptor) < strtotime('-10 minutes'));
        $needsUpdate = false;
        foreach ($rj->matches as $m){
            $needsUpdate = ((time() > strtotime($m->utcDate)) and ($m->status !== "FINISHED"));
        }
        
        if ($fileOld and $needsUpdate){
            $rj = hitAPI($GLOBALS['FOOTBALL_DATA_BASE_URL'], $matches_suffix_URL, $GLOBALS['HEADERS_FIRST_API']);
            file_put_contents($file_descriptor, json_encode($rj));
        }
    } else {
        $rj = hitAPI($GLOBALS['FOOTBALL_DATA_BASE_URL'], $matches_suffix_URL, $GLOBALS['HEADERS_FIRST_API']);
        file_put_contents($file_descriptor, json_encode($rj));
    }
    return $rj;
}

function hitMatchesFileOrSecondAPI($date) {
    $fd = './json/api-football/matches/' . $date . '.json';
    if (file_exists($fd)) {
        $rj = json_decode(file_get_contents($fd));
        $fileOld = (filemtime($fd) < strtotime('-10 minutes'));
        $needsUpdate = false;
        foreach ($rj->api->fixtures as $m){
            $needsUpdate = ((time() > strtotime($m->event_timestamp)) and ($m->status !== "Match Finished"));
        }
        
        if ($fileOld and $needsUpdate){
            $rj = hitAPI($GLOBALS['EUROPA_LEAGUE_ENDPINT'], $date, $GLOBALS['HEADERS_SECOND_API']);
            file_put_contents($fd, json_encode($rj));
        }
    } else {
        $rj = hitAPI($GLOBALS['EUROPA_LEAGUE_ENDPOINT'], $date, $GLOBALS['HEADERS_SECOND_API']);
        file_put_contents($fd, json_encode($rj));
    }
    return $rj;
}

function getTeamJSON($identifier) {
	$prefix = './json/football-data/teams/';
	$suffix = '.json';
	$teams_suffix = 'teams/';
	$path = $prefix . $identifier . $suffix;
	if (file_exists($path)){
		$r = json_decode(file_get_contents($path));
		if (empty($r->id)){
			$r = hitAPI('https://api.football-data.org/v2/', $teams_suffix . $identifier, array('X-AUTH-TOKEN: 1a65e8acccdb47949431186d2d4ea406'));
		}
	} else {
		$r = hitAPI('https://api.football-data.org/v2/', $teams_suffix . $identifier, array('X-AUTH-TOKEN: 1a65e8acccdb47949431186d2d4ea406'));
	}
	if (is_null($r->crestUrl) or empty($r->crestUrl)){ $r->crestUrl = "images/blank.png"; }-
	file_put_contents($path, json_encode($r));
	return $r;
}
?>