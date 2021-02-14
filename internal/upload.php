<?php
chdir('../');
require('lib/common.php');

if (!$log) die('how');

$nextId = result("SELECT id FROM levels ORDER BY id DESC LIMIT 1") + 1;

$levelData = file_get_contents('php://input');

$zipFile = new \PhpZip\ZipFile();
$zipFile->openFromString($levelData);
$manifest = $zipFile->getEntryContents('META-INF/MANIFEST.MF');

$level = [];
preg_match('/Level-Name: (.+)/', $manifest, $level['name']);	$level['name'] = $level['name'][1];
preg_match('/Level-Type: ([\w]+)/', $manifest, $level['type']);		$level['type'] = $level['type'][1];
preg_match('/Level-Tags: ([\w ]+)/', $manifest, $level['tags']);	$level['tags'] = (isset($level['tags'][1]) ? $level['tags'][1] : '');
$level['description'] = $zipFile->getEntryContents('descr');

//printf('levelname: "%s", leveltype "%s", tags "%s", description "%s"', $level['name'], $level['type'], $level['tags'], $level['description']);

if (!isset($level['name']) || !isset($level['type']) || !isset($level['description'])) die('Fill in all the fields!');
if (!type_to_cat($level['type'])) die('Invalid level type.');
//if ($level['type'] == 'interactive') die("Uploading interactive levels isn't supported.");

$zipFile->saveAsFile(sprintf('levels/%s.jar', $nextId));

query("INSERT INTO levels (cat, title, description, tags, author, time) VALUES (?, ?, ?, ?, ?, ?)",
	[type_to_cat($level['type']), $level['name'], $level['description'], $level['tags'], $userdata['id'], time()]);

printf('OK:%s', $nextId);