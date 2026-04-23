<?php
$webhook = "https://discord.com/api/webhooks/1491936474273419495/QQoiBEDr6mUBPiqo52LiPYGEEPyX9olrZiKd6oRJBjBMe-YcGEODNVTt7KpEbTM14Rzl";

$data = json_encode([
    "content" => "✅ LIFX SYSTEM ACTIVE! Webhook berfungsi dengan baik.",
    "embeds" => [[
        "title" => "Webhook Test",
        "description" => "Jika Anda melihat pesan ini, berarti webhook sudah benar dan siap menerima data.",
        "color" => 0x00ff00,
        "footer" => ["text" => "😈 | LIFX System"]
    ]]
]);

$ch = curl_init($webhook);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h1>Discord Webhook Test</h1>";
echo "<p>HTTP Code: <strong>$httpCode</strong></p>";
echo "<p>Response: " . htmlspecialchars($response) . "</p>";

if($httpCode == 204) {
    echo "<p style='color:green;'>✅ WEBHOOK BERFUNGSI! Cek channel Discord kamu.</p>";
} else {
    echo "<p style='color:red;'>❌ WEBHOOK GAGAL! Periksa URL webhook.</p>";
}
?>