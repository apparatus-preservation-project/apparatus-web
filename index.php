<?php
require('lib/common.php');

$latestquery = "SELECT $userfields l.id,l.title,l.locked FROM levels l JOIN users u ON l.author = u.id WHERE l.cat = %d AND l.locked = 0 ORDER BY l.id DESC LIMIT 5";
$latestapparatus = query(sprintf($latestquery, 1));
$latestchallenge = query(sprintf($latestquery, 2));

echo twigloader()->render('index.twig', [
	'apparatus_levels' => fetchArray($latestapparatus),
	'challenge_levels' => fetchArray($latestchallenge)
]);
