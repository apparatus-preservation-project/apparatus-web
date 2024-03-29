<?php
chdir('../');
require('lib/common.php');

$name = $_GET['u'] ?? null;
$mail = $_GET['e'] ?? null;
$pass = $_GET['p'] ?? null;

if (!isset($name)) die('give me an username');
if (!isset($mail)) die('give me an email');
if (!isset($pass) || strlen($pass) < 6) die('give me a good password');
if (result("SELECT COUNT(*) FROM users WHERE name = ?", [$name])) die('give me an unique username'); // "This username is already taken."

// All possible invalid credentials have been checked, it should be successful now.
$token = bin2hex(random_bytes(20));
query("INSERT INTO users (name, password, email, token, joined) VALUES (?,?,?,?,?)",
	[$name,password_hash($pass, PASSWORD_DEFAULT), $mail, $token, time()]);

die("OK:$token");
