<?php
$ch = curl_init('https://getcomposer.org/versions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_CAINFO, "C:/Program Files (x86)/php-8.4.13/cacert.pem");
$result = curl_exec($ch);
if ($result === false) {
    echo "CURL Error: " . curl_error($ch) . "\n";
    echo "Error number: " . curl_errno($ch) . "\n";
} else {
    echo "SSL connection successful!\n";
}
curl_close($ch);
?>