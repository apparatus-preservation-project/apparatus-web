<?php
require('lib/common.php');

if (!$log) die();

if (isset($_POST['action'])) {
	$customcolor	= strtolower($_POST['customcolor']) != '#0000aa' ? $_POST['customcolor'] : null;
	$about			= $_POST['about'] ?: null;
	$darkmode		= $_POST['darkmode'] ? 1 : 0; // clamp it for good measure
	$timezone		= $_POST['timezone'] != 'Europe/Stockholm' ? $_POST['timezone'] : null;

	// check custom color
	$customcolor = ltrim($customcolor, '#');
	if (!preg_match('/^([A-Fa-f0-9]{6})$/', $_POST['customcolor'])) {
		// reset if invalid
		$customcolor = $userdata['customcolor'];
	}

	query("UPDATE users SET customcolor = ?, about = ?, darkmode = ?, timezone = ? WHERE id = ?",
		[$customcolor, $about, $darkmode, $timezone, $userdata['id']]);

	redirect(sprintf("/user.php?id=%s&edited", $userdata['id']));
}

$timezones = [];
foreach (timezone_identifiers_list() as $tz)
	$timezones[] = $tz;

echo twigloader()->render('settings.twig', [
	'timezones' => $timezones
]);