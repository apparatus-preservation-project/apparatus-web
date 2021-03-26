<?php

$lvl_example = [
	'id' => 1,
	'title' => 'Example Level',
	'u_id' => 1,
	'u_name' => 'null',
];

function type_to_cat($type) {
	switch ($type) {
		case 'apparatus':	return 1;
		case 'challenge':	return 2;
		case 'interactive':	return 3;
	}
}

function cat_to_type($cat) {
	switch ($cat) {
		case 1:		return 'apparatus';
		case 2:		return 'challenge';
		case 3:		return 'interactive';
	}
}

function cmtTypeToNum($type) {
	switch ($type) {
		case 'level':	return 1;
		case 'news':	return 2;
		case 'contest':	return 3;
		case 'user':	return 4;
		case 'chat':	return 5;
		case 'package':	return 6;
	}
}

/**
 * Create a level box.
 *
 * @param array $lvl Level information. For an example list of fields, check $lvl_example.
 * @return string Created level box.
 */
function level($lvl, $featuredtext = '', $pkg = false) {
	// TODO: rewrite this entire function...
	$img = (!$pkg ? (!$lvl['locked'] ? "levels/thumbs/{$lvl['id']}.jpg" : 'assets/locked_thumb.svg') : 'assets/package_thumb.svg');
	$levelname = htmlspecialchars($lvl['title']);
	return <<<HTML
<li class="level" id="l-{$lvl['id']}">
	<a href="level.php?id={$lvl['id']}">
		<img src="$img" class="icon">
		<h3>{$levelname}</h3>
		<p>by {$lvl['u_name']}</p>
		<p></p>
	</a>
</li>
HTML;
}
