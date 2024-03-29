<?php

function userlink($user, $pre = '') {
	if ($user[$pre.'customcolor'])
		$user[$pre.'name'] = sprintf('<span style="color:#%s">%s</span>', $user[$pre.'customcolor'], $user[$pre.'name']);

	return <<<HTML
		<a class="user" href="/user.php?id={$user[$pre.'id']}"><span class="t_user">{$user[$pre.'name']}</span></a>
HTML;
}

function userfields() {
	$fields = ['id', 'name', 'customcolor'];

	$out = '';
	foreach ($fields as $field) {
		$out .= sprintf('u.%s u_%s,', $field, $field);
	}

	return $out;
}