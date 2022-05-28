<?php
$host = '127.0.0.1';
$db   = 'apparatus';
$user = '';
$pass = '';

$tplCache = 'templates/cache';
$tplNoCache = false; // **DO NOT SET AS TRUE IN PROD - DEV ONLY**

$lpp = 25;

// Should dark mode be default?
$darkModeDefault = true;

// Redirect all non-internal pages to https.
$https = true;

// Cookie token name. Don't change this too often as it'll invalidate old logins!
$cookieName = 't';

// Website domain.
$domain = 'https://example.org';

// Stub function to put special information in the footer.
function customInfo() { }
