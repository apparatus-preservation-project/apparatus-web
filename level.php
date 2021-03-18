<?php
require('lib/common.php');

$lid = (isset($_GET['id']) ? $_GET['id'] : 0);

$level = fetch("SELECT $userfields l.* FROM levels l JOIN users u ON l.author = u.id WHERE l.id = ?", [$lid]);

if (!$level) {
	error('404', "The requested level wasn't found.");
}

if (isset($_GET['lal'])) {
	if (isAndroidWebview()) {
		query("UPDATE levels SET downloads = downloads + '1' WHERE id = ?", [$lid]);
	}

	die('test');
}

if ($log) {
	// like
	$hasLiked = result("SELECT COUNT(*) FROM likes WHERE user = ? AND level = ?", [$userdata['id'], $lid]) == 1 ? true : false;
	if (isset($_GET['vote'])) {
		if (!$hasLiked) {
			query("UPDATE levels SET likes = likes + '1' WHERE id = ?", [$lid]);
			query("INSERT INTO likes VALUES (?,?)", [$userdata['id'], $lid]);
		}
		die();
	}

	// toggle lock
	if (isset($_GET['togglelock']) && ($level['author'] == $userdata['id'] || $userdata['powerlevel'] > 1)) {
		$lock = ($level['locked'] ? 0 : 1);
		query("UPDATE levels SET locked = ? WHERE id = ?", [$lock, $lid]);
		$level['locked'] = $lock;
	}

	// remove notifications
	query("DELETE FROM notifications WHERE type = 1 AND level = ? AND recipient = ?", [$level['id'], $userdata['id']]);
}

if (!isset($hasLiked)) $hasLiked = false;

query("UPDATE levels SET views = views + '1' WHERE id = ?", [$lid]);
$level['views']++;

$markdown = new Parsedown();
$markdown->setSafeMode(true);
$level['description'] = $markdown->text($level['description']);

$comments = query("SELECT $userfields c.* FROM comments c JOIN users u ON c.author = u.id WHERE c.type = 1 AND c.level = ? ORDER BY c.time DESC", [$lid]);

// TODO: Increment downloads.
$twig = twigloader();

echo $twig->render('level.twig', [
	'lid' => $lid,
	'level' => $level,
	'has_liked' => $hasLiked,
	'comments' => fetchArray($comments)
]);
