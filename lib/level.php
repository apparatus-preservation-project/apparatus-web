<?php

function type_to_cat($type) {
	return match ($type) {
		'apparatus'		=> 1,
		'challenge'		=> 2,
		'interactive'	=> 3,
		default 		=> 99, // Fallback option: none
	};
}

function cat_to_type($cat) {
	return match ($cat) {
		1 => 'apparatus',
		2 => 'challenge',
		3 => 'interactive'
	};
}

function cmtTypeToNum($type) {
	return match ($type) {
		'level'		=> 1,
		'news'		=> 2,
		//'contest'	=> 3,
		'user'		=> 4,
		'chat'		=> 5,
		//'package'	=> 6
	};
}

function level($lvl) {
	// TODO: rewrite this entire function...
	$img = (!isset($lvl['locked']) || !$lvl['locked'] ? "levels/thumbs/low/{$lvl['id']}.jpg" : 'assets/locked_thumb.svg');
	$levelname = htmlspecialchars($lvl['title']);
	return <<<HTML
<li class="level" id="l-{$lvl['id']}">
	<a href="/level.php?id={$lvl['id']}">
		<img src="/$img" class="icon">
		<h3>{$levelname}</h3>
		<p>by {$lvl['u_name']}</p>
	</a>
</li>
HTML;
}
