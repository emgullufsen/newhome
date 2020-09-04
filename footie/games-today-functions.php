<?php
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
?>
