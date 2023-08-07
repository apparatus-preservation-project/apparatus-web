<?php
require('lib/common.php');

if ($userdata['rank'] < 2) error('403', 'You don\'t have access to this page.');

$lockedlevels = query("SELECT $userfields l.id,l.title,l.locked FROM levels l JOIN users u ON l.author = u.id WHERE l.locked = 1 ORDER BY l.id DESC");

echo twigloader()->render('viewlocked.twig', [
	'levels' => fetchArray($lockedlevels)
]);
