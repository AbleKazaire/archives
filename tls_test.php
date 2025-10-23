<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
$host = 'getcomposer.org';
$port = 443;
$ctx = stream_context_create([
  'ssl'=>[
    'capture_peer_cert'=>true,
    'verify_peer'=>true,
    'verify_peer_name'=>true,
    'allow_self_signed'=>false,
    'cafile' => 'C:/Users/WH-COMPUTERS/Desktop/new XAMMP/php/cacert.pem'
  ]
]);
$fp = @stream_socket_client('tls://' . $host . ':' . $port, $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx);
var_dump($fp);
var_dump($errno, $errstr);
if ($fp) {
  $meta = stream_get_meta_data($fp);
  var_dump($meta);
  $cert = stream_context_get_options($fp);
  var_export($cert);
  fclose($fp);
}
?>
