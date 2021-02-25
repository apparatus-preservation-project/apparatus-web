<?php
chdir('../');
require('lib/common.php');

if (!$log) die('how');

$nextId = result("SELECT id FROM levels ORDER BY id DESC LIMIT 1") + 1;

$levelContent = file_get_contents('php://input');

$zipFile = new \PhpZip\ZipFile();
$zipFile->openFromString($levelContent);
$manifest = $zipFile->getEntryContents('META-INF/MANIFEST.MF');

$level = [];
preg_match('/Level-Name: (.+)/', $manifest, $level['name']);		$level['name'] = $level['name'][1];
preg_match('/Level-Type: ([\w]+)/', $manifest, $level['type']);		$level['type'] = $level['type'][1];
preg_match('/Level-Tags: ([\w ]+)/', $manifest, $level['tags']);	$level['tags'] = (isset($level['tags'][1]) ? $level['tags'][1] : '');
preg_match('/Level-ID: ([0-9]+)/', $manifest, $level['id']);		$level['id'] = (isset($level['id'][1]) ? $level['id'][1] : 0);
$level['description'] = $zipFile->getEntryContents('descr');

//printf('levelname: "%s", leveltype "%s", tags "%s", description "%s"', $level['name'], $level['type'], $level['tags'], $level['description']);

if (!isset($level['name']) || !isset($level['type']) || !isset($level['description'])) die('Fill in all the fields!');
if (!type_to_cat($level['type'])) die('Invalid level type.');
//if ($level['type'] == 'interactive') die("Uploading interactive levels isn't supported.");

if ($level['id']) {
	$cid = $level['id'];

	// get some level info we need
	$leveldata = fetch("SELECT cat, author, revision, locked FROM levels WHERE id = ?", [$cid]);

	// die if level does not exist or this isn't the user's level.
	if (!$leveldata || ($userdata['id'] != $leveldata['author'] && $userdata['powerlevel'] < 3)) {
		die('What are you trying to do? ;)');
	}

	// back up previous revision level ...
	rename("levels/$cid.jar", sprintf('levels/backup/%s.jar.bak.%s', $cid, $leveldata['revision']));
	// ... and thumb
	if (file_exists("levels/thumbs/$cid.jpg")) {
		rename("levels/thumbs/$cid.jpg", sprintf('levels/thumbs/backup/%s.jpg.bak.%s', $cid, $leveldata['revision']));
	}

	$zipFile->saveAsFile(sprintf('levels/%s.jar', $cid));

	query("UPDATE levels SET cat = ?, title = ?, description = ?, tags = ?, revision = revision + 1, revision_time = ? WHERE id = ?",
		[type_to_cat($level['type']), $level['name'], $level['description'], $level['tags'], time(), $cid]);

	printf('OK:%s', $cid);
} else {
	$zipFile->saveAsFile(sprintf('levels/%s.jar', $nextId));

	query("INSERT INTO levels (cat, title, description, tags, author, time) VALUES (?, ?, ?, ?, ?, ?)",
		[type_to_cat($level['type']), $level['name'], $level['description'], $level['tags'], $userdata['id'], time()]);

	printf('OK:%s', $nextId);
}
