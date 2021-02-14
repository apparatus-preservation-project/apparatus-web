<?php
require('lib/common.php');

$twig = twigloader();
echo $twig->render('_markdown.twig', [
	'pagetitle' => 'Tips & Tricks',
	'file' => 'tips.md'
]);