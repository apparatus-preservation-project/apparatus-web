<?php
if (!file_exists('conf/config.php'))
	die('Please read the installing instructions in the README file.');

// load profiler first
require_once('lib/profiler.php');
$profiler = new Profiler();

require('conf/config.php');
require('../principia-web/vendor/autoload.php');
foreach (glob("lib/*.php") as $file)
	require_once($file);

$userfields = userfields();

if (!isCli()) {
	// Shorter variables for common $_SERVER values.
	$ipaddr = $_SERVER['REMOTE_ADDR'];
	$useragent = $_SERVER['HTTP_USER_AGENT'] ?? null;

	// Do redirects if this is a non-internal page
	if (!str_contains($_SERVER['SCRIPT_NAME'], 'internal')) {
		// Redirect all non-internal pages to https if https is enabled.
		if ($https && !isset($_SERVER['HTTPS']))
			redirect("https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
	}
} else {
	// Dummy values for CLI usage
	$ipaddr = '127.0.0.1';
	$useragent = 'apparatus-web/cli (sexy, like PHP)';
}

if (isset($_GET[$cookieName]))
	$_COOKIE[$cookieName] = $_GET[$cookieName];

// Authentication code.
$log = false;

if (isset($_COOKIE[$cookieName])) {
	$id = result("SELECT id FROM users WHERE token = ?", [$_COOKIE[$cookieName]]);

	if ($id) // Valid cookie, user is logged in.
		$log = true;
}

if ($log) {
	$userdata = fetch("SELECT * FROM users WHERE id = ?", [$id]);
	$notificationCount = result("SELECT COUNT(*) FROM notifications WHERE recipient = ?", [$userdata['id']]);

	query("UPDATE users SET lastview = ?, ip = ? WHERE id = ?", [time(), $ipaddr, $userdata['id']]);
} else {
	$userdata = [
		'rank' => 0,
		'darkmode' => true
	];
}

if (!$log || !$userdata['timezone'])
	$userdata['timezone'] = 'Europe/Stockholm'; // I'm a self-centered egomaniac! Time itself centers around me!

date_default_timezone_set($userdata['timezone']);
