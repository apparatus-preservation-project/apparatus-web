<?php
require('lib/common.php');

$newsid = $_GET['id'] ?? 0;

if ($newsid) {
	$newsdata = fetch("SELECT * FROM news WHERE id = ?", [$newsid]);

	if (!$newsdata)
		error('404', "The requested news article wasn't found.");

	$time = date('jS F Y', $newsdata['time']).' at '.date('H:i:s', $newsdata['time']);

	$comments = query("SELECT $userfields c.* FROM comments c JOIN users u ON c.author = u.id WHERE c.type = 2 AND c.level = ? ORDER BY c.time DESC", [$newsid]);

	echo twigloader()->render('news.twig', [
		'newsid' => $newsid,
		'news' => $newsdata,
		'time' => $time,
		'comments' => $comments
	]);
} else {
	$newsdata = query("SELECT id,title FROM news ORDER BY id DESC");

	echo twigloader()->render('news.twig', [
		'newsid' => $newsid,
		'news' => fetchArray($newsdata)
	]);
}
