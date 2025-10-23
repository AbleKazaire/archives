<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$ctx = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 15]]);
$s = @file_get_contents('https://getcomposer.org/versions', false, $ctx);
var_dump($s);
?>