<?php
chdir('../');
require('lib/common.php');

if (isset($_GET['t'])) {
	$logindata = fetch("SELECT id,password,token FROM users WHERE token = ?", [$_GET['t']]);

	if ($logindata) {
		setcookie('t', $logindata['token'], 2147483647, '/');
	}

	header("Location: /");
}
