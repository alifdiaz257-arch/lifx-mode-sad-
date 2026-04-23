<?php
$data = json_decode(file_get_contents('php://input'), true);
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
$time = date('Y-m-d H:i:s');

if (isset($data['lat']) && isset($data['lon'])) {
    $log = "[$time] IP: $ip | Lat: {$data['lat']} | Lon: {$data['lon']}\n";
    file_put_contents('locations.txt', $log, FILE_APPEND);
}

echo json_encode(["status" => "ok"]);
?>