<?php
require('lib/common.php');

if (isset($_GET['id']))
	$userpagedata = fetch("SELECT * FROM users WHERE id = ?", [$_GET['id']]);
else if (isset($_GET['name']))
	$userpagedata = fetch("SELECT * FROM users WHERE name = ?", [$_GET['name']]);

if (!isset($userpagedata) || !$userpagedata) error('404', "No user specified.");

$page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1);
$forceuser = isset($_GET['forceuser']);

$limit = sprintf("LIMIT %s,%s", (($page - 1) * $lpp), $lpp);
$levels = query("SELECT $userfields l.id,l.title FROM levels l JOIN users u ON l.author = u.id WHERE l.author = ? AND l.locked = 0 ORDER BY l.id DESC $limit",
	[$userpagedata['id']]);
$count = result("SELECT COUNT(*) FROM levels l WHERE l.author = ? AND l.locked = 0", [$userpagedata['id']]);

// Personal user page stuff
if (isset($userdata['id']) && $userdata['id'] == $userpagedata['id'] && !$forceuser) {
	if (isset($_GET['markread'])) {
		query("DELETE FROM notifications WHERE recipient = ?", [$userdata['id']]);
		$notificationCount = 0;
	}

	$notifsdata = query("SELECT $userfields n.*, l.id l_id, l.title l_title FROM notifications n LEFT JOIN levels l ON n.level = l.id JOIN users u ON n.sender = u.id WHERE n.recipient = ?", [$userdata['id']]);

	$notifications = [];
	while ($notifdata = $notifsdata->fetch()) {
		if ($notifdata['type'] == 1) {
			$notifications[] = sprintf(
				'%s commented on your level <a href="/level.php?id=%s">%s</a>.',
			userlink($notifdata, 'u_'), $notifdata['l_id'], $notifdata['l_title']);
		} elseif ($notifdata['type'] == 2) {
			$notifications[] = sprintf(
				'%s commented on your <a href="/user.php?id=%s&forceuser">user page</a>.',
			userlink($notifdata, 'u_'), $userdata['id']);
		}
	}
} else { // general profile details stuff
	$comments = query("SELECT $userfields c.* FROM comments c JOIN users u ON c.author = u.id WHERE c.type = 4 AND c.level = ? ORDER BY c.time DESC", [$userpagedata['id']]);

	if (isset($userdata['id']) && $userpagedata['id'] == $userdata['id'])
		query("DELETE FROM notifications WHERE type = 2 AND recipient = ?", [$userdata['id']]);
}

echo twigloader()->render('user.twig', [
	'id' => $userpagedata['id'],
	'name' => $userpagedata['name'],
	'userpagedata' => $userpagedata,
	'levels' => fetchArray($levels),
	'forceuser' => $forceuser,
	'page' => $page,
	'level_count' => $count,
	'notifs' => $notifications ?? [],
	'markread' => (isset($_GET['markread']) ? true : false),
	'edited' => (isset($_GET['edited']) ? true : false),
	'comments' => $comments ?? null
]);
