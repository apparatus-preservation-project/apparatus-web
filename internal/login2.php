<?php
chdir('../');
include('lib/common.php');

$name = (isset($_GET['u']) ? $_GET['u'] : null);
$pass = (isset($_GET['p']) ? $_GET['p'] : null);

$logindata = fetch("SELECT id,password,token FROM users WHERE name = ?", [$name]);

if ($logindata && password_verify($pass, $logindata['password'])) {
	setcookie($cookieName, $logindata['token'], 2147483647);
	die('OK:'.$logindata['token']);
} else {
	echo 'Invalid credentials.';
}

