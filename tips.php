<?php
require('lib/common.php');

echo twigloader()->render('_markdown.twig', [
	'pagetitle' => 'Tips & Tricks',
	'file' => 'tips.md'
]);