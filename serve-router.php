<?php

// Small router for testing on phones through the Cloudflare Tunnel.
// It works no matter which folder PowerShell is in, because it uses this file's own folder.
// Real files (CSS, JS, images) are sent as they are. Everything else goes to Laravel.

$public = __DIR__.'/public';
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');

if ($uri !== '/' && is_file($public.$uri)) {
    return false;
}

require_once $public.'/index.php';
