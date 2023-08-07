<?php
require('lib/common.php');

$levels = query("SELECT $userfields l.id,l.title FROM levels l JOIN users u ON l.author = u.id WHERE l.locked = 0 ORDER BY l.downloads DESC, l.id DESC LIMIT $lpp");

echo twigloader()->render('popular.twig', ['levels' => fetchArray($levels)]);
