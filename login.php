<?php
include('lib/common.php');

if (isset($_POST['logout'])) {
	setcookie('t', '');
	redirect('./');
}

if ($log) redirect('./');

if (isset($_POST['action'])) {
	$logindata = fetch("SELECT id,password,token FROM users WHERE name = ?", [$_POST['name']]);

	if (password_verify($_POST['pass'], $logindata['password'])) {
		setcookie('t', $logindata['token'], 2147483647);
	}
	redirect('./');
}

$twig = twigloader();
echo $twig->render('login.twig');