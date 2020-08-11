<?php
require "../footie/football-data-functions.php";
if (!is_null($_GET["DATE"])) {
	$d_obj = DateTimeImmutable::createFromFormat('Y-m-d', $_GET["DATE"]);
}
else {
	$d_obj = new DateTimeImmutable();
}

$qs_date =  $d_obj->format('Y-m-d');

$rj_E = hitSecondAPIMatches($qs_date);

echo json_encode($rj_E);
?>
