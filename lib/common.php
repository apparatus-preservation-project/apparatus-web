<?php
if (!file_exists('conf/config.php')) {
	die('Please read the installing instructions in the README file.');
}

$start = microtime(true);

require('conf/config.php');
require('../principia-web/vendor/autoload.php');
foreach (glob("lib/*.php") as $file) {
	require_once($file);
}

// Redirect all non-internal pages to https if https is enabled.
if (!isCli() && $https && $_SERVER["HTTPS"] != "on" && strpos($_SERVER['SCRIPT_NAME'], 'internal') === false) {
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
	die();
}

$userfields = userfields();

if (!isCli()) {
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
}

if (isset($_GET[$cookieName])) {
	$_COOKIE[$cookieName] = $_GET[$cookieName];
}

// Authentication code.
if (isset($_COOKIE[$cookieName])) {
	$id = result("SELECT id FROM users WHERE token = ?", [$_COOKIE[$cookieName]]);

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
