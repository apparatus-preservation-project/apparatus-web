<?php
if (!file_exists('conf/config.php')) {
	die('Please read the installing instructions in the README file.');
}

$start = microtime(true);

require('conf/config.php');

// Redirect all non-internal pages to https if https is enabled.
if ($https && $_SERVER["HTTPS"] != "on" && strpos($_SERVER['SCRIPT_NAME'], 'internal') === false) {
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
	die();
}

require('../principia-web/vendor/autoload.php');

require('lib/discord.php');
require('lib/layout.php');
require('lib/level.php');
require('lib/mysql.php');
require('lib/twig.php');
require('lib/user.php');

$userfields = userfields();

$ipban = fetch("SELECT * FROM ipbans WHERE ? LIKE ip", [$_SERVER['REMOTE_ADDR']]);
if ($ipban) {
	http_response_code(403);

	printf(
		"<p>Your IP adress has been banned.</p>".
		"<p><strong>Reason:</strong> %s</p>".
		"<p>If you believe this is in error, send an email to %s to appeal.</p>",
	$ipban['reason'], $_SERVER['SERVER_ADMIN']);

	die();
}

if (isset($_GET['t'])) {
	$_COOKIE['t'] = $_GET['t'];
}

// Authentication code.
if (isset($_COOKIE['t'])) {
	$id = result("SELECT id FROM users WHERE token = ?", [$_COOKIE['t']]);

	if ($id) {
		// Valid password cookie.
		$log = true;
	} else {
		// Invalid password cookie.
		$log = false;
	}
} else {
	// No password cookie.
	$log = false;
}

if ($log) {
	$userdata = fetch("SELECT * FROM users WHERE id = ?", [$id]);
	$notificationCount = result("SELECT COUNT(*) FROM notifications WHERE recipient = ?", [$userdata['id']]);

	query("UPDATE users SET lastview = ?, ip = ? WHERE id = ?", [time(), $_SERVER['REMOTE_ADDR'], $userdata['id']]);
} else {
	$userdata['powerlevel'] = 1;
}

if (!$log || !$userdata['timezone'])
	$userdata['timezone'] = 'Europe/Stockholm'; // I'm a self-centered egomaniac! Time itself centers around me!

date_default_timezone_set($userdata['timezone']);
