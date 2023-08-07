<?php
require('lib/common.php');

echo twigloader()->render('_markdown.twig', [
	'pagetitle' => 'Download',
	'file' => 'download.md'
]);
