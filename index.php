<?php
require('lib/common.php');

$latestquery = "SELECT $userfields l.id id,l.title title,l.locked locked FROM levels l JOIN users u ON l.author = u.id WHERE l.cat = %d AND l.locked = 0 ORDER BY l.id DESC LIMIT 5";
$latestapparatus = query(sprintf($latestquery, 1));
$latestchallenge = query(sprintf($latestquery, 2));

$twig = twigloader();

echo $twig->render('index.twig', [
	'apparatus_levels' => fetchArray($latestapparatus),
	'challenge_levels' => fetchArray($latestchallenge)
]);
